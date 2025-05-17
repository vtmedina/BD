<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $idQuadra = intval($_POST['id_quadra']);
    $idClube = intval($_POST['id_clube']);
    
    $sql = "INSERT INTO Suporte (data_solicitacao, descricao, status, ID_Quadra, ID_Clube) 
            VALUES (CURDATE(), '$descricao', 'pendente', $idQuadra, $idClube)";
    
    if ($conn->query($sql) === TRUE) {
        echo "Novo pedido de suporte registrado com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

$pendentesSql = "SELECT * FROM Suporte WHERE status = 'pendente'";
$pendentesResult = $conn->query($pendentesSql);

$resolvidosSql = "SELECT * FROM Suporte WHERE status = 'resolvido'";
$resolvidosResult = $conn->query($resolvidosSql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0 450px;
            padding: 0;
            background-color: #f2f2f2;
            text-align: center;
        }
        .container {
            width: 770px;
            margin: 0;
            padding: 20px;
            background-color: #fff;
            position: fixed;
            min-height: 640px;
        }

        .logo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100px;
        }
        h1 {
            margin-left: -120px;
            color: #333;
            font-size: 14px;
        }
        h2 {
            color: #333;
        }
        form {
            position: absolute;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 200px;
            padding-bottom: 10px;
        }
        input[type=text], textarea, select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 8px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 555px;
            align: right;
            margin-left: 220px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            background: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .popup h2 {
            margin-top: 0;
        }
        .popup button.close {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .popup button.delete {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }
        .popup button.mark-completed {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">
        <img src="images/cadastros.jpeg" alt="Logotipo">
    </div>

    <h2>Suporte</h2>
    <form action="suporte.php" method="POST" style="width:200px; align:right;">
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" rows="4" required></textarea>

        <label for="id_quadra">Quadra:</label>
        <select id="id_quadra" name="id_quadra" required>
            <?php
            $quadrasSql = "SELECT ID, tipo FROM Quadras WHERE disponibilidade = 'disponível'";
            $quadrasResult = $conn->query($quadrasSql);
            
            while ($row = $quadrasResult->fetch_assoc()) {
                echo "<option value='" . $row['ID'] . "'>" . $row['tipo'] . "</option>";
            }
            ?>
        </select>

        <label for="id_clube">Clube:</label>
        <select id="id_clube" name="id_clube" required>
            <?php
            $clubesSql = "SELECT ID, nome FROM Clube";
            $clubesResult = $conn->query($clubesSql);
            
            while ($row = $clubesResult->fetch_assoc()) {
                echo "<option value='" . $row['ID'] . "'>" . $row['nome'] . "</option>";
            }
            ?>
        </select>

        <input type="submit" name="submit" value="Registrar Pedido">
    </form>

    <h1>Pedidos de Suporte Pendentes</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Quadra</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($pendentesResult->num_rows > 0) {
                while($row = $pendentesResult->fetch_assoc()) {
                    // Obter o tipo da quadra
                    $quadraSql = "SELECT tipo FROM Quadras WHERE ID = " . $row['ID_Quadra'];
                    $quadraResult = $conn->query($quadraSql);
                    $quadraTipo = $quadraResult->fetch_assoc()['tipo'];

                    echo "<tr>";
                    echo "<td>" . $row['ID'] . "</td>";
                    echo "<td>" . $row['data_solicitacao'] . "</td>";
                    echo "<td>" . $quadraTipo . "</td>";
                    echo "<td><button onclick='showPopup(" . $row['ID'] . ")'>Detalhes</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhum pedido pendente.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h1>Pedidos de Suporte Resolvidos</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Quadra</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resolvidosResult->num_rows > 0) {
                while($row = $resolvidosResult->fetch_assoc()) {
                    // Obter o tipo da quadra
                    $quadraSql = "SELECT tipo FROM Quadras WHERE ID = " . $row['ID_Quadra'];
                    $quadraResult = $conn->query($quadraSql);
                    $quadraTipo = $quadraResult->fetch_assoc()['tipo'];

                    echo "<tr>";
                    echo "<td>" . $row['ID'] . "</td>";
                    echo "<td>" . $row['data_solicitacao'] . "</td>";
                    echo "<td>" . $quadraTipo . "</td>";
                    echo "<td><button onclick='showPopup(" . $row['ID'] . ")'>Detalhes</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhum pedido resolvido.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Popup -->
<div id="popup" class="popup">
    <button class="close" onclick="closePopup()">X</button>
    <h2>Detalhes do Pedido</h2>
    <div id="popupContent"></div>
    <button id="markCompleted" class="mark-completed" style="display: none;" onclick="markCompleted()">Marcar como Concluído</button>
</div>

<script>
function showPopup(id) {
    fetch('get_suporte_details.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        let popupContent = `
            <p><strong>ID:</strong> ${data.ID}</p>
            <p><strong>Data de Solicitação:</strong> ${data.data_solicitacao}</p>
            <p><strong>Descrição:</strong> ${data.descricao}</p>
            <p><strong>Status:</strong> ${data.status}</p>
            <p><strong>ID da Quadra:</strong> ${data.ID_Quadra}</p>
            <p><strong>ID do Clube:</strong> ${data.ID_Clube}</p>
        `;
        document.getElementById('popupContent').innerHTML = popupContent;
        document.getElementById('popup').style.display = 'block';

        // Mostrar o botão de marcar como concluído apenas para pedidos pendentes
        if (data.status === 'pendente') {
            document.getElementById('markCompleted').style.display = 'block';
            document.getElementById('markCompleted').setAttribute('data-id', data.ID);
        } else {
            document.getElementById('markCompleted').style.display = 'none';
        }
    });
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

function markCompleted() {
    let id = document.getElementById('markCompleted').getAttribute('data-id');
    fetch('mark_completed.php?id=' + id)
    .then(response => response.text())
    .then(text => {
        alert(text);
        closePopup();
        location.reload(); // Recarregar a página para refletir as mudanças
    });
}
</script>

</body>
</html>

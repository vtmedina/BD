<?php
include 'conexao.php';

$search = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = htmlspecialchars(strip_tags($_GET['search']));
}

$sql = "SELECT id, nome, data, hora_inicio, hora_fim, local FROM eventos";
if (!empty($search)) {
    $sql .= " WHERE nome LIKE ? OR local LIKE ?";
}
$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $id = intval($_POST['delete']);
        $stmt = $conn->prepare("DELETE FROM eventos WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Evento excluído com sucesso!";
            $stmt->close();
        } else {
            $message = "Erro: " . $stmt->error;
        }
    } else {
        $nome = isset($_POST['nome']) ? htmlspecialchars(strip_tags($_POST['nome'])) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : '';
        $horaInicio = isset($_POST['horaInicio']) ? $_POST['horaInicio'] : '';
        $horaFim = isset($_POST['horaFim']) ? $_POST['horaFim'] : '';
        $local = isset($_POST['local']) ? htmlspecialchars(strip_tags($_POST['local'])) : '';
        $descricao = isset($_POST['descricao']) ? htmlspecialchars(strip_tags($_POST['descricao'])) : '';

        $stmt = $conn->prepare("INSERT INTO eventos (nome, data, hora_inicio, hora_fim, local, descricao) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome, $data, $horaInicio, $horaFim, $local, $descricao);

        if ($stmt->execute()) {
            $message = "Evento criado com sucesso!";
            $stmt->close();
        } else {
            $message = "Erro: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
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
            min-height: 640px;
            margin: 0;
            padding: 20px;
            background-color: #fff;
            position: fixed;
        }

        .logo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100px;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 200px;
            padding-bottom: 10px;
        }

        input[type="text"], input[type="date"], input[type="time"], textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
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

        .message {
            color: green;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }

        table {
            width: 555px;
            margin-top: -415px;
            margin-left: 215px;
            margin-right: auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

.popup {
    display: none;
    position: fixed;
    top: 20%;
    left: 30%;
    width: 40%;
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    text-align: center; /* Centraliza o conteúdo */
}

.popup button {
    padding: 8px 16px;
    font-size: 16px;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.popup .delete-btn {
    background-color: #f44336;
    margin-top: 20px; /* Espaço acima do botão */
}

.popup .delete-btn:hover {
    background-color: #e53935;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images/cadastros.jpeg" alt="Logotipo">
        </div>

        <h1>Eventos</h1>

        <?php if ($message): ?>
            <p class="<?php echo strpos($message, 'Erro') === false ? 'message' : 'error'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="nome" placeholder="Nome do Evento" required>
            <input type="date" name="data" placeholder="Data" required>
            <input type="time" name="horaInicio" placeholder="Hora de Início" required>
            <input type="time" name="horaFim" placeholder="Hora de Fim" required>
            <input type="text" name="local" placeholder="Local" required>
            <textarea name="descricao" placeholder="Descrição do Evento" rows="4" required></textarea>
            <input type="submit" value="Criar Evento">
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Data</th>
                <th>Hora de Início</th>
                <th>Hora de Fim</th>
                <th>Local</th>
                <th>Ações</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["nome"] . "</td>
                        <td>" . $row["data"] . "</td>
                        <td>" . $row["hora_inicio"] . "</td>
                        <td>" . $row["hora_fim"] . "</td>
                        <td>" . $row["local"] . "</td>
                        <td><button onclick=\"showDetails(" . $row["id"] . ")\">ver</button></td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Nenhum evento encontrado</td></tr>";
            }
            ?>
        </table>
    </div>

<div id="popup" class="popup">
    <span id="popupClose">&times;</span>
    <h2 id="popupTitle"></h2>
    <p id="popupDescription"></p>
    <p id="popupData"></p>
    <p id="popupHoraInicio"></p>
    <p id="popupHoraFim"></p>
    <p id="popupLocal"></p>
    <form id="deleteForm" method="POST" action="">
        <input type="hidden" id="popupId" name="delete">
        <button type="submit" class="delete-btn">Deletar Evento</button>
    </form>
</div>

    <script>
    function showDetails(eventId) {
        fetch('get_evento.php?id=' + eventId)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    document.getElementById('popupTitle').innerText = data.nome;
                    document.getElementById('popupDescription').innerText = data.descricao;
                    document.getElementById('popupData').innerText = 'Data: ' + data.data;
                    document.getElementById('popupHoraInicio').innerText = 'Hora de Início: ' + data.hora_inicio;
                    document.getElementById('popupHoraFim').innerText = 'Hora de Fim: ' + data.hora_fim;
                    document.getElementById('popupLocal').innerText = 'Local: ' + data.local;
                    document.getElementById('popupId').value = eventId;
                    document.getElementById('popup').style.display = 'block';
                }
            });
    }

    document.getElementById('popupClose').onclick = function() {
        document.getElementById('popup').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('popup')) {
            document.getElementById('popup').style.display = 'none';
        }
    }
    </script>
</body>
</html>

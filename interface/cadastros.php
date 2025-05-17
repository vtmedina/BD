<?php
include 'conexao.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Previne SQL Injection
    $stmt = $conn->prepare("DELETE FROM membros WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: cadastros.php?success=2");
        exit();
    } else {
        echo "Erro: " . $stmt->error;
    }
}

$search = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = htmlspecialchars(strip_tags($_GET['search']));
}

$sql = "SELECT id, nome, email, telefone FROM membros";
if (!empty($search)) {
    $sql .= " WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ?";
}
$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Atualizar informações do membro
        $stmt = $conn->prepare("UPDATE membros SET nome = ?, email = ?, telefone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $email, $telefone, $id);

        $nome = htmlspecialchars(strip_tags($_POST['nome']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $telefone = htmlspecialchars(strip_tags($_POST['telefone']));
        $id = intval($_POST['id']);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: cadastros.php?success=2");
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }
    } else {
        // Inserir novo membro
        $stmt = $conn->prepare("INSERT INTO membros (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $telefone);

        $nome = htmlspecialchars(strip_tags($_POST['nome']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $telefone = htmlspecialchars(strip_tags($_POST['telefone']));

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: cadastros.php?success=1");
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
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
            height: 640px;
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

        input[type="text"], input[type="email"], input[type="tel"], input[type="search"] {
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
            margin-top: -286px;
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

        .success-message {
            color: green;
            margin-bottom: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 10px;
            border: 1px solid #888;
            width: 220px;
            margin-top:-20px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images/cadastros.jpeg" alt="Logotipo">
        </div>

        <h1>Membros</h1>

        <?php if (isset($_GET['success'])): ?>
            <p class="success-message">
                <?php 
                echo ($_GET['success'] == 1) ? "Novo membro cadastrado com sucesso!" : "Informações do membro atualizadas com sucesso!";
                ?>
            </p>
        <?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] == 1): ?>
        <p class="success-message">Novo membro cadastrado com sucesso!</p>
    <?php elseif ($_GET['success'] == 2): ?>
        <p class="success-message">Membro excluído com sucesso!</p>
    <?php endif; ?>
<?php endif; ?>

        <form method="GET" action="cadastros.php">
            <input type="search" name="search" placeholder="Buscar por nome, e-mail ou telefone" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Buscar">
        </form>

        <form method="POST" action="cadastros.php">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="tel" name="telefone" placeholder="Telefone" required>
            <input type="submit" value="Cadastrar Membro">
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
            <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["nome"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>" . $row["telefone"] . "</td>
                    <td>
                        <a href='?delete=" . $row["id"] . "' onclick=\"return confirm('Tem certeza de que deseja excluir este membro?');\">Deletar</a>
                        <button onclick=\"openModal(" . $row["id"] . ")\">Ver</button>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Nenhum membro encontrado</td></tr>";
    }
    ?>
        </table>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>credenciais</h2>
            <div id="modal-body">
            </div>
        </div>
    </div>

    <script>
        function openModal(membroId) {
            var modal = document.getElementById("myModal");
            var modalBody = document.getElementById("modal-body");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_membro.php?id=" + membroId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    modalBody.innerHTML = xhr.responseText;
                    modal.style.display = "block";
                }
            };
            xhr.send();
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById("myModal")) {
                closeModal();
            }
        }
    </script>
</body>
</html>

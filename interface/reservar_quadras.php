<?php
include 'conexao.php';

$quadrasDisponiveis = [];
$reservas = [];

$sql = "SELECT ID, tipo FROM Quadras WHERE disponibilidade = 'disponível'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $quadrasDisponiveis[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'reservar') {
    $idQuadra = htmlspecialchars(strip_tags($_POST['idQuadra']));
    $idMembro = htmlspecialchars(strip_tags($_POST['idMembro']));
    $dataReserva = htmlspecialchars(strip_tags($_POST['dataReserva']));
    $horaInicio = htmlspecialchars(strip_tags($_POST['horaInicio']));
    $horaFim = htmlspecialchars(strip_tags($_POST['horaFim']));

    $sql = "INSERT INTO Reservas (ID_Quadra, ID_Membro, data_reserva, hora_inicio, hora_fim, data_criacao, ID_Clube)
            VALUES (?, ?, ?, ?, ?, CURDATE(), 1)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $idQuadra, $idMembro, $dataReserva, $horaInicio, $horaFim);

    try {
        if ($stmt->execute()) {
            $successMessage = "Reserva feita com sucesso!";
        } else {
            $errorMessage = "Erro ao fazer reserva: " . $stmt->error;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 45000) {
            $errorMessage = "Conflito de agendamento.";
        } else {
            $errorMessage = "Erro ao fazer reserva: " . $e->getMessage();
        }
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'consultar') {
    $idQuadra = htmlspecialchars(strip_tags($_POST['idQuadra']));

    $sql = "SELECT r.ID, r.data_reserva, r.hora_inicio, r.hora_fim, m.Nome as nome_membro
            FROM Reservas r
            JOIN Membros m ON r.ID_Membro = m.ID
            WHERE r.ID_Quadra = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idQuadra);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reservas[] = $row;
        }
    } else {
        $errorMessage = "Erro ao buscar reservas: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Quadra</title>
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
            width: 100px;
            position: fixed;
            left: 0;
            top: 0;
        }

        .form-container {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        .form-reserva,
        .form-consulta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 800px;
            margin: 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-reserva {
            background-color: #f2f2f2;
            width: 200px;
        }

        .form-consulta {
            background-color: #f2f2f2;
            width: 510px;
            margin-left: 240px;
            margin-top: -464px;
        }

        label {
            text-align: left;
            font-weight: bold;
        }

        select, input[type="text"], input[type="date"], input[type="time"], input[type="submit"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .success-message, .error-message {
            color: green;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
        }

        table {
    width: 533px;
    margin-top: 10px;
    margin-left: 240px;
    margin-right: auto;
    border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1000;
        }

        .popup.active {
            display: block;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .popup-overlay.active {
            display: block;
        }

        .btn-cancelar {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-cancelar:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images/cadastros.jpeg" alt="Logotipo">
        </div>

        <h1>Reservas</h1>

        <?php if (isset($successMessage)): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php elseif (isset($errorMessage)): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form class="form-reserva" method="POST" action="">
            <input type="hidden" name="action" value="reservar">
            
            <label for="idQuadraReserva">ID da Quadra:</label>
            <select id="idQuadraReserva" name="idQuadra" required>
                <option value="" disabled selected>Selecione uma quadra</option>
                <?php foreach ($quadrasDisponiveis as $quadra): ?>
                    <option value="<?php echo $quadra['ID']; ?>"><?php echo $quadra['tipo']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label for="idMembro">ID do Membro:</label>
            <input type="text" id="idMembro" name="idMembro" required>
            
            <label for="dataReserva">Data da Reserva:</label>
            <input type="date" id="dataReserva" name="dataReserva" required>
            
            <label for="horaInicio">Hora de Início:</label>
            <input type="time" id="horaInicio" name="horaInicio" required>
            
            <label for="horaFim">Hora de Fim:</label>
            <input type="time" id="horaFim" name="horaFim" required>
            
            <input type="submit" value="Reservar">
        </form>

        <form class="form-consulta" method="POST" action="">
            <input type="hidden" name="action" value="consultar">
            
            <label for="idQuadraConsulta">Escolha a Quadra:</label>
            <select id="idQuadraConsulta" name="idQuadra" required>
                <option value="" disabled selected>Selecione uma quadra</option>
                <?php foreach ($quadrasDisponiveis as $quadra): ?>
                    <option value="<?php echo $quadra['ID']; ?>"><?php echo $quadra['tipo']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="submit" value="Consultar Reservas">
        </form>

        <?php if (!empty($reservas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Hora Início</th>
                        <th>Hora Fim</th>
                        <th>Nome do Membro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo $reserva['ID']; ?></td>
                            <td><?php echo $reserva['data_reserva']; ?></td>
                            <td><?php echo $reserva['hora_inicio']; ?></td>
                            <td><?php echo $reserva['hora_fim']; ?></td>
                            <td><?php echo $reserva['nome_membro']; ?></td>
                            <td>
                                <button onclick="showPopup(<?php echo $reserva['ID']; ?>)">VER</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="popup-overlay" class="popup-overlay"></div>
    <div id="popup" class="popup">
        <h2>Detalhes da Reserva</h2>
        <div id="popup-content"></div>
        <button id="btn-cancelar" class="btn-cancelar" onclick="cancelarReserva()">Cancelar Reserva</button>
        <button onclick="closePopup()">Fechar</button>
    </div>

    <script>
        let reservaIdGlobal = null;

        function showPopup(reservaId) {
            reservaIdGlobal = reservaId; // Armazena o ID da reserva globalmente
            var overlay = document.getElementById('popup-overlay');
            var popup = document.getElementById('popup');
            var popupContent = document.getElementById('popup-content');

            fetch('get_reserva_details.php?id=' + reservaId)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        popupContent.innerHTML = `
                            <p><strong>ID da Reserva:</strong> ${data.ID}</p>
                            <p><strong>Data da Reserva:</strong> ${data.data_reserva}</p>
                            <p><strong>Hora de Início:</strong> ${data.hora_inicio}</p>
                            <p><strong>Hora de Fim:</strong> ${data.hora_fim}</p>
                            <p><strong>Nome do Membro:</strong> ${data.nome_membro}</p>
                        `;
                        overlay.classList.add('active');
                        popup.classList.add('active');
                    }
                });
        }

        function closePopup() {
            var overlay = document.getElementById('popup-overlay');
            var popup = document.getElementById('popup');
            overlay.classList.remove('active');
            popup.classList.remove('active');
        }

        function cancelarReserva() {
            if (reservaIdGlobal) {
                fetch('cancelar_reserva.php?id=' + reservaIdGlobal, {
                    method: 'POST'
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Reserva cancelada com sucesso!');
                        closePopup();
                        location.reload(); // Atualiza a página para refletir as mudanças
                    } else {
                        alert('Erro ao cancelar reserva.');
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $reservaId = htmlspecialchars(strip_tags($_GET['id']));

    $sql = "SELECT r.ID, r.data_reserva, r.hora_inicio, r.hora_fim, m.Nome AS nome_membro
            FROM Reservas r
            JOIN Membros m ON r.ID_Membro = m.ID
            WHERE r.ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservaId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $reserva = $result->fetch_assoc();
        echo json_encode($reserva);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
}

$conn->close();
?>

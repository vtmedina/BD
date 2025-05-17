<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM Suporte WHERE ID = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Nenhum dado encontrado."]);
    }
} else {
    echo json_encode(["error" => "ID não fornecido."]);
}
?>

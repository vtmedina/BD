<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE Suporte SET status = 'resolvido' WHERE ID = $id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Pedido de suporte marcado como concluído!";
    } else {
        echo "Erro: " . $conn->error;
    }
} else {
    echo "ID não fornecido.";
}

$conn->close();
?>

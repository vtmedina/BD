<?php
include 'conexao.php';

echo "<style>
    form p {
        margin: 5px 0;
    }
    form input[type='text'], form input[type='email'], form input[type='tel'] {
        margin-bottom: 10px;
        width: 100%;
    }
</style>";

if (isset($_GET['id'])) {
    $membroId = intval($_GET['id']);

    $sql = "SELECT m.id, m.nome, m.email, m.telefone, 
                   ms.status AS mensalidade_status, ms.data_inicio, ms.data_fim
            FROM membros m
            LEFT JOIN mensalidade ms ON m.id = ms.ID_Membro
            WHERE m.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $membroId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $status = ($row["mensalidade_status"] == "pago") ? "Pago" : "<span style='color: red;'>Pendente</span>";
        echo "<form method='POST' action='cadastros.php'>
                <input type='hidden' name='id' value='" . $row["id"] . "'>
                <p><strong>ID:</strong> " . $row["id"] . "</p>
                <p><strong>Nome:</strong> <input type='text' name='nome' value='" . htmlspecialchars($row["nome"]) . "' required></p>
                <p><strong>Email:</strong> <input type='email' name='email' value='" . htmlspecialchars($row["email"]) . "' required></p>
                <p><strong>Telefone:</strong> <input type='tel' name='telefone' value='" . htmlspecialchars($row["telefone"]) . "' required></p>
                <p><strong>Status da Mensalidade:</strong> " . $status . "</p>
                <p><strong>Data de Início:</strong> " . $row["data_inicio"] . "</p>
                <p><strong>Data de Fim:</strong> " . $row["data_fim"] . "</p>
                <input type='submit' name='update' value='Atualizar'>
              </form>";
    } else {
        echo "Membro não encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

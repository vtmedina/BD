<?php
include 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complexo Esportivo</title>
    <!-- Fonte do Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <!-- Estilos CSS -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('images/background.jpg'); /* Caminho para a imagem de fundo */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            font-family: 'Roboto', sans-serif;
            color: #fff;
            text-align: center;
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: center;
            align-items: left;
            backdrop-filter: brightness(0.7);
        }
        
        h1 {
            font-size: 3em;
            margin-bottom: 0.5em;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
        }
        
        p {
            font-size: 1.5em;
            margin-bottom: 1.5em;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }
        
        .menu {
            display: flex;
            flex-direction: column;
            gap: 1em;
            width: 180px;
            margin-left: 20px;
            margin-top: -220px;
        }
        
        .menu a {
            text-decoration: none;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 0.5em 2em;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            box-shadow: 2px 4px 10px rgba(0,0,0,0.5);
        }
        
        .menu a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        footer {
            position: absolute;
            bottom: 20px;
            font-size: 0.9em;
            color: #ccc;
            width: 180px;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <h1>Complexo Esportivo</h1>
    <p>sistema de gerenciamento</p>
    <div class="menu">
        <a href="cadastros.php">cadastros</a>
        <a href="reservar_quadras.php">reservas</a>
        <a href="eventos.php">eventos</a>
        <a href="suporte.php">suporte</a>
    </div>
    <footer>
        &copy; <?php echo date('Y'); ?> Complexo Esportivo. Painel de gerenciamento.
    </footer>
</body>
</html>

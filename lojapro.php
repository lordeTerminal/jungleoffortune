<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Conectar ao banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Obter o saldo atual do usuário
$stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($saldo);
$stmt->fetch();
$stmt->close();

// Verifica se o usuário clicou em um dos botões para adicionar moedas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_100'])) {
        $quantidade = 100;
        $redirectPage = "phpqrcode/trigger.php"; // Redireciona para trigger.php para 100 moedas
    } elseif (isset($_POST['add_1000'])) {
        $quantidade = 1000;
        $redirectPage = "phpqrcode/trigger40.php"; // Redireciona para trigger40.php para 1000 moedas
    }

    // Salvar o pedido de moedas na tabela pedidos_moedas
    $stmt = $conn->prepare("INSERT INTO pedidos_moedas (user_id, quantidade) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $quantidade);
    $stmt->execute();
    $stmt->close();

    // Redirecionar para a página de geração do QR Code
    header("Location: $redirectPage");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adquirir Moedas</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">

	<style>

		h2{color: #00563B; font-weight: bolder; }
		p{color: black; font-weight: bolder; }
		span{color: #B8860B;font-weight: bolder;  }
		.status{color: #581845;font-weight: bolder; }
		.back{color: #CC5500;font-weight: bolder; background-color: whitesmoke; border-radius: 10px; padding: 5px; }


	</style>


</head>
<body>
    <div class="form">
        <h2>Adquirir Mais Moedas</h2>
        <p>Seu saldo atual: <span> <?php echo $saldo; ?> FATEC Coins </span></p>

        <form method="POST">
            <button type="submit" name="add_100">Adquirir 100 Moedas</button>
            <button type="submit" name="add_1000">Adquirir 1000 Moedas</button>
        </form>

        <br>
        <a class="status"  href="status_pedidos.php">Acompanhar Status dos Pedidos</a>
        <br>
        <a class="back" href="dashboard.php">Voltar ao Dashboard</a>
    </div>
</body>
</html>


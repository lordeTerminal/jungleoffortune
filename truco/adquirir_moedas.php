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
        // Adicionar 100 moedas
        $novo_saldo = $saldo + 100;
    } elseif (isset($_POST['add_1000'])) {
        // Adicionar 1000 moedas
        $novo_saldo = $saldo + 1000;
    }

    // Atualizar o saldo no banco de dados
    $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
    $stmt->bind_param("ii", $novo_saldo, $user_id);
    $stmt->execute();
    $stmt->close();

    // Atualiza o saldo local
    $saldo = $novo_saldo;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adquirir Moedas</title>
</head>
<body>
    <h1>Adquirir Mais Moedas</h1>
    <p>Seu saldo atual: <?php echo $saldo; ?> FATEC Coins</p>

    <form method="POST">
        <button type="submit" name="add_100">Adquirir 100 Moedas</button>
        <button type="submit" name="add_1000">Adquirir 1000 Moedas</button>
    </form>

    <br>
    <a href="dashboard.php">Voltar ao Dashboard</a>
</body>
</html>


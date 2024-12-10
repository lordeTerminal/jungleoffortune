<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se não estiver logado
    header("Location: login.html");
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "golimar10*";
$dbname = "fatec_cassino";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Preparar e executar a consulta para obter o saldo
$stmt = $conn->prepare("SELECT nome, saldo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nome, $saldo);
$stmt->fetch();

// Fecha a consulta e a conexão
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h1>
    <p>Seu saldo atual é: <?php echo $saldo; ?> FATEC Coins</p>

    <a href="perfil.php">Perfil</a>
    <br><br>

    <a href="jogo.php">Ir para o jogo</a> <!-- Link para o jogo -->
    
    <br><br>
    <a href="adquirir_moedas.php">Conseguir mais moedas!</a> <!-- Link para o jogo -->

    <br><br>
    <a href="loja.php">Ir para a Loja</a>
    <br><br>
    <a href="logout.php">Sair</a> <!-- Link para logout -->
</body>
</html>


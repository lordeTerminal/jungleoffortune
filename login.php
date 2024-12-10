<?php
session_start(); // Inicia a sessão


$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";



// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter dados do formulário
$nome = $_POST['nome'];
$password_entered = $_POST['password'];

// Preparar e executar a consulta
$stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE nome = ?");
$stmt->bind_param("s", $nome);
$stmt->execute();
$stmt->bind_result($user_id, $stored_hashed_password);
$stmt->fetch();

if ($stored_hashed_password && password_verify($password_entered, $stored_hashed_password)) {
    // Senha correta, define variáveis de sessão
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $nome;

    // Redireciona para o dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // Senha incorreta ou usuário não encontrado
    echo "Nome de usuário ou senha inválidos.";
}

$stmt->close();
$conn->close();
?>


<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require 'vendor/autoload.php';
use OTPHP\TOTP;

// Configurações do banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter dados do formulário
$nome = $_POST['nome'];
$password_entered = $_POST['password'];
$codigo_2fa = $_POST['codigo_2fa'];

// Preparar e executar a consulta para obter dados do usuário
$stmt = $conn->prepare("SELECT id, password, chave_secreta FROM usuarios WHERE nome = ?");
$stmt->bind_param("s", $nome);
$stmt->execute();
$stmt->bind_result($user_id, $stored_hashed_password, $chave_secreta);
$stmt->fetch();
$stmt->close();

// Verificar se o usuário foi encontrado e a senha é válida
if (!$user_id || !password_verify($password_entered, $stored_hashed_password)) {
    die("Nome de usuário ou senha inválidos.");
}

// Verificar o código 2FA usando a chave secreta do usuário
$totp = TOTP::create($chave_secreta);
if ($totp->verify($codigo_2fa)) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $nome;
    header("Location: dashboard.php");
    exit();
} else {
    echo "Código 2FA incorreto.";
}

$conn->close();
?>


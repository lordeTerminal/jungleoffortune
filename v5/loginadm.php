<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar a sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Verificar se todos os campos foram preenchidos
if (empty($nome) || empty($password_entered) || empty($codigo_2fa)) {
    die("Todos os campos são obrigatórios.");
}

// Preparar e executar a consulta
$stmt = $conn->prepare("SELECT id, password, chave_secreta FROM usuariosadm WHERE nome = ?");
$stmt->bind_param("s", $nome);
$stmt->execute();
$stmt->bind_result($user_id, $stored_hashed_password, $chave_secreta);
$stmt->fetch();
$stmt->close();

// Verificar se o usuário foi encontrado
if (!$user_id) {
    die("Usuário não encontrado.");
}

// Verificar a senha
if (!password_verify($password_entered, $stored_hashed_password)) {
    die("Senha incorreta.");
}

// Verificar o código 2FA usando a chave secreta
require 'vendor/autoload.php';
$totp = \OTPHP\TOTP::create($chave_secreta); // Usando o método correto para criar o TOTP

if ($totp->verify($codigo_2fa)) {
    // 2FA válido, autenticação bem-sucedida
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $nome;
    header("Location: ./painel.php");
    exit();
} else {
    die("Código 2FA incorreto.");
}

$conn->close();
?>


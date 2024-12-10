<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Obter o ID do usuário e o código 2FA enviado pelo formulário
$user_id = $_POST['user_id'] ?? null;
$codigo_2fa = $_POST['codigo_2fa'] ?? null;

if (!$user_id || !$codigo_2fa) {
    die("ID de usuário ou código 2FA não fornecido.");
}

// Recuperar a chave secreta do usuário
$stmt = $conn->prepare("SELECT nome, chave_secreta FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nome, $chave_secreta);
$stmt->fetch();
$stmt->close();

if (!$chave_secreta) {
    die("Chave secreta não encontrada para o usuário.");
}

// Criar o TOTP com a chave secreta e validar o código 2FA
$totp = TOTP::create($chave_secreta);
$totp->setLabel($nome);
$totp->setIssuer("FATEC_Cassino");

if ($totp->verify($codigo_2fa)) {
    // Se o código 2FA for válido, redirecionar para o login
    echo "<p>2FA configurado com sucesso! Redirecionando para o login...</p>";
    header("Refresh: 3; url=login.html");
    exit();
} else {
    // Código 2FA inválido
    echo "Código 2FA incorreto. Tente novamente.";
}

$conn->close();
?>


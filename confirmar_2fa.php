<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter o ID do usuário e o código 2FA enviado pelo formulário
$user_id = $_POST['user_id'];
$codigo_2fa = $_POST['codigo_2fa'];

// Recuperar a chave secreta do administrador no banco de dados
$stmt = $conn->prepare("SELECT chave_secreta FROM usuariosadm WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($chave_secreta);
$stmt->fetch();
$stmt->close();

if (!$chave_secreta) {
    echo "Chave secreta não encontrada para o usuário.";
    exit();
}

// Verificar o código TOTP usando a chave secreta
require 'vendor/autoload.php'; // Verifique o caminho do autoload se necessário
$totp = \OTPHP\TOTP::create($chave_secreta); // Usando o método correto para criar o TOTP
$totp->setLabel("Administrador $user_id"); // Opcional: define um label para identificar

if ($totp->verify($codigo_2fa)) {
    echo "2FA configurado com sucesso!";
    header("Location: loginadm.html");
    exit();
} else {
    echo "Código 2FA incorreto. Tente novamente.";
}

$conn->close();


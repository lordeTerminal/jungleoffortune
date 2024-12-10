<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$password_raw = $_POST['password'];

// Gerar chave secreta para 2FA
require 'vendor/autoload.php'; // Se estiver usando o Composer para o autoload
$totp = \OTPHP\TOTP::create();
$totp->setLabel($nome); // Remove o ':' do label
$totp->setIssuer("FATEC Cassino");
$chave_secreta = $totp->getSecret();

// Gerar URI para o QR Code
$uri = $totp->getProvisioningUri();

// Gerar o QR Code
include 'qrcode/qrlib.php'; // Biblioteca QR Code, como PHP QR Code
QRcode::png($uri, "totp_$nome.png");

// Hash da senha para segurança
$password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

// Preparar e salvar o administrador e chave secreta no banco de dados
$stmt = $conn->prepare("INSERT INTO usuariosadm (nome, password, chave_secreta) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $password_hashed, $chave_secreta);

if ($stmt->execute()) {
    echo "Novo administrador criado com sucesso!<br>";
    echo "Escaneie o QR Code com o aplicativo autenticador para configurar o 2FA:<br>";
    echo "<img src='totp_$nome.png' alt='QR Code para 2FA'><br>";
    echo "<form action='confirmar_2fa.php' method='POST'>";
    echo "<input type='hidden' name='user_id' value='" . $stmt->insert_id . "'>";
    echo "<label for='codigo_2fa'>Código 2FA:</label>";
    echo "<input type='text' id='codigo_2fa' name='codigo_2fa' required>";
    echo "<button type='submit'>Confirmar 2FA</button>";
    echo "</form>";
} else {
    echo "Erro ao registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>


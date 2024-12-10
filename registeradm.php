<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use OTPHP\TOTP;

// Configurações do banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

// Criar conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter dados do formulário
$nome = $_POST['nome'] ?? '';
$password_raw = $_POST['password'] ?? '';

if (empty($nome) || empty($password_raw)) {
    die("Por favor, preencha todos os campos.");
}

// Gerar chave secreta para 2FA
$totp = TOTP::create();
$totp->setLabel($nome);
$totp->setIssuer("FATEC Cassino");
$chave_secreta = $totp->getSecret();

// Gerar URI para o QR Code
$uri = $totp->getProvisioningUri();

// Gerar o QR Code
$qrCode = QrCode::create($uri)
    ->setSize(300) // Define o tamanho
    ->setMargin(10) // Define a margem
    ->setEncoding('UTF-8');

// Criar o Writer para salvar o QR Code
$writer = new PngWriter();
$result = $writer->write($qrCode);

// Salvar a imagem gerada
$qrCodePath = "totp_$nome.png";
file_put_contents($qrCodePath, $result->getString());

// Hash da senha para segurança
$password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

// Inserir os dados no banco de dados
$stmt = $conn->prepare("INSERT INTO usuariosadm (nome, password, chave_secreta) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $password_hashed, $chave_secreta);

if ($stmt->execute()) {
    echo "Novo administrador criado com sucesso!<br>";
    echo "Escaneie o QR Code com o aplicativo autenticador para configurar o 2FA:<br>";
    echo "<img src='$qrCodePath' alt='QR Code para 2FA'><br>";

    // Exibir formulário para confirmar o 2FA
    echo "<form action='confirmar_2fa.php' method='POST'>";
    echo "<input type='hidden' name='user_id' value='" . $stmt->insert_id . "'>";
    echo "<label for='codigo_2fa'>Código 2FA:</label>";
    echo "<input type='text' id='codigo_2fa' name='codigo_2fa' required>";
    echo "<button type='submit'>Confirmar 2FA</button>";
    echo "</form>";
} else {
    echo "Erro ao registrar: " . $stmt->error;
}

// Fechar conexões
$stmt->close();
$conn->close();
?>


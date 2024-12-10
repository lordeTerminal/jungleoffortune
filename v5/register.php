<?php
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

// Obter dados do formulário
$nome = $_POST['nome'];
$password_raw = $_POST['password'];

// Hash da senha
$password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

// Gerar a chave secreta para 2FA
$totp = TOTP::create();
$totp->setLabel($nome); // Configura o label do TOTP com o nome do usuário
$totp->setIssuer("FATEC_Cassino"); // Configura o emissor do TOTP
$chave_secreta = $totp->getSecret();
$uri = $totp->getProvisioningUri(); // Gera o URI de provisionamento com label e issuer

// Salvar o usuário e a chave secreta no banco de dados
$stmt = $conn->prepare("INSERT INTO usuarios (nome, password, chave_secreta) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $password_hashed, $chave_secreta);

if ($stmt->execute()) {
    // Gerar o QR Code para o usuário escanear
    include 'qrcode/qrlib.php';
    $qrFile = "qrcode_$nome.png";
    QRcode::png($uri, $qrFile);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Configuração de 2FA</title>
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="login.css">
    </head>
    <body>

        <div class="form">
            <h2>Configuração de 2FA</h2>
            <p style="color: #4caf50; font-size: 0.9em; font-weight: bold;">
                Para completar o cadastro, escaneie o QR Code abaixo com o seu aplicativo autenticador e insira o código gerado.
                A autenticação de dois fatores é necessária para garantir a segurança da sua conta.
            </p>

            <!-- Exibe o QR Code para o usuário escanear -->
            <img src="<?php echo $qrFile; ?>" alt="QR Code para 2FA" style="margin: 20px 0;">

            <!-- Formulário para o código 2FA -->
            <form action="confirm_2fa.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $stmt->insert_id; ?>">
                <label for="codigo_2fa">Código 2FA:</label>
                <input type="text" id="codigo_2fa" name="codigo_2fa" required placeholder="Digite o código 2FA">
                <button type="submit">Confirmar 2FA</button>
            </form>
        </div>

    </body>
    </html>
    <?php
} else {
    echo "Erro ao registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>


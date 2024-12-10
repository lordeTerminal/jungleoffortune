<?php

$caminho_qrcode = '/var/www/html/apimobile/phpqrcode/pixqrcodegen.png';
// Remove o QR code antigo, se existir
if (file_exists($caminho_qrcode)) {
    unlink($caminho_qrcode);
}

// Crie o comando para executar o script Python com os argumentos necessários
$comando = "python3 /var/www/html/truco/phpqrcode/pix40.py";

// Execute o comando
$output = shell_exec($comando);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar QR Code Pix</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../login.css">
</head>
<body>
    <div class="form">
        <h2>QR Code Pix Gerado</h2>

        <?php if ($output): ?>
            <img src="pixqrcodegen.png" alt="QR Code Pix" style="margin: 20px 0;">
            <label for="ld">Linha Digitável:</label>
            <textarea id="ld" name="ld" rows="5" cols="50" readonly onclick="this.select();"><?php echo htmlspecialchars($output); ?></textarea>
            <br><br>
            <a href="../lojapro.php" style="display: block; margin-top: 20px; color: #FFD700;">Voltar à Loja</a>
        <?php else: ?>
            <p>Erro ao gerar QR Code.</p>
        <?php endif; ?>
    </div>
</body>
</html>


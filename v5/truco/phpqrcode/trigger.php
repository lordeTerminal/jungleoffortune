<?php


$caminho_qrcode = '/var/www/html/apimobile/phpqrcode/pixqrcodegen.png';
// Remove o QR code antigo, se existir
if (file_exists($caminho_qrcode)) {
    unlink($caminho_qrcode);
}
// Crie o comando para executar o script Python com os argumentos necessários
$comando = "python3 /var/www/html/apimobile/phpqrcode/pix.py";

// Execute o comando
$output = shell_exec($comando);

// Verifique o output e exiba a imagem ou uma mensagem de erro
if ($output) {
    // Assumindo que o script Python salva o QR Code como 'pixqrcodegen.png' no diretório especificado
    echo "<img src='pixqrcodegen.png' alt='QR Code Pix'>";
    echo "<p>Linha Digitável Pix: $output</p>";
} else {
    echo "Erro ao gerar QR Code.";
}

?>


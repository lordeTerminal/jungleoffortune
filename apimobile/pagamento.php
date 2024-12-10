<?php

// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'phpqrcode/phpqrcode.php';

// Defina os parâmetros do Pix
$nome = 'KevinVanBerghem';
$chavepix = '41164508806';
$valor = '4.00';
$cidade = 'Barueri';
$txtId = 'apresentacaoFATEC';

// Concatenando as informações do Pix para o QR Code
$dadosPix = "Nome: $nome\nChave: $chavepix\nValor: $valor\nCidade: $cidade\nTxId: $txtId";

// Gerando o QR Code com o nível de correção de erro alto
$qrCode = QRCode::getMinimumQRCode($dadosPix, QR_ERROR_CORRECT_LEVEL_H);
$image = $qrCode->createImage(10, 2); // Ajuste o tamanho conforme necessário

// Caminho para salvar a imagem
$filePath = '/var/www/html/apimobile/phpqrcode/qr_code.png';

// Salvando a imagem no caminho especificado
if ($image) {
    imagepng($image, $filePath);
    imagedestroy($image);
    echo "QR Code gerado com sucesso em $filePath";
} else {
    echo "Erro ao gerar o QR Code.";
}

?>


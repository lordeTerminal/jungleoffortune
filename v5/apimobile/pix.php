<?php

require_once 'phpqrcode/phpqrcode.php';

// Função para calcular CRC16 usando o polinômio especificado pelo padrão Pix
function gerarCRC16($payload) {
    $poly = 0x11021;
    $crc = 0xFFFF;

    for ($i = 0; $i < strlen($payload); $i++) {
        $crc ^= (ord($payload[$i]) << 8);
        for ($j = 0; $j < 8; $j++) {
            if (($crc & 0x8000) != 0) {
                $crc = ($crc << 1) ^ $poly;
            } else {
                $crc = $crc << 1;
            }
        }
    }
    return strtoupper(dechex($crc & 0xFFFF));
}

// Função para criar o payload Pix
function criarPayloadPix($nome, $chavepix, $valor, $cidade, $txtId) {
    $payloadFormat = '000201';
    $merchantAccount = '26' . str_pad(14 + strlen($chavepix) + 4, 2, '0', STR_PAD_LEFT) . '0014BR.GOV.BCB.PIX01' . str_pad(strlen($chavepix), 2, '0', STR_PAD_LEFT) . $chavepix;
    $merchantCategCode = '52040000';
    $transactionCurrency = '5303986';
    $transactionAmount = '54' . str_pad(strlen($valor), 2, '0', STR_PAD_LEFT) . $valor;
    $countryCode = '5802BR';
    $merchantName = '59' . str_pad(strlen($nome), 2, '0', STR_PAD_LEFT) . $nome;
    $merchantCity = '60' . str_pad(strlen($cidade), 2, '0', STR_PAD_LEFT) . $cidade;
    $addDataField = '62' . str_pad(4 + strlen($txtId), 2, '0', STR_PAD_LEFT) . '05' . str_pad(strlen($txtId), 2, '0', STR_PAD_LEFT) . $txtId;

    $payloadSemCRC = $payloadFormat . $merchantAccount . $merchantCategCode . $transactionCurrency . $transactionAmount . $countryCode . $merchantName . $merchantCity . $addDataField . '6304';

    $crc16 = gerarCRC16($payloadSemCRC);
    return $payloadSemCRC . str_pad($crc16, 4, '0', STR_PAD_LEFT);
}

// Parâmetros para o QR Code
$nome = 'KevinVanBerghem';
$chavepix = '41164508806';
$valor = '4.00';
$cidade = 'Barueri';
$txtId = 'apresentacaoFATEC';

$payloadPix = criarPayloadPix($nome, $chavepix, $valor, $cidade, $txtId);

// Criando a imagem do QR Code
$qrCode = new QRcode();
$image = $qrCode->createImage($payloadPix, 10, 2);

// Salvando a imagem como PNG
$diretorio = 'phpqrcode/pixqrcode.png';
imagepng($image, $diretorio);
imagedestroy($image);

// Exibindo a imagem e o payload Pix
header('Content-Type: text/html; charset=utf-8');
echo "<h3>QR Code Pix gerado:</h3>";
echo "<img src='$diretorio' alt='QR Code Pix'><br>";
echo "<h3>Linha digitável Pix:</h3>";
echo "<p>$payloadPix</p>";

?>


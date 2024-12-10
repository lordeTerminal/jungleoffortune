<?php

$caminho_qrcode = '/var/www/html/v5/phpqrcode/pixqrcodegen.png';
// Remove o QR code antigo, se existir
if (file_exists($caminho_qrcode)) {
    unlink($caminho_qrcode);
}

// Crie o comando para executar o script Python com os argumentos necessários
$comando = "python3 /var/www/html/v5/phpqrcode/pix40.py";

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

	<style>

		body, html{
			padding: 0;
			margin: 0;
			border: 0;
			overflow: hidden;
		}

		.form{
			width: 35%;
			height: 80vh;
			margin: 10px auto;
			display: block;
			position: relative;
			color: black;
			font-weight: bolder;
		}

		img{
			position: fixed;
			transform: translate(50%, 50%);
			width: 20%;
		}

		.qrcodeNum p{
			width: 30%;
			position: absolute;
			trasnform: translate(-100%, 100%);
			color: whitesmoke;
			font-weight: bolder;
		}

		#ld{
			width: 80%;
			position: absolute;
			trasnform: translate(50%, 100%);
			color: black;
			font-weight: bolder;
		}

		a{
			position: fixed;
			right: 38%;
			bottom: 15%;
			color: green;
			font-weight: bolder;
			background-color: whitesmoke;
			border-radius: 10px;
			padding: 5px;

		}

		h2{	color: black;
			font-weight: bolder;}

		@media (max-width: 800px){

			img{
				position: fixed;
				bottom: 50%;
				width: 20%;
			}

		}


	</style>


</head>
<body>
    <div class="form">
        <h2>QR Code Pix Gerado</h2>

        <?php if ($output): ?>
            <img src="pixqrcodegen.png" alt="QR Code Pix">
            <label for="ld">Linha Digitável:</label><br>
            <textarea id="ld" name="ld" rows="5" cols="50" readonly onclick="this.select();"><?php echo htmlspecialchars($output); ?></textarea>

            <a href="../lojapro.php">Voltar à Loja</a>
        <?php else: ?>

	     <div class="q">
		<p>linha digitavel: 00020126330014BR.GOV.BCB.PIX011141164508806520400005303986540539.995802BR5915KevinVanBerghem6007Barueri62210517apresentacaoFATEC6304A27D</p>
            	<p>QR code R$4,00.<br><img src="pix4.png"></p>
	     </div>
        <?php endif; ?>
    </div>
</body>
</html>


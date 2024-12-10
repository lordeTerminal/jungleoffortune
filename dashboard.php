<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}


$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";



// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Preparar e executar a consulta para obter o saldo
$stmt = $conn->prepare("SELECT nome, saldo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nome, $saldo);
$stmt->fetch();

// Fecha a consulta e a conexão
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Casino</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        body, html {
		margin: 0px;
		padding: 0px;
	        box-sizing: border-box;
		overflow: hidden;
        }

	.containerMaster{
	    font-family: 'Press Start 2P', sans-serif;
            background-color: #006400;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
	    height: 100%;
            padding: 20px;
        }

        .menu {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            width: 100%;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .menu h1, .menu p {
            text-align: center;
            margin: 0 0 20px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            display: block;
            margin: 10px 0;
            text-align: center;
        }

        .menu a:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

	.displayCarrossel{
	    display: flex;
	    width: 80%;
	    height: 100vh;
	    flex-direction: column;
	}

        .background {
            display: flex;
            justify-content: center;
            align-items: center;
            top: 45%;
            width: 100%;
            position: relative;
        }

        .background img {
            width: 100%;
	    position: absolute;
   	    left: 0%;
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .background img.active {
            opacity: 1;
        }

        .hint {
            text-align: center;
            font-size: 14px;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            margin-top:40px;
	    margin-right: 1%;
            max-width: 90%;
        }

        /* Responsividade */
        @media (max-width: 768px) {
		body, html{overflow-y: scroll}

            .containerMaster{
	      	display: block;
		height: 100%;
		margin: auto;
           }
	    .container{
		width: 100%;
	    	height: 100vh;
		margin: auto;
	   }
            .menu, .background {
		margin: 0 auto;
		margin-left: 0;
                width: 90%;
            }

            .background{margin-top: -25%;}

            .menu h1, .menu p, .menu a {
                font-size: 14px;
            }

	     .displayCarrossel{
		margin:auto;
		transform: translateY(-50%);
	     }
            .hint {
		margin-top: 0px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

  <div class='containerMaster'>
    <div class="container">
        <!-- Coluna da esquerda: Menu e informações do usuário -->
        <div class="menu">
            <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h1>
            <p>Seu saldo atual é: <?php echo $saldo; ?> FATEC Coins</p>
            <a href="perfil.php">Perfil</a>
            <a href="jogobkp.php">Ir para o jogo</a>
            <a href="lojapro.php">Conseguir mais moedas!</a>
            <a href="loja.html">Ir para a Loja</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>

    <div class='displayCarrossel'>

       <div class="hint">"Recarregue seus créditos na Atlética FATEC!"</div>

        <!-- Coluna da direita: Carrossel de imagens -->
        <div class="background">
            <img src="./assets/onca1.png" class="active" alt="Jaguro in Casino">
            <img src="./assets/onca2.png" alt="Jaguro in Business">
            <img src="./assets/onca3.png" alt="Jaguro in Jungle">
            <img src="./assets/onca4.png" alt="Jaguro in Luxury">
            <img src="./assets/onca5.png" alt="Jaguro in Victory">
            <img src="./assets/onca6.png" alt="Jaguro in Command">
            <img src="./assets/onca7.png" alt="Jaguro in Strategy">
        </div>

     </div>
  </div>
    <script>
        const images = document.querySelectorAll('.background img');
        const hints = [
            "Não gaste dinheiro que você não pode perder!",
            "Lembre-se, a casa sempre tem a vantagem.",
            "Gerencie seu saldo com sabedoria.",
            "A sorte favorece os preparados.",
            "Saiba a hora de parar.",
            "Confie nos seus instintos, mas não ignore as probabilidades.",
            "A persistência é a chave, mas a prudência é sua melhor amiga."
        ];
        let currentIndex = 0;

        function showNextImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.add('active');
            document.querySelector('.hint').textContent = hints[currentIndex];
        }

        setInterval(showNextImage, 3000);
    </script>

</body>
</html>


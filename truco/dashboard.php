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
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #006400;
    color: white;
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow: hidden;
    display: flex;
}

.menu {
    width: 30%;
    padding: 20px;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.menu a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    padding: 10px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    display: block;
    margin-bottom: 20px; /* Espaçamento entre os botões */
    text-align: center;
}

.menu a:hover {
    background-color: rgba(255, 255, 255, 0.3);
}

.menu h1, .menu p {
    margin: 0;
    padding: 0;
    margin-bottom: 20px;
}

.background {
    position: relative;
    width: 70%;
    height: 100%;
}

.background img {
    width: 80%;
    height: auto; /* Mantém a proporção da imagem */
    max-height: 80%; /* Não permite que a imagem ultrapasse 80% da altura */
    object-fit: contain;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.background img.active {
    opacity: 1;
}

.hint {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    padding: 10px;
    background-color: rgba(0, 0, 0, 0.5);
}

    </style>
</head>
<body>

    <!-- Coluna da esquerda: Menu e informações do usuário -->
    <div class="menu">
        <div>
            <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h1>
            <p>Seu saldo atual é: <?php echo $saldo; ?> FATEC Coins</p>
        </div>
        <div>
            <a href="perfil.php">Perfil</a><br><br><br>
            <a href="jogo.php">Ir para o jogo</a><br><br><br>
            <a href="lojapro.php">Conseguir mais moedas!</a><br><br><br>
            <a href="loja.php">Ir para a Loja</a><br><br><br>
            <a href="logout.php">Sair</a><br><br><br>
        </div>
    </div>

    <!-- Coluna da direita: Carrossel de imagens -->
    <div class="background">
        <img src="./assets/onca1.png" class="active" alt="Jaguro in Casino">
        <img src="./assets/onca2.png" alt="Jaguro in Business">
        <img src="./assets/onca3.png" alt="Jaguro in Jungle">
        <img src="./assets/onca4.png" alt="Jaguro in Luxury">
        <img src="./assets/onca5.png" alt="Jaguro in Victory">
        <img src="./assets/onca6.png" alt="Jaguro in Command">
        <img src="./assets/onca7.png" alt="Jaguro in Strategy">
        <div class="hint">"Recarregue seus créditos na Atlética FATEC!"</div>
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


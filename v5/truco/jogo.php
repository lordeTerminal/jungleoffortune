<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Conectar ao banco de dados

$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";




$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Obter o saldo do usuário
$stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($saldo);
$stmt->fetch();
$stmt->close();

// Inicializa a variável para armazenar os índices das linhas vencedoras e a linha ganhadora
$winningIndices = [];
$linha_ganhadora = 0; // Inicialmente, ninguém ganha (0)

// Verifica se o usuário clicou em "Jogar"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aposta = isset($_POST['aposta']) ? (int)$_POST['aposta'] : 10;

    if ($saldo >= $aposta) {
        $novo_saldo = $saldo - $aposta;

        // Gira a slot machine (gera 9 números aleatórios entre 1 e 9)
        $slots = [];
        for ($i = 0; $i < 9; $i++) {
            $slots[] = rand(1, 5);
        }

        $ganhou = false;

        // Verifica linhas horizontais
        if ($slots[0] == $slots[1] && $slots[1] == $slots[2]) {
            $ganhou = true;
            $linha_ganhadora = 1; // Linha horizontal superior
            $winningIndices = [0, 1, 2];
        } elseif ($slots[3] == $slots[4] && $slots[4] == $slots[5]) {
            $ganhou = true;
            $linha_ganhadora = 2; // Linha horizontal do meio
            $winningIndices = [3, 4, 5];
        } elseif ($slots[6] == $slots[7] && $slots[7] == $slots[8]) {
            $ganhou = true;
            $linha_ganhadora = 3; // Linha horizontal inferior
            $winningIndices = [6, 7, 8];
        }

        // Verifica colunas verticais
        if ($slots[0] == $slots[3] && $slots[3] == $slots[6]) {
            $ganhou = true;
            $linha_ganhadora = 4; // Coluna vertical esquerda
            $winningIndices = [0, 3, 6];
        } elseif ($slots[1] == $slots[4] && $slots[4] == $slots[7]) {
            $ganhou = true;
            $linha_ganhadora = 5; // Coluna vertical do meio
            $winningIndices = [1, 4, 7];
        } elseif ($slots[2] == $slots[5] && $slots[5] == $slots[8]) {
            $ganhou = true;
            $linha_ganhadora = 6; // Coluna vertical direita
            $winningIndices = [2, 5, 8];
        }

        // Verifica diagonais
        if ($slots[0] == $slots[4] && $slots[4] == $slots[8]) {
            $ganhou = true;
            $linha_ganhadora = 7; // Diagonal top-left
            $winningIndices = [0, 4, 8];
        } elseif ($slots[2] == $slots[4] && $slots[4] == $slots[6]) {
            $ganhou = true;
            $linha_ganhadora = 8; // Diagonal bottom-left
            $winningIndices = [2, 4, 6];
        }

        $valor_ganho = 0;
        if ($ganhou) {
            $valor_ganho = $aposta * 2;
            $novo_saldo += $valor_ganho;
            $mensagem = "Parabéns! Você ganhou $valor_ganho FATEC Coins!";
        } else {
            $mensagem = "Essa foi por pouco! Tentar novamente?";
        }

        // Atualiza o saldo do usuário
        $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
        $stmt->bind_param("ii", $novo_saldo, $user_id);
        $stmt->execute();
        $stmt->close();

        // Insere o log do jogo, incluindo a linha ganhadora
        $stmt = $conn->prepare("INSERT INTO logs_jogos (user_id, aposta, ganhou, valor_ganho, linha_ganhadora) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $user_id, $aposta, $ganhou, $valor_ganho, $linha_ganhadora);
        $stmt->execute();
        $stmt->close();

        $saldo = $novo_saldo;
    } else {
        $mensagem = "Saldo insuficiente para apostar.";
    }
}

$conn->close();

// Função para mapear números para imagens
function getImageForNumber($number) {
    return "imagens/symbol{$number}.png"; // Ajuste o caminho para a pasta de imagens
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slot Machine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #006400;
            color: white;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: grid;
            grid-template-columns: 1fr 2fr; /* Left menu takes 1 part, right slot takes 2 parts */
            gap: 20px;
        }

        /* Left menu styles */
        .menu {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu h1, .menu p {
            margin: 0;
            padding: 10px 0;
        }

        .menu select, .menu button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            border-radius: 5px;
        }

        .menu button {
            align-self: flex-end;
            background-color: rgba(0, 128, 0, 0.8);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .menu button:hover {
            background-color: rgba(0, 128, 0, 1);
        }

        /* Right side for slot machine */
        .slot-machine {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .slots-container {
            display: grid;
            grid-template-columns: repeat(3, 100px); /* Three columns */
            grid-gap: 10px;
        }

        .slot {
            width: 100px;
            height: 100px;
            border: 5px solid white; /* Add border to slot */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2em;
            background-color: rgba(0, 255, 0, 0.7);
        }

        .highlight {
            border-color: red; /* Highlight border color */
            box-shadow: 0 0 10px red; /* Adds a glowing effect to the winning slots */
        }

        .slot img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures the image covers the entire div */
        }

        a {
            text-decoration: none;
            color: white;
            margin-top: 20px;
        }

        a:hover {
            color: lightgray;
        }
    </style>
</head>
<body>
    <!-- Left side menu -->
    <div class="menu">
        <div>
            <h1>Bem-vindo ao Slot Machine!</h1>
            <p>Seu saldo atual: <?php echo $saldo; ?> FATEC Coins</p>
            <?php if (isset($mensagem)) { echo "<p>$mensagem</p>"; } ?>
        </div>

        <form method="POST">
            <label for="aposta">Escolha o valor da aposta:</label>
            <select name="aposta" id="aposta">
                <option value="10">10 FATEC Coins</option>
                <option value="50">50 FATEC Coins</option>
                <option value="100">100 FATEC Coins</option>
            </select>
            <br><br>
            <button type="submit">Jogar</button>
        </form>

        <a href="dashboard.php">Voltar ao Dashboard</a>
    </div>

    <!-- Right side slot machine -->
    <div class="slot-machine">
        <?php if (isset($slots)): ?>
        <div class="slots-container">
            <?php foreach ($slots as $index => $slot): ?>
                <div class="slot<?php echo (in_array($index, $winningIndices) ? ' highlight' : ''); ?>">
                    <img src="<?php echo getImageForNumber($slot); ?>" alt="Slot Symbol">
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>


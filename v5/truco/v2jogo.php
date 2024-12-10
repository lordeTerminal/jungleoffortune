<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "golimar10*";
$dbname = "fatec_cassino";

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

$winning_line = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aposta = isset($_POST['aposta']) ? (int)$_POST['aposta'] : 10; // Define a aposta como 10, 50 ou 100

    if ($saldo >= $aposta) {
        // Deduz o valor da aposta do saldo
        $novo_saldo = $saldo - $aposta;

        // Gira a slot machine (gera 9 números aleatórios entre 1 e 5)
        $slots = [];
        for ($i = 0; $i < 9; $i++) {
            $slots[] = rand(1, 5);
        }

        // Verifica se o jogador ganhou
        $ganhou = false;

        // Verifica linhas horizontais
        if ($slots[0] == $slots[1] && $slots[1] == $slots[2]) {
            $ganhou = true;
            $winning_line = [0, 1, 2]; // Horizontal line 1
        } elseif ($slots[3] == $slots[4] && $slots[4] == $slots[5]) {
            $ganhou = true;
            $winning_line = [3, 4, 5]; // Horizontal line 2
        } elseif ($slots[6] == $slots[7] && $slots[7] == $slots[8]) {
            $ganhou = true;
            $winning_line = [6, 7, 8]; // Horizontal line 3
        }

        // Verifica colunas verticais
        if ($slots[0] == $slots[3] && $slots[3] == $slots[6]) {
            $ganhou = true;
            $winning_line = [0, 3, 6]; // Vertical line 1
        } elseif ($slots[1] == $slots[4] && $slots[4] == $slots[7]) {
            $ganhou = true;
            $winning_line = [1, 4, 7]; // Vertical line 2
        } elseif ($slots[2] == $slots[5] && $slots[5] == $slots[8]) {
            $ganhou = true;
            $winning_line = [2, 5, 8]; // Vertical line 3
        }

        // Verifica diagonais
        if ($slots[0] == $slots[4] && $slots[4] == $slots[8]) {
            $ganhou = true;
            $winning_line = [0, 4, 8]; // Diagonal principal
        } elseif ($slots[2] == $slots[4] && $slots[4] == $slots[6]) {
            $ganhou = true;
            $winning_line = [2, 4, 6]; // Diagonal secundária
        }

        $valor_ganho = 0;

        if ($ganhou) {
            $valor_ganho = $aposta * 2; // Prêmio: o dobro da aposta
            $novo_saldo += $valor_ganho;
            $mensagem = "Parabéns! Você ganhou $valor_ganho FATEC Coins!";
        } else {
            $mensagem = "Essa foi por pouco! Tentar novamente?";
        }

        // Atualiza o saldo no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
        $stmt->bind_param("ii", $novo_saldo, $user_id);
        $stmt->execute();
        $stmt->close();

        // Salva a jogada no log
        $stmt = $conn->prepare("INSERT INTO logs_jogos (user_id, aposta, ganhou, valor_ganho) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $user_id, $aposta, $ganhou, $valor_ganho);
        $stmt->execute();
        $stmt->close();

        // Atualiza o saldo local para refletir a nova situação
        $saldo = $novo_saldo;
    } else {
        $mensagem = "Saldo insuficiente para apostar.";
    }
}

$conn->close();
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
            background-color: #222;
            color: #fff;
            text-align: center;
        }
        h1, p {
            color: #f4e04d;
        }
        button {
            padding: 10px 20px;
            background-color: #f4e04d;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #c4b04d;
        }
        .slot-grid {
            display: grid;
            grid-template-columns: repeat(3, 50px);
            grid-gap: 10px;
            justify-content: center;
        }
        .slot {
            width: 50px;
            height: 50px;
            background-color: #444;
            border: 2px solid #fff;
            color: #fff;
            line-height: 50px;
            font-size: 18px;
        }
        .highlight {
            background-color: #f44;
        }
    </style>
</head>
<body>
    <h1>Bem-vindo ao Slot Machine!</h1>
    <p>Seu saldo atual: <?php echo $saldo; ?> FATEC Coins</p>

    <?php if (isset($mensagem)) { echo "<p>$mensagem</p>"; } ?>

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

    <?php if (isset($slots)): ?>
        <h2>Resultado:</h2>
        <div class="slot-grid">
            <?php foreach ($slots as $index => $slot): ?>
                <div class="slot <?php echo in_array($index, $winning_line) ? 'highlight' : ''; ?>">
                    <?php echo $slot; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">Voltar ao Dashboard</a>
</body>
</html>


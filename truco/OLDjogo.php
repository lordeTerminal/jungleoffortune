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

// Definir o custo por rodada e o prêmio
$custo_por_rodada = 10; // Custo de 10 coins por rodada
$premio = 100; // Prêmio básico

// Verifica se o usuário clicou em "Jogar"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($saldo >= $custo_por_rodada) {
        // Deduz o custo da rodada do saldo
        $novo_saldo = $saldo - $custo_por_rodada;

        // Gira a slot machine (gera 9 números aleatórios entre 1 e 5)
        $slots = [];
        for ($i = 0; $i < 9; $i++) {
            $slots[] = rand(1, 5);
        }

        // Verifica se o jogador ganhou
        $ganhou = false;

        // Verifica linhas horizontais
        if (($slots[0] == $slots[1] && $slots[1] == $slots[2]) || // Linha 1
            ($slots[3] == $slots[4] && $slots[4] == $slots[5]) || // Linha 2
            ($slots[6] == $slots[7] && $slots[7] == $slots[8])) { // Linha 3
            $ganhou = true;
        }

        // Verifica colunas verticais
        if (($slots[0] == $slots[3] && $slots[3] == $slots[6]) || // Coluna 1
            ($slots[1] == $slots[4] && $slots[4] == $slots[7]) || // Coluna 2
            ($slots[2] == $slots[5] && $slots[5] == $slots[8])) { // Coluna 3
            $ganhou = true;
        }

        // Verifica diagonais
        if (($slots[0] == $slots[4] && $slots[4] == $slots[8]) || // Diagonal principal
            ($slots[2] == $slots[4] && $slots[4] == $slots[6])) { // Diagonal secundária
            $ganhou = true;
        }

        $valor_ganho = 0;

        if ($ganhou) {
            $novo_saldo += $premio; // Adiciona o prêmio ao saldo
            $valor_ganho = $premio;
            $mensagem = "Parabéns! Você ganhou $premio FATEC Coins!";
        } else {
            $mensagem = "Você perdeu essa rodada.";
        }

        // Atualiza o saldo no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
        $stmt->bind_param("ii", $novo_saldo, $user_id);
        $stmt->execute();
        $stmt->close();

        // Salva a jogada no log
        $stmt = $conn->prepare("INSERT INTO logs_jogos (user_id, aposta, ganhou, valor_ganho) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $user_id, $custo_por_rodada, $ganhou, $valor_ganho);
        $stmt->execute();
        $stmt->close();

        // Atualiza o saldo local para refletir a nova situação
        $saldo = $novo_saldo;
    } else {
        $mensagem = "Saldo insuficiente para jogar.";
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
</head>
<body>
    <h1>Bem-vindo ao Slot Machine!</h1>
    <p>Seu saldo atual: <?php echo $saldo; ?> FATEC Coins</p>

    <?php if (isset($mensagem)) { echo "<p>$mensagem</p>"; } ?>

    <form method="POST">
        <button type="submit">Jogar</button>
    </form>

    <?php if (isset($slots)): ?>
        <h2>Resultado:</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 50px); grid-gap: 10px;">
            <?php foreach ($slots as $slot): ?>
                <div style="width: 50px; height: 50px; border: 1px solid black; text-align: center; line-height: 50px;">
                    <?php echo $slot; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">Voltar ao Dashboard</a>
</body>
</html>


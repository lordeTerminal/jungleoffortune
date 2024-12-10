<?php
// Conectar ao banco de dados

$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";







$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtém as estatísticas gerais
$result = $conn->query("SELECT COUNT(*) as total_games, SUM(aposta) as total_apostado, SUM(valor_ganho) as total_ganho, SUM(ganhou) as total_vitorias FROM logs_jogos");
$stats = $result->fetch_assoc();

// Calcula o saldo geral dos jogadores (moedas ganhas - moedas apostadas)
$saldo_jogadores = ($stats['total_ganho'] ?: 0) - ($stats['total_apostado'] ?: 0);

// Saldo do cassino será o inverso do saldo geral dos jogadores
$saldo_cassino = -$saldo_jogadores;

// Variáveis para estatísticas de usuário específico
$userStats = null;

// Verifica se foi enviado um user_id via formulário
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $stmt = $conn->prepare("SELECT COUNT(*) as total_games, SUM(aposta) as total_apostado, SUM(valor_ganho) as total_ganho, SUM(ganhou) as total_vitorias FROM logs_jogos WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userStats = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Calcula o saldo do usuário específico (moedas ganhas - moedas apostadas)
    $user_balance = ($userStats['total_ganho'] ?: 0) - ($userStats['total_apostado'] ?: 0);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas do Jogo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #006400;
            color: white;
            padding: 20px;
        }

        h1, h2 {
            color: #FFD700;
        }

        .stats-container {
            margin-top: 20px;
        }

        .stats-container div {
            margin-bottom: 10px;
        }

        input, button {
            padding: 10px;
            font-size: 16px;
            margin-top: 10px;
        }

        button {
            cursor: pointer;
            background-color: #FFD700;
            border: none;
            color: black;
        }

        button:hover {
            background-color: #FFA500;
        }

        .results {
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .balance-positive {
            color: lightgreen;
        }

        .balance-negative {
            color: red;
        }
    </style>
</head>
<body>

<h1>Estatísticas Gerais</h1>
<div class="stats-container">
    <div>Total de jogos jogados: <?php echo $stats['total_games']; ?></div>
    <div>Total de moedas apostadas: <?php echo $stats['total_apostado'] ?: 0; ?> FATEC Coins</div>
    <div>Total de moedas ganhas: <?php echo $stats['total_ganho'] ?: 0; ?> FATEC Coins</div>
    <div>Total de vitórias: <?php echo $stats['total_vitorias']; ?></div>
    <div>Saldo geral dos jogadores: 
        <span class="<?php echo $saldo_jogadores >= 0 ? 'balance-positive' : 'balance-negative'; ?>">
            <?php echo ($saldo_jogadores >= 0 ? '+' : '') . $saldo_jogadores; ?> FATEC Coins
        </span>
    </div>
    <div>Saldo do cassino: 
        <span class="<?php echo $saldo_cassino >= 0 ? 'balance-positive' : 'balance-negative'; ?>">
            <?php echo ($saldo_cassino >= 0 ? '+' : '') . $saldo_cassino; ?> FATEC Coins
        </span>
    </div>
</div>

<hr>

<h2>Pesquisar Estatísticas de Usuário</h2>
<form method="GET" action="estatisticas.php">
    <label for="user_id">ID do Usuário:</label>
    <input type="number" name="user_id" id="user_id" required>
    <button type="submit">Ver Estatísticas</button>
</form>

<?php if ($userStats): ?>
    <div class="results">
        <h2>Estatísticas do Usuário ID: <?php echo htmlspecialchars($user_id); ?></h2>
        <div>Total de jogos jogados: <?php echo $userStats['total_games']; ?></div>
        <div>Total de moedas apostadas: <?php echo $userStats['total_apostado'] ?: 0; ?> FATEC Coins</div>
        <div>Total de moedas ganhas: <?php echo $userStats['total_ganho'] ?: 0; ?> FATEC Coins</div>
        <div>Total de vitórias: <?php echo $userStats['total_vitorias']; ?></div>
        <div>Saldo do jogador: 
            <span class="<?php echo $user_balance >= 0 ? 'balance-positive' : 'balance-negative'; ?>">
                <?php echo ($user_balance >= 0 ? '+' : '') . $user_balance; ?> FATEC Coins
            </span>
        </div>
    </div>
<?php elseif (isset($_GET['user_id'])): ?>
    <div class="results">
        <h2>Nenhum resultado encontrado para o Usuário ID: <?php echo htmlspecialchars($user_id); ?></h2>
    </div>
<?php endif; ?>

</body>
</html>


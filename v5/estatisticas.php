<?php
// Conectar ao banco de dados

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificação de sessão
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: loginadm.html");
    exit();
}

// Configurações do banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Estatísticas gerais
$result = $conn->query("SELECT COUNT(*) as total_games, SUM(aposta) as total_apostado, SUM(valor_ganho) as total_ganho, SUM(ganhou) as total_vitorias FROM logs_jogos");
$stats = $result->fetch_assoc();
$saldo_jogadores = ($stats['total_ganho'] ?: 0) - ($stats['total_apostado'] ?: 0);
$saldo_cassino = -$saldo_jogadores;

// Variável para estatísticas do usuário específico
$userStats = null;
$user_name = "";
$user_balance = 0; // Inicializamos para evitar erros

// Verificar a entrada de busca do usuário
if (isset($_GET['user_query'])) {
    $user_query = $_GET['user_query'];

    if (is_numeric($user_query)) {
        // Consulta pelo ID
        $stmt = $conn->prepare("SELECT usuarios.nome, COUNT(logs_jogos.id) as total_games, SUM(aposta) as total_apostado, SUM(valor_ganho) as total_ganho, SUM(ganhou) as total_vitorias FROM logs_jogos JOIN usuarios ON logs_jogos.user_id = usuarios.id WHERE usuarios.id = ?");
        $stmt->bind_param("i", $user_query);
    } else {
        // Consulta pelo nome
        $stmt = $conn->prepare("SELECT usuarios.id, COUNT(logs_jogos.id) as total_games, SUM(aposta) as total_apostado, SUM(valor_ganho) as total_ganho, SUM(ganhou) as total_vitorias FROM logs_jogos JOIN usuarios ON logs_jogos.user_id = usuarios.id WHERE usuarios.nome = ?");
        $stmt->bind_param("s", $user_query);
    }

    $stmt->execute();
    $userStats = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($userStats) {
        $user_name = is_numeric($user_query) ? $userStats['nome'] : $user_query;
        $user_balance = ($userStats['total_ganho'] ?: 0) - ($userStats['total_apostado'] ?: 0);
    }
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
        /* Estilos existentes */
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
    <label for="user_query">ID ou Nome do Usuário:</label>
    <input type="text" name="user_query" id="user_query" required>
    <button type="submit">Ver Estatísticas</button>
</form>

<?php if ($userStats): ?>
    <div class="results">
        <h2>Estatísticas do Usuário <?php echo htmlspecialchars($user_name); ?></h2>
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
<?php elseif (isset($_GET['user_query'])): ?>
    <div class="results">
        <h2>Nenhum resultado encontrado para a consulta: <?php echo htmlspecialchars($user_query); ?></h2>
    </div>
<?php endif; ?>

</body>
</html>


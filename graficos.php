<?php
// Conectar ao banco de dados

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    // Redireciona para a página de login caso a sessão de admin não esteja ativa
    header("Location: loginadm.html");
    exit();
}

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

// Saldo geral
$saldo_jogadores = ($stats['total_ganho'] ?: 0) - ($stats['total_apostado'] ?: 0);
$saldo_cassino = -$saldo_jogadores;

// Estatísticas de usuário específico
$userStats = null;
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $stmt = $conn->prepare("SELECT COUNT(*) as total_games, SUM(aposta) as total_apostado, SUM(valor_ganho) as total_ganho, SUM(ganhou) as total_vitorias FROM logs_jogos WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userStats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $user_balance = ($userStats['total_ganho'] ?: 0) - ($userStats['total_apostado'] ?: 0);
}

// Coletar dados para melhores horários de jogos
$hourly_result = $conn->query("SELECT HOUR(timestamp) as hour, COUNT(*) as games_count FROM logs_jogos GROUP BY hour");
$hourly_data = [];
while ($row = $hourly_result->fetch_assoc()) {
    $hourly_data[intval($row['hour'])] = intval($row['games_count']);
}

// Coletar dados para melhores dias da semana
$weekly_result = $conn->query("SELECT DAYOFWEEK(timestamp) as day, COUNT(*) as games_count FROM logs_jogos GROUP BY day");
$weekly_data = [];
while ($row = $weekly_result->fetch_assoc()) {
    $weekly_data[intval($row['day'])] = intval($row['games_count']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas do Jogo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos existentes para a página */
    </style>
</head>
<body>

<h1>Estatísticas Gerais</h1>
<div class="stats-container">
    <!-- Exibir estatísticas gerais -->
</div>

<hr>

<h2>Pesquisar Estatísticas de Usuário</h2>
<form method="GET" action="estatisticas.php">
    <!-- Formulário para pesquisa por ID de usuário -->
</form>

<?php if ($userStats): ?>
    <!-- Exibir estatísticas específicas do usuário -->
<?php elseif (isset($_GET['user_id'])): ?>
    <div class="results">
        <h2>Nenhum resultado encontrado para o Usuário ID: <?php echo htmlspecialchars($user_id); ?></h2>
    </div>
<?php endif; ?>

<!-- Gráfico: Melhores Horários -->
<h2>Melhores Horários de Jogos</h2>
<canvas id="hourlyChart" width="400" height="200"></canvas>

<!-- Gráfico: Melhores Dias da Semana -->
<h2>Melhores Dias para Jogos</h2>
<canvas id="weeklyChart" width="400" height="200"></canvas>

<script>
// Dados para o gráfico de horários
const hourlyLabels = Array.from({length: 24}, (_, i) => i + "h");
const hourlyData = <?php echo json_encode(array_values($hourly_data)); ?>;

// Gráfico de horários
const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
new Chart(ctxHourly, {
    type: 'bar',
    data: {
        labels: hourlyLabels,
        datasets: [{
            label: 'Jogos por Hora',
            data: hourlyData,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Dados para o gráfico de dias da semana
const weeklyLabels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const weeklyData = <?php echo json_encode(array_values($weekly_data)); ?>;

// Gráfico de dias da semana
const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
new Chart(ctxWeekly, {
    type: 'bar',
    data: {
        labels: weeklyLabels,
        datasets: [{
            label: 'Jogos por Dia',
            data: weeklyData,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>


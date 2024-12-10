<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}



$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";



// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Query to retrieve game statistics
$sql = "SELECT COUNT(*) AS total_games, SUM(aposta) AS total_bet, SUM(valor_ganho) AS total_won 
        FROM logs_jogos WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_games, $total_bet, $total_won);
$stmt->fetch();
$stmt->close();

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
            margin: 0;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .stats-container {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .stat {
            font-size: 18px;
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: white;
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        a:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>

<h1>Estatísticas do Jogo</h1>

<div class="stats-container">
    <div class="stat">
        <strong>Total de Jogos:</strong> <?php echo $total_games; ?>
    </div>
    <div class="stat">
        <strong>Total Apostado:</strong> <?php echo $total_bet; ?> FATEC Coins
    </div>
    <div class="stat">
        <strong>Total Ganhado:</strong> <?php echo $total_won; ?> FATEC Coins
    </div>
    <div class="stat">
        <strong>Saldo Final:</strong> <?php echo ($total_won - $total_bet); ?> FATEC Coins
    </div>
</div>

<a href="dashboard.php">Voltar ao Dashboard</a>

</body>
</html>


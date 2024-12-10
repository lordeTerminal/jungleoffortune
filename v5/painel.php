<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: loginadm.html");
    exit();
}

// Exibe mensagem de confirmação, se houver
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Limpa a mensagem após exibi-la
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

// Consultar pedidos de compra de moeda
$result = $conn->query("SELECT * FROM pedidos_moedas WHERE confirmado = 0");
$pedidos = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="form">
        <h2>Painel do Administrador</h2>

        <?php if ($message): ?>
            <p style="color: #4caf50; font-size: 0.9em; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>

        <p style="color: #4caf50; font-size: 0.9em; font-weight: bold;">
            Visualize e gerencie os pedidos de compra de moeda abaixo.
        </p>

        <table>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Quantidade</th>
                <th>Data do Pedido</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?php echo $pedido['id']; ?></td>
                    <td><?php echo $pedido['user_id']; ?></td>
                    <td><?php echo $pedido['quantidade']; ?> FATEC Coins</td>
                    <td><?php echo $pedido['data_pedido']; ?></td>
                    <td>
                        <form action="confirmar_pedido.php" method="POST" style="display:inline;">
                            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                            <button type="submit">Confirmar Pagamento</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div style="margin-top: 20px;">
            <a href="estatisticas.php" style="color: #FFD700;">Ver Estatísticas do Jogo</a><br>
	    <a href="graficos.php" style="color: #FFD700;">Ver Gráficos</a><br>
            <a href="confirmacoes.php" style="color: #FFD700;">confirmação legacy</a><br>
            <a href="logout.php" style="color: #FFD700;">Logout</a>
        </div>
    </div>

</body>
</html>


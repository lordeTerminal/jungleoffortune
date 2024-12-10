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

// Consultar os pedidos do usuário
$stmt = $conn->prepare("SELECT quantidade, data_pedido, confirmado FROM pedidos_moedas WHERE user_id = ? ORDER BY data_pedido DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($quantidade, $data_pedido, $confirmado);

// Armazenar os resultados em um array para exibição
$pedidos = [];
while ($stmt->fetch()) {
    $pedidos[] = [
        'quantidade' => $quantidade,
        'data_pedido' => $data_pedido,
        'status' => $confirmado ? 'Confirmado' : 'Pendente'
    ];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status dos Pedidos</title>
</head>
<body>
    <h1>Status dos Pedidos de Moedas</h1>

    <?php if (count($pedidos) > 0): ?>
        <table border="1">
            <tr>
                <th>Quantidade</th>
                <th>Data do Pedido</th>
                <th>Status</th>
            </tr>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?php echo $pedido['quantidade']; ?></td>
                    <td><?php echo $pedido['data_pedido']; ?></td>
                    <td><?php echo $pedido['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Você ainda não fez nenhum pedido de moedas.</p>
    <?php endif; ?>

    <br>
    <a href="lojapro.php">Voltar à página de aquisição de moedas</a>
</body>
</html>


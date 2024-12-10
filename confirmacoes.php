<?php
session_start(); // Inicia a sessão

// Dados de conexão com o banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se o usuário está logado como administrador
if (!isset($_SESSION['user_id'])) {
    header("Location: loginadm.php");
    exit();
}

// Função para confirmar pagamento
if (isset($_POST['confirmar'])) {
    $pedido_id = $_POST['pedido_id'];
    
    // Seleciona o pedido e informações do usuário
    $query = "SELECT * FROM pedidos_moedas WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $pedido = $stmt->get_result()->fetch_assoc();

    if ($pedido && $pedido['confirmado'] == 0) {
        $user_id = $pedido['user_id'];
        $quantidade = $pedido['quantidade'];

        // Inicia a transação
        $conn->begin_transaction();
        
        try {
            // Atualiza o saldo do usuário
            $update_saldo = "UPDATE usuarios SET saldo = saldo + ? WHERE id = ?";
            $stmt_saldo = $conn->prepare($update_saldo);
            $stmt_saldo->bind_param("ii", $quantidade, $user_id);
            $stmt_saldo->execute();

            // Atualiza o pedido como confirmado
            $update_pedido = "UPDATE pedidos_moedas SET confirmado = 1, saldo_atualizado = 1 WHERE id = ?";
            $stmt_pedido = $conn->prepare($update_pedido);
            $stmt_pedido->bind_param("i", $pedido_id);
            $stmt_pedido->execute();

            // Confirma a transação
            $conn->commit();
            echo "<p>Pagamento confirmado com sucesso para o pedido ID: $pedido_id</p>";
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $conn->rollback();
            echo "<p>Erro ao confirmar pagamento: " . $e->getMessage() . "</p>";
        }
    }
}

// Exibe todos os pedidos
$query = "SELECT p.id, p.user_id, p.quantidade, p.confirmado, u.nome AS usuario_nome, p.data_pedido 
          FROM pedidos_moedas p
          JOIN usuarios u ON p.user_id = u.id
          ORDER BY p.data_pedido DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
</head>
<body>
    <h1>Painel Administrativo</h1>
   <h2><a href="graficos.php">graficos</a></h2>
<h2><a href="estatisticas.php">estatisticas</a></h2> 
    <table border="1">
        <tr>
            <th>ID do Pedido</th>
            <th>Usuário</th>
            <th>Quantidade</th>
            <th>Confirmado</th>
            <th>Data do Pedido</th>
            <th>Ação</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['usuario_nome']; ?></td>
                <td><?php echo $row['quantidade']; ?></td>
                <td><?php echo $row['confirmado'] ? 'Sim' : 'Não'; ?></td>
                <td><?php echo $row['data_pedido']; ?></td>
                <td>
                    <?php if ($row['confirmado'] == 0): ?>
                        <form method="post" action="">
                            <input type="hidden" name="pedido_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="confirmar">Confirmar Pagamento</button>
                        </form>
                    <?php else: ?>
                        Confirmado
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>


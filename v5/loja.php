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

// Processar compra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];

    // Verificar se o item existe
    $stmt = $conn->prepare("SELECT preco FROM itens_loja WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($preco_item);
    if ($stmt->fetch()) {
        $stmt->close();

        if ($saldo >= $preco_item) {
            // Deduzir o preço do saldo do usuário
            $novo_saldo = $saldo - $preco_item;

            // Atualizar o saldo do usuário
            $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
            $stmt->bind_param("ii", $novo_saldo, $user_id);
            $stmt->execute();
            $stmt->close();

            // Inserir o item na tabela usuarios_itens
            $stmt = $conn->prepare("INSERT INTO usuarios_itens (user_id, item_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $item_id);
            $stmt->execute();
            $stmt->close();

            $mensagem = "Item comprado com sucesso!";
            $saldo = $novo_saldo; // Atualizar o saldo local
        } else {
            $mensagem = "Saldo insuficiente para comprar este item.";
        }
    } else {
        $mensagem = "Item não encontrado.";
        $stmt->close();
    }
}

// Obter os itens disponíveis na loja
$result = $conn->query("SELECT id, nome, descricao, preco, tipo, imagem FROM itens_loja");

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Loja</title>
    <link rel="stylesheet" href="./assets/css/store_style.css">
</head>
<body>
<div class="wrapper">
    <div id="header" class="header">
        <h1>Loja de Itens</h1>
        <p>Seu saldo: <?php echo $saldo; ?> FATEC Coins</p>
    </div>
    
    <div id="content" class="content">
        <?php if (isset($mensagem)) { echo "<p>$mensagem</p>"; } ?>

    <div style="display: flex; flex-wrap: wrap;">
        <?php while ($item = $result->fetch_assoc()): ?>
                <div style="border: 1px solid #ccc; padding: 10px; margin: 10px;">
                <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                <p><?php echo htmlspecialchars($item['descricao']); ?></p>
                <p>Preço: <?php echo $item['preco']; ?> FATEC Coins</p>
                <?php if ($item['imagem']): ?>
                    <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" style="max-width: 100px;">
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <button type="submit">Comprar</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
    </div>
    <div id="footer" class="footer">
        <br>
        <a href="dashboard.php"><span id="back_menu">Voltar ao Dashboard</span></a>
    </div>
</div>
</body>
</html>


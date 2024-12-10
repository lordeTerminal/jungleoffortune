<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Atualizar avatar selecionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['avatar_id'])) {
    $avatar_id = (int)$_POST['avatar_id'];

    // Verificar se o usuário possui o avatar
    $stmt = $conn->prepare("SELECT ui.item_id FROM usuarios_itens ui JOIN itens_loja il ON ui.item_id = il.id WHERE ui.user_id = ? AND il.tipo = 'avatar' AND il.id = ?");
    $stmt->bind_param("ii", $user_id, $avatar_id);
    $stmt->execute();
    if ($stmt->fetch()) {
        $stmt->close();

        // Atualizar o avatar do usuário
        $stmt = $conn->prepare("UPDATE usuarios SET avatar_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $avatar_id, $user_id);
        $stmt->execute();
        $stmt->close();

        $mensagem = "Avatar atualizado com sucesso!";
    } else {
        $mensagem = "Você não possui este avatar.";
        $stmt->close();
    }
}

// Obter o avatar atual do usuário
$stmt = $conn->prepare("SELECT avatar_id FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($avatar_atual_id);
$stmt->fetch();
$stmt->close();

// Obter os itens do usuário
$stmt = $conn->prepare("SELECT il.id, il.nome, il.descricao, il.tipo, il.imagem FROM usuarios_itens ui JOIN itens_loja il ON ui.item_id = il.id WHERE ui.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$badges = [];
$avatares = [];

while ($item = $result->fetch_assoc()) {
    if ($item['tipo'] == 'badge') {
        $badges[] = $item;
    } elseif ($item['tipo'] == 'avatar') {
        $avatares[] = $item;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #006400; /* Fundo verde estilo cassino */
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: rgba(0, 0, 0, 0.7); /* Fundo escuro para contrastar com o verde */
            padding: 20px;
            border-radius: 10px;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar-section, .badge-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .avatar-container, .badge-container {
            display: inline-block;
            text-align: center;
            margin: 10px;
        }

        .avatar-container img, .badge-container img {
            border-radius: 50%;
            max-width: 100px;
            max-height: 100px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        form .avatar-container {
            max-width: 150px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Perfil de <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>

    <?php if (isset($mensagem)) { echo "<p>$mensagem</p>"; } ?>

    <h2>Avatar Atual</h2>
    <div class="avatar-section">
    <?php
    if ($avatar_atual_id) {
        foreach ($avatares as $avatar) {
            if ($avatar['id'] == $avatar_atual_id) {
                echo '<div class="avatar-container">';
                echo '<img src="' . htmlspecialchars($avatar['imagem']) . '" alt="Avatar Atual">';
                echo '</div>';
                break;
            }
        }
    } else {
        echo "<p>Você não selecionou um avatar.</p>";
    }
    ?>
    </div>

    <h2>Seus Avatares</h2>
    <?php if ($avatares): ?>
        <form method="POST">
            <div class="avatar-section">
                <?php foreach ($avatares as $avatar): ?>
                    <div class="avatar-container">
                        <img src="<?php echo htmlspecialchars($avatar['imagem']); ?>" alt="<?php echo htmlspecialchars($avatar['nome']); ?>"><br>
                        <input type="radio" name="avatar_id" value="<?php echo $avatar['id']; ?>" <?php if ($avatar['id'] == $avatar_atual_id) echo 'checked'; ?>>
                        <?php echo htmlspecialchars($avatar['nome']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <br>
            <button type="submit">Atualizar Avatar</button>
        </form>
    <?php else: ?>
        <p>Você não possui avatares. Visite a <a href="loja.php">loja</a> para comprar um.</p>
    <?php endif; ?>

    <h2>Seus Badges</h2>
    <div class="badge-section">
    <?php if ($badges): ?>
        <?php foreach ($badges as $badge): ?>
            <div class="badge-container">
                <img src="<?php echo htmlspecialchars($badge['imagem']); ?>" alt="<?php echo htmlspecialchars($badge['nome']); ?>"><br>
                <?php echo htmlspecialchars($badge['nome']); ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Você não possui badges. Visite a <a href="loja.php">loja</a> para comprar um.</p>
    <?php endif; ?>
    </div>

    <br><br>
    <a href="dashboard.php">Voltar ao Dashboard</a>
</div>

</body>
</html>


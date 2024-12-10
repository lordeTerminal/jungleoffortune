<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit();
}

// Conectar ao banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Erro de conexão com o banco de dados"]);
    exit();
}

// Verifica se a solicitação é para buscar saldo
if (isset($_GET['fetch_saldo']) && $_GET['fetch_saldo'] == 'true') {
    $user_id = $_SESSION['user_id'];

    // Obter o saldo do usuário
    $stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($saldo);
    $stmt->fetch();

    header("Content-Type: application/json");
    echo json_encode(["saldo" => $saldo]);

    $stmt->close();
    $conn->close();
    exit();
}

// Restante do código para processar compras (não alterado)
// Processar a compra apenas em requisições POST
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

            echo "Item comprado com sucesso!";
            $saldo = $novo_saldo; // Atualizar o saldo local
        } else {
            echo "Saldo insuficiente para comprar este item.";
        }
    } else {
        echo "Item não encontrado.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/store_style.css">
</head>

	<style>
		body, html{
			margin: 0px;
			padding: 0px;
			box-sizing: border-box;
		}

		.dash{
			margin-top: 2%;
			height: 50%;
			color: #03300B;
			font-weight: bolder;
			background-color: whitesmoke;
			padding: 5px;
			border-radius: 10px;
		}
		.dash:hover{background-color: black;}

		.dashB{
			color: black;
			font-weight: bolder;
			text-decoration: none;
			background-color: black;
		}

		.cardColor{
			background-color: orange;
		}

		h1{
			margin-left: 50%;
			color: whitesmoke;

		}

	</style>

<body>

<div >


    <div id="header" class="navbar navbar-expand-lg navbar-dark bg-dark top-0">

	<div class='container-fluid'>
        	<a class="navbar-brand dash" href="dashboard.php"><span id="back_menu">Dashboard</span></a>
               <h1>Loja de Itens</h1>
        </div>

    </div>

  <div id="content" class="container my-4">
        <?php if (isset($mensagem)) { echo "<p>$mensagem</p>"; } ?>

   <div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>

        <?php while ($item = $result->fetch_assoc()): ?>
            <div class='cardColor col-12 col-md-6 col-lg-4' style="border: 1px solid #ccc; padding: 10px; margin: 10px;">
	     <div class="card h-100 text-center d-flex align-items-center justify-content-center cardColor'">
                <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                <p><?php echo htmlspecialchars($item['descricao']); ?></p>
                <p>Preço: <?php echo $item['preco']; ?> FATEC Coins</p>
              <div class="card-body">
	        <?php if ($item['imagem']): ?>
                    <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" style="max-width: 100px;">
                <?php endif; ?>
	      </div>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <button class='btn btn-primary' type="submit">Comprar</button>
                </form>
             </div>
            </div>
        <?php endwhile; ?>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


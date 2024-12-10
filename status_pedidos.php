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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status dos Pedidos de Moedas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fundo e cores */
        body {
            background-color: #2c6e49; /* Cor verde selva */
            color: #fff;
            font-family: 'Arial', sans-serif;
            padding: 20px;
        }

        h1 {
	     margin: 0 15%;
            /*text-align: center;*/
            color: #ffcc00; /* Cor dourada para o título */
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        table {
            margin: 0 10%; /* Centraliza a tabela */
            width: 50%;
            border-collapse: collapse;
            background-color: #1a4d34; /* Fundo verde escuro para a tabela */
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #fff;
        }

        th {
            background-color: #336633; /* Cabeçalho com um tom mais claro de verde */
        }

        tr:nth-child(even) {
            background-color: #1f8033; /* Linhas pares com tom diferente */
        }

        tr:hover {
            background-color: #4d9d69; /* Efeito hover nas linhas */
        }

        p {
            text-align: center;
            font-size: 1.2rem;
        }

        a { margin: 0 20%;
            display: block;
            /*text-align: center;*/
            margin-top: 30px;
            color: #ffcc00;
            font-size: 1.2rem;
            text-decoration: none;
        }

        a:hover {
            color: #ffd700;
        }

        /* Estilo para a área lateral */
        .side-image {
            position: absolute;
            right: 0;
            top: 5%;
            width: 30%;
            height: auto;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <h1>Status dos Pedidos de Moedas</h1>

    <?php if (count($pedidos) > 0): ?>
        <table>
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

    <a href="lojapro.php">Voltar à página de aquisição de moedas</a>

    <!-- Imagem da Onça ou Tigre -->
    <img src= "./assets/onca3.png" alt="Onça" class="side-image">
</body>
</html>

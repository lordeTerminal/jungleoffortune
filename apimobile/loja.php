<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuração do banco de dados
$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Falha na conexão com o banco de dados"));
    exit();
}

// Função para autenticação via token (exemplo simplificado)
function getUserIdFromToken($token) {
    // Aqui você pode verificar o token, por exemplo, consultando um banco de dados ou decodificando um JWT
    // Neste exemplo, retornamos um ID fictício para simplificar
    return 123; // Substitua pela lógica de autenticação real
}

// Recupera o token do cabeçalho da requisição
$token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$user_id = getUserIdFromToken($token);

if (!$user_id) {
    echo json_encode(array("status" => "error", "message" => "Token de autenticação inválido ou ausente"));
    exit();
}

// Rotas para diferentes tipos de requisição
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Obter saldo do usuário e itens da loja
        $stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($saldo);
        $stmt->fetch();
        $stmt->close();

        $result = $conn->query("SELECT id, nome, descricao, preco, tipo, imagem FROM itens_loja");
        $itens = [];

        while ($item = $result->fetch_assoc()) {
            $itens[] = array(
                "id" => $item['id'],
                "nome" => $item['nome'],
                "descricao" => $item['descricao'],
                "preco" => $item['preco'],
                "tipo" => $item['tipo'],
                "imagem" => $item['imagem']
            );
        }

        echo json_encode(array(
            "status" => "success",
            "saldo" => $saldo,
            "itens" => $itens
        ));
        break;

    case 'POST':
        // Processar compra de item
        $input = json_decode(file_get_contents('php://input'), true);
        $item_id = $input['item_id'] ?? null;

        if (!$item_id) {
            echo json_encode(array("status" => "error", "message" => "Item não especificado"));
            exit();
        }

        // Verifica o saldo e o preço do item
        $stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($saldo);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT preco FROM itens_loja WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->bind_result($preco_item);
        
        if ($stmt->fetch()) {
            $stmt->close();

            if ($saldo >= $preco_item) {
                $novo_saldo = $saldo - $preco_item;

                // Atualiza o saldo do usuário
                $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
                $stmt->bind_param("ii", $novo_saldo, $user_id);
                $stmt->execute();
                $stmt->close();

                // Inserir o item na tabela `usuarios_itens`
                $stmt = $conn->prepare("INSERT INTO usuarios_itens (user_id, item_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $user_id, $item_id);
                $stmt->execute();
                $stmt->close();

                echo json_encode(array("status" => "success", "message" => "Item comprado com sucesso!", "novo_saldo" => $novo_saldo));
            } else {
                echo json_encode(array("status" => "error", "message" => "Saldo insuficiente para comprar este item."));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Item não encontrado."));
            $stmt->close();
        }
        break;

    default:
        echo json_encode(array("status" => "error", "message" => "Método de requisição não suportado."));
        break;
}

$conn->close();
?>


<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


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

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = $input['usuario'];
    $senha = $input['senha'];

    // Verificar se os campos estão preenchidos
    if (empty($usuario) || empty($senha)) {
        echo json_encode(array("status" => "error", "message" => "Usuário ou senha não podem estar vazios"));
        exit();
    }

    // Verifica o nome e o saldo do usuário
    $stmt = $conn->prepare("SELECT nome, saldo, password FROM usuarios WHERE nome = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['password'])) {
            // Retorna o nome e o saldo do usuário
            echo json_encode(array("status" => "success", "nome" => $row['nome'], "saldo" => $row['saldo']));
        } else {
            echo json_encode(array("status" => "error", "message" => "Senha incorreta"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Usuário não encontrado"));
    }

    $stmt->close();
}
$conn->close();
?>

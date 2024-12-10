<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Conexão com o banco de dados MySQL
$servername = "localhost";
$username = "root";
$password = "golimar10*";
$dbname = "fatec_cassino";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Falha na conexão com o banco de dados")));
}

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = $input['usuario'];
    $senha = $input['senha'];

    // Verificar se os campos estão preenchidos
    if (empty($usuario) || empty($senha)) {
        echo json_encode(array("status" => "error", "message" => "Usuário ou senha não podem estar vazios"));
        exit();
    }

    // Preparar e executar a consulta no banco de dados
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nome = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verificar se a senha está correta (supondo que a senha esteja criptografada)
        if (password_verify($senha, $row['password'])) {
            echo json_encode(array("status" => "success", "message" => "Login bem-sucedido", "saldo" => $row['saldo']));
        } else {
            echo json_encode(array("status" => "error", "message" => "Senha incorreta"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Usuário não encontrado"));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Método de requisição inválido"));
}

$conn->close();
?>


<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "golimar10*";
$dbname = "fatec_cassino";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Falha na conexão com o banco de dados"));
    exit();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("status" => "error", "message" => "Usuário não logado"));
    exit();
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Obter o avatar atual do usuário
$stmt = $conn->prepare("SELECT avatar_id FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($avatar_atual_id);
$stmt->fetch();
$stmt->close();

// Obter avatares do usuário
$stmt = $conn->prepare("SELECT il.id, il.nome, il.imagem FROM usuarios_itens ui JOIN itens_loja il ON ui.item_id = il.id WHERE ui.user_id = ? AND il.tipo = 'avatar'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$avatares = array();
while ($row = $result->fetch_assoc()) {
    $avatares[] = $row;
}
$stmt->close();

// Obter badges do usuário
$stmt = $conn->prepare("SELECT il.id, il.nome, il.imagem FROM usuarios_itens ui JOIN itens_loja il ON ui.item_id = il.id WHERE ui.user_id = ? AND il.tipo = 'badge'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$badges = array();
while ($row = $result->fetch_assoc()) {
    $badges[] = $row;
}
$stmt->close();

// Obter a imagem do avatar atual (caso o usuário tenha um avatar selecionado)
$avatar_atual_imagem = null;
if ($avatar_atual_id) {
    $stmt = $conn->prepare("SELECT imagem FROM itens_loja WHERE id = ?");
    $stmt->bind_param("i", $avatar_atual_id);
    $stmt->execute();
    $stmt->bind_result($avatar_atual_imagem);
    $stmt->fetch();
    $stmt->close();
}

// Fechar a conexão
$conn->close();

// Retornar os dados do perfil em JSON
echo json_encode(array(
    "status" => "success",
    "avatar_atual_imagem" => $avatar_atual_imagem,
    "avatares" => $avatares,
    "badges" => $badges
));
?>


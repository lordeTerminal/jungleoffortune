<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificação de sessão de admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: loginadm.html");
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

// Verifica se o ID do pedido foi enviado via POST
if (isset($_POST['pedido_id'])) {
    $pedido_id = intval($_POST['pedido_id']);
    
    // Atualiza o status do pedido para confirmado
    $stmt = $conn->prepare("UPDATE pedidos_moedas SET confirmado = 1 WHERE id = ?");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Pedido confirmado com sucesso!";
    } else {
        $_SESSION['message'] = "Erro ao confirmar o pedido ou pedido já confirmado.";
    }
    
    $stmt->close();
} else {
    $_SESSION['message'] = "ID do pedido não fornecido.";
}

$conn->close();

// Redireciona de volta para o painel com uma mensagem de confirmação
header("Location: painel.php");
exit();


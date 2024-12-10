<?php
// Conexão com o banco de dados
$servername = "localhost"; // Ajuste conforme necessário
$username = "root";        // Usuário do MySQL
$password = "golimar10*";   // Senha do MySQL
$dbname = "fatec_cassino";  // Nome do banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Preparando a consulta SQL para buscar todos os registros de logs
$sql = "SELECT id, user_id, aposta, ganhou, valor_ganho, timestamp FROM logs_jogos";
$result = $conn->query($sql);

$logs = [];

if ($result->num_rows > 0) {
    // Pegando os resultados
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }

    // Retornando os logs em formato JSON
    echo json_encode($logs);
} else {
    echo json_encode(["message" => "Nenhum registro encontrado."]);
}

$conn->close();
?>


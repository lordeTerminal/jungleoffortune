<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if user_id and aposta are passed
if (!isset($_POST['user_id']) || !isset($_POST['aposta'])) {
    echo json_encode(array("status" => "error", "message" => "Dados incompletos"));
    exit();
}

// Get user_id and aposta from POST data
$user_id = intval($_POST['user_id']);
$aposta = intval($_POST['aposta']);

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "golimar10*";
$dbname = "fatec_cassino";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Falha na conexão com o banco de dados"));
    exit();
}

// Fetch user's current saldo
$stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($saldo);
$stmt->fetch();
$stmt->close();

if ($saldo >= $aposta) {
    // Deduct the bet from the saldo
    $novo_saldo = $saldo - $aposta;

    // Spin the slot machine (generate 9 random numbers)
    $slots = [];
    for ($i = 0; $i < 9; $i++) {
        $slots[] = rand(1, 5);
    }

    // Check if the user won
    $ganhou = false;
    // Horizontal check
    if (($slots[0] == $slots[1] && $slots[1] == $slots[2]) ||
        ($slots[3] == $slots[4] && $slots[4] == $slots[5]) ||
        ($slots[6] == $slots[7] && $slots[7] == $slots[8])) {
        $ganhou = true;
    }

    // Vertical check
    if (($slots[0] == $slots[3] && $slots[3] == $slots[6]) ||
        ($slots[1] == $slots[4] && $slots[4] == $slots[7]) ||
        ($slots[2] == $slots[5] && $slots[5] == $slots[8])) {
        $ganhou = true;
    }

    // Diagonal check
    if (($slots[0] == $slots[4] && $slots[4] == $slots[8]) ||
        ($slots[2] == $slots[4] && $slots[4] == $slots[6])) {
        $ganhou = true;
    }

    $valor_ganho = 0;
    if ($ganhou) {
        $valor_ganho = $aposta * 2; // Double the bet if the user wins
        $novo_saldo += $valor_ganho;
        $mensagem = "Parabéns! Você ganhou $valor_ganho FATEC Coins!";
    } else {
        $mensagem = "Essa foi por pouco! Tente novamente!";
    }

    // Update saldo in the database
    $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
    $stmt->bind_param("ii", $novo_saldo, $user_id);
    $stmt->execute();
    $stmt->close();

    // Log the game result
    $stmt = $conn->prepare("INSERT INTO logs_jogos (user_id, aposta, ganhou, valor_ganho) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $user_id, $aposta, $ganhou ? 1 : 0, $valor_ganho);
    $stmt->execute();
    $stmt->close();

    // Return the game result
    echo json_encode(array(
        "status" => "success",
        "saldo" => $novo_saldo,
        "slots" => $slots,
        "ganhou" => $ganhou,
        "valor_ganho" => $valor_ganho,
        "mensagem" => $mensagem
    ));
} else {
    echo json_encode(array("status" => "error", "message" => "Saldo insuficiente para apostar."));
}

$conn->close();
?>


<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Usuário não está logado."]);
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
    echo json_encode(["error" => "Falha na conexão com o banco de dados."]);
    exit();
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Obter o saldo do usuário
$stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($saldo);
$stmt->fetch();
$stmt->close();

// Inicializa variáveis
$linha_ganhadora = 0;
$ganhou = false;
$winningIndices = [];

// Verifica se o valor da aposta foi enviado
$aposta = isset($_POST['aposta']) ? (int)$_POST['aposta'] : 10;

// Se o saldo for suficiente para a aposta
if ($saldo >= $aposta) {
    // Deduz a aposta do saldo
    $novo_saldo = $saldo - $aposta;

    // Gira a slot machine (gera 9 números aleatórios entre 1 e 9)
    $slots = [];
    for ($i = 0; $i < 9; $i++) {
        $slots[] = rand(1, 9);
    }

    // Verifica combinações vencedoras (horizontais, verticais e diagonais)

    // Verifica linhas horizontais
    if ($slots[0] == $slots[1] && $slots[1] == $slots[2]) {
        $ganhou = true;
        $linha_ganhadora = 1; // Linha horizontal superior
        $winningIndices = [0, 1, 2];
    } elseif ($slots[3] == $slots[4] && $slots[4] == $slots[5]) {
        $ganhou = true;
        $linha_ganhadora = 2; // Linha horizontal do meio
        $winningIndices = [3, 4, 5];
    } elseif ($slots[6] == $slots[7] && $slots[7] == $slots[8]) {
        $ganhou = true;
        $linha_ganhadora = 3; // Linha horizontal inferior
        $winningIndices = [6, 7, 8];
    }

    // Verifica colunas verticais
    if ($slots[0] == $slots[3] && $slots[3] == $slots[6]) {
        $ganhou = true;
        $linha_ganhadora = 4; // Coluna vertical esquerda
        $winningIndices = [0, 3, 6];
    } elseif ($slots[1] == $slots[4] && $slots[4] == $slots[7]) {
        $ganhou = true;
        $linha_ganhadora = 5; // Coluna vertical do meio
        $winningIndices = [1, 4, 7];
    } elseif ($slots[2] == $slots[5] && $slots[5] == $slots[8]) {
        $ganhou = true;
        $linha_ganhadora = 6; // Coluna vertical direita
        $winningIndices = [2, 5, 8];
    }

    // Verifica diagonais
    if ($slots[0] == $slots[4] && $slots[4] == $slots[8]) {
        $ganhou = true;
        $linha_ganhadora = 7; // Diagonal principal (top-left para bottom-right)
        $winningIndices = [0, 4, 8];
    } elseif ($slots[2] == $slots[4] && $slots[4] == $slots[6]) {
        $ganhou = true;
        $linha_ganhadora = 8; // Diagonal inversa (top-right para bottom-left)
        $winningIndices = [2, 4, 6];
    }

    // Calcula o valor ganho
    $valor_ganho = $ganhou ? $aposta * 2 : 0;
    $novo_saldo += $valor_ganho;

    // Atualiza o saldo no banco de dados
    $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE id = ?");
    $stmt->bind_param("ii", $novo_saldo, $user_id);
    $stmt->execute();
    $stmt->close();

    // Insere o log do jogo
    $stmt = $conn->prepare("INSERT INTO logs_jogos (user_id, aposta, ganhou, valor_ganho, linha_ganhadora) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiii", $user_id, $aposta, $ganhou, $valor_ganho, $linha_ganhadora);
    $stmt->execute();
    $stmt->close();

    // Retorna o resultado em formato JSON para o frontend
    echo json_encode([
        "saldo" => $novo_saldo,
        "slots" => $slots,
        "ganhou" => $ganhou,
        "valor_ganho" => $valor_ganho,
        "winningIndices" => $winningIndices
    ]);
} else {
    echo json_encode(["error" => "Saldo insuficiente."]);
}

$conn->close();

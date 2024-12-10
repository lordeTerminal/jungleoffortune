<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Conexão com o banco de dados
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
    $aposta = $input['aposta'];

    // Verificar se os campos estão preenchidos
    if (empty($usuario) || empty($aposta)) {
        echo json_encode(array("status" => "error", "message" => "Usuário ou aposta não podem estar vazios"));
        exit();
    }

    // Verifica o saldo do usuário
    $stmt = $conn->prepare("SELECT saldo FROM usuarios WHERE nome = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $saldo = $row['saldo'];

        // Verifica se o usuário tem saldo suficiente para apostar
        if ($saldo < $aposta) {
            echo json_encode(array("status" => "error", "message" => "Saldo insuficiente"));
            exit();
        }

        // Gira a roleta e calcula o resultado
        $resultado = girarRoleta();

        // Verifica se o usuário ganhou
        $ganhou = verificarVitoria($resultado);
        $premio = 0;

        if ($ganhou) {
            $premio = $aposta * 2; // Exemplo: multiplica por 2 o valor da aposta
            $novoSaldo = $saldo + $premio;
            $mensagem = "Parabéns! Você ganhou $premio FatecCoins!";
        } else {
            $novoSaldo = $saldo - $aposta;
            $mensagem = "Que pena! Tente novamente.";
        }

        // Atualiza o saldo do usuário no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET saldo = ? WHERE nome = ?");
        $stmt->bind_param("is", $novoSaldo, $usuario);
        $stmt->execute();

        // Retorna o resultado e o novo saldo
        echo json_encode(array(
            "status" => "success",
            "resultado" => $resultado,
            "ganhou" => $ganhou,
            "premio" => $premio,
            "saldo" => $novoSaldo,
            "message" => $mensagem
        ));
    } else {
        echo json_encode(array("status" => "error", "message" => "Usuário não encontrado"));
    }

    $stmt->close();
}
$conn->close();

// Função para girar a roleta e gerar o resultado
function girarRoleta() {
    // Exemplo de 9 símbolos possíveis
    $simbolos = array("Boto", "Onça", "Arara", "Macaco", "Capivara", "Moedas", "Espinho", "Tucano", "Tesouro");

    // Gera um array de 3x3 com símbolos aleatórios
    $roleta = array(
        array($simbolos[array_rand($simbolos)], $simbolos[array_rand($simbolos)], $simbolos[array_rand($simbolos)]),
        array($simbolos[array_rand($simbolos)], $simbolos[array_rand($simbolos)], $simbolos[array_rand($simbolos)]),
        array($simbolos[array_rand($simbolos)], $simbolos[array_rand($simbolos)], $simbolos[array_rand($simbolos)])
    );

    return $roleta;
}

// Função para verificar se houve vitória
function verificarVitoria($resultado) {
    // Verifica linhas horizontais, verticais e diagonais
    return (
        // Linhas horizontais
        ($resultado[0][0] === $resultado[0][1] && $resultado[0][1] === $resultado[0][2]) ||
        ($resultado[1][0] === $resultado[1][1] && $resultado[1][1] === $resultado[1][2]) ||
        ($resultado[2][0] === $resultado[2][1] && $resultado[2][1] === $resultado[2][2]) ||

        // Colunas verticais
        ($resultado[0][0] === $resultado[1][0] && $resultado[1][0] === $resultado[2][0]) ||
        ($resultado[0][1] === $resultado[1][1] && $resultado[1][1] === $resultado[2][1]) ||
        ($resultado[0][2] === $resultado[1][2] && $resultado[1][2] === $resultado[2][2]) ||

        // Diagonais
        ($resultado[0][0] === $resultado[1][1] && $resultado[1][1] === $resultado[2][2]) ||
        ($resultado[0][2] === $resultado[1][1] && $resultado[1][1] === $resultado[2][0])
    );
}
?>


<?php

// Função para embaralhar o array
function shuffleArray($array) {
    for ($i = count($array) - 1; $i > 0; $i--) {
        $j = rand(0, $i);
        // Troca os elementos
        $temp = $array[$i];
        $array[$i] = $array[$j];
        $array[$j] = $temp;
    }
    return $array;
}

function distribuiCartas() {
    // Cria o array de 0 a 39
    $baralho = range(0, 39);

    // Embaralha o baralho
    $baralho = shuffleArray($baralho);

    // Cria os arrays para os jogadores e o vira
    $jogador1 = [];
    $jogador2 = [];
    $jogador3 = [];
    $jogador4 = [];
    $vira = [];

    // Distribui 3 cartas para cada jogador
    for ($i = 0; $i < 3; $i++) {
        $jogador1[] = array_pop($baralho);
        $jogador2[] = array_pop($baralho);
        $jogador3[] = array_pop($baralho);
        $jogador4[] = array_pop($baralho);
    }

    // Define o "vira"
    $vira[] = array_pop($baralho);

    // Exibe os arrays
    echo "<br>Baralho restante: " . implode(", ", $baralho) . "\n";
    echo "<br>Jogador 1: " . implode(", ", $jogador1) . "\n";
    echo "<br>Jogador 2: " . implode(", ", $jogador2) . "\n";
    echo "<br>Jogador 3: " . implode(", ", $jogador3) . "\n";
    echo "<br>Jogador 4: " . implode(", ", $jogador4) . "\n";
    echo "<br>Vira: " . implode(", ", $vira) . "\n";
}

// Chama a função para distribuir as cartas e exibir os arrays
distribuiCartas();

?>


<?php
// functions.php

// Define power values for each card
$cardPowers = [
    '4' => 0,
    '5' => 1,
    '6' => 2,
    '7' => 3,
    'Q' => 4,
    'J' => 5,
    'K' => 6,
    'A' => 7,
    '2' => 8,
    '3' => 9
];

// Update power values for manilhas
function updateManilhaPowers($manilhas, &$cardPowers) {
    foreach ($manilhas as $index => $manilha) {
        $cardPowers[$manilha] = 10 + $index;  // Assign high power values to manilhas
    }
}

// Compare two cards based on their power values
function compareCards($card1, $card2, $cardPowers) {
    $value1 = explode(' ', $card1)[0];
    $value2 = explode(' ', $card2)[0];

    $power1 = $cardPowers[$value1];
    $power2 = $cardPowers[$value2];

    if ($power1 > $power2) {
        return 1;  // Card 1 wins
    } elseif ($power1 < $power2) {
        return -1; // Card 2 wins
    } else {
        return 0;  // It's a tie
    }
}


<?php
// game.php
include 'core.php';
include 'functions.php';

// Update manilha powers
updateManilhaPowers($manilhas, $cardPowers);

// Example round of play
$roundResult = playRound($player1Hand[0], $player2Hand[0], $player3Hand[0], $player4Hand[0], $cardPowers);

echo $roundResult;
?>


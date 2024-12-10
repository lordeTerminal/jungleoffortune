<?php
// index.php
include 'game.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truco Game</title>
    <style>
        /* Include your CSS here */
    </style>
</head>
<body>
    <div class="game-board">
        <div id="player1" class="player-hand">
            <?php foreach ($player1Hand as $card): ?>
                <div class="card"><?= $card ?></div>
            <?php endforeach; ?>
        </div>

        <div id="player2" class="player-hand">
            <?php foreach ($player2Hand as $card): ?>
                <div class="card card-back">?</div>
            <?php endforeach; ?>
        </div>

        <div id="player3" class="player-hand">
            <?php foreach ($player3Hand as $card): ?>
                <div class="card card-back">?</div>
            <?php endforeach; ?>
        </div>

        <div id="player4" class="player-hand">
            <?php foreach ($player4Hand as $card): ?>
                <div class="card card-back">?</div>
            <?php endforeach; ?>
        </div>

        <div class="turn-up-card">
            <h3>Turned Up Card</h3>
            <div class="card"><?= $turnUpCard ?></div>
        </div>

        <div class="manilhas">
            <h3>Manilhas</h3>
            <?php foreach ($manilhas as $manilha): ?>
                <div class="card"><?= $manilha ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>


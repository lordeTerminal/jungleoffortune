<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truco Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2b2b2b;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .game-board {
            position: relative;
            width: 80%;
            height: 80%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .player-hand {
            position: absolute;
            display: flex;
            gap: 10px;
        }

        #player1 {
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        #player2 {
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        #player3 {
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        #player4 {
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .card {
            width: 60px;
            height: 90px;
            background-color: #2980b9;
            border-radius: 5px;
            text-align: center;
            line-height: 90px;
            font-size: 18px;
            color: white;
            display: inline-block;
            margin: 5px;
        }

        .card-back {
            background-image: url('./assets/card-back.png');
            background-size: cover;
        }
    </style>
</head>
<body>
    <div class="game-board">
        <div id="player1" class="player-hand">
            <!-- Your cards will be displayed here -->
        </div>

        <div id="player2" class="player-hand">
            <!-- Your ally's cards (hidden) -->
        </div>

        <div id="player3" class="player-hand">
            <!-- Left opponent's cards (hidden) -->
        </div>

        <div id="player4" class="player-hand">
            <!-- Right opponent's cards (hidden) -->
        </div>
    </div>

    <!-- Link to external JavaScript files -->
    <script src="roundlogic.js"></script>
    <script src="trucocalls.js"></script>

    <script>
        // Example data for the cards in each player's hand
        const player1Hand = ['4 de Paus', '5 de Copas', '6 de Espadas'];
        const player2Hand = ['?', '?', '?']; // Ally's cards are hidden
        const player3Hand = ['?', '?', '?']; // Left opponent's cards are hidden
        const player4Hand = ['?', '?', '?']; // Right opponent's cards are hidden

        // Function to render the cards
        function renderCards(playerId, hand) {
            const playerDiv = document.getElementById(playerId);
            playerDiv.innerHTML = ''; // Clear any existing cards

            hand.forEach(card => {
                const cardDiv = document.createElement('div');
                cardDiv.classList.add('card');
                cardDiv.textContent = card;
                if (card === '?') {
                    cardDiv.classList.add('card-back'); // Apply the card-back style for hidden cards
                }
                playerDiv.appendChild(cardDiv);
            });
        }

        // Render the hands
        renderCards('player1', player1Hand);
        renderCards('player2', player2Hand);
        renderCards('player3', player3Hand);
        renderCards('player4', player4Hand);
    </script>
</body>
</html>


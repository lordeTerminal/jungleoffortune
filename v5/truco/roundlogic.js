const cardPowers = {
    '4': 0,
    '5': 1,
    '6': 2,
    '7': 3,
    'Q': 4,
    'J': 5,
    'K': 6,
    'A': 7,
    '2': 8,
    '3': 9
};

// Example manilhas (determined by the turned-up card)
const manilhas = ['7 de Ouros', '7 de Espadas', '7 de Copas', '7 de Paus'];

// Function to determine the power of a card
function getCardPower(card) {
    const [value, naipe] = card.split(' de ');
    if (manilhas.includes(card)) {
        return 10 + manilhas.indexOf(card);  // Manilhas have the highest power
    }
    return cardPowers[value];
}

// Simulate other players' moves
function simulateOpponentMove(playerHand) {
    return playerHand.pop(); // Simplified: take the last card from their hand
}

// Function to handle playing a card
function playCard(playerCard) {
    // Simulate other players' moves
    const opponent1Card = simulateOpponentMove(player3Hand);
    const opponent2Card = simulateOpponentMove(player4Hand);
    const allyCard = simulateOpponentMove(player2Hand);

    // Compare the cards
    const playedCards = [
        { player: 'Player 1', card: playerCard, power: getCardPower(playerCard) },
        { player: 'Opponent 1', card: opponent1Card, power: getCardPower(opponent1Card) },
        { player: 'Opponent 2', card: opponent2Card, power: getCardPower(opponent2Card) },
        { player: 'Ally', card: allyCard, power: getCardPower(allyCard) }
    ];

    // Determine the winning card
    playedCards.sort((a, b) => b.power - a.power);
    const winner = playedCards[0];

    // Display the result
    alert(`${winner.player} wins the round with ${winner.card}!`);
}

// Event listener for Player 1's card click
document.querySelectorAll('#player1 .card').forEach(card => {
    card.addEventListener('click', function() {
        const playerCard = this.textContent;
        playCard(playerCard);

        // Remove the card from the hand
        this.remove();
    });
});


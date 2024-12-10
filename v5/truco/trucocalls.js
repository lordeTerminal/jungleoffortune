let currentPoints = 1;

document.getElementById('call-truco').addEventListener('click', function() {
    const trucoResponse = confirm("Truco! Do you accept?");
    if (trucoResponse) {
        currentPoints = 3;
        alert("Truco accepted! Round is now worth 3 points.");
    } else {
        alert("Opponent refused the Truco. You win the round.");
        // Logic to end the round and give points to the player
    }
});


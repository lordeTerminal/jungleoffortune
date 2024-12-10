document.getElementById('spin-button').addEventListener('click', spinReels);

let playerCredits;

// Atualiza a exibição de créditos na interface
function updateCreditsDisplay() {
    let resultContainer = document.querySelector('.results p');
    resultContainer.textContent = `Créditos: ${playerCredits}`;
}

// Envia a solicitação para o servidor PHP e inicia o jogo
function spinReels() {
    const aposta = 10; // Valor fixo da aposta, pode ser dinâmico

    fetch('jogo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `aposta=${aposta}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        // Atualiza os créditos do jogador
        playerCredits = data.saldo;
        updateCreditsDisplay();

        // Mostra os resultados da slot machine com animação assíncrona
        const results = data.slots;  // Resultado das roletas
        const winningIndices = data.winningIndices;  // Índices vencedores

        // Anima cada coluna com tempos de giro e paradas diferentes
        animateReels(results, winningIndices);
    })
    .catch(error => console.error('Erro:', error));
}

// Função para animar cada reel com tempos de giro diferentes
function animateReels(results, winningIndices) {
    const reels = [
        document.getElementById('reel1').querySelector('.symbols'),
        document.getElementById('reel2').querySelector('.symbols'),
        document.getElementById('reel3').querySelector('.symbols')
    ];

    reels.forEach((reel, index) => {
        const randomStartDelay = Math.random() * 500; // Atraso aleatório para iniciar o giro de cada coluna
        const randomSpinDuration = 3000 + Math.random() * 2000; // Tempo de giro aleatório para cada coluna

        // Inicia o giro após o atraso aleatório
        setTimeout(() => {
            startSpin(reel);  // Inicia a animação do giro
        }, randomStartDelay);

        // Para o giro após a duração definida
        setTimeout(() => {
            stopSpin(reel, index, results[index], winningIndices.includes(index)); // Para a coluna e aplica o resultado final
        }, randomStartDelay + randomSpinDuration);
    });
}

// Função para iniciar o giro
function startSpin(reel) {
    const symbols = reel.querySelector('.symbols');
    symbols.classList.add('spin');  // Inicia a animação de giro
    symbols.style.transform = '';   // Resseta a posição do giro
}

// Função para parar o giro e aplicar o resultado do PHP
function stopSpin(reel, reelIndex, result, isWinning) {
    const symbols = reel.querySelector('.symbols');
    symbols.classList.remove('spin');  // Remove a animação de giro

    // Adiciona a classe 'stopSpin' para controlar a desaceleração
    symbols.classList.add("stopSpin");

    // Remove a classe 'stopSpin' após a desaceleração
    setTimeout(() => {
        symbols.classList.remove("stopSpin");
    }, 1000);

    // Verifica se a imagem está disponível
    const imgPath = `imagens/animal${result}.webp`;
    const img = new Image();
    img.src = imgPath;
    img.style.width = '100%';
    img.style.height = '100%';
    img.alt = 'Símbolo';

    img.onload = function () {
        // Se a imagem carregar corretamente, insere-a no reel
        symbols.innerHTML = '';
        symbols.appendChild(img);

        // Se este reel faz parte de uma linha vencedora, destaque-o
        if (isWinning) {
            img.classList.add('highlight');
        }
    };

    img.onerror = function () {
        console.error(`Erro ao carregar a imagem: ${imgPath}`);
    };

}

document.getElementById('spin-button').addEventListener('click', spinReels);

let credits = 40;//isso será dinâmico, dependendo dos ganhos salvos no banco de dados
let playerCredits = credits;

// Atualiza a exibição de créditos na interface
function updateCreditsDisplay() {
    let resultContainer = document.querySelector('.results p');
    resultContainer.textContent = `Créditos: ${playerCredits}`;
}

// Lista de imagens disponíveis
const images = [
    'imagens/animal1.webp',
    'imagens/animal2.webp',
    'imagens/animal3.webp',
    'imagens/animal4.webp',
    'imagens/animal5.webp',
    'imagens/animal6.webp',
    'imagens/animal7.webp',
    'imagens/animal8.webp',
    'imagens/animal9.webp'
];

// Função para duplicar e embaralhar as imagens para cada coluna
function shuffleImages(array) {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return [...shuffled, ...shuffled]; // Duplicamos as imagens embaralhadas para criar o efeito contínuo
}

// Inicializa as roletas com imagens (sem embaralhar)
function initializeReels() {
    const reels = [
        document.getElementById('reel1').querySelector('.symbols'),
        document.getElementById('reel2').querySelector('.symbols'),
        document.getElementById('reel3').querySelector('.symbols')
    ];

    reels.forEach(reel => {
        reel.innerHTML = '';

        // Embaralha e duplica as imagens para cada roleta
        const imagesArray = shuffleImages(images);

        // Adiciona as imagens ao reel
        imagesArray.forEach(imgSrc => {
            const img = document.createElement('img');
            img.src = imgSrc;
            img.style.width = '100%';
            img.style.height = '100%';
            img.alt = 'Símbolo';
            reel.appendChild(img);
        });
    });
}

// Função para girar as roletas
function spinReels() {
    const reels = [
        document.getElementById('reel1'),
        document.getElementById('reel2'),
        document.getElementById('reel3')
    ];

    const results = []; // Aqui armazenamos o resultado das imagens após a roleta parar
    let reelsStopped = 0; // Para contar quando todas as roletas pararam

    reels.forEach((reel, index) => {
        const randomStartDelay = Math.random() * 500; // Atraso aleatório para iniciar o giro de cada coluna
        const randomSpinDuration = 3000 + Math.random() * 2000; // Tempo de giro aleatório para cada coluna

        // Inicia o giro após o atraso aleatório
        setTimeout(() => {
            startSpin(reel);
        }, randomStartDelay);

        // Para o giro após a duração definida
        setTimeout(() => {
            stopSpin(reel, index, results);
            reelsStopped++; // Conta cada roleta que parou

            // Após todas as roletas pararem, verifica a combinação
            if (reelsStopped === reels.length) {
                checkForWin(results); // Verifica a combinação após todas pararem
            }
        }, randomStartDelay + randomSpinDuration);
    });
}

// Função para iniciar o giro
function startSpin(reel) {
    const symbols = reel.querySelector('.symbols');
    symbols.classList.add('spin');
    symbols.style.transform = ''; // Resseta a posição para iniciar o giro
}

// Função para parar o giro
function stopSpin(reel, reelIndex, results) {
    const symbols = reel.querySelector('.symbols');
    symbols.classList.remove('spin');
    symbols.classList.add("stopSpin");

    // Remove a classe 'stopSpin' após a animação de desaceleração
    setTimeout(() => {
        symbols.classList.remove("stopSpin");
    }, 1000);

    // Seleciona uma posição aleatória para parar
    const symbolHeight = symbols.querySelector('img').clientHeight;
    const numSymbols = symbols.children.length / 2; // Como as imagens foram duplicadas
    const stopIndex = Math.floor(Math.random() * numSymbols);

    // Define a posição para mostrar o símbolo de parada
    symbols.style.transform = `translateY(${-stopIndex * symbolHeight}px)`;

    // Salva o resultado para cada roleta
    results[reelIndex] = stopIndex;
}

// Função para verificar as combinações vencedoras
function checkForWin(results) {
    const symbols = [
        document.getElementById('reel1').querySelector('.symbols'),
        document.getElementById('reel2').querySelector('.symbols'),
        document.getElementById('reel3').querySelector('.symbols')
    ];

    // Obtenha o símbolo em cada roleta, para as três linhas (superior, meio, inferior)
    const topRow = [symbols[0].children[results[0]].src, symbols[1].children[results[1]].src, symbols[2].children[results[2]].src];
    const middleRow = [symbols[0].children[results[0] + 1].src, symbols[1].children[results[1] + 1].src, symbols[2].children[results[2] + 1].src];
    const bottomRow = [symbols[0].children[results[0] + 2].src, symbols[1].children[results[1] + 2].src, symbols[2].children[results[2] + 2].src];

    if(credits > 0){
        if (isWinningLine(topRow)) {
            playerCredits += 10; // Créditos para a linha superior
        }else if (isWinningLine(middleRow)) {
            playerCredits += 40; // Créditos para a linha do meio
        } else if (isWinningLine(bottomRow)) {
            playerCredits += 10; // Créditos para a linha inferior
        } else if (isWinningLine([topRow[0], middleRow[1], bottomRow[2]])) {
            playerCredits += 30; // Créditos para a diagonal principal
        } else if (isWinningLine([topRow[2], middleRow[1], bottomRow[0]])) {
            playerCredits += 30; // Créditos para a diagonal inversa
        }else{
            playerCredits -= 2;
        }
    }
        
    // console.log(`Créditos do jogador: ${playerCredits}`);
    setTimeout(() => {
        let resultContainer = document.querySelector('.results p');
        resultContainer.textContent = `Créditos:${playerCredits}`;
    }, 2000);

}

// Função para verificar se todas as imagens na linha são iguais (vencedora)
function isWinningLine(line) {
    return line[0] === line[1] && line[1] === line[2];
}

// Chama a função para inicializar as roletas quando a página carregar
initializeReels();
updateCreditsDisplay();

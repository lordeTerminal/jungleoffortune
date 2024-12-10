const btn = document.querySelector('.showGame');
const btnExit = document.querySelector('.btnExit');
let containerCabana = document.querySelector(".container-cabana");
let backG = document.querySelector('.backG');
let imgCabana = containerCabana.querySelector('img');

btn.addEventListener('click', () => {
    if (btn) {     
        // Adiciona a classe de zoom na imagem
        imgCabana.classList.add('zoom-in');

        // Oculta o botão e ajusta o tempo da animação
        setTimeout(() => {
            containerCabana.style.display = "none"; // Esconde a imagem da cabana após o zoom
            backG.style.visibility = "visible"; 
            btnExit.style.visibility = 'visible';
        }, 4000); // O zoom dura 5 segundos
        btn.style.visibility = 'hidden';
    }
});

btnExit.addEventListener('click', () => {
    if (btnExit) {
        // Remove a classe de zoom na imagem
        imgCabana.classList.remove('zoom-in');

        setTimeout(() => {
            containerCabana.style.display = "";
            backG.style.visibility = "hidden";
        }, 1000);
        btnExit.style.visibility = 'hidden';
        btn.style.visibility = 'visible';
    }
});


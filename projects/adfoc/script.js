// Création d'un élément audio
const audio = new Audio();

// Définition du son à jouer (vous pouvez remplacer le lien par un son de votre choix)
audio.src = 'https://arnaudlemascon.fr/projects/adfoc/ping.mp3';

// Fonction de gestion du clic sur une image
function handleClick() {
    if (!this.classList.contains('clicked')) { // Vérifie si l'élément n'a pas déjà été cliqué
        this.style.filter = 'none'; // Supprime le filtre noir et blanc contrasté
        this.classList.add('clicked'); // Ajoute une classe pour changer la couleur de l'image
        audio.play(); // Joue le son
        count--; // Diminue le count seulement si c'est la première fois que l'image est cliquée
        countDisplay.textContent = count;
    }
}

// Variables pour le suivi du déplacement de l'image de fond et des images
let isDragging = false;
let startMousePosition = { x: 0, y: 0 };
let startBackgroundPosition = { x: 0, y: 0 };
let startImagePositions = [];

// Fonction pour commencer le déplacement (souris)
function startDragging(e) {
    isDragging = true;
    startMousePosition = { x: e.clientX, y: e.clientY };
    startBackgroundPosition = { 
        x: parseInt(getComputedStyle(imagesContainer).backgroundPositionX),
        y: parseInt(getComputedStyle(imagesContainer).backgroundPositionY)
    };
    startImagePositions = [];
    images.forEach(image => {
        startImagePositions.push({
            x: parseInt(getComputedStyle(image).left),
            y: parseInt(getComputedStyle(image).top)
        });
    });
    e.preventDefault(); // Empêcher la sélection de texte pendant le déplacement
}

// Fonction pour arrêter le déplacement (souris)
function stopDragging() {
    isDragging = false;
}

// Fonction pour effectuer le déplacement (souris)
function drag(e) {
    if (isDragging) {
        const dx = e.clientX - startMousePosition.x;
        const dy = e.clientY - startMousePosition.y;

        imagesContainer.style.backgroundPositionX = startBackgroundPosition.x + dx + 'px';
        imagesContainer.style.backgroundPositionY = startBackgroundPosition.y + dy + 'px';

        images.forEach((image, index) => {
            const startX = startImagePositions[index].x;
            const startY = startImagePositions[index].y;
            image.style.left = startX + dx + 'px';
            image.style.top = startY + dy + 'px';
        });
    }
}

// Fonction pour commencer le déplacement (tactile)
function startDraggingTouch(e) {
    isDragging = true;
    startMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
    startBackgroundPosition = { 
        x: parseInt(getComputedStyle(imagesContainer).backgroundPositionX),
        y: parseInt(getComputedStyle(imagesContainer).backgroundPositionY)
    };
    startImagePositions = [];
    images.forEach(image => {
        startImagePositions.push({
            x: parseInt(getComputedStyle(image).left),
            y: parseInt(getComputedStyle(image).top)
        });
    });
    e.preventDefault(); // Empêcher la sélection de texte pendant le déplacement
}

// Fonction pour effectuer le déplacement (tactile)
function dragTouch(e) {
    if (isDragging) {
        const dx = e.touches[0].clientX - startMousePosition.x;
        const dy = e.touches[0].clientY - startMousePosition.y;

        imagesContainer.style.backgroundPositionX = startBackgroundPosition.x + dx + 'px';
        imagesContainer.style.backgroundPositionY = startBackgroundPosition.y + dy + 'px';

        images.forEach((image, index) => {
            const startX = startImagePositions[index].x;
            const startY = startImagePositions[index].y;
            image.style.left = startX + dx + 'px';
            image.style.top = startY + dy + 'px';
        });
    }
}

// Récupération de l'élément #images
const imagesContainer = document.getElementById('images');

// Récupération des éléments images
const images = document.querySelectorAll('.clickable');
const countDisplay = document.getElementById('count');

// Initialisation du compteur
let count = images.length;
countDisplay.textContent = count;

// Ajout d'un gestionnaire d'événement à chaque image (souris)
images.forEach(image => {
    image.addEventListener('click', handleClick);
});

// Ajout d'un gestionnaire d'événement à chaque image (tactile)
images.forEach(image => {
    image.addEventListener('touchstart', function(e) {
        e.preventDefault(); // Empêche le défilement pendant le déplacement
        handleClick.call(this); // Appelle la fonction handleClick en conservant le contexte de l'image
    });
});

// Ajout des gestionnaires d'événements pour le déplacement de l'image de fond (souris)
imagesContainer.addEventListener('mousedown', startDragging);
imagesContainer.addEventListener('mouseup', stopDragging);
imagesContainer.addEventListener('mousemove', drag);

// Ajout des gestionnaires d'événements pour le déplacement de l'image de fond (tactile)
imagesContainer.addEventListener('touchstart', startDraggingTouch);
imagesContainer.addEventListener('touchend', stopDragging);
imagesContainer.addEventListener('touchmove', dragTouch);

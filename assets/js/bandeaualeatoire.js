// Tableau contenant les noms de fichiers des images webp
var images = ['./assets/pictures/banner/cat1.webp', './assets/pictures/banner/sofa.webp', './assets/pictures/banner/cat2.webp', './assets/pictures/banner/shelf1.webp', './assets/pictures/banner/shelf2.webp', './assets/pictures/banner/window1.webp', './assets/pictures/banner/window2.webp', './assets/pictures/banner/frames.webp', './assets/pictures/banner/frame.webp'];

// Fonction pour charger les images de manière aléatoire dans le bandeau
function chargerImagesAleatoires() {
    // Mélanger le tableau d'images
    shuffle(images);

    // Récupérer le div du bandeau
    var banner = document.getElementById('BANNER');

    // Effacer le contenu précédent du bandeau
    banner.innerHTML = '';

    // Ajouter les images au bandeau
    for (var i = 0; i < images.length; i++) {
        var img = document.createElement('img');
        img.src = images[i];
        img.setAttribute('alt', 'Description de l\'image ' + i); // Ajout de la propriété alt
        banner.appendChild(img);
    }



}


// Fonction pour mélanger un tableau
function shuffle(array) {
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
}

// Charger les images aléatoires au chargement de la page
window.onload = function () {
    chargerImagesAleatoires();
};

/*
// Créer l'élément div avec la classe "animation"
var divAnimation = document.createElement("div");
divAnimation.className = "animation";

// Créer l'élément div avec la classe "nessclass"
var divNessclass = document.createElement("div");
divNessclass.className = "nessclass";

// Créer les éléments div avec les IDs "blackNess", "whiteNess" et "pinkNess"
var divBlackNess = document.createElement("div");
divBlackNess.id = "blackNess";

var divWhiteNess = document.createElement("div");
divWhiteNess.id = "whiteNess";

var divPinkNess = document.createElement("div");
divPinkNess.id = "pinkNess";

// Créer l'élément div avec la classe "eyes"
var divEyes = document.createElement("div");
divEyes.className = "eyes";

// Créer les éléments div avec les classes "eye", "eyelid" et les IDs "R-eyelid" et "L-eyelid"
var divEye1 = document.createElement("div");
divEye1.className = "eye";

var divEye2 = document.createElement("div");
divEye2.className = "eye";

var divREyelid = document.createElement("div");
divREyelid.id = "R-eyelid";
divREyelid.className = "eyelid";

var divLEyelid = document.createElement("div");
divLEyelid.id = "L-eyelid";
divLEyelid.className = "eyelid";

// Créer l'élément div avec l'id "eyebrows"
var divEyebrows = document.createElement("div");
divEyebrows.id = "eyebrows";

// Créer les éléments p avec les IDs "positionDuBas", "positionBande" et "resultat"
var pPositionDuBas = document.createElement("p");
pPositionDuBas.id = "positionDuBas";

var pPositionBande = document.createElement("p");
pPositionBande.id = "positionBande";

var pResultat = document.createElement("p");
pResultat.id = "resultat";

// Ajouter les éléments créés comme enfants des éléments parent appropriés
divEyes.appendChild(divEye1);
divEyes.appendChild(divEye2);
divEyes.appendChild(divREyelid);
divEyes.appendChild(divLEyelid);

divNessclass.appendChild(divBlackNess);
divNessclass.appendChild(divWhiteNess);
divNessclass.appendChild(divPinkNess);
divNessclass.appendChild(divEyes);
divNessclass.appendChild(divEyebrows);

divAnimation.appendChild(divNessclass);
divAnimation.appendChild(pPositionDuBas);
divAnimation.appendChild(pPositionBande);
divAnimation.appendChild(pResultat);


// Ajouter le div animation avec la classe banner et l'id banner comme enfant du body
var divBanner = document.createElement("div");
divBanner.className = "banner";
divBanner.id = "banner";
divBanner.appendChild(divAnimation);

document.body.appendChild(divBanner);

*/
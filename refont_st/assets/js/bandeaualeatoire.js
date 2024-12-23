var images = ['./assets/pictures/banner/cat1.webp', './assets/pictures/banner/sofa.webp', './assets/pictures/banner/cat2.webp', './assets/pictures/banner/shelf1.webp', './assets/pictures/banner/shelf2.webp', './assets/pictures/banner/window1.webp', './assets/pictures/banner/frames.webp'];

function chargerImagesAleatoires() {
    shuffle(images);

    var banner = document.getElementById('BANNER');

    banner.innerHTML = '';

    for (var i = 0; i < images.length; i++) {
        var img = document.createElement('img');
        img.src = images[i];
        img.setAttribute('alt', 'Description de l\'image ' + i); // Ajout de la propriété alt
        banner.appendChild(img);
    }
}

function shuffle(array) {
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
}

window.onload = function () {
    chargerImagesAleatoires();
};
document.addEventListener('DOMContentLoaded', function () {
    const menuElements = {
        VALUES: document.getElementById('VALUES'),
        STORY: document.getElementById('STORY'),
        CV: document.getElementById('CV'),
    };
    const achievementContainer = document.getElementById('ACHIEVEMENT');
    const introElement = document.getElementById('INTRO');
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    let currentLanguage = 'en'; // Langue par défaut

    // Fonction pour charger les données JSON
    function loadData() {
        return fetch(jsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des données JSON.');
                }
                return response.json();
            });
    }

    // Fonction pour mettre à jour le menu
    function updateMenu(menuData) {
        Object.keys(menuElements).forEach(key => {
            if (menuData[key]) {
                menuElements[key].textContent =
                    menuData[key][currentLanguage] || menuData[key]['en'];
            }
        });
    }

    // Fonction pour mettre à jour l'introduction
    function updateIntro(infoData) {
        if (infoData['INTRO']) {
            introElement.textContent =
                infoData['INTRO'][currentLanguage] || infoData['INTRO']['en'];
        }
    }

    // Fonction pour générer les réalisations
    function updateAchievements(achievementsData) {
        achievementContainer.innerHTML = ''; // Réinitialiser le conteneur
        for (const key in achievementsData) {
            if (achievementsData.hasOwnProperty(key)) {
                achievementsData[key].forEach(item => {
                    const achievementElement = document.createElement('div');
                    achievementElement.classList.add('container-achievement');
                    achievementElement.setAttribute(
                        'data-image',
                        `./assets/picture/gallery/${item.gallery}`
                    );

                    achievementElement.innerHTML = `
                        <img src="./assets/picture/achievement/${item.icon}" alt="${item.alt}" class="card-img-achievement">
                        <div class="tooltip-text">
                            <div class="tooltip-title">${item.title[currentLanguage] || item.title['en']}</div>
                            <div class="tooltip-description">${item.description[currentLanguage] || item.description['en']}</div>
                        </div>
                    `;
                    achievementContainer.appendChild(achievementElement);
                });
            }
        }
    }

    // Fonction pour mettre à jour tous les éléments en fonction de la langue
    function updateContent(data) {
        const menuData = data.MENU[0];
        const infoData = data.INFO[0];
        const achievementsData = data.ACHIEVEMENTS[0];

        updateMenu(menuData);
        updateIntro(infoData);
        updateAchievements(achievementsData);
    }

    // Gestion du changement de langue via les boutons radio
    const languageRadios = document.querySelectorAll('input[name="language"]');
    languageRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            switch (this.value) {
                case 'fr':
                    currentLanguage = 'fr';
                    break;
                case 'es':
                    currentLanguage = 'sp';
                    break;
                case 'gb':
                    currentLanguage = 'en';
                    break;
                default:
                    currentLanguage = 'en';
            }
            // Recharger le contenu avec la nouvelle langue
            loadData().then(updateContent).catch(console.error);
        });
    });

    // Charger les données au chargement de la page
    loadData()
        .then(updateContent)
        .catch(error => {
            console.error('Erreur lors du chargement initial:', error);
        });
});

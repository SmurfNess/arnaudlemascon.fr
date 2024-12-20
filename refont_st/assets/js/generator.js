document.addEventListener('DOMContentLoaded', function () {
    const menuElements = {
        VALUES: document.getElementById('VALUES'),
        STORY: document.getElementById('STORY'),
        CV: document.getElementById('CV'),
    };
    const achievementContainer = document.getElementById('ACHIEVEMENT');
    const positionContainer = document.getElementById('POSITIONS');
    const introElement = document.getElementById('INTRO');
    const profilePicture = document.querySelector('.img-profile-picture');
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    let currentLanguage = 'en'; // Langue par défaut
    let originalProfilePictureSrc = profilePicture ? profilePicture.src : '';

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

        // Configure les événements après avoir ajouté les éléments
        setupAchievementHover();
    }

    // Fonction pour configurer les événements de survol sur les réalisations
    function setupAchievementHover() {
        if (!profilePicture) {
            console.error('Image de profil introuvable. Vérifiez la classe .img-profile-picture');
            return;
        }

        const achievements = document.querySelectorAll('.container-achievement');

        // Stockez la source originale si elle n'a pas encore été sauvegardée
        if (!originalProfilePictureSrc) {
            originalProfilePictureSrc = profilePicture.src;
        }

        achievements.forEach((achievement) => {
            achievement.addEventListener('mouseover', () => {
                const newSrc = achievement.getAttribute('data-image');
                if (newSrc) {
                    profilePicture.src = newSrc;
                } else {
                    console.error('L\'achievement ne contient pas d\'attribut data-image.');
                }
            });

            achievement.addEventListener('mouseout', () => {
                profilePicture.src = originalProfilePictureSrc;
            });
        });
    }

    function updatePositions(positionsData) {
        positionContainer.innerHTML = ''; // Réinitialiser le conteneur

        for (const key in positionsData) {
            if (positionsData.hasOwnProperty(key)) {
                positionsData[key].forEach(item => {
                    const positionElement = document.createElement('div');
                    positionElement.classList.add('container-achievement');

                    positionElement.innerHTML = `
                            <div class="card-content">
                                <div class="card-enterprise-asset row">
                                    <div class="col-4 card-enterprise first">
                                        <img src="./assets/picture/ent/${item.enterprise-logo}" alt="">
                                    </div>
                                    <div class="col-4">
                                        <div class="card-enterprise-name">${item.enterprise}</div>
                                        <div class="card-enterprise-filiale">${item.client}</div>
                                        <div class="card-enterprise-position">${item.position[currentLanguage] || item.title['en']}</div>
                                        <div class="card-enterprise-duration">01/01/2024</div>
                                    </div>
                                    <div class="col-4 card-enterprise second">
                                        <img src="./assets/picture/ent/${item.client-logo}" alt="">
                                    </div>
                                    <div class="col-6">
                                        <div class="card-enterprise-grid">
                                            <div class="container-tools">
                                                <img src="./assets/picture/techno/excel.png" alt="tools" class="card-img-tools">
                                                <div class="tooltip-text"><div class="tooltip-title">Excel</div><div class="tooltip-description"></div><div class="tooltip-year"></div></div>
                                            </div>
                                            <div class="container-tools">
                                                <img src="./assets/picture/techno/snow.png" alt="tools" class="card-img-tools">
                                                <div class="tooltip-text"><div class="tooltip-title">Service<br>Now</div><div class="tooltip-description"></div><div class="tooltip-year"></div></div>
                                            </div>
                                            <div class="container-tools">
                                                <img src="./assets/picture/techno/itil.png" alt="tools" class="card-img-tools">
                                                <div class="tooltip-text"><div class="tooltip-title">ITIL4</div><div class="tooltip-description"></div><div class="tooltip-year"></div></div>
                                            </div>
                                            <div class="container-tools">
                                                <img src="./assets/picture/techno/365.png" alt="tools" class="card-img-tools">
                                                <div class="tooltip-text"><div class="tooltip-title">Micorsoft 365</div><div class="tooltip-description"></div><div class="tooltip-year"></div></div>
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="col-6">
                                        <div class="card-enterprise-mission">${item.description[currentLanguage] || item.title['en']}</div>
                                    </div>             
                                </div>
                            </div>
                            `;
                    achievementContainer.appendChild(positionElement);
                });
            }
        }
    }

    // Fonction pour mettre à jour tout le contenu
    function updateContent(data) {
        const menuData = data.MENU[0];
        const infoData = data.INFO[0];
        const achievementsData = data.ACHIEVEMENTS[0];
        const positionsData = data.POSITIONS[0];
        
        updateMenu(menuData);
        updateIntro(infoData);
        updateAchievements(achievementsData);
        updatePositions(positionsData);
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

document.addEventListener('DOMContentLoaded', function () {
    const menuElements = {
        VALUES: document.getElementById('VALUES'),
        STORY: document.getElementById('STORY'),
        CV: document.getElementById('CV'),
    };
    const achievementContainer = document.getElementById('ACHIEVEMENT');
    const positionContainer = document.getElementById('POSITIONS');
    const introElement = document.getElementById('INTRO');
    const workingElement = document.getElementById('WORKING');
    const currentElement = document.getElementById('CURRENT');
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

        // Fonction pour mettre à jour l'introduction
        function updateWorking(infoData) {
            if (infoData['WORKING']) {
                workingElement.textContent =
                    infoData['WORKING'][currentLanguage] || infoData['WORKING']['en'];
            }
        }
            // Fonction pour mettre à jour l'introduction
            function updateCurrent() {
                    workingElement.textContent ="noob";
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
    
        // Trier les années dans l'ordre décroissant
        const sortedYears = Object.keys(positionsData).sort().reverse();
        let displayedCount = 0; // Compteur pour les cartes complètes
    
        // Fonction pour calculer la durée en mois, et en années si nécessaire
        function calculateDuration(beginningDate, endingDate) {
            const start = new Date(beginningDate);
            const end = new Date(endingDate);
    
            // Calcul de la différence en mois
            const monthsDifference = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
            
            // Si la différence de mois est négative (ce qui arrive si on compare une date de fin avant la date de début)
            const daysInStartMonth = new Date(start.getFullYear(), start.getMonth() + 1, 0).getDate();
            const extraMonths = end.getDate() >= start.getDate() ? 0 : -1;
            
            const totalMonths = monthsDifference + extraMonths;
    
            const years = Math.floor(totalMonths / 12); // Nombre d'années
            const months = totalMonths % 12; // Nombre de mois restants
    
            if (years > 0) {
                return `${years} an(s) ${months} mois`;
            } else {
                return `${months} mois`;
            }
        }
    
        // Parcourir les années triées
        sortedYears.forEach(year => {
            positionsData[year].slice().reverse().forEach(item => {
                const positionElement = document.createElement('div');
                positionElement.classList.add('position-card');
    
                // Format de la durée
                const beginningDate = item.beginning || 'N/A';
                const endingDate = item.ending || 'Present';
                let duration = '';
    
                if (displayedCount < 3) {
                    // Carte complète pour les 3 entreprises les plus récentes
                    const technologies = Object.values(item.techno[0]).flatMap(techArray =>
                        techArray.map(tech => `
                            <div class="container-tools">
                                <img src="./assets/picture/techno/${tech.logo}" alt="${tech.title}" class="card-img-tools">
                                <div class="tooltip-text">
                                    <div class="tooltip-title">${tech.title}</div>
                                    <div class="tooltip-description"></div>
                                </div>
                            </div>
                        `)
                    ).join('');
    
                    positionElement.innerHTML = `
                        <div class="card-content">
                            <div class="card-enterprise-asset row">
                                <div class="col-4 card-enterprise first">
                                    <img src="./assets/picture/ent/${item.enterpriseLogo}" alt="${item.enterprise}">
                                </div>
                                <div class="col-4">
                                    <div class="card-enterprise-name">${item.enterprise}</div>
                                    <div class="card-enterprise-filiale">${item.client}</div>
                                    <div class="card-enterprise-position">
                                        ${item.position[currentLanguage] || item.position['en']}
                                    </div>
                                    <div class="card-enterprise-duration">${duration}</div>
                                </div>
                                <div class="col-4 card-enterprise second">
                                    <img src="./assets/picture/ent/${item.clientLogo}" alt="${item.client}">
                                </div>
                                <div class="col-6">
                                    <div class="card-enterprise-grid">
                                        ${technologies}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card-enterprise-mission">
                                        ${item.description[currentLanguage] || item.description['en']}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    displayedCount++;
                } else {
                    // Carte simplifiée pour les autres entreprises sous forme de container individuel
                    duration = calculateDuration(beginningDate, endingDate); // Calculer la durée pour les anciennes positions
    
                    positionElement.classList.add('position-container');
    
                    positionElement.innerHTML = `
                        <div class="position-details">
                            <div class="position-info">
                                <p class="position-text">
                                    <strong>${item.position[currentLanguage] || item.position['en']}</strong> chez 
                                    <strong>${item.enterprise}</strong> pour <strong>${item.client}</strong><br>
                                    <strong>${duration}</strong>
                                </p>
                            </div>
                            <div class="logos">
                                <img src="./assets/picture/ent/${item.enterpriseLogo}" alt="${item.enterprise}" class="logo">
                                <img src="./assets/picture/ent/${item.clientLogo}" alt="${item.client}" class="logo">
                            </div>
                        </div>
                    `;
                }
    
                positionContainer.appendChild(positionElement);
            });
        });
    }
    
    
    
    
    
    
    

    // Fonction pour mettre à jour tout le contenu
    function updateContent(data) {
        const menuData = data.MENU[0];
        const infoData = data.INFO[0];
        const achievementsData = data.ACHIEVEMENTS[0];
        const positionsData = data.POSITIONS[0];
        
        updateMenu(menuData);
        updateIntro(infoData);
        updateWorking(infoData);
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

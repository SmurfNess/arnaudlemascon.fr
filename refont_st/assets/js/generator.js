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
    const currentElement = document.getElementById("CURRENT");
    const catElement = document.getElementById("CAT");
    const successElement = document.getElementById("SUCCESS");
    const historyElement = document.getElementById("HISTORY");
    const workElement = document.getElementById("WORK");
    const contactElement = document.getElementById("CONTACT");
    const certificatElement = document.getElementById("CERTIFICATE");
    const statusElement = document.getElementById("STATUS");
    const diplomasElement = document.getElementById("DIPLOMAS");
    const languagesElement = document.getElementById("LANGUAGES");
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

    // Fonction pour mettre à jour la section "WORKING"
    function updateWorking(infoData) {
        if (infoData['WORKING']) {
            workingElement.textContent =
                infoData['WORKING'][currentLanguage] || infoData['WORKING']['en'];
        }
    }

    // Fonction pour mettre à jour les titres de carte
    function updateCardTitle(infoData) {
        const keysToUpdate = {
            CAT: catElement,
            SUCCESS: successElement,
            HISTORY: historyElement,
            WORK: workElement,
            CONTACT: contactElement,
            STATUS: statusElement,
            CERTIFICATE: certificatElement,
            LANGUAGES: languagesElement,
            DIPLOMAS: diplomasElement,
        };

        Object.keys(keysToUpdate).forEach(key => {
            if (infoData[key]) {
                keysToUpdate[key].textContent =
                    infoData[key][currentLanguage] || infoData[key]['en'];
            }
        });
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
                        `./assets/pictures/gallery/${item.gallery}`
                    );

                    achievementElement.innerHTML = `
                        <img src="./assets/pictures/achievement/${item.icon}" alt="${item.alt}" class="card-img-achievement">
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

    // Fonction pour mettre à jour les positions
    function updatePositions(positionsData) {
        positionContainer.innerHTML = ''; // Réinitialiser le conteneur

        // Trier les années dans l'ordre décroissant
        const sortedYears = Object.keys(positionsData).sort().reverse();
        let displayedCount = 0; // Compteur pour les cartes complètes

        // Fonction pour calculer la durée
        function calculateDuration(beginningDate, endingDate = null) {
            const start = new Date(beginningDate);

            if (endingDate) {
                const end = new Date(endingDate);
                const monthsDifference = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
                const extraMonths = end.getDate() >= start.getDate() ? 0 : -1;
                const totalMonths = monthsDifference + extraMonths;
                const years = Math.floor(totalMonths / 12);
                const months = totalMonths % 12;

                return years > 0 ? `${years} an(s) ${months} mois` : `${months} mois`;
            } else {
                const now = new Date();
                const monthsDifference = (now.getFullYear() - start.getFullYear()) * 12 + (now.getMonth() - start.getMonth());
                return `${Math.ceil(monthsDifference / 12)}`;
            }
        }

        let mostRecentPosition = null;

        sortedYears.forEach(year => {
            positionsData[year].slice().reverse().forEach(item => {
                const positionElement = document.createElement('div');
                positionElement.classList.add('position-card');

                const beginningDate = item.beginning || 'N/A';
                const endingDate = item.ending || 'Present';
                const duration = calculateDuration(beginningDate, endingDate === 'Present' ? null : endingDate);

                if (!mostRecentPosition) {
                    mostRecentPosition = item;
                }

                if (displayedCount < 3) {
                    const technologies = Object.values(item.techno[0]).flatMap(techArray =>
                        techArray.map(tech => `
                            <div class="container-tools">
                                <img src="./assets/pictures/techno/${tech.logo}" alt="${tech.title}" class="card-img-tools">
                                <div class="tooltip-text">
                                    <div class="tooltip-title">${tech.title}</div>
                                </div>
                            </div>
                        `)
                    ).join('');

                    positionElement.innerHTML = `
                        <div class="card-content">
                            <div class="card-enterprise-asset row">
                                <div class="col-4 card-enterprise first">
                                    <img src="./assets/pictures/ent/${item.enterpriseLogo}" alt="${item.enterprise}">
                                </div>
                                <div class="col-4">
                                    <div class="card-enterprise-name">${item.enterprise}</div>
                                    <div class="card-enterprise-filiale">${item.client}</div>
                                    <div class="card-enterprise-position">
                                        ${item.position[currentLanguage] || item.position['en']}
                                    </div>
                                    <div class="card-enterprise-duration">${item.beginning} - ${item.ending}</div>
                                </div>
                                <div class="col-4 card-enterprise second">
                                    <img src="./assets/pictures/ent/${item.clientLogo}" alt="${item.client}">
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
                    positionContainer.appendChild(positionElement);
                    displayedCount++;
                }
            });
        });

        // Mise à jour des informations de la position actuelle
        if (mostRecentPosition) {
            currentElement.textContent =
                mostRecentPosition.position[currentLanguage] || mostRecentPosition.position['en'];
        }
    }

    // Charger et appliquer les données
    loadData()
        .then(data => {
            updateMenu(data.menu);
            updateIntro(data.info);
            updateWorking(data.info);
            updateCardTitle(data.info);
            updateAchievements(data.achievements);
            updatePositions(data.positions);
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour du contenu :', error);
        });
});

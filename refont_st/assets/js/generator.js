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
    const projectsElement = document.getElementById('PROJECTS');
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

    // Fonction pour mettre à jour l'introduction
    function updateIntro(infoData) {
        if (infoData['INTRO']) {
            introElement.textContent =
                infoData['INTRO'][currentLanguage] || infoData['INTRO']['en'];
        }
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
    function updateWorking(infoData) {
        if (infoData['WORKING']) {
            workingElement.textContent =
                infoData['WORKING'][currentLanguage] || infoData['WORKING']['en'];
        }
    }
    // Fonction pour mettre à jour l'introduction
    function updateCardTitle(infoData) {
        if (infoData['CAT']) {
            catElement.textContent =
                infoData['CAT'][currentLanguage] || infoData['CAT']['en'];
        }
        if (infoData['SUCCESS']) {
            successElement.textContent =
                infoData['SUCCESS'][currentLanguage] || infoData['SUCCESS']['en'];
        }
        if (infoData['HISTORY']) {
            historyElement.textContent =
                infoData['HISTORY'][currentLanguage] || infoData['HISTORY']['en'];
        }
        if (infoData['WORK']) {
            workElement.textContent =
                infoData['WORK'][currentLanguage] || infoData['WORK']['en'];
        }
        if (infoData['CONTACT']) {
            contactElement.textContent =
                infoData['CONTACT'][currentLanguage] || infoData['CONTACT']['en'];
        }
        if (infoData['STATUS']) {
            statusElement.textContent =
                infoData['STATUS'][currentLanguage] || infoData['STATUS']['en'];
        }
        if (infoData['CERTIFICATE']) {
            certificatElement.textContent =
                infoData['CERTIFICATE'][currentLanguage] || infoData['CERTIFICATE']['en'];
        }
        if (infoData['LANGUAGES']) {
            languagesElement.textContent =
                infoData['LANGUAGES'][currentLanguage] || infoData['LANGUAGES']['en'];
        }
        if (infoData['DIPLOMAS']) {
            diplomasElement.textContent =
                infoData['DIPLOMAS'][currentLanguage] || infoData['DIPLOMAS']['en'];
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

        // Fonction pour calculer la durée
        function calculateDuration(beginningDate, endingDate = null) {
            const start = new Date(beginningDate);

            if (endingDate) {
                // Si une date de fin est fournie, calcul standard
                const end = new Date(endingDate);

                const monthsDifference = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
                const daysInStartMonth = new Date(start.getFullYear(), start.getMonth() + 1, 0).getDate();
                const extraMonths = end.getDate() >= start.getDate() ? 0 : -1;

                const totalMonths = monthsDifference + extraMonths;

                const years = Math.floor(totalMonths / 12); // Nombre d'années complètes
                const months = totalMonths % 12; // Nombre de mois restants

                if (years > 0) {
                    return `${years} an(s) ${months} mois`;
                } else {
                    return `${months} mois`;
                }
            } else {
                // Si aucune date de fin n'est fournie, calcul avec années entamées
                const now = new Date();
                const monthsDifference = (now.getFullYear() - start.getFullYear()) * 12 + (now.getMonth() - start.getMonth());

                // Calcul des années entamées
                const yearsEntamees = Math.ceil(monthsDifference / 12);
                return `${yearsEntamees}`;
            }
        }

        // Variable pour stocker la position la plus récente
        let mostRecentPosition = null;

        // Parcourir les années triées
        sortedYears.forEach(year => {
            positionsData[year].slice().reverse().forEach(item => {
                const positionElement = document.createElement('div');
                positionElement.classList.add('position-card');

                // Format de la durée
                const beginningDate = item.beginning || 'N/A';
                const endingDate = item.ending || 'Present';
                let duration = calculateDuration(beginningDate, endingDate === 'Present' ? null : endingDate);

                // Vérifier si c'est la position la plus récente
                if (!mostRecentPosition) {
                    mostRecentPosition = item; // Stocker la position la plus récente
                }

                if (displayedCount < 3) {
                    // Carte complète pour les 3 entreprises les plus récentes
                    const technologies = Object.values(item.techno[0]).flatMap(techArray =>
                        techArray.map(tech => `
                            <div class="container-tools">
                                <img src="./assets/pictures/techno/${tech.logo}" alt="${tech.title}" class="card-img-tools">
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
                    displayedCount++;
                } else {
                    // Carte simplifiée pour les autres entreprises sous forme de container individuel
                    positionElement.classList.add('position-container');
                    positionElement.classList.add('card-entreprise-sup');
                    positionElement.innerHTML = `
                        <div class="position-details">
                            <div class="position-info">
                                <p class="position-text">
                                    <strong>${item.position[currentLanguage] || item.position['en']}</strong> ${item.text[0].boss[currentLanguage]}
                                    <strong>${item.enterprise}</strong> ${item.text[0].client[currentLanguage]} <strong>${item.client}</strong><br>
                                    <strong>${duration}</strong>
                                </p>
                            </div>
                            <div class="logos">
                                <img src="./assets/pictures/ent/${item.enterpriseLogo}" alt="${item.enterprise}" class="logo">
                                <img src="./assets/pictures/ent/${item.clientLogo}" alt="${item.client}" class="logo">
                            </div>
                        </div>
                    `;
                }

                positionContainer.appendChild(positionElement);
            });
        });

        // Calculer la durée pour la position la plus récente
        if (mostRecentPosition) {
            const currentDuration = calculateDuration(
                mostRecentPosition.beginning,
                mostRecentPosition.ending || null
            );
            const currentDiv = document.getElementById('CURRENT');
            currentDiv.innerHTML = `
                ${currentDuration}
            `;

            const currentEntDiv = document.getElementById('CURRENTENT');
            const CurrentEnt = mostRecentPosition.enterprise
            currentEntDiv.innerHTML = `
                ${CurrentEnt}
            `;
        }
    }

    function updateProjects(projectsData) {
        projectsElement.innerHTML = ''; // Réinitialiser le conteneur

        for (const key in projectsData) {
            console.log(key);
            if (projectsData.hasOwnProperty(key)) {
                projectsData[key].forEach(item => {
                    // Ajouter le contenu sans remplacer ce qui existe déjà
                    projectsElement.innerHTML += `
                        <div class="card-content">
                            <div class="row">
                                <div class="card-project-asset col-4">
                                    <a href="${item.link}" target="_blank">                                  
                                        <img src="./assets/pictures/project/${item.picture}" alt="${item.title}">
                                    </a>
                                </div>
                                <div class="col-4">
                                    <div class="card-project-name">${item.title}</div>
                                    <div class="container-techno">
                                        ${item.techno.map(tech => `<div class="techno-label" id="${tech.toUpperCase()}"> ${tech.toUpperCase()} </div>`).join('')}
                                    </div>
                                </div>
                                <div class="card-project-desc col-4">
                                    ${item.description[currentLanguage] || item.description['en']
                        }
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        }
    }

    // Fonction pour mettre à jour tout le contenu
    function updateContent(data) {
        const infoData = data.INFO[0];
        const achievementsData = data.ACHIEVEMENTS[0];
        const positionsData = data.POSITIONS[0];
        const projectsData = data.PROJECTS[0];
        const menuData = data.MENU[0];

        updateIntro(infoData);
        updateMenu(menuData);
        updateWorking(infoData);
        updateAchievements(achievementsData);
        updatePositions(positionsData);
        updateProjects(projectsData);
        updateCardTitle(infoData);
        console.log(data);
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

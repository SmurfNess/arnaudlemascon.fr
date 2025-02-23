document.addEventListener('DOMContentLoaded', function () {
    const menuElements = {
        AMBIANCE: document.getElementById('AMBIANCE'),
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
    const certifNumberElement = document.getElementById("CERTIFNUMBER");
    const certifListElement = document.getElementById("CERTIFLIST");
    const certifListElementMob = document.getElementById("CERTIFLISTMOB");
    const statusElement = document.getElementById("STATUS");
    const diplomasElement = document.getElementById("DIPLOMAS");
    const diplomasListElement = document.getElementById("DIPLOMASLIST");
    const diplomasListElementMob = document.getElementById("DIPLOMASLISTMOB");
    const languagesElement = document.getElementById("LANGUAGES");
    const profilePicture = document.querySelector('.img-profile-picture');
    const jsonUrl = 'https://arnaudlemascon.fr/assets/json/data.json';
    const footerElement = document.getElementById("FOOTER");

    let currentLanguage = 'en'; // Langue par défaut
    let originalProfilePictureSrc = profilePicture ? profilePicture.src : '';

// Appliquer le thème "steam" par défaut
document.documentElement.classList.add('steam');

// Fonction pour changer de thème
function changeTheme(theme) {
    document.documentElement.classList.remove('steam', 'outdoor', 'cosy');
    document.documentElement.classList.add(theme);
}

// Ajouter un event listener aux éléments du menu "AMBIANCE"
document.querySelectorAll('.theme-selector').forEach(item => {
    item.addEventListener('click', function (event) {
        event.preventDefault();
        changeTheme(this.getAttribute('data-theme'));
    });
});

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
            console.error('Image de profil introuvable ${item.title[currentLanguage]. Vérifiez la classe .img-profile-picture');
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
                    console.error('L\'achievement ${item.title[currentLanguage] ne contient pas d\'attribut data-image.');
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
                const endingDate = item.ending && item.ending !== 'Present' 
                ? item.ending 
                : currentLanguage === 'fr'
                    ? 'maintenant'
                    : currentLanguage === 'sp'
                        ? 'hoy'
                        : currentLanguage === 'en'
                            ? 'now'
                            : 'now'; // Valeur par défaut si `currentLanguage` n'est pas pris en charge
            
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
                                    <div class="tooltip-description">Description example</div>
                                </div>
                            </div>

                        `)
                    ).join('');

                    positionElement.innerHTML = `
                    <div class="card-content card-ent">
                        <div class="card-enterprise-asset row">
                            <div class="col-6 col-sm-4 card-enterprise first">
                                <img src="./assets/pictures/ent/${item.enterpriseLogo}" alt="${item.enterprise}">
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="card-enterprise-name">${item.enterprise}</div>
                                <div class="card-enterprise-filiale">${item.client}</div>
                                <div class="card-enterprise-position">
                                    ${item.position[currentLanguage] || item.position['en']}
                                </div>
                                <div class="card-enterprise-duration">${item.beginning} - ${endingDate}</div>
                            </div>
                            <div class="col-0 col-sm-4 card-enterprise second">
                                <img src="./assets/pictures/ent/${item.clientLogo}" alt="${item.client}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="card-enterprise-grid">
                                    ${technologies}
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
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
                                <img src="./assets/pictures/ent/${item.clientLogo}" alt="${item.client}" class="logo nomob">
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
            currentElement.innerHTML = `
                ${currentDuration}
            `;

            const currentEntDiv = document.getElementById('CURRENTENT');
            const CurrentEnt = mostRecentPosition.enterprise
            currentEntDiv.innerHTML = `
            ${mostRecentPosition.text[0].current[currentLanguage]} ${CurrentEnt}
        `;
        
        }
    }

    function updateProjects(projectsData) {
        projectsElement.innerHTML = ''; // Réinitialiser le conteneur

        for (const key in projectsData) {
            if (projectsData.hasOwnProperty(key)) {
                projectsData[key].forEach(item => {
                    // Ajouter le contenu sans remplacer ce qui existe déjà
                    projectsElement.innerHTML += `
                        <div class="card-content">
                            <div class="row">
                                <div class="card-project-asset col-12">
                                    <a href="${item.link}" target="_blank">                                  
                                        <img src="./assets/pictures/project/${item.picture}" alt="${item.title}">
                                    </a>
                                    <div class="tooltip-text project">
                                        <div class="tooltip-title">${item.title}</div>
                                        <div class="tooltip-description">${item.description[currentLanguage] || item.description['en']}</div>

                                        <div class="container-techno">
                                            ${item.techno.map(tech => `<div class="techno-label" id="${tech.toUpperCase()}"> ${tech.toUpperCase()} </div>`).join('')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        }
    }

    function updateCertif(certifData) {
        const elements = [certifListElement, certifListElementMob];
        
        elements.forEach(element => {
            element.innerHTML = ''; // Réinitialiser le conteneur
        });
        
        for (const key in certifData) {
            if (certifData.hasOwnProperty(key)) {
                certifData[key].forEach(item => {
                    const certifHTML = `
                        <div class="container-certif ${item.status}">
                            <img src="./assets/pictures/cert/${item.picture}" alt="${item.name}"
                                class="card-img-skill-cert ${item.status}">
                            <div class="tooltip-text">
                                <div class="tooltip-title">${item.name}</div>
                                <div class="tooltip-description">${item.description}</div>
                                <div class="tooltip-grade">${item.grade}</div>
                                <div class="tooltip-year">${item.year}</div>
                            </div>
                        </div>
                    `;
                    
                    elements.forEach(element => {
                        element.innerHTML += certifHTML; // Ajouter le contenu
                    });
                });
            }
        }
    }
    
    function updateDiplomas(diplomasData) {
        diplomasListElement.innerHTML = ''; // Réinitialiser le conteneur

        for (const key in diplomasData) {
            if (diplomasData.hasOwnProperty(key)) {
                diplomasData[key].forEach(item => {
                    // Ajouter le contenu sans remplacer ce qui existe déjà
                    diplomasListElement.innerHTML += `
                            <div class="container-diplomas">
                                <img src="./assets/pictures/cert/diplomas.png" alt="diplomas" class="card-img-skill-cert">
                                <div class="card-diploma-title"> ${item.name} - ${item.year}</div>
                                <div class="tooltip-text">
                                    <div class="tooltip-title">${item.description[currentLanguage] || item.description['en']}</div>
                                    <div class="tooltip-scholl">${item.school}</div>
                                </div>
                            </div>
                        `;
                });
            }
        }
    }

    function updateDiplomasMob(diplomasData) {
        diplomasListElementMob.innerHTML = ''; // Réinitialiser le conteneur

        for (const key in diplomasData) {
            if (diplomasData.hasOwnProperty(key)) {
                diplomasData[key].forEach(item => {
                    // Ajouter le contenu sans remplacer ce qui existe déjà
                    diplomasListElementMob.innerHTML += `
                            <div class="container-diplomasMob">
                                <img src="./assets/pictures/cert/diplomas.png" alt="diplomas" class="card-img-skill-cert">
                                <div class="card-diploma-title"> ${item.name}</div>
                                <div class="tooltip-text">
                                    <div class="tooltip-title">${item.description[currentLanguage] || item.description['en']}</div>
                                    <div class="tooltip-scholl">${item.school}</div>
                                </div>
                            </div>
                        `;
                });
            }
        }
    }

    function updateFooter(infoData) {
        if (!footerElement) {
            console.error("L'élément du footer n'est pas défini !");
            return;
        }
    
        // Vérifier si "FOOTER" existe dans infoData
        if (infoData && infoData['FOOTER']) {
            let footerText = infoData['FOOTER'][currentLanguage] || infoData['FOOTER']['en'] || "Texte non disponible";
            footerElement.innerHTML = footerText; // innerHTML pour bien gérer <br>
        } else {
            console.warn("FOOTER non trouvé dans infoData.");
        }
    }  
    
    // Fonction pour mettre à jour tout le contenu
    function updateContent(data) {
        const infoData = data.INFO[0];
        const achievementsData = data.ACHIEVEMENTS[0];
        const positionsData = data.POSITIONS[0];
        const projectsData = data.PROJECTS[0];
        const menuData = data.MENU[0];
        const certifData = data.CERTIF[0];
        const diplomasData = data.DIPLOMAS[0];
        const footerData = data.INFO[0];
        
        updateIntro(infoData);
        updateMenu(menuData);
        updateWorking(infoData);
        updateAchievements(achievementsData);
        updatePositions(positionsData);
        updateProjects(projectsData);
        updateDiplomas(diplomasData);
        updateDiplomasMob(diplomasData);
        updateCertif(certifData);
        updateCardTitle(infoData);
        updateFooter(footerData);
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

        function updateBackground(style) {
            const container = document.querySelector(".container-background-origin");
            container.innerHTML = ""; // Vider le conteneur
        
            let htmlContent = "";
        
            if (style === "steam" || style === "par défaut") {
                htmlContent = `
                    <div class="background animated-background">
                        ${'<div class="particle"></div>'.repeat(20)}
                        <div class="end"></div>
                    </div>
                `;
            } else if (style === "cosy") {
                htmlContent = `
   <div class="parallax-container">
                    <div class="layer layer5" data-speed="0.5">
<svg version="1.1" viewBox="0 0 1890 1417" width="1890" height="1417" xmlns="http://www.w3.org/2000/svg">
<path transform="translate(733,335)" d="m1 115h171v26l108 1v285 1h-279z" fill="#BAE3FC"/>
<path transform="translate(153,270)" d="m0 44h219v315l-2 2-5 28-4 11-6 10-5 5h-2l-3 12-1 1h-191z" fill="#BAE3FC"/>
<path transform="translate(1636,391)" d="m0 0h160v276l-8 6-11 4-6 1h-8l-12-3-9-5-6-5-4-5v-2h-25l-1 22-1 6h-69l-1-34-2 6h-120v-241h123z" fill="#BAE3FC"/>
<path transform="translate(1334,438)" d="m0 0h137v198l-2 8-3 14-5 9-5 6-2 6h-120z" fill="#BAE3FC"/>
            </svg>
        </div>
              <div class="layer layer4" data-speed="1">
<svg version="1.1" viewBox="0 0 1890 1417" width="1890" height="1417" xmlns="http://www.w3.org/2000/svg" style="transform:scale(1.1);">
<path d="m37 135 20-8 43-18 36-15 26-11 2 1v538h-66l-4-14 1 3 3 11h-92v-472l28-13h0v-1l2-92z" fill="#7CBDE6"/>
<path d="m289 117 6 6 17 17 7 7 11 11 7 7 16 16v3h6v392l-6 3h-2v2h-17-3l-2 5h-54l1-4-33-1-3-5-2-8-3-36-1 4 3 44v6h-27v-402h11l0-3z" fill="#7CBDE6"/>
<path transform="translate(1311,252)" d="m0 0h34l20 3 15 4 13 1v4l9 3 19 10 4 3 9 1 1 6 9 7 10 9 8 8 2 3 8 1 1 1 1 10 11 18 4 7v2h8l1 20 5 18v4h10v28h-6v16h6v28h-10l-2 11-7 21-9 19-9 14-10 13-9 10-11 11-17 13-13 8v1l7 1 1 17 8 17v10l-5 5-5 1-7-3-5-8-5-11-6-1-1-14-2-5h-3v25h-24v-17l-14 3h-3v22h-24v-20h-2v19l-4 1-1-1-1-19-25-3-5-1-1 24h-24v-27l10-1-14-5h-3l-3 6-1 9-4 1-14 29-4 6-3 2h-9l-5-6v-8l13-28 1-2 1-21 5-1-13-9-1 14-1 1h-23v-28h-24v-28l8-1-6-9-21-1v-28h7l-4-10-1-4-14-1v-27l1-1h7v-7h-7l-1-1v-26l1-1h7v-9l1-6h-2v-27l1-1h9l2-9 4-9 1-24 13-1 12-16 5-6 1-7 7-1 10-9 15-11 1-6 10-1 17-8 2-6 17-1 11-3h4v-2zm15 9v128l11 1 9-21 32-80v-1h-9v-22l-10-1v15l-23 1v-19l-1-1zm-11 0v20h-24v-17l-9 3v21h-10l3 10 11 31 18 49 5 13 2 1 10-3v-127l-1-1zm78 14v13h-10l-36 90-5 12v3l7 5 4-1 35-35 7-8 46-46-1-4-5-4h-1v9h-24v-26l-15-8zm-137 1-2 1v25h-24v-10l-4 2-9 7 3 2 1 20 76 76 4-1 4-3-3-10-18-49-17-46-1-2h-7v-12zm189 34-19 19-7 8-54 54-7 8-3 3 2 5 3 2 41-18 60-26 12-6v-6h4l-2-6-3-4h-23l1-29zm-249 12-6 7v30h-17l-4 7v3l24 9 59 21 36 13 3-1 3-5-1-4-25-25-7-8-39-39h-25v-8zm274 44-39 17-69 30-1 2 2 11h133v-3h-7v-28l4-1-4-14h-16v-14zm-297 10v26h-14l-2 15h9v9h121l1-9-30-11-42-15-41-15zm-7 55v14h-9l1 7h8v28h-2l3 11 2 1 39-17 46-20 30-13 5-2-1-8-1-1zm197 0-1 7 12 5 59 21 50 18h3v-2l4-1 3-12h-4v-28h7l1-7-1-1zm-4 12-5 8 1 5 7 8 26 26 7 8 52 52 7-6 10-12 12-19 9-19 1-6-44-16-56-20-25-9zm-71 2-43 19-42 18-25 11-3 2 2 1 1 18 9 15 4 5h6v7l6 7 7-6 51-51 7-8 29-29-2-5-3-4zm12 12-9 10-53 53-7 8-23 23 2 4 8 7h3v3l13 9 9 5 16-32 11-23 20-41 11-23zm26 8v136h13l22-3 8-2v-2h7l1-3-21-58-15-41-10-27zm-6 3-10 19-11 23-16 33-15 32-5 12v2l21 7 20 4 17 1v-133zm19 4 1 2zm1 2 1 6 17 46 23 63 1 2 6-1 4-2-2-6-19-42-16-35-14-31zm24 0 3 9 14 30 15 33 14 31h4l11-7 14-11 5-6-79-79zm-167 79v9h9l-6-7z" fill="#7CBDE6"/>
            </svg>
        </div>
              <div class="layer layer6" data-speed="1.5">
<svg version="1.1" viewBox="0 0 1890 1417" width="1890" height="1417" xmlns="http://www.w3.org/2000/svg" style="transform:scale(1.3);">
                <path transform="translate(646,283)" d="m0 0h22l18 3 16 5 16 7 16 10 13 12 10 10 9 13 7 12 6 15 5 19 1 9h6v20l-1 2-1 212 39-1 7-6 11-14 9-11 13-16 11-14 14-17 11-13 8-10 26-32 7-9 1-3h6l3 7 17 17 11 9 13 10 21 13 16 8 19 6 8 2h50l20-8 21-11 19-12 10-9 11-10 8-7 11-10 8-7 2-3h5l3 7 12 14 11 13 12 14 9 11 9 10 9 11 13 15 9 11 13 15 12 14 9 10 5 3h59l-1-238-4-6-2-7v-7l4-4h30l1-56h-12l-2-3 1-1 12-1 1-7h3v8h15l-1 4h-14v57h34l4 3v7l-4 15v80l22-13 12-8-3-11 2-5h6v-7l4-1 3 2 2 6 4 2-1 8-1 6 22 12 12 6 5 3-1-80-7-13-1-11 4-3 35-1 1-57h-10v-4h10v-6l3-1v7h12v4h-12l1 4v50l-1 4 9-1h25l4 3-1 9-6 14v238l112 1 13 2 16 5 11 6 11 8 9 8 9 12 8 16 4 13 1 9h-1752l2-12 5-15 6-11 8-10 11-11 15-10 15-6 12-3 8-1h62l39 1v-216h63v69h5v-12l4-3 5 1 2 3v11h9v-11l3-4h6l3 4v11h9v-11l2-3h7l2 3v11h8v-11l3-3 6 1 2 3v10h6v-12l3-3h5l3 3v12h6v-68l1-1h62v216h44l-1-209-1-3v-18l3-3h5v-8l5-19 6-15 9-16 11-14 13-13 14-10 14-8 15-6 16-4zm768 111v82h14v-82zm18 0v82h14v-82zm18 0v82h14v-82zm120 0-2 1v81h14v-82zm18 0v82h14v-82zm20 0v82h14v-82zm-1070 29v59h36v-59zm49 0v59h36v-59zm50 0v59h36v-59zm52 0v59h36v-59zm47 0v59h36v-59zm778 63-10 3-9 6-6 9-3 10 1 11 4 9 8 9 8 4 8 2 10-1 9-4 7-6 5-7 3-11-1-11-5-10-4-5-10-6-8-2zm-300 5m4 0-10 13-5 7v56l9 2h4l1-11 3-6v-61zm7 0v64h2l1 26 7 2 35 9 27 10 18 7 12 5 6 4 2-1-12-13-9-11-9-10-9-11-13-15-11-13-12-14-9-11-13-15-11-13zm-12 1-10 9v6l4-4 7-9zm-268 1-11 13-11 14-9 11-11 13-8 10-13 16-11 14-11 13-11 14-6 8 5-2 11-7 23-11 19-7 33-8 9-2 1-30 2-2v-57zm7 2v55l3 2 1 19h4v-64l-7-12zm-414 4v59h36v-59zm49 0v59h36v-59zm50 0v59h36v-59zm52 0-1 59h37v-59zm47 0v59h36v-59zm462 6-14 13-6 5-1 16 2 1 8-11 10-13 3-4v-7zm-235 1 1 5 16 27 3 3v-19l-13-11-6-5zm0 8v56l14-2 6-2v-20l-16-27zm236 2-21 27v21l14 2h8v-50zm-214 9v22l10 17h9l2-1v-24l-11-8-8-6zm189 2-10 7-15 9-1 1v17l4 1h9l10-12 4-5v-18zm-166 14v22l14-1 3-1v-12l-15-8zm135 5-17 9 1 5 18 1v-15zm31 3-10 12 1 2 10 1v-15zm-147 2v10h12l2-1-1-5-11-4zm-42 1v14l8-1-6-12zm137 4-4 3 5 1 1-4zm-79 1v3h9l-2-2zm74 5v1h6v-1zm10 0 4 1zm5 1v1h12v-1zm37 3v1h6v-1zm9 0v2l14 2h8l-3-2zm-64 3v1h9v-1zm13 0v1l17 1-1-2zm-589 1v58l1 1h35v-59zm49 0v59h35l1-1v-58zm50 0v58l1 1h35v-59zm51 0v58l1 1h35l1-1v-58zm48 0v59h36v-59zm467 0v1l5 1h7v-1l-5-1zm-124 15-52 2-45 4-21 3-3 1v35h258v-35l-15-3-38-4-28-2-34-1zm184 30-12 5-9 7-1 3 78 1v-2l-24-11-12-3zm-364 4-21 2-12 3-9 5 1 2 75-1-5-5-10-5-6-1z" fill="#5198C5"/>
<path transform="translate(1216,489)" d="m0 0h1v5l-12 15-1 2-1 56 9 1 1 3h-18l2-2-18-2v-1l13-1 8 1-1-49-12 16-8 10-1 21h-3l-1-14-8 10-2-1 2-4 8-10v-17l-10 7-15 9-1 17h-3v-15l-17 8v-3l23-12 19-12 10-9 11-10 8-7 11-10zm-2 2m-1 1-10 9v6l4-4 7-9zm-15 13-14 13-6 5-1 16 2 1 8-11 10-13 3-4v-7z" fill="#A8CCE2"/>
<path transform="translate(959,501)" d="m0 0 5 5 1 5 16 27 1 2v-18l-11-9 1-3 11 9 13 10 9 6-1 3-15-10-4-3 1 22 8 14-3 1-5-9-1 13h-1l-1-15-2-6-9-16-8-13-1 54-3 1-1-64-1-4z" fill="#E7F1F7"/>
<path transform="translate(943,491)" d="m0 0 2 3-9 11-13 16-8 10-11 13-8 10-13 16-11 14-11 13-11 14-6 8-3 3-2-2 11-14 14-17 8-10 13-16 8-10 11-13 8-10 26-32z" fill="#9CC5DE"/>
<path transform="translate(1229,493)" d="m0 0 4 1 13 15 9 11 13 15 12 14 11 13 12 14 9 11 13 15 10 12-3 1-10-11-9-11-9-10-9-11-13-15-11-13-12-14-9-11-13-15-8-10z" fill="#AECFE4"/>
<path transform="translate(971,510)" d="m0 0 5 3 13 11 16 11-1 3-15-10-4-3 1 22 8 14-3 1-5-9-1 13h-1l-1-15-2-6-1-4h3l-1-19-11-9z" fill="#A5CAE1"/>
<path transform="translate(1202,511)" d="m0 0h1v56l9 1 1 3h-18l2-2-18-2v-1l13-1 8 1-1-51h2z" fill="#E1EEF5"/>
<path transform="translate(886,561)" d="m0 0 2 3-9 11-13 16-11 14-11 13-1 3-3 1-1-2 11-14 14-17 8-10 13-16z" fill="#ABCEE3"/>
<path transform="translate(1298,573)" d="m0 0 4 2 12 14 11 13 10 12-3 1-10-11-9-11-9-10-6-8z" fill="#9FC6DF"/>
<path transform="translate(1229,493)" d="m0 0 4 1 13 15 9 11 10 12-2 2-10-11-9-11-13-15z" fill="#A4CAE1"/>
<path transform="translate(1216,489)" d="m0 0h1v5l-12 15-3-1-1-5v8h-1v-6l-3 1v-3l13-12zm-2 2m-1 1-10 9v6l4-4 7-9z" fill="#C7DEEC"/>
<path transform="translate(971,510)" d="m0 0 5 3 13 11 16 11-1 3-15-10-2-2-4-1-1-3-11-9z" fill="#8CBCD9"/>
<path transform="translate(943,491)" d="m0 0 2 3-9 11-13 16-5 7-2-3 9-11 13-16z" fill="#A8CCE2"/>
<path transform="translate(1147,541)" d="m0 0h2l-1 20h-3v-15l-17 8v-3z" fill="#B8D5E7"/>
<path transform="translate(859,595)" d="m0 0h2l-2 5-14 17-2 4-3 1-1-2 11-14z" fill="#A5CAE1"/>
<path transform="translate(1201,501)" d="m0 0h1v7h2v3h-2v55h-1v-51l-4 1 3-5v-9z" fill="#5098C5"/>
<path transform="translate(1171,526)" d="m0 0h3l-2 4-22 13-1-3 20-12z" fill="#A0C7DF"/>
<path transform="translate(1316,594)" d="m0 0 4 2 9 11 6 7-3 1-10-11-6-8z" fill="#9CC5DE"/>
<path transform="translate(1031,550)" d="m0 0 11 3 10 3v3l-9-2-1 3-2-4-11-4z" fill="#A4CAE1"/>
<path transform="translate(1282,554)" d="m0 0 4 2 9 11 4 5-2 2-10-11-5-6z" fill="#A7CBE2"/>
<path transform="translate(1216,489)" d="m0 0h1v5l-12 15-1-4 10-13v-2z" fill="#ADCFE4"/>
<path transform="translate(1195,505)" d="m0 0h2l-2 4-11 10-4 2v-3l11-10z" fill="#9BC4DE"/>
<path transform="translate(886,561)" d="m0 0 2 3-9 11-2 3h-3l2-4z" fill="#A8CCE2"/>
<path transform="translate(915,526)" d="m0 0h2l-1 4-11 13-2-2 9-11z" fill="#9EC6DF"/>
<path transform="translate(1178,520)" d="m0 0h2l-1 3-1 15h-1l-1-12v17h-1l-1-16v-3z" fill="#D8E9F2"/>
<path transform="translate(1229,493)" d="m0 0 4 1 9 11-2 2-10-11z" fill="#A3C9E0"/>
<path transform="translate(959,501)" d="m0 0 5 5 1 5 2 4-6-3-3-9z" fill="#90BEDA"/>
<path transform="translate(1010,539)" d="m0 0 5 2 9 5-1 3-15-8z" fill="#A7CBE2"/>
<path transform="translate(1196,516)" d="m0 0 3 1-10 13-2-2z" fill="#ABCDE3"/>
<path transform="translate(992,527)" d="m0 0 9 5 4 3-1 3-14-9z" fill="#A0C7DF"/>
<path transform="translate(925,514)" d="m0 0 3 1-10 13-2-3z" fill="#AFD0E4"/>
<path transform="translate(859,595)" d="m0 0h2l-2 5-6 7-2-2z" fill="#A1C8E0"/>
<path transform="translate(973,529)" d="m0 0 4 2 5 10h-3l-6-10z" fill="#99C3DD"/>
<path transform="translate(1201,501)" d="m0 0h1v7h2v3h-2v5h-5l3-5v-9z" fill="#5FA1CA"/>
<path transform="translate(902,542)" d="m0 0h2l-2 5-5 6-2-1 2-4z" fill="#95C1DC"/>
<path transform="translate(1170,550)" d="m0 0 3 1-7 9-2-1 2-4z" fill="#A4C9E1"/>
<path transform="translate(1031,550)" d="m0 0 11 3-2 3-11-4z" fill="#B0D0E5"/>
<path transform="translate(1157,535)" d="m0 0h2v3l-10 5v-3z" fill="#A7CBE2"/>
<path transform="translate(971,510)" d="m0 0 5 3 5 5-3 1-7-6z" fill="#A0C7DF"/>
<path transform="translate(1212,490)" d="m0 0 2 2-8 7-2-2z" fill="#A0C7DF"/>
<path transform="translate(1186,565)" d="m0 0h6l2 1v2l-15-1v-1z" fill="#BBD7E8"/>

            </svg>
        </div>
        <div class="layer layer1" data-speed="2">
<svg version="1.1" viewBox="0 0 3000 2000" width="3000" height="2500" xmlns="http://www.w3.org/2000/svg" style="top:0px;">
                <path d="M1-105H318V951H2627V162H318V0-108H3000V1374H0" fill="lavenderblush" />
            </svg>
        </div>
        <div class="layer layer2" data-speed="5">
<svg version="1.1" viewBox="0 0 3000 2000" width="3000" height="2000" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 2000H3000V1067H0" fill="#CE6A6B" />
            </svg>
        </div>
        <div class="layer layer3" data-speed="5">
<svg version="1.1" viewBox="0 0 1890 1417" width="1890" height="1417" xmlns="http://www.w3.org/2000/svg">>
                <path d="M166 763C130 784 119 850 171 889L275 891C336 842 304 787 281 767 266 725 284 667 262 641 288 607 269 565 264 541 249 542 242 552 241 561 228 561.3333 215 561.6667 196 560 196.3333 554 190.6667 546 176 539 168 567 156 612 180 641 160 659 175 744 166 763" fill="black" />
            </svg>
        </div>
    </div>
                `;
            }
        
            container.innerHTML = htmlContent;
        }
      
        
        // Ajouter un événement au menu déroulant pour mettre à jour le fond
        document.querySelectorAll('.theme-selector').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault(); // Empêcher le comportement par défaut du lien
                const selectedTheme = e.target.getAttribute('data-theme');
                updateBackground(selectedTheme);
            });
        });
        
        // Exemple d'utilisation :
        updateBackground("steam"); // ou "cosy" selon ton choix
        

        function updateTransform() {
            document.querySelectorAll(".layer").forEach(layer => {
                layer.style.width = "200%";
                layer.style.height = "200%";
            });
        }
        
        window.addEventListener("resize", updateTransform);
        updateTransform();
        
        document.addEventListener("mousemove", (e) => {
            let x = (window.innerWidth / 2 - e.clientX) / 50;
            let y = (window.innerHeight / 2 - e.clientY) / 50;

            // Limiter la vitesse verticale du SVG blanc (layer1)
            const maxSpeedY = 0.5; // Limite la vitesse verticale maximale
            y = Math.max(Math.min(y, maxSpeedY), -maxSpeedY); // Contrainte entre -maxSpeedY et maxSpeedY
            
            document.querySelectorAll(".layer").forEach(layer => {
                let speed = layer.getAttribute("data-speed");
                
                if (layer.classList.contains('layer1')) {
                    // Applique la contrainte de vitesse verticale uniquement à la layer1
                    layer.style.transform = `translate(-50%, -50%) translateX(${x * speed}px) translateY(${y * speed}px)`;
                } else {
                    // Pour les autres layers, pas de limitation verticale
                    layer.style.transform = `translate(-50%, -50%) translateX(${x * speed}px) translateY(${y * speed}px)`;
                }
            });
        });
        
});

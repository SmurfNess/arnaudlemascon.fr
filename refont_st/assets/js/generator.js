document.addEventListener('DOMContentLoaded', function () {
    const achievementContainer = document.getElementById('ACHIEVEMENT');
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    // Fonction pour charger les données JSON et générer les éléments d'achievement
    function loadAchievements(language = 'en') {
        fetch(jsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des données JSON.');
                }
                return response.json();
            })
            .then(data => {
                const achievements = data.ACHIEVEMENTS[0]; // Accéder à l'objet ACHIEVEMENTS
                
                // Vider le conteneur avant de générer les nouveaux éléments
                achievementContainer.innerHTML = '';

                // Parcourir chaque type d'achievement (mariage, colombie, etc.)
                for (const key in achievements) {
                    if (achievements.hasOwnProperty(key)) {
                        achievements[key].forEach(item => {
                            // Générer les éléments HTML pour chaque achievement
                            const achievementElement = document.createElement('div');
                            achievementElement.classList.add('container-achievement');
                            achievementElement.setAttribute('data-image', `./assets/picture/gallery/${item.gallery}`);

                            achievementElement.innerHTML = `
                                <img src="./assets/picture/achievement/${item.icon}" alt="${item.alt}" class="card-img-achievement">
                                <div class="tooltip-text">
                                    <div class="tooltip-title">${item.title[language] || item.title['en']}</div>
                                    <div class="tooltip-description">${item.description[language] || item.description['en']}</div>
                                </div>
                            `;

                            // Ajouter l'élément au conteneur
                            achievementContainer.appendChild(achievementElement);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                achievementContainer.textContent = 'Erreur lors du chargement des réalisations.';
            });
    }

    // Charger les achievements en anglais par défaut
    loadAchievements('en');

    // Gestion du changement de langue via les boutons radio
    const languageRadios = document.querySelectorAll('input[name="language"]');
    languageRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            let selectedLanguage;
            switch (this.value) {
                case 'fr':
                    selectedLanguage = 'fr';
                    break;
                case 'es':
                    selectedLanguage = 'sp';
                    break;
                case 'gb':
                    selectedLanguage = 'en';
                    break;
                default:
                    selectedLanguage = 'en';
            }
            loadAchievements(selectedLanguage); // Recharger les achievements dans la langue sélectionnée
        });
    });
});

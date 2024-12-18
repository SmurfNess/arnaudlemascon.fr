document.addEventListener('DOMContentLoaded', function () {
    // Récupérer les éléments par ID
    const valuesElement = document.getElementById('VALUES');
    const storyElement = document.getElementById('STORY');
    const cvElement = document.getElementById('CV');
    const introElement = document.getElementById('INTRO');

    
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    // Fonction pour charger les données JSON
    function loadMenuData(language = 'en') {
        fetch(jsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des données JSON.');
                }
                return response.json();
            })
            .then(data => {
                const menuItems = data.MENU[0]; // Accéder à l'objet MENU

                // Mettre à jour les éléments HTML avec les bonnes valeurs dans la langue choisie
                valuesElement.textContent = menuItems['VALUES'][language] || menuItems['VALUES']['en'];
                storyElement.textContent = menuItems['STORY'][language] || menuItems['STORY']['en'];
                cvElement.textContent = menuItems['CV'][language] || menuItems['CV']['en'];

                const infoItems = data.INFO[0]; // Accéder à l'objet INFO
                introElement.textContent = infoItems['INTRO'][language] || menuItems['INTRO']['en'];
            })
            .catch(error => {
                console.error('Erreur:', error);
                valuesElement.textContent = 'Erreur lors du chargement des valeurs.';
                storyElement.textContent = 'Erreur lors du chargement de l\'histoire.';
                cvElement.textContent = 'Erreur lors du chargement du CV.';
            });
    }

    // Charger les données en anglais par défaut
    loadMenuData('en');

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
            loadMenuData(selectedLanguage); // Charger le contenu dans la langue sélectionnée
        });
    });
});

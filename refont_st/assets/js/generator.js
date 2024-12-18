document.addEventListener('DOMContentLoaded', function () {
    // Récupérer les éléments par ID
    const valuesElement = document.getElementById('VALUES');
    const storyElement = document.getElementById('STORY');
    const cvElement = document.getElementById('CV');
    
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    // Fonction pour charger les données JSON
    fetch(jsonUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors du chargement des données JSON.');
            }
            return response.json();
        })
        .then(data => {
            const menuItems = data.MENU[0]; // Accéder à l'objet MENU

            // Détecter la langue de l'utilisateur (par défaut "fr")
            const userLanguage = navigator.language.substring(0, 2) || 'fr';

            // Fonction pour afficher le texte dans la bonne langue
            const getText = (key) => {
                return menuItems[key] && menuItems[key][userLanguage] ? menuItems[key][userLanguage] : menuItems[key]['fr'];
            };

            // Remplir les éléments HTML avec les bonnes valeurs
            valuesElement.textContent = getText('VALUES');
            storyElement.textContent = getText('STORY');
            cvElement.textContent = getText('CV');
        })
        .catch(error => {
            console.error('Erreur:', error);
            valuesElement.textContent = 'Erreur lors du chargement des valeurs.';
            storyElement.textContent = 'Erreur lors du chargement de l\'histoire.';
            cvElement.textContent = 'Erreur lors du chargement du CV.';
        });

    // Gestion du changement de langue via les boutons radio
    const languageRadios = document.querySelectorAll('input[name="language"]');
    languageRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            const selectedLanguage = this.value;

            // Mettre à jour les éléments avec la langue choisie
            fetch(jsonUrl)
                .then(response => response.json())
                .then(data => {
                    const menuItems = data.MENU[0];
                    valuesElement.textContent = menuItems.VALUES[selectedLanguage] || menuItems.VALUES['fr'];
                    storyElement.textContent = menuItems.STORY[selectedLanguage] || menuItems.STORY['fr'];
                    cvElement.textContent = menuItems.CV[selectedLanguage] || menuItems.CV['fr'];
                })
                .catch(error => {
                    console.error('Erreur lors du changement de langue:', error);
                    valuesElement.textContent = 'Erreur lors du chargement des valeurs.';
                    storyElement.textContent = 'Erreur lors du chargement de l\'histoire.';
                    cvElement.textContent = 'Erreur lors du chargement du CV.';
                });
        });
    });
});

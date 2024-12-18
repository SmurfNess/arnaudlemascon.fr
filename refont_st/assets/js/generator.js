document.addEventListener('DOMContentLoaded', function () {
    // Récupérer les éléments par ID
    const valuesElement = document.getElementById('VALUES');
    const storyElement = document.getElementById('STORY');
    const cvElement = document.getElementById('CV');
    
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    // Fonction pour charger les données JSON
    function loadMenuData(language) {
        fetch(jsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des données JSON.');
                }
                return response.json();
            })
            .then(data => {
                const menuItems = data.MENU[0]; // Accéder à l'objet MENU

                // Fonction pour obtenir le texte dans la langue demandée
                const getText = (key) => {
                    return menuItems[key] && menuItems[key][language] ? menuItems[key][language] : menuItems[key]['fr'];
                };

                // Mettre à jour les éléments HTML avec les bonnes valeurs
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
    }

    // Charger les données en français par défaut
    loadMenuData('fr');

    // Gestion du changement de langue via les boutons radio
    const languageRadios = document.querySelectorAll('input[name="language"]');
    languageRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            const selectedLanguage = this.value; // La langue sélectionnée
            loadMenuData(selectedLanguage); // Charger le contenu dans la langue sélectionnée
        });
    });
});

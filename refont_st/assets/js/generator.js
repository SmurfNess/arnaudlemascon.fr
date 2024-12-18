document.addEventListener('DOMContentLoaded', function () {
    const menuContainer = document.getElementById('menu-container');
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    // Détection de la langue (par défaut : "fr")
    const userLanguage = navigator.language.substring(0, 2) || 'fr';

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
            const lang = menuItems.VALUES[userLanguage] ? userLanguage : 'fr'; // Vérifie si la langue existe

            // Création des éléments de menu
            const menuHtml = `
                <ul>
                    <li>${menuItems.VALUES[lang]}</li>
                    <li>${menuItems.STORY[lang]}</li>
                    <li>${menuItems.CV[lang]}</li>
                </ul>
            `;

            // Ajout des éléments de menu au conteneur
            menuContainer.innerHTML = menuHtml;
        })
        .catch(error => {
            console.error('Erreur:', error);
            menuContainer.innerHTML = '<p>Erreur lors du chargement du menu.</p>';
        });
});

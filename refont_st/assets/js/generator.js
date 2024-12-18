let data = {};
let currentLanguage = 'en';

// Fetch JSON data from the server
async function fetchData() {
    try {
        const response = await fetch('https://arnaudlemascon.fr/refont_st/assets/json/data.json');
        const jsonData = await response.json();
        console.log('JSON fetched:'/*, JSON.stringify(jsonData, null, 2)*/);
        
        data = jsonData;
        generateContent(); // Call the function to generate content based on the data
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

function generateContent() {
    generateMenu();
    generateAchievements();
    generatePositions();
    generateProjects();
    generateContact();
    generateService();
    generateCertifications();
    generateLanguages();
    generateDiploma();
    generateNetwork();
    console.log(`Contenu généré pour la langue : ${currentLanguage}`);
}

// Fonction pour changer la langue et mettre à jour le contenu
function changeLanguage(language) {
    currentLanguage = language; // Met à jour la langue actuelle
    generateContent(); // Regénère le contenu en fonction de la langue

    // Récupérer tous les boutons radio
    const radios = document.querySelectorAll('input[name="language"]');

    // Boucle sur chaque radio pour appliquer la logique de style
    radios.forEach(radio => {
        const parentLabel = radio.closest('.image-radio'); // Trouve le label parent
        if (radio.value === language) {
            parentLabel.classList.add('active'); // Ajoute la classe active
        } else {
            parentLabel.classList.remove('active'); // Supprime la classe active
        }
    });
}

// Écouteurs d'événements pour les boutons radio
document.querySelectorAll('input[name="language"]').forEach(radio => {
    radio.addEventListener('change', (event) => {
        changeLanguage(event.target.value); 
    });
});

generateMenu(){ 

}
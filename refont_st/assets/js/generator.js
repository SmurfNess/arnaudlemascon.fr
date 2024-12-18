let data = {};
let currentLanguage = 'en';

// Fetch JSON data from the server
async function fetchData() {
    try {
        console.log("Attempting to fetch JSON...");
        const response = await fetch('/refont_st/assets/json/data.json');


        if (!response.ok) {
            console.error(`Failed to fetch JSON. Status: ${response.status} - ${response.statusText}`);
            return;
        }

        const jsonData = await response.json();
        console.log("JSON fetched successfully:", jsonData);

        data = jsonData; // Assigner les données JSON
        generateContent(); // Générer le contenu
    } catch (error) {
        console.error("Error during fetch:", error);
    }
}


function generateContent() {
    generateMenu();
    //generateAchievements();
    //generatePositions();
    //generateProjects();
    //generateContact();
    //generateService();
    //generateCertifications();
    //generateLanguages();
    //generateDiploma();
    //generateNetwork();
    console.log(`Contenu généré pour la langue : ${currentLanguage}`);
}

// Fonction pour changer la langue et mettre à jour le contenu
function changeLanguage(language) {
    currentLanguage = language; 
    generateContent(); 

    // Récupérer tous les boutons radio
    const radios = document.querySelectorAll('input[name="language"]');

    // Boucle sur chaque radio pour appliquer la logique de style
    radios.forEach(radio => {
        const parentLabel = radio.closest('.image-radio');
        if (radio.value === language) {
            parentLabel.classList.add('active'); 
        } else {
            parentLabel.classList.remove('active'); 
        }
    });
}

// Écouteurs d'événements pour les boutons radio
document.querySelectorAll('input[name="language"]').forEach(radio => {
    radio.addEventListener('change', (event) => {
        changeLanguage(event.target.value); 
    });
});

function generateMenu() {
    // Vérifier si le JSON contient les données du menu
    if (data.MENU && data.MENU.length > 0) {
        const menuData = data.MENU[0]; // Récupérer le premier objet du tableau MENU
        
        // Sélectionner chaque élément `nav-link` par son id
        const valuesElement = document.getElementById('VALUES');
        const storyElement = document.getElementById('STORY');
        const cvElement = document.getElementById('CV');
        
        // Mettre à jour le texte des éléments en fonction de la langue actuelle
        if (valuesElement && menuData.VALUES) {
            valuesElement.textContent = menuData.VALUES[currentLanguage] || "Missing Text";
        }
        if (storyElement && menuData.STORY) {
            storyElement.textContent = menuData.STORY[currentLanguage] || "Missing Text";
        }
        if (cvElement && menuData.CV) {
            cvElement.textContent = menuData.CV[currentLanguage] || "Missing Text";
        }
    } else {
        console.error("Menu data is missing in JSON.");
    }
}

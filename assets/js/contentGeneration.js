let data = {}; // This will hold your JSON data
let currentLanguage = 'en'; // Default language

async function fetchData() {
    try {
        const response = await fetch('https://arnaudlemascon.fr/assets/json/data.json'); // Ajustez le chemin si nécessaire
        data = await response.json();
        console.log('Data fetched:', data); // Ajoutez cette ligne pour déboguer
        generateContent();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}


function generateContent() {
    generateArticle();
    generateProjects();
    generateValues();
    generateSkills();
}

function changeLanguage(language) {
    currentLanguage = language;
    generateContent();
}

function generateArticle() {
    const articleContainer = document.querySelector('#article-container');
    if (data.projects && data.projects.length > 0) {
        const project = data.projects[0]; // Utilisation d'un projet comme exemple
        articleContainer.innerHTML = `
            <h2>${project.name[currentLanguage]}</h2>
            <p>${project.description[currentLanguage]}</p>
        `;
    } else {
        articleContainer.innerHTML = '<p>No articles available.</p>';
    }
}


function generateProjects() {
    const container = document.querySelector('#PROJECTS .project-container');
    container.innerHTML = '';

    data.projects.forEach(project => {
        const technoHTML = project.techno.map(techno => `<div class="techno-label" id="${techno}">${techno}</div>`).join('');

        const projectHTML = `
            <div class="cards">
                <div class="img-box">
                    <img src="${project.image}" alt="Image">
                </div>
                <div class="text-box">
                    <a href="#">${project.name[currentLanguage]}</a>
                    <div class="techno-box">
                        ${technoHTML}
                    </div>
                    <p>${project.description[currentLanguage]}</p>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', projectHTML);
    });
}

function generateValues() {
    const container = document.querySelector('#VALUES .container-values .row');
    container.innerHTML = ''; // Clear the container

    data.values.forEach(value => {
        const valueHTML = `
            <div class="col-6 col-md-4 mb-4">
                <div class="carte">
                    <div class="carte-inner">
                        <div class="face face-avant">
                            <img src="${value.image}" alt="Image">
                            <div class="value">${value.name[currentLanguage]}</div>
                        </div>
                        <div class="face face-arriere">
                            <p>${value.description[currentLanguage]}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', valueHTML);
    });
}

function generateSkills() {
    const container = document.querySelector('#SKILLS .skills-container');
    container.innerHTML = ''; // Clear the container

    if (data.skills) {
        for (const key in data.skills) {
            if (data.skills.hasOwnProperty(key)) {
                const skill = data.skills[key];
                const gaugeHTML = `
                    <div class="card_skills">
                        <h3>${skill.name[currentLanguage]}</h3>
                        <div class="gauge">
                            <span style="width: ${skill.level}%; background-color: ${skill.type === 'development' ? '#4caf50' : '#2196f3'};"></span>
                        </div>
                        <p>${skill.level}%</p>
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', gaugeHTML);
            }
        }
    } else {
        container.innerHTML = '<p>No skills available.</p>';
    }
}


fetchData();
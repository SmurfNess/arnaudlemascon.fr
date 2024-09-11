let data = {}; // This will hold your JSON data
let currentLanguage = 'en'; // Default language

// Fetch JSON data from the server
async function fetchData() {
    try {
        const response = await fetch('https://arnaudlemascon.fr/assets/json/data.json');
        data = await response.json();
        console.log('Data loaded:', data); // Debugging
        generateContent();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

// Generate all content sections
function generateContent() {
    generateNavbar();
    generateArticle();
    generateProjects();
    generateValues();
    generateSkills();
    generateForm();
}


// Generate the navbar menu
function generateNavbar() {
    const navbarMenu = document.getElementById('navbar-menu');

    if (navbarMenu) {
        navbarMenu.innerHTML = ''; // Clear existing menu items

        const menuItems = [
            { id: 'HOME', text: data.Navbar[0].HOME },
            { id: 'PROJECTS', text: data.Navbar[0].PROJECT },
            { id: 'VALUES', text: data.Navbar[0].VALUES },
            { id: 'SKILLS', text: data.Navbar[0].SKILLS },
            { id: 'CONTACT', text: data.Navbar[0].CONTACT }
        ];

        menuItems.forEach(item => {
            const menuItemHTML = `
                <li class="nav-item">
                    <a class="nav-link" href="#${item.id}" onclick="scrollToSection('${item.id}')">${item.text[currentLanguage]}</a>
                </li>
            `;
            navbarMenu.insertAdjacentHTML('beforeend', menuItemHTML);
        });
    } else {
        console.warn('Navbar menu container not found.');
    }
}

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    const offset = 80; // Hauteur de la navbar en pixels
    const elementPosition = section.getBoundingClientRect().top;
    const offsetPosition = elementPosition - offset;

    window.scrollBy({
        top: offsetPosition,
        behavior: 'smooth'
    });
}


// Change language and regenerate content
function changeLanguage(language) {
    currentLanguage = language;
    generateContent();

    const buttons = {
        'en': document.getElementById('btn-en'),
        'fr': document.getElementById('btn-fr'),
        'sp': document.getElementById('btn-sp')
    };

    // Loop through each button, hide the active language button and show others
    for (let lang in buttons) {
        if (lang === language) {
            buttons[lang].style.display = 'none';  // Hide the active language button
        } else {
            buttons[lang].style.display = 'inline-block';  // Show other language buttons
        }
    }
}

// Generate articles and place them in their respective sections
function generateArticle() {
    console.log('Generating articles...');
    const sections = {
        'HOME': document.querySelector('#HOME .article-container'),
        'PROJECTS': document.querySelector('#PROJECTS .article-container'),
        'VALUES': document.querySelector('#VALUES .article-container'),
        'SKILLS': document.querySelector('#SKILLS .article-container'),
        'CONTACT': document.querySelector('#CONTACT .article-container'),
    };

    console.log('Sections:', sections);

    if (data.Article && Array.isArray(data.Article)) {
        console.log('Articles:', data.Article);

        // Clear all article containers
        Object.values(sections).forEach(container => {
            if (container) {
                container.innerHTML = ''; // Clear the container
            } else {
                console.warn('Container not found.');
            }
        });

        data.Article.forEach(article => {
            const container = sections[article.section];
            if (container) {
                const articleHTML = `
                    <div class="article-item">
                        <h2>${article.name[currentLanguage]}</h2>
                        <p>${article.description[currentLanguage]}</p>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', articleHTML);
            } else {
                console.warn(`Section "${article.section}" not found.`);
            }
        });
    } else {
        // Handle case where there are no articles
        Object.values(sections).forEach(container => {
            if (container) {
                container.innerHTML = '<p>No articles available.</p>';
            }
        });
    }
}

// Generate project items
function generateProjects() {
    const container = document.querySelector('#PROJECTS .project-container');
    if (container) {
        container.innerHTML = ''; // Clear the container

        data.projects.forEach(project => {
            const technoHTML = project.techno.map(techno => `<div class="techno-label" id="${techno}">${techno}</div>`).join('');

            const projectHTML = `
            <div class="col-4">
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
            </div>  
            `;

            container.insertAdjacentHTML('beforeend', projectHTML);
        });
    } else {
        console.warn('Project container not found.');
    }
}

// Generate value items
function generateValues() {
    const container = document.querySelector('#VALUES .container-values .row');
    if (container) {
        container.innerHTML = ''; // Clear the container

        data.values.forEach(value => {
            const valueHTML = `
                <div class="col-4">
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
    } else {
        console.warn('Values container not found.');
    }
}

function generateSkills() {
    const container = document.querySelector('#SKILLS .skills-container');
    if (container) {
        container.innerHTML = '';

        // Categorize skills by type
        const skillTypes = {
            'development': [],
            'linux': [],
            'windows': [],
            'language': []
        };

        // Sort skills into categories
        for (const key in data.skills) {
            const skill = data.skills[key];
            skillTypes[skill.type].push(skill);
        }

        // Generate HTML for each skill type
        for (const [type, skills] of Object.entries(skillTypes)) {
            let typeTitle;
            if (type === 'development') {
                typeTitle = 'DEV';
            } else if (type === 'language') {
                typeTitle = 'Langues';
            } else if (type === 'windows') {
                typeTitle = 'Windows';
            } else if (type === 'linux') {
                typeTitle = 'Linux';
            }

            const typeHTML = `
                <div class="col-lg-2 col-md-4 col-sm-12 card_skills">
                    <div class="card_skills-type">${typeTitle}</div>
                    ${skills.map(skill => `
                        <div class="gauge-wrapper">
                            ${skill.name[currentLanguage]}
                            <div class="gauge">
                                <div class="gauge-level" style="width:${skill.level}%"></div>
                            </div>
                        </div>
                    `).join('<div class="separator"></div>')}
                </div>
            `;

            container.insertAdjacentHTML('beforeend', typeHTML);
        }
    } else {
        console.warn('Skills container not found.');
    }
}

function generateForm() {
    // Update the labels and button text for the form
    const nameLabel = document.getElementById('label-name');
    const emailLabel = document.getElementById('label-email');
    const messageLabel = document.getElementById('label-message');
    const sendButton = document.getElementById('btn-send');

    if (nameLabel && emailLabel && messageLabel && sendButton) {
        nameLabel.innerHTML = `${data.Form.nameLabel[currentLanguage]}<br><input type="text" name="name" style="width: 50%;" required>`;
        emailLabel.innerHTML = `${data.Form.emailLabel[currentLanguage]}<br><input type="email" name="email" style="width: 50%;" required>`;
        messageLabel.innerHTML = `${data.Form.messageLabel[currentLanguage]}<br><textarea name="message" rows="8" cols="0" required></textarea>`;
        sendButton.innerHTML = data.Form.submitButton[currentLanguage];
    } else {
        console.warn("Form elements not found.");
    }
}


// Fetch data when the script is loaded
fetchData();

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
    generateContactForm();
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

        // After generating the navbar, add event listeners to handle link clicks
        addNavbarLinkEventListeners(); // Add event listeners for nav-links

    } else {
        console.warn('Navbar menu container not found.');
    }
}

// Function to add event listeners to the nav-links
function addNavbarLinkEventListeners() {
    const navLinks = document.querySelectorAll('.nav-link');

    function setActiveLink(event) {
        navLinks.forEach(link => {
            link.classList.remove('active'); // Remove 'active' class from all links
        });
        event.target.classList.add('active'); // Add 'active' class to the clicked link
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            setActiveLink(event);
            closeNavbar(); // Call the function to close the navbar
        });
    });
}

// Scroll smoothly to a section when a link is clicked
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

// Close the navbar if the toggler is visible (for responsive behavior)
function closeNavbar() {
    if ($('.navbar-toggler').is(':visible')) {
        $('.navbar-toggler').click(); // Simulate a click on the toggler to close the navbar
    }
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

// Generate the articles in their respective sections
function generateArticle() {
    console.log('Generating articles...');
    const sections = {
        'HOME': document.querySelector('#HOME .article-container'),
        'PROJECTS': document.querySelector('#PROJECTS .article-container'),
        'VALUES': document.querySelector('#VALUES .article-container'),
        'SKILLS': document.querySelector('#SKILLS .article-container'),
        'CONTACT': document.querySelector('#CONTACT .article-container'),
    };

    if (data && data.Article && Array.isArray(data.Article)) {
        Object.values(sections).forEach(container => {
            if (container) {
                container.innerHTML = ''; // Clear container
            }
        });

        data.Article.forEach(article => {
            const container = sections[article.section];
            if (container) {
                const articleHTML = `
                    <div class="article-item">
                        <h2>${article.name ? article.name[currentLanguage] : 'No name'}</h2>
                        <p>${article.description ? article.description[currentLanguage] : 'No description'}</p>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', articleHTML);
            } else {
                console.warn(`Section "${article.section}" not found.`);
            }
        });
    } else {
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

// Generate skills
function generateSkills() {
    const container = document.querySelector('#SKILLS .skills-container');
    if (container) {
        container.innerHTML = '';

        const skillTypes = {
            'development': [],
            'linux': [],
            'windows': [],
            'language': []
        };

        for (const key in data.skills) {
            const skill = data.skills[key];
            skillTypes[skill.type].push(skill);
        }

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

// Generate the contact form
function generateContactForm() {
    const container = document.querySelector('#CONTACT .contact-container');
    if (!container) {
      console.warn('Contact container not found.');
      return;
    }

    if (!data || !data.Article) {
      console.error('Data or data.Article is not defined.', data);
      return;
    }

    const contactSection = data.Article.find(article => article.section === 'CONTACT');
    if (!contactSection) {
      console.warn('Contact section data not found.');
      return;
    }

    const contactHTML = `
      <h2>${contactSection.name[currentLanguage] || 'Contact'}</h2>
      <form>
        <div class="form-group">
          <label for="name">${data.Form.name[currentLanguage] || 'Name'}</label>
          <input type="text" class="form-control" id="name" placeholder="${data.Form.placeholderName[currentLanguage] || 'Enter your name'}" required>
        </div>
        <div class="form-group">
          <label for="email">${data.Form.email[currentLanguage] || 'Email'}</label>
          <input type="email" class="form-control" id="email" placeholder="${data.Form.placeholderEmail[currentLanguage] || 'Enter your email'}" required>
        </div>
        <div class="form-group">
          <label for="message">${data.Form.message[currentLanguage] || 'Message'}</label>
          <textarea class="form-control" id="message" rows="5" placeholder="${data.Form.placeholderMessage[currentLanguage] || 'Enter your message'}" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">${data.Form.submit[currentLanguage] || 'Submit'}</button>
      </form>
    `;

    container.innerHTML = contactHTML;
}


// Fetch data when the script is loaded
fetchData();

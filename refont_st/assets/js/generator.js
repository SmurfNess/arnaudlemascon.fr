document.addEventListener('DOMContentLoaded', function () {
    const menuElements = {
        VALUES: document.getElementById('VALUES'),
        STORY: document.getElementById('STORY'),
        CV: document.getElementById('CV'),
    };
    const achievementContainer = document.getElementById('ACHIEVEMENT');
    const positionContainer = document.getElementById('POSITIONS');
    const introElement = document.getElementById('INTRO');
    const workingElement = document.getElementById('WORKING');
    const profilePicture = document.querySelector('.img-profile-picture');
    const jsonUrl = 'https://arnaudlemascon.fr/refont_st/assets/json/data.json';

    let currentLanguage = 'en'; // Default language
    let originalProfilePictureSrc = profilePicture ? profilePicture.src : '';

    // Function to load JSON data
    function loadData() {
        return fetch(jsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error loading JSON data.');
                }
                return response.json();
            });
    }

    // Update menu content
    function updateMenu(menuData) {
        Object.keys(menuElements).forEach(key => {
            if (menuData[key] && menuElements[key]) {
                menuElements[key].textContent =
                    menuData[key][currentLanguage] || menuData[key]['en'];
            }
        });
    }

    // Update introduction section
    function updateIntro(infoData) {
        if (infoData['INTRO'] && introElement) {
            introElement.textContent =
                infoData['INTRO'][currentLanguage] || infoData['INTRO']['en'];
        }
    }

    // Update working section
    function updateWorking(infoData) {
        if (infoData['WORKING'] && workingElement) {
            workingElement.textContent =
                infoData['WORKING'][currentLanguage] || infoData['WORKING']['en'];
        }
    }

    // Generate achievements
    function updateAchievements(achievementsData) {
        if (!achievementContainer) return;
        achievementContainer.innerHTML = ''; // Clear container

        for (const key in achievementsData) {
            if (achievementsData.hasOwnProperty(key)) {
                achievementsData[key].forEach(item => {
                    const achievementElement = document.createElement('div');
                    achievementElement.classList.add('container-achievement');
                    achievementElement.setAttribute(
                        'data-image',
                        `./assets/pictures/gallery/${item.gallery}`
                    );

                    achievementElement.innerHTML = `
                        <img src="./assets/pictures/achievement/${item.icon}" alt="${item.alt}" class="card-img-achievement">
                        <div class="tooltip-text">
                            <div class="tooltip-title">${item.title[currentLanguage] || item.title['en']}</div>
                            <div class="tooltip-description">${item.description[currentLanguage] || item.description['en']}</div>
                        </div>
                    `;
                    achievementContainer.appendChild(achievementElement);
                });
            }
        }

        setupAchievementHover(); // Add hover effects
    }

    // Set up hover effects for achievements
    function setupAchievementHover() {
        if (!profilePicture) {
            console.error('Profile picture not found. Check the class .img-profile-picture');
            return;
        }

        const achievements = document.querySelectorAll('.container-achievement');
        achievements.forEach(achievement => {
            achievement.addEventListener('mouseover', () => {
                const newSrc = achievement.getAttribute('data-image');
                if (newSrc) {
                    profilePicture.src = newSrc;
                } else {
                    console.error('Achievement does not contain a data-image attribute.');
                }
            });

            achievement.addEventListener('mouseout', () => {
                profilePicture.src = originalProfilePictureSrc;
            });
        });
    }

    // Update positions
    function updatePositions(positionsData) {
        if (!positionContainer) return;
        positionContainer.innerHTML = ''; // Clear container

        const sortedYears = Object.keys(positionsData).sort().reverse();
        let displayedCount = 0;

        // Helper function to calculate duration
        function calculateDuration(beginningDate, endingDate = null) {
            const start = new Date(beginningDate);
            const end = endingDate ? new Date(endingDate) : new Date();
            const diffTime = end - start;
            const diffMonths = diffTime / (1000 * 60 * 60 * 24 * 30.44);

            if (diffMonths < 12) {
                return `${Math.round(diffMonths)} mois`;
            } else {
                const years = Math.floor(diffMonths / 12);
                const months = Math.round(diffMonths % 12);
                return `${years} an(s) ${months} mois`;
            }
        }

        sortedYears.forEach(year => {
            if (!positionsData[year]) return;
            positionsData[year].forEach(item => {
                if (displayedCount < 3) {
                    const positionElement = document.createElement('div');
                    positionElement.classList.add('position-card');

                    const duration = calculateDuration(item.beginning, item.ending === 'Present' ? null : item.ending);
                    positionElement.innerHTML = `
                        <div class="card-content">
                            <div class="card-enterprise-name">${item.enterprise}</div>
                            <div class="card-enterprise-position">
                                ${item.position[currentLanguage] || item.position['en']}
                            </div>
                            <div class="card-enterprise-duration">${item.beginning} - ${item.ending}</div>
                            <div class="card-enterprise-description">
                                ${item.description[currentLanguage] || item.description['en']}
                            </div>
                        </div>
                    `;
                    positionContainer.appendChild(positionElement);
                    displayedCount++;
                }
            });
        });
    }

    // Main function to initialize everything
    loadData()
        .then(data => {
            updateMenu(data.menu || {});
            updateIntro(data.info || {});
            updateWorking(data.info || {});
            updateAchievements(data.achievements || {});
            updatePositions(data.positions || {});
        })
        .catch(error => console.error('Error initializing data:', error));
});

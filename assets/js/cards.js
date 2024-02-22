var swiper = new Swiper(".swiper", {
    effect: "cards",
    grabCursor: true,
    initialSlide: 2,
    speed: 400,
    loop: true,
    rotate: true,
    mousewheel: {
    invert: false,
  },
});

// Fonction pour générer une carte projet HTML
function generateProjectCard(project) {
  const card = document.createElement('div');
  card.classList.add('swiper-slide');

  card.innerHTML = `
      <div class="swiper-card" style="background-image:url('${project.background}')">
          <div class="container-bande">
              <div class="swiper-head">
                  <div class="swiper-circle"></div>
              </div>
              <div class="swiper-border">${project.title}</div>
              <div class="techno">
                  ${project.html === "ok" ? '<img class="logo html" src="https://arnaudlemascon.fr/assets/pictures/logo/html5.png">' : ''}
                  ${project.css === "ok" ? '<img class="logo css" src="https://arnaudlemascon.fr/assets/pictures/logo/css3.png">' : ''}
                  ${project.js === "ok" ? '<img class="logo js" src="https://arnaudlemascon.fr/assets/pictures/logo/js.png">' : ''}
                  ${project.php === "ok" ? '<img class="logo php" src="https://arnaudlemascon.fr/assets/pictures/logo/php.png">' : ''}
                  ${project.mysql === "ok" ? '<img class="logo mysql" src="https://arnaudlemascon.fr/assets/pictures/logo/mysql.png">' : ''}
                  ${project.rust === "ok" ? '<img class="logo css" src="https://arnaudlemascon.fr/assets/pictures/logo/rust-logo-blk.webp">' : ''}
              </div>
          </div>
          <div class="swiper-img">
              <img src="${project.picture}">
          </div>
          <div class="container-bande">
              <div class="swiper-type">
                  <div class="swiper-circle"></div>
              </div>
              <div class="swiper-border">Projet - ${project.type}</div>
          </div>
          <div class="swiper-description">${project.description}</div>
          <div class="swiper-legend">Arnaud Lemasçon - ${project.year}</div>
      </div>
  `;

  return card;
}

// Fonction pour générer toutes les cartes projets à partir des données Excel
function generateProjectCardsFromExcel(data) {
  const swiperContainer = document.getElementById('swiper-container');

  for (let i = 0; i < data.length; i++) {
      const project = data[i];
      const card = generateProjectCard(project);
      swiperContainer.appendChild(card);
  }
}

// Fonction pour lire le fichier Excel et appeler la fonction de génération de cartes
function readExcelFile(file) {
  const reader = new FileReader();
  reader.onload = function(e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const sheetName = workbook.SheetNames[0];
      const sheet = workbook.Sheets[sheetName];
      const jsonData = XLSX.utils.sheet_to_json(sheet);
      generateProjectCardsFromExcel(jsonData);
  };
  reader.readAsArrayBuffer(file);
}

// Gestionnaire d'événement pour le chargement de fichier
document.getElementById('file-input').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
      readExcelFile(file);
  } else {
      alert("Aucun fichier sélectionné.");
  }
});

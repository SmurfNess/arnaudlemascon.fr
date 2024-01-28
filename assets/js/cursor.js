// Création du curseur personnalisé
const cursor = document.createElement("div");
cursor.style.position = "fixed";
cursor.style.top = "0";
cursor.style.left = "0";
cursor.style.width = "2px"; // Taille du curseur
cursor.style.height = "2px"; // Taille du curseur
cursor.style.borderRadius = "50%"; // Forme du curseur (rond)
cursor.style.backgroundColor = "red"; // Couleur du curseur
cursor.style.boxShadow = "0 0 3px 3px rgba(255, 0, 0, 0.8)"; // Aura lumineuse plus prononcée
cursor.style.pointerEvents = "none"; // Empêcher le curseur de prendre le focus


cursor.style.zIndex = "50000"; // pour être visible au dessus de la bannière


// Ajout du curseur personnalisé au DOM
document.body.appendChild(cursor);

// Récupération de la zone cible (la div avec la classe "banner" et l'id "banner")
const banner = document.getElementById("BANNER");

// Fonction pour mettre à jour la position du curseur en fonction de la souris, limitée à la zone cible
function updateCursorPosition(event) {
  const mouseX = event.clientX;
  const mouseY = event.clientY;
  const bannerRect = banner.getBoundingClientRect(); // Récupération des coordonnées de la zone cible
  
  // Vérification si la souris est à l'intérieur de la zone cible
  if (mouseX >= bannerRect.left && mouseX <= bannerRect.right &&
      mouseY >= bannerRect.top && mouseY <= bannerRect.bottom) {
    cursor.style.transform = `translate(${mouseX}px, ${mouseY}px)`;
    cursor.style.opacity = 1; // Show the cursor
  } else {
    cursor.style.opacity = 0; // Hide the cursor
  }
}

// Écouteur d'événement pour mettre à jour la position du curseur lors du déplacement de la souris
document.addEventListener("mousemove", updateCursorPosition);


let compteur = 0;
let totalBoutons = 4;
let boutonAttendu = 1;
let attempt =0;
let soundValue=1;

function reset() {
  compteur = 0;
  attempt = 0;
  document.getElementById("compteur").textContent = compteur;
  totalBoutons = 4;
  boutonAttendu = 1;
}

function tryAgain() {
  boutonAttendu = 1;
}


function reinitialiserBoutonAttendu() {
  boutonAttendu = 1;
}

function genererGrille() {
    const grilleContainer = document.getElementById("grille-container");
  grilleContainer.innerHTML = "";
  currentNoteIndex = 0;

  for (let i = 0; i < 8; i++) {
    for (let j = 0; j < 8; j++) {
      const caseElement = document.createElement("div");
      caseElement.classList.add("case");
      grilleContainer.appendChild(caseElement);
    }
  }

  let boutonsAjoutes = 0;
  while (boutonsAjoutes < totalBoutons) {
    const randomRow = Math.floor(Math.random() * 8);
    const randomCol = Math.floor(Math.random() * 8);
    const caseElement = grilleContainer.children[randomRow * 8 + randomCol];

    if (!caseElement.querySelector(".numero-bouton")) {
      const boutonElement = document.createElement("button");

      boutonElement.classList.add("numero-bouton");
      boutonElement.textContent = boutonsAjoutes + 1;

      boutonElement.addEventListener("click", function () {
        if (parseInt(this.textContent) === boutonAttendu) {
          playMelody();
          compteur++;
          document.getElementById("compteur").textContent = compteur;

          this.remove();
          boutonsAjoutes--;

          boutonAttendu++;

          if (boutonsAjoutes === 0) {
            totalBoutons++;
            document.getElementById("numbersSuccess").textContent = totalBoutons;
            document.getElementById("attemptDisplaySuccess").innerText = attempt + " of 3";
            reinitialiserBoutonAttendu();
            ajouterBoutonSupplementaire();
          }

          // Modifier la classe pour tous les boutons après le clic du bouton 1
          if (parseInt(this.textContent) === 1) {
            const tousLesBoutons = document.querySelectorAll(".numero-bouton");
            tousLesBoutons.forEach(bouton => {
              bouton.classList.add("bouton-1-clique");
            });
          }
        } else {
          rendreBoutonsInutilisables();
          playBeep();
        }
      });

      caseElement.appendChild(boutonElement);
      boutonsAjoutes++;
    }
  }
}

function ajouterBoutonSupplementaire() {
  var grilleContainer = document.getElementById("cache-succes");
  grilleContainer.style.display = "block";
}

function rendreBoutonsInutilisables() {
  document.getElementById("numbersFail").textContent = totalBoutons;
  const boutons = document.querySelectorAll(".numero-bouton");
  boutons.forEach((bouton) => {
    bouton.disabled = true;
    bouton.classList.add("bouton-inutilisable");
  });
  attempt++;
  document.getElementById("attempt").textContent = attempt;
  document.getElementById("attemptDisplayFail").innerText = attempt + " of 3";
  revealCacheFail();
}

function hiddenCacheStart() {
  var grilleContainer = document.getElementById("cache-start");
  grilleContainer.style.display = "none";
  var grilleContainer = document.getElementById("cache-fail");
  grilleContainer.style.display = "none";
  var grilleContainer = document.getElementById("gameOverCache");
  grilleContainer.style.display = "none"; 
  var grilleContainer = document.getElementById("cache-succes");
  grilleContainer.style.display = "none";
}

function revealCacheFail(){
  if( attempt <= 3){
  var grilleContainer = document.getElementById("cache-fail");
  grilleContainer.style.display = "block";
  }
  if (attempt > 3) {
    var gameOverContainer = document.getElementById("gameOverCache");
    gameOverContainer.style.display = "block";
    attempt = 0;
    document.getElementById("finalScore").textContent = compteur;
  }
}




       // Définition des fréquences des notes (ajustées pour être encore plus graves)
       const FREQUENCIES = {
        'C': 130.81,
        'D': 146.83,
        'E': 164.81,
        'F': 174.61,
        'G': 196.00,
        'A': 220.00,
        'B': 246.94
    };

    // Liste des notes à jouer
    const MELODY = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];

    // Variable pour suivre la position actuelle dans la séquence de notes
    let currentNoteIndex = 0;

    // Fonction pour jouer une note avec une enveloppe sonore
    function playNote(note) {
      if (soundValue === 1) {
          const audioContext = new (window.AudioContext || window.webkitAudioContext)();
          const oscillator = audioContext.createOscillator();
          const gainNode = audioContext.createGain();
  
          oscillator.type = 'sine';
          oscillator.frequency.setValueAtTime(FREQUENCIES[note], audioContext.currentTime);
  
          // Définir l'enveloppe sonore
          gainNode.gain.setValueAtTime(0, audioContext.currentTime);
          gainNode.gain.linearRampToValueAtTime(0.1, audioContext.currentTime + 0.2); // Montée rapide à 0.1
          gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.3); // Descente lente
  
          // Connecter l'oscillateur au nœud de gain et le nœud de gain à la destination audio
          oscillator.connect(gainNode);
          gainNode.connect(audioContext.destination);
  
          // Démarrer l'oscillateur
          oscillator.start();
          oscillator.stop(audioContext.currentTime + 0.5);  // Arrêter la note après 0.5 seconde
      }
  }
  

    // Fonction pour jouer la mélodie
    function playMelody() {
        // Utiliser une promesse pour gérer l'asynchronisme
        function playNoteWithDelay(note, delay) {
            return new Promise(resolve => {
                setTimeout(() => {
                    playNote(note);
                    resolve();
                }, delay);
            });
        }

        // Jouer la note actuelle
        playNoteWithDelay(MELODY[currentNoteIndex], 0);

        // Mettre à jour l'indice pour la prochaine note
        currentNoteIndex = (currentNoteIndex + 1) % MELODY.length;
    }
    function playBeep() {
      if (soundValue === 1) {
          // Créer un contexte audio
          var audioContext = new (window.AudioContext || window.webkitAudioContext)();
  
          // Créer un oscillateur (générateur de son)
          var oscillator = audioContext.createOscillator();
  
          // Créer un nœud de gain pour régler le volume
          var gainNode = audioContext.createGain();
  
          // Définir le type d'onde (sinus pour un son pur)
          oscillator.type = 'sine';
  
          // Définir la fréquence (ajustez la valeur pour obtenir le bip grave désiré)
          oscillator.frequency.setValueAtTime(100, audioContext.currentTime); // Exemple de fréquence 100 Hz
  
          // Connecter l'oscillateur au nœud de gain
          oscillator.connect(gainNode);
  
          // Connecter le nœud de gain au contexte audio
          gainNode.connect(audioContext.destination);
  
          // Régler le volume à 50% (ajustez la valeur selon vos besoins)
          gainNode.gain.setValueAtTime(0.05, audioContext.currentTime);
  
          // Démarrer l'oscillateur
          oscillator.start();
  
          // Arrêter l'oscillateur après un court délai (par exemple, 500 ms)
          oscillator.stop(audioContext.currentTime + 0.3);
      }
  }

  function toggleSound() {
    var imgSound = document.getElementById("sound");

    if (soundValue === 0) {
        imgSound.src = "volume.webp";
        soundValue = 1;
    } else if (soundValue === 1) {
        imgSound.src = "volume-mute.webp";
        soundValue = 0;
    }
}


document.head.appendChild(style);

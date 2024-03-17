const inputField = document.getElementById('input');
const outputField = document.getElementById('output');
const terminal = document.querySelector('.terminal');
const promptElement = document.createElement('div');
promptElement.classList.add('prompt');
terminal.appendChild(promptElement);

inputField.addEventListener('keyup', function(event) {
  if (event.key === 'Enter') {
    const inputValue = inputField.value;
    addToOutput(inputValue);
    processInput(inputValue);
    inputField.value = '';
  }
});

function autoFocusInput() {
  document.getElementById("input").focus();
}

setInterval(autoFocusInput, 500);

function processInput(input) {
  setTimeout(() => {
    const output = echo(input);
    addToOutput(output);
  }, 5000);
}

// Initialisation des variables
var varStrength = 0;
var varDexterity = 0;
var varIntelligence = 0;
var varMelee = 0;
var varRanged = 0;
var varNature = 0;
var varCombat = 0;
var varThief =0;

// Sélection des éléments où vous voulez afficher les variables
var displayStrengthElement = document.getElementById("strengthCounter");
var displayDexterityElement = document.getElementById("dexterityCounter");
var displayIntelligenceElement = document.getElementById("intelligenceCounter");
var displayMeleeElement = document.getElementById("meleeCounter");
var displayRangedElement = document.getElementById("rangedCounter");
var displayNatureElement = document.getElementById("natureCounter");
var displayCombatElement = document.getElementById("combatCounter");
var displayThiefElement = document.getElementById("thiefCounter");


// Fonction pour mettre à jour et afficher les variables toutes les 5 secondes
function mettreAJourEtAfficher() {

    intStrength = parseInt(varStrength);
    intDexterity = parseInt(varDexterity);
    intIntelligence = parseInt(varIntelligence);
    intMelee = parseInt(varMelee);
    intRanged = parseInt(varRanged);
    intNature = parseInt(varNature);
    intCombat = parseInt(varCombat);
    intThief = parseInt(varThief);

    // Mettre le contenu des variables dans les éléments HTML correspondants
    displayStrengthElement.innerHTML = intStrength;
    displayDexterityElement.innerHTML = intDexterity;
    displayIntelligenceElement.innerHTML = intIntelligence;
    displayMeleeElement.innerHTML = intMelee;
    displayRangedElement.innerHTML = intRanged;
    displayNatureElement.innerHTML = intNature;
    displayCombatElement.innerHTML = intCombat;
    displayThiefElement.innerHTML = intThief;

    console.log(`${varStrength} + ${varDexterity} + ${varIntelligence}`);
    console.log(`${varMelee} + ${varRanged} + ${varNature} + ${varCombat} + ${intCombat}`);
    }

function echo(text) {
  // Diviser l'entrée en mots
  const words = text.split(' ');

  // Liste des sorts par type
  const commandTypes = {
    'thief': ['search', 'steal', 'look'],
    'melee': ['hit', 'strike', 'smash', 'slap'],
    'ranged': ['shoot', 'throw', 'aim']
  };

  // Vérifier si la commande est "spell" et s'il y a au moins trois mots
  if (words.length >= 3 && words[0].toLowerCase() === 'spell') {
    const spellName = words[1].toLowerCase();
    const spellTarget = words.slice(2).join(' ');

    // Liste des sorts par type
    const natureSpells = ['zap', 'flash', 'spark', 'dancing zap'];
    const combatSpells = ['fireshot', 'acid gas', 'leech life', 'explosive powder'];

    // Vérifier si le sort est de type "nature" ou "combat"
    let type = "";
    if (natureSpells.includes(spellName)) {
      type = 'Nature';
      varNature += 0.25;
      mettreAJourEtAfficher();
    } else if (combatSpells.includes(spellName)) {
      type = 'Combat';
      varCombat += 5;
      mettreAJourEtAfficher();
    } else {
      return "Ce sort n'existe pas...";
    }

    // Retourner le résultat avec le nom du sort et le type
    return `Le sort ${spellName} est de type ${type} et cible ${spellTarget}`;
  } else if (words.length >= 2) {
    // Si ce n'est pas la commande "spell" ou si la longueur n'est pas suffisante
    const command = words[0].toLowerCase();
    const argument = words.slice(1).join(' '); // Join des mots restants pour les autres commandes

    // Vérifier si la commande est valide dans les tables
    for (let type in commandTypes) {
      if (commandTypes[type].includes(command)) {
        if(type=='melee'){
          varMelee += 1;
        }
        if(type=='thief'){
          varThief += 1;
        }
        if(type=='ranged'){
          varRanged += 1;
        }
        mettreAJourEtAfficher();
        return `Commande ${command} de type ${type} avec argument: ${argument}`;
      }
    }

    // Si la commande n'est pas valide dans les tables
    return `Commande inconnue: ${command}`;
  } else {
    // Si l'entrée ne contient pas au moins deux mots
    return "Ce n'est pas très efficace...";
  }
}


function addToOutput(text) {
  outputField.innerHTML += `<div>${text}</div>`;
}



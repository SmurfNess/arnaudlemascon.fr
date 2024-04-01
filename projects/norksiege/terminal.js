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

setInterval(autoFocusInput, 5000);

function processInput(input) {
  setTimeout(() => {
    const output = echo(input);
    addToOutput(output);
  }, 5000);
}

function addToOutput(text) {
  outputField.innerHTML += `<div>${text}</div>`;
}



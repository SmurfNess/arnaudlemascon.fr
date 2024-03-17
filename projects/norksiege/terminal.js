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
  }, 500);
}

function echo(text) {
  return 'echo ' + text;
}

function addToOutput(text) {
  outputField.innerHTML += `<div>${text}</div>`;
}

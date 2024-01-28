document.querySelector("body").addEventListener("mousemove", eyeball);
function eyeball(event) {
    var eye = document.querySelectorAll(".eye");
    eye.forEach(function (eye) {
        let x =
            eye.getBoundingClientRect().left +
            eye.clientWidth / 2;
        
        let y =
            eye.getBoundingClientRect().top +
            eye.clientHeight / 2;
        let radian =
        Math.atan2(event.clientX - x, event.clientY - y);
        let rot = radian * (180 / Math.PI) * -1 + 270;
        eye.style.transform ="rotate(" + rot + "deg)";
    });
}

const rootStyles = getComputedStyle(document.documentElement);


  setTimeout(function() {
    // Mettre ici le code à exécuter après 15 secondes
    function updateRandomLeft() {
      const randomLeft = Math.floor(Math.random() * (80 - 20 + 1)) + 20;
      document.documentElement.style.setProperty('--random-left', `${randomLeft}%`);
      document.documentElement.style.setProperty('--random-left-left', `${randomLeft - 10}%`);
      document.documentElement.style.setProperty('--random-left-right', `${randomLeft + 10}%`);
    }
    
    // Update the variable initially and then every 30 seconds
    updateRandomLeft();
    setInterval(updateRandomLeft, 30000);
    
  }, 15000); // délai en millisecondes (dans cet exemple, 15 secondes)

  
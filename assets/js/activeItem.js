function closeNavbar() {
  if ($('.navbar-toggler').is(':visible')) {
    $('.navbar-toggler').click();
  }
}

document.addEventListener("DOMContentLoaded", function() {
const navLinks = document.querySelectorAll('.nav-link');

function setActiveLink(event) {
 navLinks.forEach(link => {
     link.classList.remove('active');
 });
 event.target.classList.add('active');
}

navLinks.forEach(link => {
 link.addEventListener('click', function(event) {
     setActiveLink(event);
     closeNavbar(); // Appeler la fonction pour fermer la barre de navigation
 });
});
});

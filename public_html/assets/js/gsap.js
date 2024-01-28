gsap.registerPlugin(ScrollToPlugin, ScrollTrigger);

const tl = gsap.timeline();

if (window.innerWidth >= 768) {
  // Code de navigation horizontale pour les appareils non mobiles
  tl.to(".wrapper", 5, {x: -window.innerWidth * 3.8});

  ScrollTrigger.create({
    animation: tl,
    trigger: ".wrapper",
    start: "center center",
    end: "+=8000",
    scrub: 1,
    pin: true,
  });
}

function values() {
  gsap.to(window, {duration: 2, scrollTo: 525});
}

function trivia() {
  gsap.to(window, {duration: 2, scrollTo: 1100});
}

function informations() {
  gsap.to(window, {duration: 2, scrollTo: "#informations"});
}

function skills() {
  gsap.to(window, {duration: 2, scrollTo: 2535});
}



function home() {
  gsap.to(window, {duration: 2, scrollTo: 0});
}

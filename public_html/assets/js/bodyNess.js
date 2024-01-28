    // Fonction pour récupérer la position du bas de l'élément animé rouge et mettre à jour le texte
    function recupererPositionDuBas() {
      // Obtenez l'objet de rectangle de l'élément rectangle, qui contient les propriétés de position
      var rect = document.getElementById('blackNess').getBoundingClientRect();

      // Récupérez la position du bas de l'élément animé rouge en ajoutant la position verticale de l'élément rectangle à sa hauteur
      var bottomPosition = rect.top + rect.height;

      // Arrondir la position du bas aux dizaines
      var bottomPositionArrondi = Math.round(bottomPosition )  ;

      // Mettre à jour le texte avec la position du bas de l'élément animé rouge arrondi aux dizaines
      document.getElementById('positionDuBas').textContent = 'Position du bas de l\'element anime rouge : ' + bottomPositionArrondi + 'px';
      
      // Retourner la position du bas de l'élément animé rouge
      return bottomPositionArrondi;
    }

    // Fonction pour récupérer la position du bas de la bande noire et mettre à jour le texte
    function recupererPositionBandeDuBas() {
      // Obtenez la position verticale de la bande horizontale
      var bande = document.getElementById('maBande');
      var bandeTop = bande.offsetTop;
      var bandeHeight = bande.offsetHeight;
      var bandeBottom = bandeTop + bandeHeight;

      // Arrondir la position du bas de la bande noire aux dizaines
      var bandeBottomArrondi = Math.round(bandeBottom-5);

      // Mettre à jour le texte avec la position du bas de la bande noire arrondi aux dizaines
      document.getElementById('positionBande').textContent = 'Position du bas de la bande noire : ' + bandeBottomArrondi + 'px';

     
      // Retourner la position du bas de la bande noire
      return bandeBottomArrondi;
    }

    // Fonction pour calculer la différence entre la position du bas de l'élément animé rouge et la position du bas de la bande noire et mettre à jour le texte
    function calculerDifferencePosition() {
      var positionDuBasElement = recupererPositionDuBas();
      var positionDuBasBande = recupererPositionBandeDuBas();
	  var myElement = document.getElementById("blackNess");


      // Calculer la différence entre les deux positions - floor0
	  
      var calculdiff = positionDuBasBande - positionDuBasElement;
	  if(calculdiff <= 0)
    {
    var differencePosition = 200 + positionDuBasBande - positionDuBasElement;
    if (differencePosition > 30 ) {
    blackNess.style.clipPath = 'polygon(5px 50px,7px 25px,10px 5px,12px 0px,15px 0px,36px 15px,40px 14px,45px 14px,50px 13px,55px 14px,60px 14px,65px 15px,85px 0px,88px 0px,90px 5px,92px 25px,95px 50px,100px ' + differencePosition + 'px,0px ' + differencePosition + 'px)';
	  whiteNess.style.clipPath = 'polygon(20px ' + differencePosition + 'px,20px 60px,22px 55px,24px 52px,25px 50px,43px 45px,48px 35px,49px 35px,50px 35px,51px 35px,52px 35px,58px 45px,75px 50px,76px 52px,78px 55px,80px 60px,80px ' + differencePosition + 'px)';
	}
	  }
	  else
	  {
	  var differencePosition = 200;
    blackNess.style.clipPath = 'polygon(5px 50px,7px 25px,10px 5px,12px 2px,15px 2px,36px 25px,50px 25px,60px 25px,85px 2px,88px 2px,90px 5px,92px 25px,95px 50px,100px ' + differencePosition + 'px,0px ' + differencePosition + 'px)';
	  whiteNess.style.clipPath = 'polygon(20px ' + differencePosition + 'px,20px 60px,22px 55px,24px 52px,25px 50px,45px 45px,48px 35px,49px 35px,50px 35px,51px 35px,52px 35px,55px 45px,75px 50px,76px 52px,78px 55px,80px 60px,80px ' + differencePosition + 'px)';
  
	  }
 
  // 'polygon(120px 50px,180px 0px,190px 60px,200px 100px,200px ' + differencePosition + ',0px ' + differencePosition + ',0px 100px,10px 60px,20px 0px,80px 50px)';
	// Utilise la valeur de differencePosition pour définir la valeur de clip-path en pixels

      // Mettre à jour le texte avec la différence de position
      document.getElementById('resultat').textContent = differencePosition;
    }

    // Appeler la fonction de calcul de la différence de position chaque seconde
    setInterval(calculerDifferencePosition, 10);
	


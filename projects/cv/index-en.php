<!DOCTYPE html>
<html lang="fr" >
<head>
  <meta charset="UTF-8" >
  <title>CV - English</title>
  <style>
        :root {
        --color-principal: rgb(224, 175, 51);
        --color-other: rgb(90, 103, 160);
        --color-tertiary: rgb(61, 70, 122);
        --color-dark: #1b1f36c4;
        --color-input: white;
        --color-font-principal: rgb(0, 0, 0);
        --color-font-secondary:rgb(189, 147, 40);
        --color-font-tertiary: rgb(61, 70, 122);
        --color-gauge-front: rgba(0, 212, 250, 0.2);
        --color-gauge-back: rgba(0, 212, 250, 0.2);
        --color-gauge-h_back: rgba(0, 212, 250, 0.5);
        --color-gauge-h_front: rgb(0, 212, 250);

        --font-vbg: 30px;
        --font-bg: 20px;
        --font-md: 15px;
        --font-sm: 12px;

        --poppins-font: 'Poppins', sans-serif;
        --arial-font: Arial, sans-serif;
        }

        body {
        font-family: var(--poppins-font);
        margin: 0;
        background-color: lightgray;
        display: flex;
        justify-content: center;
        }

        .container {
        display: flex;
        height: 100vh;

        }

        .left,
        .right {
        padding: 10px;
        box-sizing: border-box;
        overflow: hidden;
        }

        .left {
        background-color: var(--color-principal);
        width: 250px;
        color: white;
        }

        .portrait-container {
        margin: 0px 35px;
        height: 180px;
        width: 150px;
        overflow: hidden;
        border: white 5px solid;
        }

        .portrait-container img {
        position: relative;
        width: 110%;
        top: -10px;
        }

        .informations,
        .skills,
        .languages,
        .hobbies {
        padding-left: 20px;
        padding-top: 10px;
        }

        .intro{
        position: relative;
        left: -50px;
        border-radius: 25px;
        height: 35px;
        width:fit-content;
        padding-left: 50px;
        padding-right: 20px;
        display: flex;
        align-items: center;
        font-weight: bold;
        font-size: var(--font-md);
        color: white;
        text-transform: uppercase;
        }

        .intro_left {
        background-color: white;
        color: black;
        }

        .intro_right{
        background-color: var(--color-principal);
        margin-top:20px;
        }

        ul {
        font-size: var(--font-md);
        margin:5px;
        }

        li {
        position: relative;
        text-transform: capitalize;
        }

        .right {
        padding: 10px;
        width: 500px;
        background-color: white;
        }

        .name{
        font-size: var(--font-vbg);
        font-weight: bold;
        text-transform: uppercase;
        color: var(--color-principal);
        margin-bottom: 10px;
        }

        .bio{
        font-size: var(--font-sm);
        }
        .position{
        font-weight: bold;
        text-transform: capitalize;
        }

    .entreprise{
        margin-top: 15px;
        text-transform: uppercase;
        text-decoration: underline 4px var(--color-principal);
        font-weight: bold;
    }
    .title, .degree{
        font-size: var(--font-md);
        font-weight: bold;
    }
        
        .cardP, .card {
        position: relative;
        padding-left: 20px;
        width: 100%;
        margin-top: 10px;
        background: linear-gradient(90deg, var(--color-principal) 0%, transparent  2%, transparent 100%);
        font-size: var(--font-sm);
        }

        .duration{
        color: var(--color-font-secondary);
        font-size: var(--font-sm);
        font-weight: bold;
        margin-bottom: 3px;
        }

        .descP{
        padding-right: 15px;
        font-size: var(--font-sd);
        }

    .separator {
        color: var(--color-principal);
        font-weight: 900;
        margin-left: 10px;
        margin-right: 10px;
        }
    
        .infobulle {
        position: relative;
        cursor: help;
        }

        .infobulle:hover::before {
        content: attr(title);
        background-color: rgb(18, 18, 18);
        color: #fff;
        padding: 5px;
        border-radius: 15px;
        position: absolute;
        z-index: 1;
        top: 10%;
        left: 35%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        width: fit-content;
        }

        .infobulle:hover::before {
        opacity: 1;
        }
</style>
</head>
<body>
  <div class="container" >
    <div class="left" >
      <div class="portrait-container" >
        <img src="./Générateur de CV_files/cv.webp" alt="Mon portrait par Mélanie Kosowski" >
      </div>
      <div class="informations" >
        <div class="calcul" id="resultat" >34 ans</div>
        <div class="location" >Lyon, France</div>
        <div class="mail" >contact@arnaudlemascon.fr</div>
      </div>
      <div class="skills" >
        <div class="intro intro_left" >Compétences</div>
        <ul>
          <li>Projet</li>
          <li>Office 365</li>
          <li>Scrum</li>
        </ul>
      </div>
      <div class="hobbies" >
        <div class="intro intro_left" >Certifications</div>
        <ul>
          <li>PSM1</li>
          <li>PSC</li>
          <li>Mener le changement</li>
        </ul>
      </div>

      <div class="languages" >
        <div class="intro intro_left" >Langues</div>
        <ul>
          <li>Français : natif</li>
          <li>Anglais : écrit professionnel</li>
          <li>Espagnol : en apprentissage</li>
        </ul>
      </div>
      <div class="hobbies" >
        <div class="intro intro_left" >Loisirs</div>
        <ul>
          <li>Projet</li>
          <li>Gaming</li>
          <li>coding</li>
        </ul>
      </div>
    </div>
    <div class="right" >
      <div class="name" >arnaud lemasçon</div>
      <div class="position" >scrum master / chef de projets</div>
      <div class="bio" >
        Après 8 ans dans le support informatique, j'ai engagé une évolution dans le management de projet en étoffant notamment dans l'accompagnement au changement et l'agilité.
        <br>Je souhaite aujourd'hui m'orienter davantage vers la culture agile.
      </div>
      <div class="positions" >
        <div class="intro intro_right" >expérience professionnelles</div>
        <div id="cvContainer" >
          <div class="entreprise" >SOGETI</div>
            <div class="cardP" >
              <div class="title" >Consultant technico-fonctionnel</div>
              <div class="duration" >09/2023 - 06/2024 (9 mois)</div>
              <div class="descP" >client SNCF - Pilote de la demande :  Pilotage de projets d'infrastructure - Suivi des besoins, des évolutions et des incidents d'un portefeuille de neuf applications</div>
            </div>
          <div class="entreprise" >Spie batignolles</div>
            <div class="cardP" >
              <div class="title" >Chargé de projet</div>
              <div class="duration" >04/2022 - 08/2023 (1 année(s) et 4 mois)</div>
              <div class="descP" >Chargé de projet pour 2 applications de gestion : Suivi demandes d'évolution et incidents - Pilotage déploiement applicatif</div>
            </div>
            <div class="cardP" >
              <div class="title" >Technicien informatique et réseau - Pôle Technique</div>
              <div class="duration" >08/2018 - 06/2022 (3 année(s) 10 mois )</div>
              <div class="descP" >Technicien helpdesk niveau 2 - Gestion de comptes AD et droit NTFS - Chargé de projet - mise à jour des catalogues d'incidents et de demande ITSM</div>
            </div>
            <div class="cardP" >
              <div class="title" >Technicien informatique et Réseau</div>
              <div class="duration" >08/2016 - 08/2018 (2 année(s) )</div>
              <div class="descP" >Technicien helpdesk - Gestionnaire de flotte mobile pour une de ces filiales - Chargé de projet sur la mise en place de Microsoft DFS et ABE</div>
            </div>
            <div class="cardP" >
              <div class="title" >Apprenti technicien informatique</div>
              <div class="duration" >08-2014 - 08-2016 (2 année(s) )</div>
            </div>
          <div class="entreprise" >Helpline</div>
            <div class="cardP" >
                  <div class="title" >Apprenti Helpdesk Agent</div>
                  <div class="duration" >10/2013 - 07/2014 (8 mois )</div>
                  </div>
        </div>
      </div>
      <div class="educations" >
        <div class="intro intro_right" >Formations</div>
        <div class="EducationContainer" >
            <div class="card" >
              <div class="degree" >TSMIR<span class="separator" >|</span><span class="school" >IPI école informatique</span></div>
              <div class="duration" >2014 - 2016</div>
            </div>
          </span>
            <div class="card" >
              <div class="degree" >TMMI<span class="separator" >|</span><span class="school" >IPI école informatique</span></div>
              <div class="duration" >2013 - 2014</div>
            </div>
          </span>
            <div class="card" >
              <div class="degree" >DNSEP<span class="separator" >|</span><span class="school" >ESA Nord Pas-de-Calais</span></div>
              <div class="duration" >2011 - 2013</div>
            </div>
          </span>
            <div class="card" >
              <div class="degree" >DNAP<span class="separator" >|</span><span class="school" >ESA Nord Pas-de-Calais</span></div>
              <div class="duration" >2008 - 2011</div>
            </div>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

    <script>
      var dateAnniversaire = new Date('1989-10-29');
      var dateActuelle = new Date();
      var age = dateActuelle.getFullYear() - dateAnniversaire.getFullYear();
      if (dateActuelle.getMonth() < dateAnniversaire.getMonth() || (dateActuelle.getMonth() === dateAnniversaire.getMonth() && dateActuelle.getDate() < dateAnniversaire.getDate())) {
        age--;
      }
      var divResultat = document.getElementById("resultat");
      divResultat.textContent = age + " ans";
    </script>
  </body>
</html>

<?php

class CVGenerator {

    private $bdd;

    public function __construct(PDO $bdd) {
        $this->bdd = $bdd;
    }

    public function generateCV() {
echo '      <div class="container" > ';
echo '      <div class="left" >';
echo '        <div class="portrait-container" >';
echo '          <img src="https://arnaudlemascon.fr/assets/pictures/cv.webp" alt="Mon portrait par Mélanie Kosowski" >';
echo '        </div>';
echo '        <div class="informations" >';
echo '          <div class="calcul" id="resultat" >34 ans</div>';
echo '          <div class="location" >Lyon, France</div>';
echo '          <div class="mail" >contact@arnaudlemascon.fr</div>';
echo '        </div>';
echo '        <div class="skills" >';
echo '          <div class="intro intro_left" >Compétences</div>';
echo '          <ul>';
echo '            <li>Projet</li>';
echo '            <li>Office 365</li>';
echo '            <li>Scrum</li>';
echo '          </ul>';
echo '        </div>';
echo '        <div class="hobbies" >';
echo '          <div class="intro intro_left" >Certifications</div>';
echo '          <ul>';
echo '            <li>PSM1</li>';
echo '            <li>PSC</li>';
echo '            <li>Mener le changement</li>';
echo '          </ul>';
echo '        </div>';
echo '  ';
echo '        <div class="languages" >';
echo '          <div class="intro intro_left" >Langues</div>';
echo '          <ul>';
echo '            <li>Français : natif</li>';
echo '            <li>Anglais : écrit professionnel</li>';
echo '            <li>Espagnol : en apprentissage</li>';
echo '          </ul>';
echo '        </div>';
echo '        <div class="hobbies" >';
echo '          <div class="intro intro_left" >Loisirs</div>';
echo '          <ul>';
echo '            <li>Projet</li>';
echo '            <li>Gaming</li>';
echo '            <li>coding</li>';
echo '          </ul>';
echo '        </div>';
echo '      </div>';
echo '      <div class="right" >';
echo '              <div class="name" >arnaud lemasçon</div>';
echo '              <div class="position" >scrum master / chef de projets</div>';
echo '              <div class="bio" >';
echo '                Après 8 ans dans le support informatique, j\'ai engagé une évolution dans le management de projet en étoffant notamment dans l\'accompagnement au changement et l\'agilité.';
echo '                <br>Je souhaite aujourd\'hui m\'orienter davantage vers la culture agile.';
echo '              </div>';

echo '              <div class="positions" >';
echo '                <div class="intro intro_right" >expérience professionnelles</div>';
echo '                <div id="cvContainer" >';
$this->generatePositionCards();
echo '                </div>';
echo '              </div>';
echo '              <div class="educations" >';
echo '                <div class="intro intro_right" >Formations</div>';
echo '                <div id="EducationContainer" >';
$this->generateEducationCards();
echo '                </div>';
echo '              </div>';
echo '              </div>';

 }

    

 private function generatePositionCards() {
  $positionQuery = $this->bdd->prepare("SELECT * FROM Position ORDER BY start DESC");
  $positionQuery->execute();
  $positions = $positionQuery->fetchAll(PDO::FETCH_ASSOC);

  // Grouper les positions par entreprise tout en conservant l'ordre
  $groupedPositions = [];
  foreach ($positions as $position) {
      if ($position['end'] == '0000-00-00') {
          $position['end'] = date('Y-m-d');
      }
      $groupedPositions[$position['enterprise']][] = $position;
  }

  // Afficher les entreprises et leurs positions dans l'ordre original
  $displayedEnterprises = [];
  foreach ($positions as $position) {
      $enterprise = $position['enterprise'];
      if (!in_array($enterprise, $displayedEnterprises)) {
          echo '<div class="entreprise">' . $enterprise . '</div>';
          $displayedEnterprises[] = $enterprise;
          foreach ($groupedPositions[$enterprise] as $enterprisePosition) {
              $startDate = new DateTime($enterprisePosition['start']);
              $endDate = new DateTime($enterprisePosition['end']);
              echo '<div class="cardP">';
              echo '<div class="title" style="display:inline-block">' . $enterprisePosition['title'] . '</div><span class="separator" >|</span><div class="duration" style="display:inline-block">' . $startDate->format('m-Y') . ' - ' . $endDate->format('m-Y') . ' (' . $this->calculateDuration($enterprisePosition['start'], $enterprisePosition['end']) . ')</div>';
              echo '';
              echo '<div class="descP" >' .  $enterprisePosition['description_fr'] . '</div>';
              echo '</div>';
          }
      }
  }
}



    private function generateEducationCards() {
        // Fetch data from the 'Education' table
        $educationQuery = $this->bdd->prepare("SELECT * FROM Education");
        $educationQuery->execute();
        $educations = $educationQuery->fetchAll(PDO::FETCH_ASSOC);

        // Loop through each education and generate the HTML cards
        foreach ($educations as $education) {
            echo '<span class="infobulle" title="' . $education['CT_fr'] . '">';
            echo '<div class="card col-md-12">';
            $startDate = new DateTime($education['start']);
            $endDate = new DateTime($education['end']);
            echo '<div class="degree">' . $education['title'] . '<span class="school"> <span>-</span> ' . $education['school'] . ' - ' . $education['city'] . '</div></div><span class="separator" >|</span>';
            echo '<div class="duration" style="display:inline-block">' . $startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y') . '</div>';
            echo '</div>';
            echo '</span>';
        }
    }

    private function generateCertificationsList() {
        // Fetch data from the 'Certification' table
        $certificationQuery = $this->bdd->prepare("SELECT * FROM Certification WHERE source != 'Linkedin'");
        $certificationQuery->execute();
        $certifications = $certificationQuery->fetchAll(PDO::FETCH_ASSOC);

        // Display certifications list in certificationsContainer
        echo '<ul class="certificationsList">';
        foreach ($certifications as $certification) {
            echo '<li>';
            echo '<a href="' . $certification['link'] . '" target="_blank">' . $certification['title'] . ' - ' . $certification['source'] . ' - ' . $certification['date'] . '</a>';
            echo '</li>';
        }
        echo '</ul>';

        // Fetch and display the sum of LinkedIn certifications
        $linkedinCertificationQuery = $this->bdd->prepare("SELECT COUNT(*) AS total FROM Certification WHERE source = 'Linkedin'");
        $linkedinCertificationQuery->execute();
        $linkedinCertificationCount = $linkedinCertificationQuery->fetch(PDO::FETCH_ASSOC);

        // Display the final sentences
        echo '<div class="cert col-12">et ' . $linkedinCertificationCount['total'] . ' certifications LinkedIn ont été passées, sur divers sujets tels que la gestion de projets, les relations interpersonnelles et l\'agilité.';
        echo '<br><a href="https://www.linkedin.com/in/arnaud-lemas%C3%A7on-ness/details/certifications/" target="_blank">Liste exhaustive ici</a></div>';
    }

    private function calculateDuration($start, $end) {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $interval = $startDate->diff($endDate);

        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;

        $duration = '';
        if ($years > 0) {
            $duration .= $years . ' année(s) ';
        }
        if ($months > 0) {
            $duration .= $months . ' mois ';
        }
        return $duration;
    }
}

require_once 'config.php' ;

try {

    $bdd = new PDO("mysql:host=$host;dbname=$dbname", $username, $password) ;

    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create an instance of the CVGenerator class
    $cvGenerator = new CVGenerator($bdd);

    // Generate the CV structure
    $cvGenerator->generateCV();

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>



<!DOCTYPE html>
<html>
<head>
    <title>Générateur de CV</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
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

        --font-vbg: 28px;
        --font-bg: 18px;
        --font-md: 14px;
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
    height: 100vh; /* Ensure body takes full viewport height */
}

.container {
    display: flex;
    max-width: 800px; /* Set a max-width to the container */
    border-radius: 8px;
    overflow: hidden;
}
.separator {
        color: var(--color-principal);
        font-weight: 900;
        margin-left: 10px;
        margin-right: 10px;
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
<script>
var dateAnniversaire = new Date('1989-10-29');
var dateActuelle = new Date();
var age = dateActuelle.getFullYear() - dateAnniversaire.getFullYear();
if (dateActuelle.getMonth() < dateAnniversaire.getMonth() || (dateActuelle.getMonth() === dateAnniversaire.getMonth() && dateActuelle.getDate() < dateAnniversaire.getDate())) {
    age--;
}
  
    var divResultat = document.getElementById("resultat");
    divResultat.textContent = "Age : " + age +" ans";
    </script>
</body>
</html>


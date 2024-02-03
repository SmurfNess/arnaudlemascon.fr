<?php

class CVGenerator {

    private $bdd;

    public function __construct(PDO $bdd) {
        $this->bdd = $bdd;
    }

    public function generateCV() {
        echo '<div class="container">';
        echo '  <div class="row">';
        $this->generateHeaderColumn();
        $this->generateMainContentColumn();
        echo '  </div>';
        echo '</div>'; 
    }

    private function generateHeaderColumn() {
        echo '    <div class="col-lg-4 header">';
        echo '      <div class="row">';
        $this->generateImageColumn();
        $this->generatePersonalInfoColumn();
        echo '      </div>';
        echo '      <div class="row">';
        echo '        <div class="col-12 EducationContainer">';
        $this->generateEducationCards();
        echo '        </div>';
        echo '        <div class="col-12 certificationsContainer">';
        $this->generateCertificationsList();
        echo '        </div>';
        echo '      </div>';
        echo '    </div>';
    }

    private function generateImageColumn() {
        echo '        <div class="col-md-6 col-sm-12">';
        echo '          <div class="image-container">';
        echo '            <img src="https://arnaudlemascon.fr/assets/pictures/cv.webp" alt="Mon portrait par Mélanie Kosowski">';
        echo '          </div>';
        echo '        </div>';
    }

    private function generatePersonalInfoColumn() {
        echo '        <div class="col-md-6 col-sm-12">';
        echo '          <div class="row">';
        echo '            <div class="col-12">Arnaud LEMASÇON</div>';
        echo '            <div class="col-12 calcul" id="resultat"></div>';
        echo '            <div class="col-12">City: Lyon</div>';
        echo '            <div class="col-12"><br>D\'un naturel curieux, j\'aime découvrir de nouveaux outils et technologies.</div>';
        echo '          </div>';
        echo '        </div>';
    }

    private function generateMainContentColumn() {
        echo '    <div class="col-lg-8">';
        echo '      <div class="col-12" id="cvContainer">';
        $this->generatePositionCards();
        echo '      </div>';
        echo '    </div>';
    }

private function generatePositionCards() {
    $positionQuery = $this->bdd->prepare("SELECT * FROM Position ORDER BY start DESC");
    $positionQuery->execute();
    $positions = $positionQuery->fetchAll(PDO::FETCH_ASSOC);

    foreach ($positions as $position) {
        if ($position['end'] == '0000-00-00') {
            $position['end'] = date('Y-m-d');
        }

        echo '<span class="infobulle" title="' . $position['description_fr'] . '">';
        echo '<div class="cardP col-md-12">';
        echo '<div class="row">';
        echo '<div class="col-md-2 logo-container"><img src="' . $position['logo'] . '" alt="Logo"></div>';
        echo '<div class="col-md-10">';
        $startDate = new DateTime($position['start']);
        $endDate = new DateTime($position['end']);
        echo '<h6 class="duration">' . $startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y') . ' (' . $this->calculateDuration($position['start'], $position['end']) . ')</h6>';
        echo '<h5>' . $position['title'] . '</h5>';
        echo '<h6 style="text-transform:uppercase;">' . $position['enterprise'] . ' - ' . $position['city'] . '</h6>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</span>';
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
            echo '<div class="duration">' . $startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y') . '</div>';
            echo '<div class="degree">' . $education['title'] . '<span class="school"> <span style="color:rgba(47, 79, 79, 0.75)">à</span> ' . $education['school'] . ' - ' . $education['city'] . '</div>';
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
        if ($days > 0) {
            $duration .= $days . ' jour(s)';
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

      root{
        --blue: rgb(166, 179, 255);
        --grey:rgb(18, 18, 18);
        --gold:rgb(190, 188, 22);
      }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: grey;
            color: white;
        }

        a {
          color: white;
          font-size:small;
          text-decoration: underline;
        }

        a:hover{
          color: var(--blue);        
        }

        li{
          color: var(--blue);
          margin-bottom: 10px;
        }

.logo-container img{
    max-height:80px;
    border-radius:50%;
}
		
.cert{
  font-size: 15px;
  text-align: center;
}

.col-md-1{
  background-color: var(--gold);
  height: 80vh;
}        

.col-md-11{
  background-color: rgb(238, 56, 56);
}        
.container{
  padding: 10px;
}
	
.image-container {
  position: relative;
  width: 140px;
  height: 180px;
  overflow: hidden;
  border-radius: 20px;
}

.image-container img {
  position: absolute;
  top: 55%;
  left: 55%;
  width: 120%;
  transform: translate(-50%, -50%) rotate(3deg); /* Déplace l'image de 50% vers la gauche et le haut, puis la fait pivoter de 10 degrés */
}

.certificationsContainer{
  position: relative;
  font-size: 10px;
  margin-top: 20px;
}

.card {
  position: relative;
  padding: 10px;
  border: none;
  background: linear-gradient(145deg, rgba(166, 179, 255, 0.5) 5%,rgba(255,255,255,0) 80%);
  border-radius: 10px 0px 0px 10px;
  margin-top:10px;
  width: 100%;
}

.cardP {
  position: relative;
  padding: 10px;
  border: none;
  background: linear-gradient(145deg, rgba(166, 179, 255, 0.5) 5%,rgba(255,255,255,0) 80%);
  border-radius: 100px 0px 0px 100px;
  margin-top:10px;
  width: 100%;
}

.duration{
	color:rgba(47, 79, 79, 0.75);
  font-size: 14px;
}

.infobulle {
  position: relative;
  cursor: help;
}

.infobulle:hover::before {
  content: attr(title);
  background-color: #333;
  color: #fff;
  padding: 5px;
  border-radius: 5px;
  position: absolute;
  z-index: 1;
  top: 10%;
  left: 75%;
  transform: translateX(-50%);
  opacity: 0;
  transition: opacity 0.3s;
  width: 500px;
}

.infobulle:hover::before {
  opacity: 0.75;
}

.header{
  background-color:rgba(47, 79, 79, 0.75);
  width: 100%;
  padding: 10px;
  border-radius: 25px;
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


<?php

class teamup {

    private $bdd;

    public function __construct(PDO $bdd) {
        $this->bdd = $bdd;
    }

    public function addEntry($name, $class, $owner) {
        $sql = "INSERT INTO TU (name, class, owner) VALUES (:name, :class, :owner)";
        $stmt = $this->bdd->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':owner', $owner);
        $stmt->execute();
    }
}

require_once 'config.php' ;

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $teamup = new teamup($bdd);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $class = $_POST['class'];
        $owner = $_POST['owner'];
        $teamup->addEntry($name, $class, $owner);
        echo "Entrée ajoutée avec succès.";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Générateur d'équipe</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Générateur d'équipe</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="name">Nom:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="class">Classe:</label>
                <input type="text" class="form-control" id="class" name="class" required>
            </div>
            <div class="form-group">
                <label for="owner">Propriétaire:</label>
                <input type="text" class="form-control" id="owner" name="owner" required>
            </div>
            <button type="submit" class="btn btn-primary">Valider</button>
        </form>
    </div>
</body>
</html>

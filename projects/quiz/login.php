<?php
// Connexion à la base de données avec PDO
$servername = "localhost";
$username = "votre_nom_utilisateur";
$password = "votre_mot_de_passe";
$dbname = "quiz";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configuration pour générer des exceptions en cas d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Préparation de la requête SQL
    $stmt = $conn->prepare("SELECT * FROM access WHERE username=:username AND password=:password");

    // Liaison des paramètres
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    // Exécution de la requête
    $stmt->execute();

    // Vérification de l'existence de l'utilisateur dans la base de données
    if ($stmt->rowCount() > 0) {
        // L'utilisateur est authentifié
        echo "Connexion réussie!";
    } else {
        // L'utilisateur n'est pas trouvé dans la base de données
        echo "Identifiants incorrects!";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Fermeture de la connexion
$conn = null;
?>

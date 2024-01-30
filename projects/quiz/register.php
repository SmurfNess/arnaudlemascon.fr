<?php

require_once 'config.php' ;

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password) ;
    // Configuration pour générer des exceptions en cas d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données du formulaire
    $newUsername = $_POST['newUsername'];
    $newPassword = $_POST['newPassword'];
    $accountType = $_POST['accountType'];

    // Préparation de la requête SQL
    $stmt = $conn->prepare("INSERT INTO access (username, password, type) VALUES (:newUsername, :newPassword, :accountType)");

    // Liaison des paramètres
    $stmt->bindParam(':newUsername', $newUsername);
    $stmt->bindParam(':newPassword', $newPassword);
    $stmt->bindParam(':accountType', $accountType);

    // Exécution de la requête
    $stmt->execute();

    // Compte créé avec succès
    echo "Compte créé avec succès!";
} catch (PDOException $e) {
    // Erreur lors de la création du compte
    echo "Erreur : " . $e->getMessage();
}

// Fermeture de la connexion
$conn = null;
?>

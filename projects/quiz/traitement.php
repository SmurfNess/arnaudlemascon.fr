<?php
// Inclure votre fichier de configuration
include('config.php');

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $form_username = $_POST['username'];
    $form_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier si les mots de passe correspondent
    if ($form_password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
        // Vous pouvez rediriger l'utilisateur vers le formulaire de création de compte ici si nécessaire
    } else {
        // Hasher le mot de passe avant de l'ajouter à la base de données (sécurité)
        $hashed_password = password_hash($form_password, PASSWORD_DEFAULT);

        // Établir la connexion à la base de données
        $connexion = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);

        // Préparer la requête d'insertion
        $requete = $connexion->prepare("INSERT INTO quiz (username, password) VALUES (?, ?)");

        // Exécuter la requête avec les données du formulaire
        $requete->execute([$form_username, $hashed_password]);

        // Fermer la connexion à la base de données
        $connexion = null;

        echo "Le compte a été créé avec succès.";
        // Vous pouvez rediriger l'utilisateur vers une page de connexion ici si nécessaire
    }
}
?>

<?php
// Dans votre page principale (index.php)
session_start();

// Vérifiez si l'utilisateur est connecté et a une variable de session 'user_type' définie
if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];

    // Affichez le contenu en fonction du type de compte
    if ($user_type == $admin) {
        // Afficher le contenu pour un compte admin
        echo "<h1>Bienvenue, Administrateur!</h1>";
        echo "<p>Vous pouvez <a href='modifier_mot_de_passe.php'>modifier le mot de passe</a> et <a href='modifier_username.php'>modifier le nom d'utilisateur</a>.</p>";
    } elseif ($user_type == $util) {
        // Afficher le contenu pour un compte util
        echo "<h1>Bienvenue, Utilisateur!</h1>";
        echo "<p>Vous pouvez <a href='modifier_mot_de_passe.php'>modifier le mot de passe</a>.</p>";
    } else {
        // Autre type de compte, afficher un message par défaut
        echo "<h1>Bienvenue!</h1>";
    }
} else {
    // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: page_de_connexion.php");
    exit();
}
?>

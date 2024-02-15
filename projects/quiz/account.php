<?php
// Dans votre page principale (index.php)
session_start();

require_once 'config.php';
require_once 'connexion_traitement.php'; // Assurez-vous de l'inclure ici

// Vérifiez si l'utilisateur est connecté et a une variable de session 'user_type' définie
if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];

    // Affichez le contenu en fonction du type de compte
    if ($user_type == $admin) {
        // Afficher le contenu pour un compte admin
        echo "<h1>Bienvenue, $login_data!</h1>";
    } elseif ($user_type == $util) {
        // Afficher le contenu pour un compte util
        echo "<h1>Bienvenue, Utilisateur!</h1>";
    } else { 
        // Autre type de compte, afficher un message par défaut
        echo "<h1>Bienvenue!</h1>";
    }
} else {
    // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: acces.html");
    exit();
}
?>

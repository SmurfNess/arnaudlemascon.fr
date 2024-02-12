<?php
// Dans votre fichier connexion_traitement.php

session_start();

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire de connexion
    $login_username = $_POST['login_username'];
    $login_password = $_POST['login_password'];

    // Établir la connexion à la base de données
    try {
        $connexion = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    } catch (PDOException $e) {
        die("<p class='message'>Erreur lors de la connexion à la base de données : " . $e->getMessage() . "</p>");
    }

    // Vérifier si le nom d'utilisateur existe
    $check_username_query = $connexion->prepare("SELECT * FROM access WHERE username = ?");
    $check_username_query->execute([$login_username]);
    $user_data = $check_username_query->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        echo "<p class='message'>Le nom d'utilisateur n'existe pas.</p>";
    } else {
        // Vérifier si le mot de passe correspond
        if (password_verify($login_password, $user_data['password'])) {
            // Mot de passe correct, stocker le nom d'utilisateur dans une variable de session
            $_SESSION['login_username'] = $login_username;
            $_SESSION['user_type'] = $user_data['type'];  // Stocker le type de compte dans la session si nécessaire
            // Rediriger vers la page account.php ou autre
            header("Location: account.php");
            exit();
        } else {
            echo "<p class='message'>Mot de passe incorrect.</p>";
        }
    }

    // Fermer la connexion à la base de données
    $connexion = null;
}

?>

<?php
session_start();

require_once 'config.php';

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    
    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        if ($user_type == $admin) {

            <form method="post" action="team.php">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" required><br><br>
                
                <label for="class">Classe :</label>
                <input type="text" id="class" name="class" required><br><br>
                
                <label for="owner">Propriétaire :</label>
                <input type="text" id="owner" name="owner" required><br><br>
                
                <input type="submit" value="Envoyer">
            </form>
            <?php

        } elseif ($user_type == $util) {

        } else { 
            echo "<h1>Bienvenue..</h1>";
        }
    } else {
        header("Location: acces.html");
        exit();
    }
} else {
    header("Location: acces.html");
    exit();
}
?>

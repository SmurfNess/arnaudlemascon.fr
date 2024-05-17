<?php
session_start();

// Inclure le fichier de configuration de la base de données
require_once 'config.php';

// Vérifier si les données du formulaire sont envoyées via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si l'utilisateur est connecté et est administrateur
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == $admin) {
        // Vérifier si les champs requis sont présents dans la demande
        if (isset($_POST['name']) && isset($_POST['class'])) {
            // Récupérer les données du formulaire
            $name = $_POST['name'];
            $class = $_POST['class'];

            // Récupérer le nom d'utilisateur à partir de la session
            if (isset($_SESSION['login_username'])) {
                $owner = $_SESSION['login_username'];

                // Connexion à la base de données avec PDO
                try {
                    $pdo = new PDO("mysql:host={$databaseConfig['server']};dbname={$databaseConfig['database']}", $databaseConfig['username'], $databaseConfig['password']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Préparer la requête d'insertion
                    $stmt = $pdo->prepare("INSERT INTO players (name, class, owner) VALUES (:name, :class, :owner)");
                    
                    // Exécuter la requête en liant les valeurs des paramètres
                    $stmt->execute(['name' => $name, 'class' => $class, 'owner' => $owner]);
                    
                    echo "Données insérées avec succès.";
                } catch (PDOException $e) {
                    echo "Erreur d'insertion : " . $e->getMessage();
                }
            } else {
                echo "Nom d'utilisateur non disponible dans la session.";
            }
        } else {
            echo "Tous les champs sont requis.";
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour effectuer cette action.";
    }
} else {
    echo "Accès refusé.";
}
?>

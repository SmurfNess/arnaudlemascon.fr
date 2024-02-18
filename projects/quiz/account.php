<?php

session_start();

require_once 'config.php';
// Inutile d'inclure le fichier connexion_traitement.php car nous allons gérer la connexion ici

// Vérifie si l'utilisateur est connecté et a une variable de session 'user_type' définie
if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    
    // Vérifie si le nom d'utilisateur est défini dans la session
    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        // Affiche le contenu en fonction du type de compte
        if ($user_type == $admin) {
            // Vérifie si le formulaire est soumis
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Créez une nouvelle connexion PDO
                try {
                    $connexion = new PDO("mysql:host={$databaseConfig['server']};dbname={$databaseConfig['database']}", $databaseConfig['username'], $databaseConfig['password']);
                    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    echo "Erreur de connexion : " . $e->getMessage();
                }
                
                // Vérifiez si toutes les données nécessaires sont fournies et validez l'URL de l'image
                if (isset($_POST['answer']) && isset($_POST['image']) && isset($_POST['prop1']) && isset($_POST['prop2']) && isset($_POST['prop3']) && isset($_POST['question']) && isset($_POST['qtype'])) {
                    // Récupérer les données du formulaire
                    $answer = $_POST['answer'];
                    $image = $_POST['image'];
                    $prop1 = $_POST['prop1'];
                    $prop2 = $_POST['prop2'];
                    $prop3 = $_POST['prop3'];
                    $question = $_POST['question'];
                    $qtype = $_POST['qtype'];
                    
                    // Valider l'URL de l'image
                    if (filter_var($image, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                        // Vérifier si les valeurs de answer, prop1, prop2 et prop3 sont différentes
                        $values = [$answer, $prop1, $prop2, $prop3];
                        if (count($values) === count(array_unique($values))) {
                            try {
                                // Préparer et exécuter la requête SQL pour insérer les données dans la base de données
                                $query = "INSERT INTO data (answer, image, prop1, prop2, prop3, question, Qtype) VALUES (?, ?, ?, ?, ?, ?, ?)";
                                $stmt = $connexion->prepare($query);
                                $stmt->execute([$answer, $image, $prop1, $prop2, $prop3, $question, $qtype]);
                                echo "<h1>Nouvelle ligne ajoutée avec succès!</h1>";
                            } catch (PDOException $e) {
                                echo "Erreur lors de l'ajout de la ligne : " . $e->getMessage();
                            }
                        } else {
                            echo "<h1>Les valeurs de answer, prop1, prop2 et prop3 doivent être différentes.</h1>";
                        }
                    } else {
                        echo "<h1>L'URL de l'image n'est pas valide.</h1>";
                    }
                } else {
                    echo "<h1>Tous les champs du formulaire sont requis.</h1>";
                }
            }
            // Affiche le formulaire pour ajouter une nouvelle ligne
            ?>
            <h1>Ajouter une nouvelle ligne</h1>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                Réponse: <input type="text" name="answer"><br>
                Image: <input type="text" name="image"><br>
                Prop1: <input type="text" name="prop1"><br>
                Prop2: <input type="text" name="prop2"><br>
                Prop3: <input type="text" name="prop3"><br>
                Question: <input type="text" name="question"><br>
                Type de question: 
                <input type="radio" name="qtype" value="duo">Duo
                <input type="radio" name="qtype" value="carré">Carré
                <input type="radio" name="qtype" value="cash">Cash
                <br><br>
                <input type="submit" name="submit" value="Ajouter">
            </form>
            <?php
        } elseif ($user_type == $util) {
            echo "<h1>BIENVENUE !</h1>";
        } else { 
            echo "<h1>Bienvenue..</h1>";
        }
    } else {
        // Le nom d'utilisateur n'est pas défini dans la session, redirigez l'utilisateur vers la page de connexion
        header("Location: acces.html");
        exit();
    }
} else {
    // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: acces.html");
    exit();
}
?>

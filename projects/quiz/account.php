<?php
session_start();

require_once 'config.php';

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    
    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        if ($user_type == $admin) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['answer'], $_POST['image'], $_POST['prop1'], $_POST['prop2'], $_POST['prop3'], $_POST['question'])) {
                    $answer = $_POST['answer'];
                    $image = $_POST['image'];
                    $prop1 = $_POST['prop1'];
                    $prop2 = $_POST['prop2'];
                    $prop3 = $_POST['prop3'];
                    $question = $_POST['question'];

                    if (!filter_var($image, FILTER_VALIDATE_URL)) {
                        echo "<h1>L'URL de l'image n'est pas valide.</h1>";
                    } elseif (substr($question, -1) !== '?') {
                        echo "<h1>La question doit se terminer par un point d'interrogation (?).</h1>";
                    } elseif ($answer === $prop1 || $answer === $prop2 || $answer === $prop3 || $prop1 === $prop2 || $prop1 === $prop3 || $prop2 === $prop3) {
                        echo "<h1>Les valeurs de answer, prop1, prop2 et prop3 doivent être différentes.</h1>";
                    } else {
                        try {
                            $connexion = new PDO("mysql:host={$databaseConfig['server']};dbname={$databaseConfig['database']}", $databaseConfig['username'], $databaseConfig['password']);
                            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo "Erreur de connexion : " . $e->getMessage();
                        }

                        try {
                            $query = "INSERT INTO data (answer, image, prop1, prop2, prop3, question) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt = $connexion->prepare($query);
                            $stmt->execute([$answer, $image, $prop1, $prop2, $prop3, $question]);
                            echo "<h1>Nouvelle ligne ajoutée avec succès!</h1>";
                        } catch (PDOException $e) {
                            echo "Erreur lors de l'ajout de la ligne : " . $e->getMessage();
                        }
                    }
                } else {
                    echo "<h1>Tous les champs du formulaire sont requis.</h1>";
                }
            }
            ?>
            <h1>Ajouter une nouvelle ligne</h1>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                Réponse: <input type="text" name="answer"><br>
                Image: <input type="text" name="image"><br>
                Prop1: <input type="text" name="prop1"><br>
                Prop2: <input type="text" name="prop2"><br>
                Prop3: <input type="text" name="prop3"><br>
                Question: <input type="text" name="question"><br>
                <input type="submit" name="submit" value="Ajouter">
            </form>
            <?php
        } elseif ($user_type == $util) {
            echo "<h1>BIENVENUE !</h1>";
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

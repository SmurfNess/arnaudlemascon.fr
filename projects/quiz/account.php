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
                <input type="submit" name="submit" value="Ajouter" disabled>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const inputs = document.querySelectorAll('input[type="text"]');
                    inputs.forEach(input => {
                        input.addEventListener('input', () => {
                            const answer = document.querySelector('input[name="answer"]').value;
                            const prop1 = document.querySelector('input[name="prop1"]').value;
                            const prop2 = document.querySelector('input[name="prop2"]').value;
                            const prop3 = document.querySelector('input[name="prop3"]').value;
                            const question = document.querySelector('input[name="question"]').value;
                            const image = document.querySelector('input[name="image"]').value;
                            const submitButton = document.querySelector('input[name="submit"]');
                            
                            if (answer !== '' && prop1 !== '' && prop2 !== '' && prop3 !== '' && question !== '' && image !== '') {
                                if (answer !== prop1 && answer !== prop2 && answer !== prop3) {
                                    if (question.endsWith('?')) {
                                        submitButton.disabled = false;
                                    }
                                }
                            } else {
                                submitButton.disabled = true;
                            }
                        });
                    });
                });
            </script>
            <?php
            // Vérifie si le formulaire est soumis
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                echo "<h1>La validation du formulaire est impossible tant que les données ne sont pas correctes.</h1>";
            }
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

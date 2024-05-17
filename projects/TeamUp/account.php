<?php
session_start();

require_once 'config.php';

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    
    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        if ($user_type == $admin) {
            // Si l'utilisateur est un administrateur, afficher le formulaire
            ?>
            <form method="post" action="team.php">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" required><br><br>
                
                <label for="class">Classe :</label>
                <input type="text" id="class" name="class" required><br><br>
                
                <input type="submit" value="Envoyer">
            </form>
            <?php

            
        } elseif ($user_type == $util) {
            try {
                $connexion = new PDO("mysql:host={$databaseConfig['server']};dbname={$databaseConfig['database']}", $databaseConfig['username'], $databaseConfig['password']);
                $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT * FROM data";
                $stmt = $connexion->query($query);
                $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
            }
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cartes</title>
            <style>
                .card {
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    padding: 10px;
                    margin: 10px;
                    width: 400px;
                    height: 400px;
                    display: inline-block;
                    position: relative;
                }

                .card img {
                    width: 100%;
                    height: 300px;
                    object-fit: cover;
                }

                .card .button-container {
                    position: absolute;
                    bottom: 10px;
                    left: 10px;
                }

                .card .button-container button {
                    margin-right: 5px;
                }

                .card .score {
                    position: absolute;
                    top: 10px;
                    left: 10px;
                    background-color: rgba(255, 0, 0, 0.7);
                    padding: 5px;
                    color: white;
                    border-radius: 5px;
                    display: none;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="count">Votre score est de <span id="score">0</span></div>
                <?php foreach ($cards as $card): ?>
                    <div class="card">
                        <div class="score"></div>
                        <div class="card-number"><?php echo $card['number']; ?></div>
                        <h2 class="card-title"><?php echo $card['Question']; ?></h2>
                        <img src="<?php echo $card['Image']; ?>" alt="Image">
                        <div class="button-container">
                            <button class="answer"><?php echo $card['Answer']; ?></button>
                            <button class="prop"><?php echo $card['prop1']; ?></button>
                            <button class="prop"><?php echo $card['prop2']; ?></button>
                            <button class="prop"><?php echo $card['prop3']; ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
                var score = 0;

                document.querySelectorAll('.card').forEach(function(card) {
                    var buttons = card.querySelectorAll('.button-container button');
                    var answerButton = card.querySelector('.answer');
                    var scoreDisplay = card.querySelector('.score');
                    var countDisplay = document.getElementById('score');

                    buttons.forEach(function(button) {
                        button.addEventListener('click', function() {
                            buttons.forEach(function(btn) {
                                btn.disabled = true;
                            });

                            if (button === answerButton) {
                                score++;
                                scoreDisplay.textContent = "+1";
                            } else {
                                scoreDisplay.textContent = "Mauvaise réponse";
                            }

                            scoreDisplay.style.display = 'block';
                            countDisplay.textContent = score; // Mise à jour du score dans la div count
                        });
                    });
                });
            </script>
        </body>
        </html>

        <?php
        } else { 
            echo "<h1>Bienvenue..</h1>";
        }
    } else {
        header("Location: teamup.html");
        exit();
    }
} else {
    header("Location: teamup.html");
    exit();
}
?>

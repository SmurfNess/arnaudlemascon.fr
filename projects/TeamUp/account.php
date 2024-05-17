<?php
session_start();

require_once 'config.php';

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    
    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        if ($user_type == $admin) {
            // Afficher le formulaire pour ajouter des joueurs
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

                // Sélectionner les joueurs de l'utilisateur connecté
                $query = "SELECT name, class, team FROM player WHERE owner = :owner";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
            }
            ?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Équipe</title>
            </head>
            <body>
                <h1>Vos joueurs</h1>
                <ul>
                    <?php foreach ($players as $player): ?>
                        <li><?php echo $player['name']; ?> - <?php echo $player['class']; ?> - <?php echo $player['team']; ?></li>
                    <?php endforeach; ?>
                </ul>
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

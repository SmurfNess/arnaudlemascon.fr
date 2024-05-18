<?php
session_start();

require_once 'config.php';

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    
    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        if ($user_type == $admin) {
            try {
                $connexion = new PDO("mysql:host={$databaseConfig['server']};dbname={$databaseConfig['database']}", $databaseConfig['username'], $databaseConfig['password']);
                $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_player'])) {
                    try {
                        $query = "DELETE FROM players WHERE name = :player_name AND class = :player_class AND owner = :owner";
                        $stmt = $connexion->prepare($query);
                        $stmt->bindParam(':player_name', $_POST['player_name']);
                        $stmt->bindParam(':player_class', $_POST['player_class']);
                        $stmt->bindParam(':owner', $login_username);
                        $stmt->execute();

                        // Rediriger vers la même page pour afficher la liste mise à jour des joueurs
                        header("Location: team.php");
                        exit();
                    } catch (PDOException $e) {
                        echo "Erreur de suppression : " . $e->getMessage();
                    }
                }

                // Sélectionner les joueurs de l'utilisateur connecté
                $query = "SELECT name, class, team FROM players WHERE owner = :owner";
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
                        <li>
                            <?php echo $player['name']; ?> - <?php echo $player['class']; ?> - <?php echo $player['team']; ?>
                            <form method="post" action="team.php">
                                <input type="hidden" name="delete_player" value="true">
                                <input type="hidden" name="player_name" value="<?php echo $player['name']; ?>">
                                <input type="hidden" name="player_class" value="<?php echo $player['class']; ?>">
                                <input type="submit" value="Supprimer">
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </body>
            </html>

            <?php
        } elseif ($user_type == $util) {
            // Si l'utilisateur est un utilisateur ordinaire, afficher un message de bienvenue
            echo "<h1>Bienvenue..</h1>";
        } else {
            // Si le type d'utilisateur n'est ni admin ni utilisateur, rediriger vers la page de connexion
            header("Location: teamup.html");
            exit();
        }
    } else {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        header("Location: teamup.html");
        exit();
    }
} else {
    // Si le type d'utilisateur n'est pas défini, rediriger vers la page de connexion
    header("Location: teamup.html");
    exit();
}
?>

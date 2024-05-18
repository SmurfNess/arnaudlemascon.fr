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

                // Traitement de l'ajout d'un joueur
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['class']) && !isset($_POST['delete_player']) && !isset($_POST['generate_teams'])) {
                    $name = $_POST['name'];
                    $class = $_POST['class'];

                    $query = "INSERT INTO players (name, class, owner) VALUES (:name, :class, :owner)";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':class', $class);
                    $stmt->bindParam(':owner', $login_username);
                    $stmt->execute();
                }

                // Traitement de la suppression d'un joueur
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_player'])) {
                    $player_name = $_POST['player_name'];
                    $player_class = $_POST['player_class'];

                    $query = "DELETE FROM players WHERE name = :player_name AND class = :player_class AND owner = :owner";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':player_name', $player_name);
                    $stmt->bindParam(':player_class', $player_class);
                    $stmt->bindParam(':owner', $login_username);
                    $stmt->execute();
                }

                // Traitement du filtrage et de la génération des équipes
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider'])) {
                    // Filtrer les joueurs par classe sélectionnée
                    $selected_classes = isset($_POST['selected_classes']) ? $_POST['selected_classes'] : [];
                    if (!empty($selected_classes)) {
                        $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                        $query = "SELECT name, class, team FROM players WHERE owner = ? AND class IN ($placeholders)";
                        $stmt = $connexion->prepare($query);
                        $stmt->execute(array_merge([$login_username], $selected_classes));
                        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Génération des équipes
                        $team_size = max(2, count($players) / 2);
                        shuffle($players);

                        $teams = [];
                        foreach ($players as $index => $player) {
                            $team_number = (int)($index / $team_size) + 1;
                            $teams[$team_number][] = $player;
                        }

                        // Mise à jour des équipes dans la base de données
                        foreach ($teams as $team_number => $team_players) {
                            foreach ($team_players as $player) {
                                $query = "UPDATE players SET team = :team WHERE name = :name AND class = :class AND owner = :owner";
                                $stmt = $connexion->prepare($query);
                                $stmt->bindParam(':team', $team_number);
                                $stmt->bindParam(':name', $player['name']);
                                $stmt->bindParam(':class', $player['class']);
                                $stmt->bindParam(':owner', $login_username);
                                $stmt->execute();
                            }
                        }

                        // Rediriger pour afficher la liste mise à jour
                        header("Location: team.php");
                        exit();
                    }
                }

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
                <form method="post" action="team.php">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required><br><br>
                    
                    <label for="class">Classe :</label>
                    <input type="text" id="class" name="class" required><br><br>
                    
                    <input type="submit" value="Envoyer">
                </form>

                <h2>Filtrer par classe</h2>
                <form method="post" action="team.php">
                    <select name="selected_classes[]" multiple>
                        <option value="all">All</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class; ?>" <?php echo in_array($class, $selected_classes) ? 'selected' : ''; ?>>
                                <?php echo $class; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" name="valider" value="Valider">
                </form>

                <h2>Liste des joueurs</h2>
                <ul>
                    <?php foreach ($players as $player): ?>
                        <li>
                            <?php echo $player['name']; ?> - <?php echo $player['class']; ?> - <?php echo $player['team']; ?>
                            <form method="post" action="team.php" style="display:inline;">
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

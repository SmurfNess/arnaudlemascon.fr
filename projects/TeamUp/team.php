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

                // Sélectionner les joueurs de l'utilisateur connecté
                $query = "SELECT name, class, team FROM players WHERE owner = :owner";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Récupérer les classes distinctes des joueurs
                $query = "SELECT DISTINCT class FROM players WHERE owner = :owner";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Génération des équipes en fonction des classes sélectionnées
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_teams']) && isset($_POST['team_size']) && isset($_POST['selected_classes'])) {
                    $team_size = max(2, (int)$_POST['team_size']);
                    $selected_classes = $_POST['selected_classes'];

                    // Sélectionner les joueurs des classes sélectionnées
                    $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                    $query = "SELECT name, class, team FROM players WHERE owner = ? AND class IN ($placeholders)";
                    $stmt = $connexion->prepare($query);
                    $stmt->execute(array_merge([$login_username], $selected_classes));
                    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mélanger les joueurs et les répartir dans les équipes
        shuffle($players);
        $teams = [];
        foreach ($players as $index => $player) {
            $team_number = (int)($index / $team_size) + 1;
            $teams[$team_number][] = $player;
        }

        // Vérifier si une équipe a moins de 2 joueurs
        foreach ($teams as $team_number => $team_players) {
            if (count($team_players) < 2) {
                // Ajouter les joueurs restants à d'autres équipes
                $remaining_players = array_splice($team_players, 2 - count($team_players));
                foreach ($remaining_players as $remaining_player) {
                    $random_team = array_rand($teams);
                    $teams[$random_team][] = $remaining_player;
                }
            }
        }

        // Mettre à jour les équipes dans la base de données...

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

                    // Après la mise à jour des équipes, récupérer uniquement les joueurs des équipes sélectionnées
                    $query = "SELECT name, class, team FROM players WHERE owner = :owner AND class IN ($placeholders)";
                    $stmt = $connexion->prepare($query);
                    $stmt->execute(array_merge([$login_username], $selected_classes));
                    $selected_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Afficher uniquement les joueurs des équipes sélectionnées
                    foreach ($selected_players as $player): ?>
                        <tr>
                            <td><?php echo $player['name']; ?></td>
                            <td><?php echo $player['class']; ?></td>
                            <td><?php echo isset($player['team']) ? $player['team'] : ''; ?></td>
                        </tr>
                    <?php endforeach;

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
    <h1>Gestion des joueurs et des équipes</h1>

    <section>
        <h2>Ajouter un joueur</h2>
        <form method="post" action="team.php">
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required><br><br>
                                
            <label for="class">Classe :</label>
            <input type="text" id="class" name="class" required><br><br>
            
            <input type="submit" value="Ajouter">
        </form>
    </section>

    <section>
        <h2>Générer les équipes</h2>
        <form method="post" action="team.php">
            <label for="team_size">Taille de l'équipe :</label>
            <input type="number" id="team_size" name="team_size" value="2" min="2" required><br><br>
            
            <label for="selected_classes[]">Sélectionner les classes :</label>
            <select name="selected_classes[]" multiple>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                <?php endforeach; ?>
            </select><br><br>
            
            <input type="hidden" name="generate_teams" value="true">
            <input type="submit" value="Générer les équipes">
        </form>
    </section>

    <section>
        <h2>Résultat de la génération d'équipes</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Classe</th>
                    <th>Équipe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?php echo $player['name']; ?></td>
                        <td><?php echo $player['class']; ?></td>
                        <td><?php echo isset($player['team']) ? $player['team'] : ''; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Liste complète des joueurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Classe</th>
                    <th>Équipe</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?php echo $player['name']; ?></td>
                        <td><?php echo $player['class']; ?></td>
                        <td><?php echo isset($player['team']) ? $player['team'] : ''; ?></td>
                        <td>
                            <form method="post" action="team.php" style="display:inline;">
                                <input type="hidden" name="delete_player" value="true">
                                <input type="hidden" name="player_name" value="<?php echo $player['name']; ?>">
                                <input type="hidden" name="player_class" value="<?php echo $player['class']; ?>">
                                <input type="submit" value="Supprimer">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
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


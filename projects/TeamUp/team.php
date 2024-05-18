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

                // Récupérer les classes distinctes des joueurs
                $query = "SELECT DISTINCT class FROM players WHERE owner = :owner";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Sélectionner les classes filtrées
                $selected_classes = isset($_GET['class_filter']) ? $_GET['class_filter'] : [];

                // Initialisation du tableau des joueurs
                $players_by_class = [];

                if (!empty($selected_classes)) {
                    // Sélectionner les joueurs de l'utilisateur connecté en fonction du filtre
                    foreach ($selected_classes as $class) {
                        $query = "SELECT name, class, team FROM players WHERE owner = ? AND class = ?";
                        $stmt = $connexion->prepare($query);
                        $stmt->execute([$login_username, $class]);
                        $players_by_class[$class] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                } else {
                    // Sélectionner tous les joueurs de l'utilisateur connecté
                    $query = "SELECT name, class, team FROM players WHERE owner = ?";
                    $stmt = $connexion->prepare($query);
                    $stmt->execute([$login_username]);
                    $players_by_class['all'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                // Génération des équipes aléatoires
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_teams']) && isset($_POST['team_size'])) {
                    $team_size = intval($_POST['team_size']);
                    if ($team_size >= 2) {
                        $all_players = [];
                        foreach ($players_by_class as $players) {
                            $all_players = array_merge($all_players, $players);
                        }

                        shuffle($all_players);

                        $team_number = 1;
                        $remaining_players = [];
                        foreach ($all_players as $index => $player) {
                            if ($index % $team_size === 0 && count($remaining_players) >= 2) {
                                $team_number++;
                            }
                            $remaining_players[] = $player;
                            if (count($remaining_players) == $team_size) {
                                foreach ($remaining_players as $team_player) {
                                    $query = "UPDATE players SET team = ? WHERE name = ? AND class = ? AND owner = ?";
                                    $stmt = $connexion->prepare($query);
                                    $stmt->execute([$team_number, $team_player['name'], $team_player['class'], $login_username]);
                                }
                                $remaining_players = [];
                            }
                        }

                        if (count($remaining_players) > 0) {
                            foreach ($remaining_players as $team_player) {
                                $query = "UPDATE players SET team = ? WHERE name = ? AND class = ? AND owner = ?";
                                $stmt = $connexion->prepare($query);
                                $stmt->execute([rand(1, $team_number), $team_player['name'], $team_player['class'], $login_username]);
                            }
                        }

                        // Rafraîchir la page pour afficher les nouvelles équipes
                        header("Location: team.php");
                        exit();
                    } else {
                        echo "La taille de l'équipe doit être au moins 2.";
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

                <form method="get" action="team.php">
                    <label for="class_filter">Filtrer par classe :</label>
                    <select id="class_filter" name="class_filter[]" multiple>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['class']; ?>" <?php echo in_array($class['class'], $selected_classes) ? 'selected' : ''; ?>>
                                <?php echo $class['class']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Filtrer">
                </form>

                <form method="post" action="team.php">
                    <label for="team_size">Taille de l'équipe :</label>
                    <input type="number" id="team_size" name="team_size" min="2" required>
                    <input type="hidden" name="generate_teams" value="true">
                    <input type="submit" value="Générer des équipes">
                </form>

                <?php if (!empty($selected_classes)): ?>
                    <?php foreach ($selected_classes as $class): ?>
                        <h2>Classe : <?php echo $class; ?></h2>
                        <ul>
                            <?php if (isset($players_by_class[$class])): ?>
                                <?php foreach ($players_by_class[$class] as $player): ?>
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
                            <?php else: ?>
                                <li>Aucun joueur trouvé dans cette classe.</li>
                            <?php endif; ?>
                        </ul>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h2>Tous les joueurs</h2>
                    <ul>
                        <?php foreach ($players_by_class['all'] as $player): ?>
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
                <?php endif; ?>
            </body>
            </html>

           

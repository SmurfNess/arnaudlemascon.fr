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

                // Génération des équipes en fonction des classes sélectionnées
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_teams']) && isset($_POST['team_size'])) {
                    $team_size = max(2, (int)$_POST['team_size']);
                    $selected_classes = $_POST['selected_classes'] ?? [];

                    if (empty($selected_classes)) {
                        $query = "SELECT name, class, team FROM players WHERE owner = :owner";
                        $stmt = $connexion->prepare($query);
                        $stmt->bindParam(':owner', $login_username);
                        $stmt->execute();
                        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                        $query = "SELECT name, class, team FROM players WHERE owner = ? AND class IN ($placeholders)";
                        $stmt = $connexion->prepare($query);
                        $stmt->execute(array_merge([$login_username], $selected_classes));
                        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    // Mélanger les joueurs aléatoirement
                    shuffle($players);

                    // Répartir les joueurs en équipes
                    $teams = [];
                    $team_number = 1;
                    $players_count = count($players);
                    
                    for ($i = 0; $i < $players_count; $i += $team_size) {
                        $team = array_slice($players, $i, $team_size);
                        
                        // Ajouter l'équipe au tableau des équipes
                        $teams[$team_number++] = $team;
                    }
                    
                    // Ajouter les joueurs restants aux équipes déjà complètes
                    $remaining_players = $players_count % $team_size;
                    if ($remaining_players > 0 && $remaining_players < $team_size / 2) {
                        $team_number = 1;
                        for ($i = $players_count - $remaining_players; $i < $players_count; $i++) {
                            $teams[$team_number++ % (int)ceil($players_count / $team_size)][] = $players[$i];
                        }
                    }

                    // Mettre à jour les équipes dans la base de données
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

                    // Récupérer les joueurs pour affichage
                    $query = "SELECT name, class, team FROM players WHERE owner = :owner ORDER BY team ASC";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':owner', $login_username);
                    $stmt->execute();
                    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

// Récupérer les équipes avec un nombre de joueurs inférieur ou égal à la moitié de la taille d'équipe
$query = "SELECT team, COUNT(*) AS players_count FROM players WHERE owner = :owner GROUP BY team HAVING players_count =< :half_team_size";
$stmt = $connexion->prepare($query);
$stmt->bindParam(':owner', $login_username);
$half_team_size = ceil($team_size / 2);
$stmt->bindParam(':half_team_size', $half_team_size, PDO::PARAM_INT);
$stmt->execute();
$teams_with_few_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Déplacer chaque joueur de ces équipes dans une équipe complète dans l'ordre croissant
foreach ($teams_with_few_players as $team_info) {
    $team_number = $team_info['team'];
    $players_to_move = $team_size - $team_info['players_count'];

    // Récupérer les joueurs à déplacer de cette équipe
    $query = "SELECT name, class FROM players WHERE owner = :owner AND team = :team_number ORDER BY class ASC LIMIT :players_to_move";
    $stmt = $connexion->prepare($query);
    $stmt->bindParam(':owner', $login_username);
    $stmt->bindParam(':team_number', $team_number, PDO::PARAM_INT);
    $stmt->bindParam(':players_to_move', $players_to_move, PDO::PARAM_INT);
    $stmt->execute();
    $players_to_move = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trouver l'équipe disponible suivante
    $next_team_number = $team_number + 1;
    if ($next_team_number > count($teams)) {
        $next_team_number = 1;
    }

    // Déplacer les joueurs dans l'équipe suivante
    foreach ($players_to_move as $player) {
        $query = "UPDATE players SET team = :next_team_number WHERE name = :name AND class = :class AND owner = :owner";
        $stmt = $connexion->prepare($query);
        $stmt->bindParam(':next_team_number', $next_team_number, PDO::PARAM_INT);
        $stmt->bindParam(':name', $player['name']);
        $stmt->bindParam(':class', $player['class']);
        $stmt->bindParam(':owner', $login_username);
        $stmt->execute();

        // Mettre à jour l'équipe suivante pour le prochain joueur
        $next_team_number++;
        if ($next_team_number > count($teams)) {
            $next_team_number = 1;
        }
    }
}

// Requête pour récupérer les joueurs après déplacement
$query = "SELECT name, class, team FROM players WHERE owner = :owner ORDER BY team ASC";
$stmt = $connexion->prepare($query);
$stmt->bindParam(':owner', $login_username);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);


                // Nouvelle requête pour récupérer tous les joueurs du propriétaire
                $query = "SELECT name, class, team FROM players WHERE owner = :owner ORDER BY class ASC;";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $all_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Récupérer les classes distinctes des joueurs
                $query = "SELECT DISTINCT class FROM players WHERE owner = :owner";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
            }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeamUp</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <h2 style="text-align: center;">Gestion des joueurs et des équipes</h2>
    <div class="row justify-content-center">
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <section>
                <h4>Ajouter un joueur</h4>
                <form method="post" action="team.php">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required><br><br>
                                    
                    <label for="class">Classe :</label>
                    <input type="text" id="class" name="class" required><br><br>
                    
                    <input type="submit" value="Ajouter">
                </form>
            </section>
        </div>
        
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <section>
                <h4>Générer les équipes</h4>
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
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <section>
                <h4>Population par classe</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Classe</th>
                            <th>Population</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Effectuer une requête pour compter le nombre de joueurs par classe
                        $query = "SELECT class, COUNT(*) AS population FROM players WHERE owner = :owner GROUP BY class";
                        $stmt = $connexion->prepare($query);
                        $stmt->bindParam(':owner', $login_username);
                        $stmt->execute();
                        $class_population = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Afficher les résultats
                        foreach ($class_population as $class_data) {
                            echo "<tr><td>{$class_data['class']}</td><td>{$class_data['population']}</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <section>
                <h4>Résultat de la génération d'équipes</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Équipe</th>
                            <th>Nom</th>
                            <th>Classe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($players)): ?>
                            <?php foreach ($players as $player): ?>
                                <?php if (isset($player['team']) && in_array($player['class'], $selected_classes)): ?>
                                    <tr class="team-<?php echo $player['team']; ?>">
                                        <td><?php echo $player['team']; ?></td>
                                        <td><?php echo $player['name']; ?></td>
                                        <td><?php echo $player['class']; ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">Aucune équipe générée.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="void">_</div>
            </section>
        </div>
        
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <section>
                <h4>Liste complète des joueurs</h4>
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
                        <?php foreach ($all_players as $player): ?>
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
        </div>
    </div>
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

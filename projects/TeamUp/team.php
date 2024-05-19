<?php
session_start();

require_once 'config.php';

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];

    if (isset($_SESSION['login_username'])) {
        $login_username = $_SESSION['login_username'];

        if ($user_type == 'admin') {
            try {
                $connexion = new PDO("mysql:host={$databaseConfig['server']};dbname={$databaseConfig['database']}", $databaseConfig['username'], $databaseConfig['password']);
                $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $selected_classes = [];

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
                        $teams[$team_number++] = $team;
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
                } else {
                    // Récupérer les joueurs pour affichage initial
                    $query = "SELECT name, class, team FROM players WHERE owner = :owner ORDER BY team ASC";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':owner', $login_username);
                    $stmt->execute();
                    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                // Récupérer les classes distinctes des joueurs
                $query = "SELECT DISTINCT class FROM players WHERE owner = :owner";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Récupérer la population des équipes pour les classes sélectionnées
                if (!empty($selected_classes)) {
                    $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                    $query = "SELECT team, COUNT(*) AS population FROM players WHERE owner = ? AND class IN ($placeholders) GROUP BY team ORDER BY team ASC";
                    $stmt = $connexion->prepare($query);
                    $stmt->execute(array_merge([$login_username], $selected_classes));
                    $team_population = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $query = "SELECT team, COUNT(*) AS population FROM players WHERE owner = :owner GROUP BY team ORDER BY team ASC";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':owner', $login_username);
                    $stmt->execute();
                    $team_population = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
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
                    
                    <input type="submit" name="generate_teams" value="Générer">
                </form>
            </section>
        </div>
    </div>
    
    <h4 class="d-flex justify-content-center">Liste des joueurs</h4>
    <div class="row justify-content-center">
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Classe</th>
                        <th>Équipe</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($player['name']); ?></td>
                            <td><?php echo htmlspecialchars($player['class']); ?></td>
                            <td><?php echo htmlspecialchars($player['team']); ?></td>
                            <td>
                                <form method="post" action="team.php">
                                    <input type="hidden" name="player_name" value="<?php echo htmlspecialchars($player['name']); ?>">
                                    <input type="hidden" name="player_class" value="<?php echo htmlspecialchars($player['class']); ?>">
                                    <button type="submit" name="delete_player" class="btn btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <h4 class="d-flex justify-content-center">Population des équipes</h4>
    <div class="row justify-content-center">
        <div class="col-8 col-sm-4 m-2 d-flex justify-content-center">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Équipe</th>
                        <th>Population</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team_population as $population): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($population['team']); ?></td>
                            <td><?php echo htmlspecialchars($population['population']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
            }
        } else {
            echo "Nom d'utilisateur non trouvé.";
        }
    } else {
        echo "Type d'utilisateur non trouvé.";
    }
} else {
    echo "Vous n'êtes pas connecté.";
}
?>

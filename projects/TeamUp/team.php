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
                        // Afficher le résultat de chaque itération dans la console
                        echo "Équipe $team_number : ";
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

// Récupérer les équipes avec moins de joueurs que la moitié de la taille d'équipe
$query = "SELECT team, COUNT(*) AS players_count FROM players WHERE owner = :owner GROUP BY team HAVING players_count < :half_team_size";
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

                }

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
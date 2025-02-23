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
                    // Mettre à NULL la colonne team pour chaque ligne
                    $query = "UPDATE players SET team = NULL WHERE owner = :owner";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':owner', $login_username);
                    $stmt->execute();

                    $team_size = max(2, (int)$_POST['team_size']);
                    $selected_classes = $_POST['selected_classes'] ?? [];

                    if (empty($selected_classes)) {
                        $query = "SELECT id, name, class FROM players WHERE owner = :owner";
                        $stmt = $connexion->prepare($query);
                        $stmt->bindParam(':owner', $login_username);
                        $stmt->execute();
                        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                        $query = "SELECT id, name, class FROM players WHERE owner = ? AND class IN ($placeholders)";
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
                            $query = "UPDATE players SET team = :team WHERE id = :id";
                            $stmt = $connexion->prepare($query);
                            $stmt->bindParam(':team', $team_number);
                            $stmt->bindParam(':id', $player['id']);
                            $stmt->execute();
                        }
                    }

// Redistribution des joueurs des équipes sous-peuplées
// Récupérer les équipes avec un nombre de joueurs inférieur à la moitié de la taille d'équipe
$query = "SELECT team, COUNT(*) AS players_count FROM players WHERE owner = :owner GROUP BY team HAVING players_count < :half_team_size";
$stmt = $connexion->prepare($query);
$stmt->bindParam(':owner', $login_username);
$half_team_size = ceil($team_size / 2);
$stmt->bindParam(':half_team_size', $half_team_size, PDO::PARAM_INT);
$stmt->execute();
$teams_with_few_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Trier les équipes sous-peuplées par nombre de joueurs décroissant
usort($teams_with_few_players, function($a, $b) {
    return $b['players_count'] - $a['players_count'];
});

// Redistribuer les joueurs des équipes sous-peuplées
foreach ($teams_with_few_players as $team_info) {
    $team_number = $team_info['team'];
    $query = "SELECT id, name, class FROM players WHERE owner = :owner AND team = :team_number";
    $stmt = $connexion->prepare($query);
    $stmt->bindParam(':owner', $login_username);
    $stmt->bindParam(':team_number', $team_number, PDO::PARAM_INT);
    $stmt->execute();
    $players_to_move = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Redistribuer les joueurs aux équipes moins peuplées
foreach ($players_to_move as $player) {
    // Trouver l'équipe avec le moins de joueurs
    $min_team = min(array_column($teams_with_few_players, 'players_count'));
    $target_team = array_search($min_team, array_column($teams_with_few_players, 'players_count'));

    // Ajuster le numéro de l'équipe cible pour commencer à partir de 1
    $target_team += 1;

    // Déplacer le joueur vers l'équipe cible
    $query = "UPDATE players SET team = :target_team WHERE id = :player_id";    
    $stmt = $connexion->prepare($query);
    $stmt->bindParam(':target_team', $target_team, PDO::PARAM_INT);
    $stmt->bindParam(':player_id', $player['id'], PDO::PARAM_INT);
    $stmt->execute();

    // Mettre à jour le compteur de joueurs dans l'équipe cible
    $teams_with_few_players[$target_team]['players_count']++;
}

}

                }

                // Nouvelle requête pour récupérer tous les joueurs du propriétaire
                $query = "SELECT name, class, team FROM players WHERE owner = :owner ORDER BY name ASC";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->execute();
                $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion des équipes</title>
    <style>
        /* Styles CSS pour la présentation */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .team {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .team-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .player-list {
            list-style: none;
            padding: 0;
        }
        .player-list li {
            padding: 5px 0;
        }

        .team-1,.team-7,.team-13 {
    background-color: #9999ff;
    color: black;
}

.team-2,.team-8,.team-14 {
    background-color: #7C9ACC;
    color: black;
}

.team-3,.team-9,.team-15 {
    background-color: #90B7CF;
    color: black;
}
.team-4,.team-10,.team-16 {
    background-color: #B3DDCD;
    color: black;
}

.team-5,.team-11,.team-17 {
    background-color: #D0E7DA;
    color: black;
}

.team-6,.team-12,.team-18 {
    background-color: #E8BED3;
    color: black;
}
    </style>
</head>
<body>
<div class="container">
    <h1>Gestion des équipes</h1>

    <!-- Formulaire d'ajout de joueur -->
    <h2>Ajouter un joueur</h2>
    <form method="post">
        <label for="name">Nom du joueur :</label>
        <input type="text" id="name" name="name" required>
        
        <label for="class">Classe :</label>
        <input type="text" id="class" name="class" required>
        
        <button type="submit">Ajouter</button>
    </form>

    <!-- Formulaire de génération des équipes -->
    <h2>Générer des équipes</h2>
    <form method="post">
        <label for="team_size">Taille des équipes :</label>
        <input type="number" id="team_size" name="team_size" required>
        
        <label for="selected_classes">Classes sélectionnées :</label>
        <select id="selected_classes" name="selected_classes[]" multiple>
            <!-- Ajouter des options de classes ici -->
            <?php
            // Générer dynamiquement les options des classes avec le nombre d'élèves par classe
            $query = "SELECT class, COUNT(*) as student_count FROM players WHERE owner = :owner GROUP BY class ORDER BY class ASC";
            $stmt = $connexion->prepare($query);
            $stmt->bindParam(':owner', $login_username);
            $stmt->execute();
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($classes as $class) {
                echo '<option value="' . htmlspecialchars($class['class']) . '">' . htmlspecialchars($class['class']) . ' (' . $class['student_count'] . ' élèves)</option>';
            }
            ?>
        </select>
        
        <button type="submit" name="generate_teams">Générer les équipes</button>
    </form>

<!-- Affichage des équipes -->
<?php
// Récupérer les équipes de la base de données
$query = "SELECT DISTINCT team FROM players WHERE owner = :owner ORDER BY team ASC";
$stmt = $connexion->prepare($query);
$stmt->bindParam(':owner', $login_username);
$stmt->execute();
$team_numbers = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Afficher les équipes
foreach ($team_numbers as $team_number) {
    $query = "SELECT name, class FROM players WHERE owner = :owner AND team = :team ORDER BY name ASC";
    $stmt = $connexion->prepare($query);
    $stmt->bindParam(':owner', $login_username);
    $stmt->bindParam(':team', $team_number);
    $stmt->execute();
    $team_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($team_players)) {
        echo '<div class="team team-' . $team_number . '">';
        echo '<div class="team-title">Équipe ' . $team_number . '</div>';
        echo '<ul class="player-list">';
        foreach ($team_players as $player) {
            echo '<li>' . htmlspecialchars($player['name']) . ' (Classe : ' . htmlspecialchars($player['class']) . ')</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
}
?>


    <!-- Affichage des joueurs -->
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
            <?php
            foreach ($players as $player):
            ?>
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
</div>
</body>
</html>

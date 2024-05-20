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

                    // Redistribution des joueurs des équipes sous-peuplées
                    // Récupérer les équipes avec un nombre de joueurs inférieur à la moitié de la taille d'équipe
                    $query = "SELECT team, COUNT(*) AS players_count FROM players WHERE owner = :owner GROUP BY team HAVING players_count < :half_team_size";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':owner', $login_username);
                    $half_team_size = ceil($team_size / 2);
                    $stmt->bindParam(':half_team_size', $half_team_size, PDO::PARAM_INT);
                    $stmt->execute();
                    $teams_with_few_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Récupérer les joueurs de ces équipes sous-peuplées
                    $players_to_move = [];
                    foreach ($teams_with_few_players as $team_info) {
                        $team_number = $team_info['team'];
                        $query = "SELECT id, name, class FROM players WHERE owner = :owner AND team = :team_number";
                        $stmt = $connexion->prepare($query);
                        $stmt->bindParam(':owner', $login_username);
                        $stmt->bindParam(':team_number', $team_number, PDO::PARAM_INT);
                        $stmt->execute();
                        $players_to_move = array_merge($players_to_move, $stmt->fetchAll(PDO::FETCH_ASSOC));
                    }
                    
// Redistribuer les joueurs dans des équipes non complètes
foreach ($players_to_move as $player) {
    // Rechercher une équipe non complète pour ce joueur
    $team_found = false;
    foreach ($teams_to_fill as &$team_info) {
        if ($team_info['players_count'] < $team_size) {
            // Déplacer le joueur dans l'équipe non vide
            $query = "UPDATE players SET team = :team_number WHERE id = :player_id";
            $stmt = $connexion->prepare($query);
            $stmt->bindParam(':team_number', $team_info['team'], PDO::PARAM_INT);
            $stmt->bindParam(':player_id', $player['id'], PDO::PARAM_INT);
            $stmt->execute();

            // Augmenter le compteur de joueurs de l'équipe
            $team_info['players_count']++;
            
            // Marquer que l'équipe a été trouvée pour ce joueur
            $team_found = true;
            break; // Sortir de la boucle une fois que le joueur est déplacé
        }
    }
    
// Si aucune équipe n'a été trouvée pour le joueur, attribuer une équipe complète
if (!$team_found) {
    // Trouver la première équipe complète
    foreach ($teams_to_fill as &$team_info) {
        if ($team_info['players_count'] < $team_size) {
            // Attribuer l'équipe au joueur
            $query = "UPDATE players SET team = :team_number WHERE id = :player_id";
            $stmt = $connexion->prepare($query);
            $stmt->bindParam(':team_number', $team_info['team'], PDO::PARAM_INT);
            $stmt->bindParam(':player_id', $player['id'], PDO::PARAM_INT);
            $stmt->execute();

            // Augmenter le compteur de joueurs de l'équipe
            $team_info['players_count']++;
            break; // Sortir de la boucle une fois que le joueur est déplacé
        }
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

    // Affichage des équipes
<?php if (isset($team_population)): ?>
    <h2>Équipes générées</h2>
    <?php foreach ($team_population as $team_info): ?>
        <div class="team">
            <div class="team-title">Équipe <?php echo $team_info['team']; ?></div>
            <ul class="player-list">
                <?php
                // Récupérer les joueurs de cette équipe
                $query = "SELECT name, class FROM players WHERE owner = :owner AND team = :team_number ORDER BY name ASC";
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                $stmt->bindParam(':team_number', $team_info['team']);
                $stmt->execute();
                $team_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($team_players as $player): ?>
                    <li><?php echo htmlspecialchars($player['name']); ?> (Classe : <?php echo htmlspecialchars($player['class']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


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

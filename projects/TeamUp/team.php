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
                // ...

                // Traitement de la suppression d'un joueur
                // ...

                // Sélectionner les joueurs de l'utilisateur connecté
                // ...

                // Récupérer les classes distinctes des joueurs
                // ...

                // Génération des équipes en fonction des classes sélectionnées
                // ...

                // Récupérer uniquement les joueurs des classes sélectionnées pour affichage
                $selected_players = [];
                if (isset($team_size, $selected_classes)) {
                    $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                    $query = "SELECT name, class, team FROM players WHERE owner = ? AND class IN ($placeholders) ORDER BY team ASC";
                    $stmt = $connexion->prepare($query);
                    $stmt->execute(array_merge([$login_username], $selected_classes));
                    $selected_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Équipe</title>
</head>
<body>
    <h1>Gestion des joueurs et des équipes</h1>

    <section>
        <!-- Ajouter un joueur -->
    </section>

    <section>
        <!-- Générer les équipes -->
    </section>

    <section>
        <h2>Résultat de la génération d'équipes</h2>
        <table>
            <thead>
                <tr>
                    <th>Équipe</th>
                    <th>Nom</th>
                    <th>Classe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($selected_players as $player): ?>
                    <tr>
                        <td><?php echo isset($player['team']) ? $player['team'] : ''; ?></td>
                        <td><?php echo $player['name']; ?></td>
                        <td><?php echo $player['class']; ?></td>
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

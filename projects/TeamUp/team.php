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
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['class']) && !isset($_POST['delete_player'])) {
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

                // Sélectionner la classe filtrée
                $selected_class = isset($_GET['class_filter']) ? $_GET['class_filter'] : 'all';

                // Sélectionner les joueurs de l'utilisateur connecté en fonction du filtre
                $query = "SELECT name, class, team FROM players WHERE owner = :owner";
                if ($selected_class !== 'all') {
                    $query .= " AND class = :class";
                }
                $stmt = $connexion->prepare($query);
                $stmt->bindParam(':owner', $login_username);
                if ($selected_class !== 'all') {
                    $stmt->bindParam(':class', $selected_class);
                }
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
                <form method="post" action="team.php">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required><br><br>
                    
                    <label for="class">Classe :</label>
                    <input type="text" id="class" name="class" required><br><br>
                    
                    <input type="submit" value="Envoyer">
                </form>

                <form method="get" action="team.php">
                    <label for="class_filter">Filtrer par classe :</label>
                    <select id="class_filter" name="class_filter" onchange="this.form.submit()">
                        <option value="all" <?php echo $selected_class === 'all' ? 'selected' : ''; ?>>Tous</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['class']; ?>" <?php echo $selected_class === $class['class'] ? 'selected' : ''; ?>>
                                <?php echo $class['class']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

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

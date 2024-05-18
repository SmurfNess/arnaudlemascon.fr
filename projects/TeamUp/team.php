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

                // Filtrer les joueurs par classe sélectionnée
                $selected_classes = isset($_POST['selected_classes']) ? $_POST['selected_classes'] : [];
                if (!empty($selected_classes) && $selected_classes[0] !== 'all') {
                    // Filtrage des joueurs par classe sélectionnée
                    $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                    $query = "SELECT name, class, team FROM players WHERE owner = ? AND class IN ($placeholders)";
                    $stmt = $connexion->prepare($query);
                    $stmt->execute(array_merge([$login_username], $selected_classes));
                    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                // Génération des équipes aléatoires
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_teams']) && isset($_POST['team_size'])) {
                    $team_size = max(2, (int)$_POST['team_size']);
                    $filtered_players = $players; // Copie des joueurs non filtrés par défaut

                    if (!empty($selected_classes) && $selected_classes[0] !== 'all') {
                        // Filtrage des joueurs par classe sélectionnée
                        $placeholders = implode(',', array_fill(0, count($selected_classes), '?'));
                        $query = "SELECT name, class, team FROM players WHERE owner = ? AND class IN ($placeholders)";
                        $stmt = $connexion->prepare($query);
                        $stmt->execute(array_merge([$login_username], $selected_classes));
                        $filtered_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    shuffle($filtered_players);

                    $teams = [];
                    foreach ($filtered_players as $index => $player) {
                        $team_number = (int)($index / $team_size) + 1;
                        $teams[$team_number][] = $player;
                    }

                    // Redistribution des joueurs si la dernière équipe a moins de 2 joueurs
                    $last_team = end($teams);
                    if (count($last_team) < 2 && count($teams) > 1) {
                        array_pop($teams);  // Remove the last team
                        foreach ($last_team as $player) {
                            $random_team = array_rand($teams);
                            $teams[$random_team][] = $player;
                        }
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

                    // Après la mise à jour, rediriger pour afficher la liste mise à jour
                    header("Location: team.php");
                    exit();
                }
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
            }
            ?>

            <!DOCTYPE html>
           

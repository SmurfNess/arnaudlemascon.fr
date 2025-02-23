<?php
// Inclure le fichier de configuration
include('config.php');

try {
    // Vérifier si le formulaire de connexion a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire de connexion
        $login_username = $_POST['login_username'];
        $login_password = $_POST['login_password'];

        // Établir la connexion à la base de données avec TCP/IP
        $dsn = "mysql:host=$servername;port=3306;dbname=$dbname;charset=utf8mb4";
        $connexion = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Vérifier si le nom d'utilisateur existe
        $check_username_query = $connexion->prepare("SELECT * FROM access WHERE username = ?");
        $check_username_query->execute([$login_username]);
        $user_data = $check_username_query->fetch();

        if (!$user_data) {
            echo "<p class='message'>Le nom d'utilisateur n'existe pas.</p>";
        } else {
            // Vérifier si le mot de passe correspond
            if (password_verify($login_password, $user_data['password'])) {
                // Démarrer une session et stocker les informations de l'utilisateur
                session_start();
                $_SESSION['user_type'] = $user_data['type'];
                $_SESSION['login_username'] = $login_username;
                header("Location: account.php");
                exit();
            } else {
                echo "<p class='message'>Mot de passe incorrect.</p>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<p class='message'>Erreur de connexion : " . $e->getMessage() . "</p>";

}
?>

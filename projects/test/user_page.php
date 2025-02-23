<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un utilisateur standard
if (!isset($_SESSION['username']) || $_SESSION['type'] != '404') {
    header("Location: index.php");
    exit();
}

// Configuration de la base de données
$servername = "192.168.1.234";
$username = "ziuq";
$password = "quiz?2025!";
$dbname = "quiz";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer toutes les questions de la table "data"
$stmt = $conn->prepare("SELECT * FROM data");
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Utilisateur</title>
</head>
<body>
    <h1>Page Utilisateur</h1>

    <h2>Liste des questions</h2>
    <table border="1">
        <tr>
            <th>Question</th>
            <th>Image</th>
            <th>Réponse</th>
            <th>Propositions</th>
        </tr>
        <?php foreach ($questions as $question): ?>
            <tr>
                <td><?php echo htmlspecialchars($question['Question']); ?></td>
                <td><?php echo htmlspecialchars($question['Image']); ?></td>
                <td><?php echo htmlspecialchars($question['Answer']); ?></td>
                <td>
                    <?php echo htmlspecialchars($question['prop1']) . ', ' . htmlspecialchars($question['prop2']) . ', ' . htmlspecialchars($question['prop3']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

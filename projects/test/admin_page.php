<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['username']) || $_SESSION['type'] != '898989') {
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

// Ajouter une question à la base de données
if (isset($_POST['add_question'])) {
    $question = $_POST['question'];
    $image = $_POST['image'];
    $answer = $_POST['answer'];
    $prop1 = $_POST['prop1'];
    $prop2 = $_POST['prop2'];
    $prop3 = $_POST['prop3'];
    $creator = $_SESSION['username']; // Récupérer le username de l'admin connecté

    $stmt = $conn->prepare("INSERT INTO data (Question, Image, Answer, prop1, prop2, prop3, creator) 
                            VALUES (:question, :image, :answer, :prop1, :prop2, :prop3, :creator)");
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':answer', $answer);
    $stmt->bindParam(':prop1', $prop1);
    $stmt->bindParam(':prop2', $prop2);
    $stmt->bindParam(':prop3', $prop3);
    $stmt->bindParam(':creator', $creator);

    if ($stmt->execute()) {
        echo "Question ajoutée avec succès !";
    } else {
        echo "Erreur lors de l'ajout de la question.";
    }
}


// Supprimer une question de la base de données
if (isset($_POST['delete_question'])) {
    $question_id = $_POST['delete_question'];

    // Préparer la requête de suppression
    $stmt = $conn->prepare("DELETE FROM data WHERE number = :number");
    $stmt->bindParam(':number', $question_id, PDO::PARAM_INT);

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Question supprimée avec succès !";
        // Rediriger pour éviter le renvoi du formulaire après la suppression
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erreur lors de la suppression de la question.";
    }
}

// Récupérer toutes les questions de la table "data"
$stmt = $conn->prepare("SELECT * FROM data");
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les données de la table "data"
$stmt = $conn->prepare("SELECT * FROM data");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Admin</title>
    <style>
.card {
    width: 300px;
    height: 500px; /* Hauteur ajustée pour tenir l'image et les réponses */
    border: 2px solid #000;
    padding: 20px;
    margin: 20px;
    display: inline-block;
    text-align: center;
    background-color: #f9f9f9;
    opacity: 1;
    transition: opacity 0.3s ease;
    position: relative;
    box-sizing: border-box;
    overflow: hidden; /* Assurer que tout débordement est masqué */
}

.image-container {
    width: 100%;
    height: 200px; /* Fixe la hauteur du conteneur de l'image */
    overflow: hidden;
    position: relative;
    margin-bottom: 15px;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.question{
    height:50px;
}

.answers {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Deux colonnes égales pour les boutons */
    gap: 10px;
    margin-top: 10px; /* Ajout d'un peu d'espace entre les réponses et l'image */
    justify-items: center; /* Centrer les boutons */
}

.answer-btn {
    padding: 12px; /* Un peu plus d'espace pour rendre le bouton plus grand */
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    font-size: calc(1vw - 12px); /* Cela ajuste la taille de la police en fonction de la largeur de l'écran */
    width: 90%; /* Utilise une largeur de 90% de l'espace de la cellule pour rendre les boutons uniformes */
    height: 80px;
    box-sizing: border-box; /* Pour que la largeur prenne en compte les paddings */
    text-align: center;
    border-radius: 5px; /* Bords arrondis pour un aspect plus moderne */
    transition: background-color 0.3s ease;
}

.answer-btn:hover {
    background-color: #45a049;
}

.number {
    font-size: 12px;
    position: absolute;
    bottom: 10px;
    right: 10px;
}

.point {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 18px;
    font-weight: bold;
    color: black;
}

.disabled {
    background-color: #ddd !important;
    cursor: not-allowed;
}


    </style>
</head>
<body>
    <h1>Page Admin</h1>

    <h2>Ajouter une question</h2>
    <form method="POST">
        <input type="text" name="question" placeholder="Question" required><br>
        <input type="text" name="image" placeholder="Image (URL)" required><br>
        <input type="text" name="answer" placeholder="Réponse correcte" required><br>
        <input type="text" name="prop1" placeholder="Proposition 1" required><br>
        <input type="text" name="prop2" placeholder="Proposition 2" required><br>
        <input type="text" name="prop3" placeholder="Proposition 3" required><br>
        <button type="submit" name="add_question">Ajouter</button>
    </form>

    <h2>Liste des questions</h2>
<table border="1">
    <tr>
        <th>Question</th>
        <th>Image</th>
        <th>Réponse</th>
        <th>Propositions</th>
        <th>Créateur</th>
        <th>Action</th> <!-- Colonne pour le bouton de suppression -->
    </tr>
    <?php foreach ($questions as $question): ?>
        <tr>
            <td><?php echo htmlspecialchars($question['Question']); ?></td>
            <td><?php echo htmlspecialchars($question['Image']); ?></td>
            <td><?php echo htmlspecialchars($question['Answer']); ?></td>
            <td>
                <?php echo htmlspecialchars($question['prop1']) . ', ' . htmlspecialchars($question['prop2']) . ', ' . htmlspecialchars($question['prop3']); ?>
            </td>
            <td><?php echo htmlspecialchars($question['creator']); ?></td> <!-- Affichage du créateur -->
            <td>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="delete_question" value="<?php echo $question['number']; ?>" style="background:none;border:none;color:red;font-size:20px;">&#10006;</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


    <h2>Cartes de Questions</h2>
    <p>Score: <span id="score">0</span></p>

    <?php foreach ($data as $row): ?>
        <div class="card" id="card-<?php echo htmlspecialchars($row['number']); ?>" data-number="<?php echo htmlspecialchars($row['number']); ?>">
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="Image">
            </div>
            <p><strong><div class="question"><?php echo htmlspecialchars($row['Question']); ?></div></strong></p>

            <?php
                $answers = [
                    $row['Answer'],
                    $row['prop1'],
                    $row['prop2'],
                    $row['prop3']
                ];
                shuffle($answers);
            ?>

            <div class="answers">
                <?php foreach ($answers as $answer): ?>
                    <button class="answer-btn" onclick="handleAnswerClick(this, '<?php echo htmlspecialchars($row['number']); ?>', '<?php echo $answer; ?>', '<?php echo htmlspecialchars($row['Answer']); ?>')">
                        <?php echo htmlspecialchars($answer); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Affichage du point au dessus de la carte -->
            <div class="point" id="point-<?php echo htmlspecialchars($row['number']); ?>">0</div>
            <div class="number"><?php echo htmlspecialchars($row['number']); ?></div>
        </div>
    <?php endforeach; ?>

    <script>
        let score = 0;

        function handleAnswerClick(button, cardNumber, answer, correctAnswer) {
            const card = document.getElementById('card-' + cardNumber);
            const pointDiv = document.getElementById('point-' + cardNumber);

            const buttons = card.querySelectorAll('.answer-btn');
            buttons.forEach(btn => {
                btn.classList.add('disabled');
                btn.disabled = true;
            });

            card.style.opacity = 0.5;

            if (answer === correctAnswer) {
                score += 1;
                pointDiv.innerHTML = "+1";
                pointDiv.style.color = "green"; // Correct answer
            } else {
                score -= 1;
                pointDiv.innerHTML = "-1";
                pointDiv.style.color = "red"; // Incorrect answer
            }

            document.getElementById('score').innerText = score;
        }
    </script>

</body>
</html>

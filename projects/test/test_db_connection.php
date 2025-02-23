<?php
session_start();

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

// Inscription
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $conn->prepare("SELECT * FROM access WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "Ce nom d'utilisateur est déjà pris.";
    } else {
        $stmt = $conn->prepare("INSERT INTO access (username, password, type, other) VALUES (:username, :password, '404', NULL)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        if ($stmt->execute()) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }
}

// Connexion
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM access WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['type'] = $user['type'];
        
        if ($user['type'] == '898989') {
            header("Location: admin_page.php");
            exit();
        } elseif ($user['type'] == '404') {
            header("Location: user_page.php");
            exit();
        } else {
            echo "Type d'utilisateur inconnu.";
        }
    } else {
        echo "Identifiants incorrects.";
    }
}

// Récupérer les données de la table "access"
$stmt = $conn->prepare("SELECT * FROM data");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page d'accès</title>
</head>
<body>
    <h2>Inscription</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit" name="register">S'inscrire</button>
    </form>

    <h2>Connexion</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit" name="login">Se connecter</button>
    </form>

    <?php
session_start();

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

// Récupérer les données de la table "data"
$stmt = $conn->prepare("SELECT * FROM data");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz en Pile</title>
    <style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 100vh;
    margin: 0;
    overflow: hidden;
}

.quiz-wrapper {
    display: flex;
    flex-direction: column; /* Change de ligne à colonne pour empiler les deux tas */
    gap: 20px; /* Espacement entre les tas */
    align-items: center; /* Centre les tas horizontalement */
    justify-content: center; /* Centre verticalement si le contenu est moins que la hauteur de l'écran */
}

.quiz-container {
    position: relative;
    width: 300px;
    height: 500px;
}

.answered-stack {
    position: relative;
    opacity: 0;
    left: 100px;
    width: 500px;
    height: 500px;
    overflow-y: hidden; /* Permet d'ajouter un scroll si les cartes s'accumulent */
}

.answered-stack img {
display:none;
}

.answered-stack .card {
height:250px;    
}

.card {
    width: 300px;
    height: 450px;
    border: 2px solid #000;
    padding: 20px;
    position: absolute;
    top: 0;
    left: 0;
    background-color: #f9f9f9;
    transition: transform 0.5s ease-in-out, opacity 0.5s ease;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
}

.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.card:nth-child(n) {
    transform: translateY(calc(var(--index) * 10px));
}

.disabled {
    pointer-events: none;
}

.answer-btn {
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    margin: 5px 0;
    text-align: center;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.answer-btn:hover {
    background-color: #45a049;
}

.answered-stack .card {
    position: absolute;
    left: 0;
    top: 0;
    transform: translateY(calc(var(--stack-index) * 10px)) scale(0.95);
    transition: transform 0.5s ease-in-out;
}
    </style>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz en Pile</title>
    <style>
        /* Ajoute le CSS que j'ai modifié ici */
    </style>
</head>
<body>
    <h2>Quiz</h2>
    <p>Score: <span id="score">0</span></p>
    <div class="quiz-wrapper">
        <div class="quiz-container" id="quiz-container">
            <!-- Affichage des questions à répondre -->
            <?php foreach ($data as $index => $row): ?>
                <div class="card" id="card-<?php echo $index; ?>" style="--index: <?php echo $index; ?>">
                    <img src="<?php echo htmlspecialchars($row['Image']); ?>">
                    <p><strong><?php echo htmlspecialchars($row['Question']); ?></strong></p>
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
                            <button class="answer-btn" onclick="handleAnswer(this, '<?php echo $index; ?>', '<?php echo htmlspecialchars($answer); ?>', '<?php echo htmlspecialchars($row['Answer']); ?>')">
                                <?php echo htmlspecialchars($answer); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="answered-stack" id="answered-stack">
            <!-- Les questions répondus seront ajoutées ici -->
        </div>
    </div>

    <script>
        let score = 0;
        let answeredCount = 0;

        function handleAnswer(button, cardIndex, answer, correctAnswer) {
            let card = document.getElementById('card-' + cardIndex);
            let buttons = card.querySelectorAll('.answer-btn');

            buttons.forEach(btn => btn.disabled = true);
            card.classList.add('disabled');

            if (answer === correctAnswer) {
                score++;
            } else {
                score--;
            }
            document.getElementById('score').innerText = score;
            card.style.transform = "translateY(250px)";
            setTimeout(() => {
                document.getElementById('answered-stack').appendChild(card);
                card.style.position = "absolute";
                card.style.transform = `translateY(${answeredCount * 10}px) scale(0.95)`;
                card.style.setProperty('--stack-index', answeredCount);
                answeredCount++;
            }, 500);
        }
    </script>
</body>
</html>

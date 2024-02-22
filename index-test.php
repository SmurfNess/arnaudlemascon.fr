<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page avec des cartes PHP</title>
    <!-- Liens vers vos fichiers CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Liens vers la bibliothèque Swiper -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>
<body>

    <!-- Votre contenu HTML avant les cartes -->

    <!-- Début de la section où les cartes seront affichées -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php
            // Connexion à la base de données
            $host = 'localhost'; // ou l'adresse de votre hébergement Hostinger
            $dbname = 'u818602086_project';
            $username = 'u818602086_project';
            $password = 'Nooneelse123456!';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Requête SQL pour récupérer les données
                $query = "SELECT title, type, picture, background, description, html, css, js, php, mysql, rust, year FROM project";
                $stmt = $pdo->query($query);

                // Parcourir les résultats et générer les cartes
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $title = $row['title'];
                    $type = $row['type'];
                    $picture = $row['picture'];
                    $background = $row['background'];
                    $description = $row['description'];
                    $year = $row['year'];
                    $html = $row['html'];
                    $css = $row['css'];
                    $js = $row['js'];
                    $php = $row['php'];
                    $mysql = $row['mysql'];
                    $rust = $row['rust'];

                    // Générer la carte HTML
                    ?>
                    <div class="swiper-slide">
                        <div class="swiper-card" style="background-image:url('<?php echo $background; ?>')">
                            <div class="container-bande">
                                <div class="swiper-head"><div class="swiper-circle"></div></div>
                                <div class="swiper-border"><?php echo $title; ?></div>
                                <div class="techno">
                                    <?php if ($html) echo '<img class="logo html" src="https://arnaudlemascon.fr/assets/pictures/logo/html5.png" style="display: inline;">'; ?>
                                    <?php if ($css) echo '<img class="logo css" src="https://arnaudlemascon.fr/assets/pictures/logo/css3.png" style="display: inline;">'; ?>
                                    <?php if ($js) echo '<img class="logo js" src="https://arnaudlemascon.fr/assets/pictures/logo/js.png" style="display: inline;">'; ?>
                                    <?php if ($php) echo '<img class="logo php" src="https://arnaudlemascon.fr/assets/pictures/logo/php.png" style="display: inline;">'; ?>
                                    <?php if ($mysql) echo '<img class="logo mysql" src="https://arnaudlemascon.fr/assets/pictures/logo/mysql.png" style="display: inline;">'; ?>
                                    <?php if ($rust) echo '<img class="logo rust" src="https://arnaudlemascon.fr/assets/pictures/logo/rust-logo-blk.webp" style="display: inline;">'; ?>
                                </div>
                            </div>
                            <div class="swiper-img"><img src="<?php echo $picture; ?>"></div>
                            <div class="container-bande">
                                <div class="swiper-type"><div class="swiper-circle"></div></div>
                                <div class="swiper-border">Projet - <?php echo $type; ?></div>
                            </div>
                            <div class="swiper-description"><?php echo $description; ?></div>
                            <div class="swiper-legend">Arnaud Lemasçon - <?php echo $year; ?></div>
                        </div>
                    </div>
                    <?php
                }

            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
            }
            ?>
        </div>
    </div>
    <!-- Fin de la section des cartes -->

    <!-- Votre contenu HTML après les cartes -->

    <!-- Liens vers vos fichiers JavaScript -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

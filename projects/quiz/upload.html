<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $ftp_server = "ftp://195.35.49.219";
    $ftp_user_name = "u818602086";
    $ftp_user_pass = "votre_mot_de_passe";
    $upload_dir = "public_html/sous_repertoire/"; // Répertoire de destination sur le serveur FTP

    // Connexion au serveur FTP
    $conn_id = ftp_connect($ftp_server);
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

    // Vérifier la connexion FTP
    if ($conn_id && $login_result) {
        // Téléchargement du fichier vers le serveur FTP
        $remote_file = $upload_dir . basename($_FILES["file"]["name"]);
        if (ftp_put($conn_id, $remote_file, $_FILES["file"]["tmp_name"], FTP_BINARY)) {
            echo "Le fichier " . basename($_FILES["file"]["name"]) . " a été téléchargé avec succès.";
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
        // Fermeture de la connexion FTP
        ftp_close($conn_id);
    } else {
        echo "Connexion FTP échouée.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formulaire de téléchargement FTP</title>
</head>
<body>
    <h2>Téléchargement de fichier via FTP</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <input type="submit" value="Télécharger">
    </form>
</body>
</html>

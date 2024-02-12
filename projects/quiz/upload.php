<?php
// Informations FTP
$ftp_server = "ftp://arnaudlemascon.fr";
$ftp_username = "u818602086";
$ftp_password = "LeMotDePasseDuFTP!3010!";
$ftp_dir = "/public_html/upload";

// Vérifie si un fichier a été soumis
if(isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    
    // Établir une connexion FTP
    $conn_id = ftp_connect($ftp_server);
    $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
    
    // Vérifie si la connexion FTP est établie
    if ($login_result) {
        // Télécharge le fichier vers le serveur FTP
        if (ftp_put($conn_id, $ftp_dir . $file_name, $file_tmp, FTP_BINARY)) {
            echo "Fichier téléchargé avec succès.";
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
        
        // Ferme la connexion FTP
        ftp_close($conn_id);
    } else {
        echo "Impossible de se connecter au serveur FTP.";
    }
} else {
    echo "Aucun fichier n'a été soumis.";
}
?>

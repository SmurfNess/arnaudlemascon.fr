<?php
echo "salut toi meme !";

$server ="87.90.78.93";
$username ="portfolio_cv";
$password ="123456";

try{
    $bdd = new PDO("mysql:host=$server;dbname=CV",$username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connexion réussie !";

        // Prepare and execute the SQL query
        $query = "SELECT enterprise FROM Position";
        $statement = $bdd->prepare($query);
        $statement->execute();
    
        // Fetch the results
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        // Display the results
        foreach ($results as $result) {
            echo $result['enterprise'] . "<br>";
        }
}
catch(PDOException $e){
    echo "erreur : ".$e->getMessage();
}

$bdd="SELECT enterprise from Position";

?>


<?php
    // Ce script copie des enregistrements d'une table dans une autre.
    $bdd = new PDO("mysql:host=localhost;dbname=livrets;charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $rep = $bdd->query("SELECT * FROM Scores WHERE Type='PR'");
    $records = "";
    while($data = $rep->fetch()){
        // Mettre des doubles quotes échappées si un champ est susceptible de contenir des chaînes avec des simples quotes :
        $records = $records."(\"".$data["Ref"]."\", \"".$data["Name"]."\", \"".$data["Page"]."\"),";
    }
    $bdd->query("INSERT INTO Prefaces (Ref, Name, Page) VALUES ".substr($records, 0, -1).";");
    $rep->closeCursor();
?>


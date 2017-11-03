<?php
    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $data_in = $_POST;
    $data_out = array();
    $grid = array("IN", "GR", "AL", "OF", "CO", "KY", "GL", "SA", "CR");
    
    // Pour chaque jour de la retraite, création d'un array qui contiendra les retours de la base de données :
    for($i = 0; $i < 5; $i++){
        $data_out[$i] = array();

        // Traitement des 5 grilles ("IN", "GR", etc.) :
        for($g = 0; $g < count($grid); $g++){
            $label = $grid[$g];
            $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$label."' AND Ref = '".$data_in[$i][$label]."';");
            if($rep = $back->fetch()){
                $data_out[$i][$label] = array($rep["Name"], $rep["Page"]);
            }
            $back->closeCursor();
        }

        // Traitement de Tierce (page et antienne) :
        $back = $connect->query("SELECT * FROM Tierce WHERE Page='".$data_in[$i]["tierce_page"]."';");
        if($rep = $back->fetch()){
            $data_out[$i]["tierce_ant"] = $rep["Antienne"];
            $data_out[$i]["tierce_page"] = $data_in[$i]["tierce_page"];
        }
        $back->closeCursor();

        // Traitement de la préface :
        $back_day = $connect->query("SELECT * FROM Days WHERE Day='".$data_in[$i]["ref"]."';");
        if($rep_day = $back_day->fetch()){
            // TODO : Ça marche pas. Soit il faut sortir cette 2e requête du bloc de la 1ère,
            // soit, mieux, il faut lier les tables Days et Prefs, et faire une seule requête.
            $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref='".$rep_day["Pref"]."';");
            if($rep_pref = $back_pref->fetch()){
                $data_out[$i]["pref_name"] = $rep_pref["Name"];
                $data_out[$i]["pref_page"] = $rep_pref["Page"];
            }
            $back_pref->closeCursor();
        }
        $back_day->closeCursor();
    }
    print(json_encode($data_out));
?>


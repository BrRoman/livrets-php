<?php
    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $data_in = $_POST;
    $data_out = array();
    
    // Pour chaque jour de la retraite, création d'un array qui contiendra les retours de la base de données :
    for($i = 0; $i < 5; $i++){
        $data_out[$i] = array();

        // Jour civil :
        $data_out[$i]["civil_day"] = $data_in[$i]["civil_day"];

        // Jour liturgique :
        $data_out[$i]["liturg_day"] = $data_in[$i]["day_lit"]["day_lit_letters"];

        // Traitement des 5 grilles ("IN", "GR", etc.) :
        $grid = array("IN", "GR", "AL", "OF", "CO", "KY", "GL", "SA", "CR");
        for($g = 0; $g < count($grid); $g++){
            $label = $grid[$g];
            $grid_ref = $data_in[$i][$label];
            $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$label."' AND Ref = '".$grid_ref."';");
            if($rep = $back->fetch()){
                $data_out[$i][$label] = array($rep["Name"], $rep["Page"]);
            }
            else{
                $data_out[$i][$label] = array(str_replace(",", "_", $grid_ref), "");
            }
            $back->closeCursor();
        }
        // Traitement de Tierce (antienne et page) :
        $back_day = $connect->query("SELECT * FROM Days WHERE Ref = '".$data_in[$i]["day_lit"]["day_lit_ref"]."';");
        if($rep_day = $back_day->fetch()){
            if($rep_day["Tierce"] == ""){
                $back_tierce = $connect->query("SELECT * FROM Tierce WHERE Page = '".$data_in[$i]["tierce_page"]."';");
                if($rep_tierce = $back_tierce->fetch()){
                    $data_out[$i]["tierce_ant"] = $rep_tierce["Antienne"];
                }
                $back_tierce->closeCursor();
            }
            else{
                $data_out[$i]["tierce_ant"] = $rep_day["Tierce"];
            }
        }
        $back_day->closeCursor();
        $data_out[$i]["tierce_page"] = $data_in[$i]["tierce_page"];

        // Traitement de la préface :
        $back_day = $connect->query("SELECT * FROM Days WHERE Ref = '".$data_in[$i]["day_lit"]["day_lit_ref"]."';");
        if($rep_day = $back_day->fetch()){
            $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref = '".$rep_day["Pref"]."';");
            if($rep_pref = $back_pref->fetch()){
                $data_out[$i]["pref"] = array($rep_pref["Name"], $rep_pref["Page"]);
            }
            else{
                $data_out[$i]["pref"] = array($rep_day["Pref"], "");
            }
            $back_pref->closeCursor();
        }
        $back_day->closeCursor();
    }

    print(json_encode($data_out));
?>


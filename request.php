<?php
    include("calculate.php");

    $data_in = $_POST;
    $data_out = array();
    $weekdays_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $months_fr = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    
    // Pour chaque jour de la retraite, création d'un array qui contiendra les retours de la base de données :
    for($i = 0; $i < 5; $i++){
        $in = $data_in[$i];
        $timestamp = $in["timestamp"] / 1000;
        $out = array();

        // Données générales sur l'année :
        $year = Date("Y", $timestamp);
        // Calcul du 1er dim. de l'Avent de l'année civile courante :
        $current_adv = calculate_adv($year);
        // Le timestamp est peut-être dans l'année liturgique suivante (année civile courante + 1) :
        if($timestamp >= $current_adv){
            $year++;
        }
        $year_even = $year % 2 == 0 ? "2" : "1";
        $year_letters = array("A", "B", "C");
        $year_letter = $year_letters[($year - 2011) % 3];

        // Jour civil :
        $weekday = $weekdays_fr[(int) Date("w", $timestamp)];
        $day = Date("j", $timestamp);
        if($day == 1){
            $day = "1\\textsuperscript{er}";
        }
        $month = $months_fr[Date("m", $timestamp) - 1];
        $out["civil_day"] = $weekday." ".$day." ".$month." ".$year;

        // Jour liturgique :
        // On remplit le out comme s'il n'y avait que le Temporal :
        $tempo = calculate_tempo($timestamp);
        $liturg_time = split("_", $tempo)[0];
        $back_tempo = $connect->query("SELECT * FROM Days WHERE Ref = '".$tempo."';");
        if($rep_tempo = $back_tempo->fetch()){
            $out["lit_day"] = $rep_tempo["Day"];
            $force_tempo = $rep_tempo["Precedence"];
            $out["rang"] = $rang_tempo;
            
            // Tierce :
            if($rep_tempo["Tierce"] == ""){
                $back_tierce = $connect->query("SELECT * FROM Tierce WHERE Page = '".$in["tierce_page"]."';");
                if($rep_tierce = $back_tierce->fetch()){
                    $out["tierce_ant"] = $rep_tierce["Antienne"];
                }
                $back_tierce->closeCursor();
            }
            else{
                $out["tierce_ant"] = $rep_tempo["Tierce"];
            }
            $out["tierce_page"] = $in["tierce_page"];

            // Oraisons :
            if($rep_tempo["Oraisons"] != ""){
                $out["orationes"] = array("source" => "MG", "ref" => split("-", $rep_tempo["Oraisons"]));
            }
            else{
                $out["orationes"] = array("source" => "Files", "ref" => $rep_tempo["Ref"]);
            }
            
            // Lectures :
            if($rep_tempo["Lect_cycle"] == "3"){
                $out["readings"] = $rep_tempo["Ref"]."_".$year_letter;
            }
            elseif($rep_tempo["Lect_cycle"] == "2"){
                $out["readings"] = $rep_tempo["Ref"]."_".$year_even;
            }
            else{
                $out["readings"] = $rep_tempo["Ref"];
            }
            
            // Préface :
            if($rep_tempo["Pref"] != ""){
                $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref = '".$rep_tempo["Pref"]."';");
                if($rep_pref = $back_pref->fetch()){
                    $out["pref"] = array("ref" => $rep_tempo["Pref"], "name" => $rep_pref["Name"], "page" => $rep_pref["Page"], "name_la" => $rep_tempo["Pref_name_la"], "name_fr" => $rep_tempo["Pref_name_fr"]);
                }
                $back_pref->closeCursor();
            }
            else{
                // Semaine 3 de l'Avent : préface I ou II de l'Avent, selon que…
            }
        }
        $back_tempo->closeCursor();
        
        // On cherche s'il y a un Sancto :
        $sancto = Date("m", $timestamp).Date("d", $timestamp);
        $back_sancto = $connect->query("SELECT * FROM Days WHERE Ref = '".$sancto."';");
        if($rep_sancto = $back_sancto->fetch()){
            $force_sancto = $rep_sancto["Precedence"];
            // Si le Sancto est plus fort, on fait les remplacements dans le out :
            if($force_sancto > $force_tempo){
                $out["lit_day"] = $rep_sancto["Day"];
                $out["rang"] = $rep_sancto["Rang"];
                if($rep_sancto["Oraisons"] != ""){
                    $out["orationes"] = array("source" => "MG", "ref" => split("-", $rep_sancto["Oraisons"]));
                }
                else{
                    $out["orationes"] = array("source" => "Files", "ref" => $rep_sancto["Ref"]);
                }
                if($rep_sancto["Lect_propres"] == "True"){
                    $out["propre"] = "True";
                    if($rep_sancto["Lect_cycle"] == "3"){
                        $out["readings"] = $rep_sancto["Ref"]."_".$year_letter;
                    }
                    elseif($rep_sancto["Lect_cycle"] == "2"){
                        $out["readings"] = $rep_sancto["Ref"]."_".$year_even;
                    }
                    else{
                        $out["readings"] = $rep_sancto["Ref"];
                    }
                }
                $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref = '".$rep_sancto["Pref"]."';");
                if($rep_pref = $back_pref->fetch()){
                    $out["pref"] = array("ref" => $rep_sancto["Pref"], "name" => $rep_pref["Name"], "page" => $rep_pref["Page"], "name_la" => $rep_sancto["Pref_name_la"], "name_fr" => $rep_sancto["Pref_name_fr"]);
                }
                $back_pref->closeCursor();
                if($rep_sancto["Tierce"] != ""){
                    $out["tierce_ant"] = "AM/".$rep_sancto["Tierce"];
                }
            }
        }
        $back_sancto->closeCursor();

        // Asperges me :
        $weekday = Date("w", $timestamp);
        $out["asp"] = "";
        if($weekday == 0){
            if($liturg_time == "tp"){
                $out["asp"] = "\\TitreB{Vidi aquam}\\Normal{(p. 71).}"; // Vidi aquam.
            }
            else if($liturg_time == "adv" or $liturg_time == "qua"){
                $out["asp"] = "\\TitreB{Asperges me II}\\Normal{(p. 71).}"; // Avent et Carême.
            }
            else if($day < 8){
                $out["asp"] = "\\TitreB{Asperges me}\\Normal{(p. 70.}";
            }
            else{
                $out["asp"] = "\\TitreB{Asperges me I}\\Normal{(p. 71).}";
            }
        }

        // Traitement des 5 grilles ("IN", "GR", etc.) :
        $grid = array("IN", "GR", "AL", "OF", "CO", "KY", "GL", "SA", "CR");
        for($g = 0; $g < count($grid); $g++){
            $label = $grid[$g];
            $grid_ref = $in[$label];
            if($label == "SA"){
                $back = $connect->query("SELECT * FROM Scores WHERE Type = 'SA' AND Ref = '".$grid_ref."';");
                if($rep = $back->fetch()){
                    $out["SA"] = array($rep["Name"], $rep["Page"]);
                }
                else{
                    $out["SA"] = array("", "");
                }
                $back->closeCursor();
                $back = $connect->query("SELECT * FROM Scores WHERE Type = 'AG' AND Ref = '".$grid_ref."';");
                if($rep = $back->fetch()){
                    $out["AG"] = array($rep["Name"], $rep["Page"]);
                }
                else{
                    $out["AG"] = array("", "");
                }
                $back->closeCursor();
            }
            else{
                $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$label."' AND Ref = '".$grid_ref."';");
                if($rep = $back->fetch()){
                    $out[$label] = array($rep["Name"], $rep["Page"]);
                }
                else{
                    $out[$label] = array(str_replace(",", "_", $grid_ref), "");
                }
                $back->closeCursor();
            }
        }

        $data_out[$i] = $out;
    }
    print(json_encode($data_out));
?>


<?php
    include("calculate.php");

    $data_in = $_POST;
    $data_out = array();
    $weekdays_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $months_fr = array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
    date_default_timezone_set('Etc/GMT-2)'); // Pour compatibilité avec les timestamps de JS. (Code JS: console.log(date.getTimezoneOffset()); => '-120')

    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "sql", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    
    // Pour chaque jour de la retraite, création d'un array qui contiendra les retours de la base de données :
    for($i = 0; $i < 5; $i++){
        $in = $data_in[$i];
        $timestamp = $in["timestamp"] / 1000;
        $out = array();

        // Données générales sur l'année :
        $year = date("Y", $timestamp);
        $lit_year = $year;
        // Calcul du 1er dim. de l'Avent de l'année civile courante :
        $current_adv = calculate_adv($year);
        // Le timestamp est peut-être dans l'année liturgique suivante (année civile courante + 1) :
        if($timestamp >= $current_adv){
            $lit_year++;
        }
        $year_even = $lit_year % 2 == 0 ? "2" : "1";
        $year_letters = array("A", "B", "C");
        $year_letter = $year_letters[($lit_year - 2011) % 3];

        // Jour civil :
        $weekday = $weekdays_fr[(int) date("w", $timestamp)];
        $out["weekday"] = $weekday;
        $day = date("j", $timestamp);
        if($day == 1){
            $day = "1\\textsuperscript{er}";
        }
        $month = $months_fr[date("m", $timestamp) - 1];
        $out["civil_day"] = $weekday." ".$day." ".$month." ".$year;

        // Page de Tierce :
        $out["tierce_page"] = $in["tierce_page"];

        // Jour liturgique :
        // On remplit le out comme s'il n'y avait que le Temporal :
        $tempo = calculate_tempo($timestamp);
        $out["tempo"] = $tempo;
        $liturg_time = explode("_", $tempo)[0];
        $back_tempo = $connect->query("SELECT * FROM Days WHERE Ref = '".$tempo."';");
        if($rep_tempo = $back_tempo->fetch()){
            $out["lit_day"] = $rep_tempo["Day"];
            $force_tempo = $rep_tempo["Precedence"];
            $out["rang"] = $rep_tempo["Rang"];
            
            // Antienne de Tierce :
            if($rep_tempo["Tierce"] == NULL){
                if($liturg_time == "adv"){
                    $adv_hebd = explode("_", $tempo)[1];
                    if($adv_hebd == "1"){
                        $out["tierce_ant"] = "jucundare";
                    }
                    else if($adv_hebd == "2"){
                        $out["tierce_ant"] = "urbs_fortitudinis";
                    }
                    else if($adv_hebd == "3"){
                        $out["tierce_ant"] = "jerusalem_gaude";
                    }
                }
                else if($liturg_time == "quadr"){
                    $out["tierce_ant"] = "advenerunt_nobis";
                }
                else if($liturg_time == "pass"){
                    $out["tierce_ant"] = "judicasti_domine";
                }
                else if($liturg_time == "tp"){
                    $out["tierce_ant"] = ($weekday == "Dimanche") ? "alleluia_dim_tp" : "alleluia_feries_tp";
                }
                else{
                    $back_tierce = $connect->query("SELECT * FROM Tierce WHERE Page = '".$in["tierce_page"]."';");
                    if($rep_tierce = $back_tierce->fetch()){
                        $out["tierce_ant"] = $rep_tierce["Antienne"];
                    }
                    $back_tierce->closeCursor();
                }
            }
            else{
                $out["tierce_ant"] = $rep_tempo["Tierce"];
            }

            // Oraisons :
            if($rep_tempo["Oraisons_MG"] != NULL){
                $out["orationes"] = array("source" => "MG", "ref" => explode("/", $rep_tempo["Oraisons_MG"]));
            }
            else{
                // Temps de Noël :
                if($liturg_time == "noel" && explode("_", $tempo)[1] == "time"){
                    if(explode("_", $tempo)[2] == "2"){ // Temps avant l'Épiphanie.
                        $out["orationes"] = array("source" => "Files", "ref" => "noel_time_before_ep_".date("w", $timestamp));
                    }
                    else if(explode("_", $tempo)[2] == "3"){ // Temps après l'Épiphanie.
                        $out["orationes"] = array("source" => "Files", "ref" => "noel_time_after_ep_".date("w", $timestamp));
                    }
                }
                // Temps per annum :
                else if($liturg_time == "pa"){ // Ne concerne que la 1ère semaine Per annum (toutes les autres sont dans le MG).
                    $out["orationes"] = array("source" => "Files", "ref" => "pa_".explode("_", $tempo)[1]);
                }
                // Tout le reste (carême, tp, etc.) :
                else{
                    $out["orationes"] = array("source" => "Files", "ref" => $rep_tempo["Ref"]);
                }
            }
            
            // Lectures :
            if($rep_tempo["Lect_cycle"] == "3"){
                $out["readings"] = $rep_tempo["Ref"]."_".$year_letter;
            }
            elseif($rep_tempo["Lect_cycle"] == "2"){
                $out["readings"] = $rep_tempo["Ref"]."_".$year_even;
            }
            else{
                if($liturg_time == "noel" && explode("_", $tempo)[1] == "time"){
                    $out["readings"] = date("m", $timestamp).date("d", $timestamp);
                }
                else{
                    $out["readings"] = $rep_tempo["Ref"];
                }
            }
            
            // Préface :
            if($rep_tempo["Pref"] != NULL){
                $pref = $rep_tempo["Pref"];
            }
            else{
                // Avent : préface I ou II de l'Avent, selon que avant ou après 17/12 :
                if($liturg_time == "adv"){
                    $pref = $day < 17 ? "adv_1" : "adv_2";
                }
            }
            $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref = '".$pref."';");
            if($rep_pref = $back_pref->fetch()){
                $out["pref"] = array("ref" => $rep_tempo["Pref"], "name" => $rep_pref["Name"], "page" => $rep_pref["Page"], "name_la" => $rep_tempo["Pref_name_la"], "name_fr" => $rep_tempo["Pref_name_fr"]);
            }
            else{
                $out["pref"] = $pref;
            }
            $back_pref->closeCursor();
        }
        $back_tempo->closeCursor();
        
        // On cherche s'il y a un Sancto :
        $sancto = date("m", $timestamp).date("d", $timestamp);
        $back_sancto = $connect->query("SELECT * FROM Days WHERE Ref = '".$sancto."';");
        if($rep_sancto = $back_sancto->fetch()){
            $force_sancto = $rep_sancto["Precedence"];
            // Si le Sancto est plus fort, on fait les remplacements dans le out :
            if($force_sancto > $force_tempo){
                $out["lit_day"] = $rep_sancto["Day"];
                $out["rang"] = $rep_sancto["Rang"];
                if($rep_sancto["Tierce"] != NULL){
                    $out["tierce_ant"] = $rep_sancto["Tierce"];
                }
                if($rep_sancto["Oraisons_MG"] != NULL){
                    $out["orationes"] = array("source" => "MG", "ref" => explode("/", $rep_sancto["Oraisons_MG"]));
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
            }
        }
        $back_sancto->closeCursor();

        // On cherche s'il y a une mémoire de la Ste Vierge :
        $out["tempo"] = $tempo;
        if($weekday == "Samedi" and $force_tempo < 30 and $force_sancto < 30){
            $bmv = date("j", $timestamp) < 8 ? "icm" : date("n", $timestamp)."_".ceil(date("j", $timestamp) / 7);
            $back = $connect->query("SELECT * FROM BMV WHERE Ref = '".$bmv."';");
            if($rep = $back->fetch()){
                $out["lit_day"] = $rep["Title"];
                $out["rang"] = "Mémoire majeure";
                $out["tierce_ant"] = $timestamp < mktime(0, 0, 0, 2, 2, $year) ? "quando_natus_es" : "laeva_ejus";
                $out["orationes"] = array("source" => "Files", "ref" => "bmv_".$rep["CM"]);
                $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref = '".$rep["Preface"]."';");
                if($rep_pref = $back_pref->fetch()){
                    $out["pref"] = array("ref" => $rep["Preface"], "name" => $rep_pref["Name"], "page" => $rep_pref["Page"], "name_la" => NULL, "name_fr" => NULL);
                }
                $back_pref->closeCursor();
            }
            $back->closeCursor();
        }

        // Asperges me :
        $out["asp"] = "";
        if($weekday == "Dimanche"){
            if($liturg_time == "tp"){
                $out["asp"] = "\\TitreB{Vidi aquam}\\Normal{(p. 71).}"; // Vidi aquam.
            }
            else if($liturg_time == "adv" or $liturg_time == "qua"){
                $out["asp"] = "\\TitreB{Asperges me II}\\Normal{(p. 71).}"; // Avent et Carême.
            }
            else if($day < 8 or $out["rang"] == "Fête" or $out["rang"] = "Solennité"){
                $out["asp"] = "\\TitreB{Asperges me}\\Normal{(p. 70).}";
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
                if($label == "GR" && $liturg_time == "tp"){
                    $back = $connect->query("SELECT * FROM Scores WHERE Type = 'AL' AND Ref = '".$grid_ref."';");
                }
                else{
                    $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$label."' AND Ref = '".$grid_ref."';");
                }
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


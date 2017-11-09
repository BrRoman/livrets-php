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
        $out = array();
        $timestamp = $in["timestamp"] / 1000;

        // Données générales sur l'année :
        $year = Date("Y", $timestamp);
        if($year % 2 == 0){
            $year_even = "2";
        }
        else{
            $year_even = "1";
        }
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

        // Jour liturgique. Pour le déterminer, on compare le Tempo et le Sancto :
        $tempo = calculate_tempo($timestamp);
        $sancto = Date("m", $timestamp).Date("d", $timestamp);
        $force_tempo = 0;
        $force_sancto = 0;
        $back = $connect->query("SELECT * FROM Days WHERE Ref = '".$tempo["ref"]."';");
        if($rep = $back->fetch()){
            $force_tempo = $rep["Precedence"];
            $rang_tempo = $rep["Rang"];
            $out["orationes"] = array("MG", $rep["Oraisons"]);
        }
        $back->closeCursor();
        $back = $connect->query("SELECT * FROM Days WHERE Ref = '".$sancto."';");
        if($rep = $back->fetch()){
            $force_sancto = $rep["Precedence"];
            $rang_sancto = $rep["Rang"];
        }
        $back->closeCursor();
        if($force_tempo > $force_sancto){
            $day_ref = $tempo["ref"];
            $out["rang"] = $rang_tempo;
            if(Date("w", $timestamp) == "0"){
                $out["readings"] = "Tempo/".$day_ref."_".$year_letter;
            }
            else{
                $out["readings"] = "Tempo/".$day_ref."_".$year_even;
            }
            $out["lit_day"] = $tempo["letters"];
        }
        else{
            $day_ref = $sancto;
            $out["rang"] = $rang_sancto;
            $out["orationes"] = array("Files", $day_ref);
            $out["readings"] = "Sancto/".$day_ref;
            $out["lit_day"] = $rep["Day"];
        }

        // Asperges me :
        $paques = calculate_paques(Date("Y", $timestamp));
        $weekday = Date("w", $timestamp);
        $day = Date("j", $timestamp);
        $out["asp"] = "";
        if($weekday == 0){
            if(($timestamp >= $paques) && ($timestamp < ($paques + (49 * 24 * 3600)))){
                $out["asp"] = "\\TitreB{Vidi aquam}\\Normal{(p. 71).}"; // Vidi aquam.
            }
            else if(($timestamp < $paques) && ($timestamp >= ($paques - (46 * 24 * 3600)))){
                $out["asp"] = "\\TitreB{Asperges me II}\\Normal{(p. 71).}"; // Carême.
            }
            if($day < 8){
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

        // Traitement de Tierce (antienne et page) :
        $back_day = $connect->query("SELECT * FROM Days WHERE Ref = '".$day_ref."';");
        if($rep_day = $back_day->fetch()){
            if($rep_day["Tierce"] == ""){
                $back_tierce = $connect->query("SELECT * FROM Tierce WHERE Page = '".$in["tierce_page"]."';");
                if($rep_tierce = $back_tierce->fetch()){
                    $out["tierce_ant"] = $rep_tierce["Antienne"];
                }
                $back_tierce->closeCursor();
            }
            else{
                $out["tierce_ant"] = $rep_day["Tierce"];
            }
        }
        $back_day->closeCursor();
        $out["tierce_page"] = $in["tierce_page"];

        // Traitement de la préface :
        $back_day = $connect->query("SELECT * FROM Days WHERE Ref = '".$day_ref."';");
        if($rep_day = $back_day->fetch()){
            $back_pref = $connect->query("SELECT * FROM Prefaces WHERE Ref = '".$rep_day["Pref"]."';");
            if($rep_pref = $back_pref->fetch()){
                $out["pref"] = array("ref" => $rep_day["Pref"], "name" => $rep_pref["Name"], "page" => $rep_pref["Page"], "name_la" => $rep_day["Pref_name_la"], "name_fr" => $rep_day["Pref_name_fr"]);
            }
            $back_pref->closeCursor();
        }
        $back_day->closeCursor();

        $data_out[$i] = $out;
    }

    print(json_encode($data_out));
?>


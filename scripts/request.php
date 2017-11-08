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

        // Jour civil :
        $weekday = $weekdays_fr[(int) Date("w", $timestamp)];
        $day = Date("j", $timestamp);
        if($day == 1){
            $day = "1\\textsuperscript{er}";
        }
        $month = $months_fr[Date("m", $timestamp) - 1];
        $year = Date("Y", $timestamp);
        $out["civil_day"] = $weekday." ".$day." ".$month." ".$year;

        // Jour liturgique. Pour le déterminer, on compare le Tempo et le Sancto :
        $tempo = calculate_tempo($timestamp);
        $sancto = Date("m", $timestamp).Date("d", $timestamp);
        $force_tempo = 0;
        $force_sancto = 0;
        $back = $connect->query("SELECT * FROM Days WHERE Ref = '".$tempo["ref"]."';");
        if($rep = $back->fetch()){
            $force_tempo = $rep["Precedence"];
        }
        $back->closeCursor();
        $back = $connect->query("SELECT * FROM Days WHERE Ref = '".$sancto."';");
        if($rep = $back->fetch()){
            $force_sancto = $rep["Precedence"];
        }
        $back->closeCursor();
        if($force_tempo > $force_sancto){
            $day_ref = $tempo["ref"];
            $out["lit_day_letters"] = $tempo["letters"];
        }
        else{
            $day_ref = $sancto;
            $out["lit_day_letters"] = $rep["Day"];
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
            $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$label."' AND Ref = '".$grid_ref."';");
            if($rep = $back->fetch()){
                $out[$label] = array($rep["Name"], $rep["Page"]);
            }
            else{
                $out[$label] = array(str_replace(",", "_", $grid_ref), "");
            }
            $back->closeCursor();
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
                $out["pref"] = array($rep_day["Pref"], $rep_pref["Name"], $rep_pref["Page"], $rep_day["Pref_name_la"], $rep_day["Pref_name_fr"]);
            }
            $back_pref->closeCursor();
        }
        $back_day->closeCursor();

        $data_out[$i] = $out;
    }

    print(json_encode($data_out));
?>


<?php
// Fonctions sur les dates. //

// Calcul de la date de Pâques en fonction de l'année entrée :
function calculate_paques($year){
    $var_1 = $year - 1900;
    $var_2 = $var_1 % 19;
    $var_3 = floor((7 * $var_2 + 1) / 19);
    $var_4 = (11 * $var_2 + 4 - $var_3) % 29;
    $var_5 = floor($var_1 / 4);
    $var_6 = ($var_1 + $var_5 + 31 - $var_4) % 7;
    $var_7 = 25 - $var_4 - $var_6;
    if($var_7 > 0){
        $mois_paques = 4;
        $jour_paques = $var_7;
    }
    else{
        $mois_paques = 3;
        $jour_paques = $var_7 + 31;
    }
    $paques = mktime(0, 0, 0, $mois_paques, $jour_paques, $year);
    return($paques);
}

// Calcul du jour liturgique demandé ($date = timestamp du jour civil),
// renvoyé sous forme de référencee : "pa_30_0", "adv_3_2", etc. :
function calculate_tempo($timestamp){
    $weekdays_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $latine_numbers = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX", "XXXI", "XXXII", "XXXXIII", "XXXIV");
    $tempo = array();
    $day = 24 * 3600;

    // Calcul des grands jours liturgiques de l'année à laquelle appartient le jour concerné :
    $year = Date("Y", $timestamp);
    $paques = calculate_paques($year);
    $noel = mktime(0, 0, 0, 12, 25, $year - 1);
    $noel_weekday = (int) Date("w", $noel);
    if($noel_weekday == 0){
        $bapteme = $noel + (14 * $day);
    }
    else{
        $bapteme = $noel + (14 * $day) + ((7 - $noel_weekday) * $day);
    }
    $cendres = $paques - (45 * $day);
    $pentecote = $paques + (49 * $day);
    $next_noel = mktime(0, 0, 0, 12, 25, $year);
    $next_noel_weekday = (int) Date("w", $next_noel);
    if($next_noel_weekday == 0){
        $adv_dim_1 = $next_noel - (28 * $day);
    }
    else{
        $adv_dim_1 = $next_noel - ((21 + $next_noel_weekday) * $day);
    }
    $christ_roi = $adv_dim_1 - (7 * $day);

    // Temps per Annum après la Pentecôte :
    if($timestamp > $pentecote && $timestamp < $christ_roi){
        $days_before_christ_roi = ceil(($christ_roi - $timestamp) / $day);
        $dim_per_annum = 34 - ceil($days_before_christ_roi / 7);
        $weekday = 7 - ($days_before_christ_roi % 7);
        if($weekday == 7){
            $weekday = 0;
        }
        $tempo["ref"] = "pa_".$dim_per_annum."_".$weekday;
        if($weekday != 0){
            $tempo["letters"] = $weekdays_fr[$weekday]." de la\\par ".$latine_numbers[$dim_per_annum]."\\textsuperscript{e} semaine\\par du Temps Ordinaire";
        }
        else{
            $tempo["letters"] = $latine_numbers[$dim_per_annum]."\\textsuperscript{e} Dimanche\\par du Temps Ordinaire";
        }
    }
    return($tempo);
}
?>

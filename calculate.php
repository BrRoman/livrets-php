<?php
// Fonctions sur les dates. //

// Calcul du début de l'Avent de l'année civile courante :
function calculate_adv($year){
    $day = 24 * 3600;
    $noel = mktime(0, 0, 0, 12, 25, $year);
    $noel_weekday = (int) Date("w", $noel);
    if($noel_weekday == 0){
        $adv = $noel - (28 * $day);
    }
    else{
        $adv = $noel - ((21 + $noel_weekday) * $day);
    }
    return($adv);
}

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

// Calcul du jour liturgique demandé (timestamp du jour civil),
// renvoyé sous forme de référencee : "pa_30_0", "adv_3_2", etc. :
function calculate_tempo($timestamp){
    $day = 24 * 3600;
    $weekday = (int) Date("w", $timestamp);

    // Calcul des grands jours liturgiques de l'année à laquelle appartient le jour concerné :
    $year = Date("Y", $timestamp);
    
    // Calcul du 1er dim. de l'Avent de l'année civile courante :
    $current_adv = calculate_adv($year);
    // Le timestamp est peut-être dans l'année liturgique suivante (année civile courante + 1) :
    if($timestamp >= $current_adv){
        $year++;
    }
    $noel = mktime(0, 0, 0, 12, 25, $year - 1);
    $noel_weekday = (int) Date("w", $noel);
    if($noel_weekday == 0){
        $adv = $noel - (28 * $day);
        $bapteme = $noel + (14 * $day);
    }
    else{
        $adv = $noel - ((21 + $noel_weekday) * $day);
        $bapteme = $noel + (7 * $day) + ((7 - $noel_weekday) * $day);
    }
    $paques = calculate_paques($year);
    $cendres = $paques - (46 * $day);
    $pentecote = $paques + (49 * $day);
    $christ_roi = $next_adv - (7 * $day);

    // Avent :
    if($timestamp >= $adv and $timestamp < $noel){
        $days_after_adv = ceil(($timestamp - $adv) / $day);
        $dim_adv = floor($days_after_adv / 7) + 1;
        if($weekday == 7){
            $weekday = 0;
        }
        $tempo = "adv_".$dim_adv."_".$weekday;
    }

    // Temps après Noël :
    if($timestamp >= $noel and $timestamp <= $bapteme){
        //
    }

    // Temps per Annum après la Pentecôte :
    if($timestamp > $pentecote and $timestamp < $current_adv){
        $days_before_current_adv = ceil(($current_adv - $timestamp) / $day);
        $dim_per_annum = 34 - floor($days_before_current_adv / 7);
        if($weekday == 7){
            $weekday = 0;
        }
        $tempo = "pa_".$dim_per_annum."_".$weekday;
    }
    return($tempo);
}
?>

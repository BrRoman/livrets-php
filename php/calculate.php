<?php
// Fonctions sur les dates. //

// Calcul du début de l'Avent de l'année civile courante :
function calculate_adv($year){
    $day = 24 * 3600;
    $noel = mktime(0, 0, 0, 12, 25, $year);
    $noel_weekday = (int) date("w", $noel);
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

// Calcul du jour liturgique demandé, renvoyé sous forme de référence : "pa_30_0", "adv_3_2", etc.
// En entrée : timestamp du jour civil.
function calculate_tempo($timestamp){
    $day = 24 * 3600;
    $weekday = (int) date("w", $timestamp);
    $first_in_month = ((int) date('j', $timestamp) < 8) ? true : false;

    // Calcul des grands jours liturgiques de l'année à laquelle appartient le jour concerné :
    $year = date("Y", $timestamp);
    $lit_year = $year;
    
    // Calcul du 1er dim. de l'Avent de l'année civile courante :
    $current_adv = calculate_adv($year);
    // Le timestamp est peut-être dans l'année liturgique suivante (année civile courante + 1) :
    if($timestamp >= $current_adv){
        $lit_year++;
    }
    $noel = mktime(0, 0, 0, 12, 25, $lit_year - 1);
    $noel_weekday = (int) date("w", $noel);
    if($noel_weekday == 0){
        $adv = $noel - (28 * $day);
        $ste_famille = $noel + (5 * $day);
        $bapteme = $noel + (14 * $day);
    }
    else{
        $adv = $noel - ((21 + $noel_weekday) * $day);
        $ste_famille = $noel + ((7 - $noel_weekday) * $day);
        $bapteme = $noel_weekday == 1 ? $noel + (13 * $day) : $noel + (14 * $day) + ((7 - $noel_weekday) * $day);
    }
    $pro_unitate = mktime(0, 0, 0, 1, 18, $year);
    if(date("w", $pro_unitate) == 0){
        $pro_unitate += $day;
    }
    else if(date("w", $pro_unitate) == 5){
        $pro_unitate += (5 * $day);
    }
    $paques = calculate_paques($lit_year);
    $cendres = $paques - (46 * $day);
    $quadr_dim_1 = $cendres + (4 * $day);
    $pentecote = $paques + (49 * $day);
    $christ_roi = $current_adv - (7 * $day);

    // Avent :
    if($timestamp >= $adv and $timestamp < $noel){
        $days_after_adv = ceil(($timestamp - $adv) / $day);
        $dim_adv = floor($days_after_adv / 7) + 1;
        $tempo = "adv_".$dim_adv."_".$weekday;
    }

    // Temps de Noël :
    if($timestamp >= $noel and $timestamp < $bapteme){
        $days_after_noel = ceil(($timestamp - $noel) / $day);
        if($days_after_noel < 7){ // Octave de Noël.
            $tempo = "noel_time_1"; 
        }
        else if($days_after_noel < 12){ // Avant l'Épiphanie.
            $tempo = $weekday == 0 ? "noel_dim_2" : "noel_time_2";
        }
        else{ // Après l'Épiphanie.
            $tempo = "noel_time_3";
        }
    }
    
    // Sainte Famille :
    if($timestamp == $ste_famille){
        $tempo = $noel_weekday == 0 ? "ste_famille_fer" : "ste_famille_dim";
    }

    // Baptême :
    if($timestamp == $bapteme){
        $tempo = "bapt";
    }

    // Temps per Annum avant le Carême :
    if($timestamp > $bapteme and $timestamp < $cendres){
        $days_after_bapt = ceil(($timestamp - $bapteme) / $day);
        $dim_per_annum = floor($days_after_bapt / 7) + 1;
        $tempo = "pa_".$dim_per_annum."_".$weekday;
    }

    // Messe votive pour l'unité des chrétiens :
    if($timestamp == $pro_unitate){
        $tempo = "pro_unitate";
    }

    // Féries après les Cendres :
    if($timestamp >= $cendres and $timestamp < $quadr_dim_1){
        $days_after_cendres = ceil(($timestamp - $cendres) / $day) - 1;
        $tempo = "cendres_".$days_after_cendres;
    }

    // Carême :
    if($timestamp >= $quadr_dim_1 and $timestamp < $paques){
        $days_careme = ceil(($timestamp - $quadr_dim_1) / $day) - 1;
        $dim_careme = floor($days_careme / 7) + 1;
        $tempo = "qua_".$dim_careme."_".$weekday;
    }

    // Temps pascal :
    if($timestamp >= $paques and $timestamp < $pentecote){
        $days_after_paques = ceil(($timestamp - $paques) / $day);
        $dim_paques = floor($days_after_paques / 7) + 1;
        $tempo = "tp_".$dim_paques."_".$weekday;
    }

    // Temps per Annum après la Pentecôte :
    if($timestamp > $pentecote and $timestamp < $current_adv){
        if(floor(($timestamp - $pentecote) / $day) == 7){
            $tempo = "trinite";
        }
        else{
            $days_before_current_adv = ceil(($current_adv - $timestamp) / $day);
            $dim_per_annum = 35 - ceil(($days_before_current_adv - 1)/ 7) - (($weekday == 6 and in_array(date('m', $timestamp), [11, 12])) ? 1 : 0); // Après le changement d'heure (donc en novembre et décembre), il faut encore enlever 1 le samedi.
            $tempo = "pa_".$dim_per_annum."_".$weekday;
        }
    }

    // Christ-Roi :
    if($timestamp == $christ_roi){
        $tempo = "christ_roi";
    }
    
    return(array($tempo, $first_in_month));
}
?>

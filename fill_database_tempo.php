<?php
    $weekdays_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $latine_num = array("I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX", "XXXI", "XXXII", "XXXIII", "XXXIV");
    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    // Avent :
    $txt = "";
    for($h = 0; $h < 4; $h++){
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par de l\'Avent";
                $txt = $txt."('adv_".($h + 1)."_".$d."', '".$day."', '', '120', '', '', 'True', '3', 'adv_1', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par de l\'Avent";
                $txt = $txt."('adv_".($h + 1)."_".$d."', '".$day."', '', '10', '', '', 'True', '1', 'adv_1', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $back = $connect->query("DELETE FROM Days");
    $back->closeCursor();
    $back = $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
    $back->closeCursor();

    //Temps per annum :
    $txt = "";
    for($h = 0; $h < 34; $h++){
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par du Temps Ordinaire";
                $pref_dim = $h % 8 != 0 ? $h % 8 : 8;
                $txt = $txt."('pa_".($h + 1)."_".$d."', '".$day."', '', '80', '', '', 'True', '3', 'pa_dim_".$pref_dim."', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par du Temps Ordinaire";
                $pref_fer = ($h + 1) % 6 != 0 ? ($h + 1) % 6 : 6;
                $txt = $txt."('pa_".($h + 1)."_".$d."', '".$day."', '', '10', '', '', 'True', '2', 'com_".$pref_fer."', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $back = $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
    $back->closeCursor();
?>

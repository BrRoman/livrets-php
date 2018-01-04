<?php
    $weekdays_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $latine_num = array("I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX", "XXXI", "XXXII", "XXXIII", "XXXIV");
    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    // Effacement des enregistrements :
    $connect->query("DELETE FROM Days");

    // Avent :
    $txt = "";
    for($h = 0; $h < 4; $h++){
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par de l\'Avent";
                $txt = $txt."('adv_".($h + 1)."_0', '".$day."', '', '120', '', '', 'True', '3', '', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par de l\'Avent";
                $txt = $txt."('adv_".($h + 1)."_".$d."', '".$day."', '', '10', '', '', 'True', '1', '', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");

    // Temps de Noël :
    $txt = "";
    for($h = 0; $h < 3; $h++){
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par après Noël";
                $txt = $txt."('noel_".($h + 1)."_0', '".$day."', '', '80', '', '', 'True', '3', '', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par de l\'Avent";
                $txt = $txt."('noel_".($h + 1)."_".$d."', '".$day."', '', '10', '', '', 'True', '1', '', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");

    // Carême :
    $txt = "('cendres_1', 'Mercredi des Cendres', '', '120', '', '', 'True', '1', 'qua_4', '', '')";
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
    $txt = "('cendres_2', 'Jeudi après les Cendres', '', '50', '', '', 'True', '1', 'qua_4', '', '')";
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
    $txt = "('cendres_3', 'Vendredi après les Cendres', '', '50', '', '', 'True', '1', 'qua_4', '', '')";
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
    $txt = "('cendres_4', 'Samedi après les Cendres', '', '50', '', '', 'True', '1', 'qua_4', '', '')";
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
    $txt = "";
    for($h = 0; $h < 4; $h++){
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par de Carême";
                $txt = $txt."('qua_".($h + 1)."_0', '".$day."', '', '120', '', '', 'True', '3', '', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par de Carême";
                $txt = $txt."('qua_".($h + 1)."_".$d."', '".$day."', '', '50', '', '', 'True', '1', 'qua_".($h + 1)."', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");

    // Passion :
    $txt = "";
    for($d = 0; $d < 7; $d++){
        if($d == 0){
            $day = "I\\\\textsuperscript{er} dimanche\\\\par de la Passion";
            $txt = $txt."('qua_5_0', '".$day."', '', '120', '', '', 'True', '3', '', '', ''),";
        }
        else{
            $day = $weekdays_fr[$d]." de la\\\\par I\\\\textsuperscript{re} semaine\\\\par de la Passion";
            $txt = $txt."('qua_5_".$d."', '".$day."', '', '50', '', '', 'True', '1', 'pa_1', '', ''),";
        }
    }
    $txt = substr($txt, 0, -1);
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");

    // Temps Pascal :
    $txt = "";
    for($h = 0; $h < 6; $h++){
        $pref = $h != 5 ? ($h + 1) : 2;
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par de Pâques";
                $txt = $txt."('tp_".($h + 1)."_0', '".$day."', '', '120', '', '', 'True', '3', 'tp_".$pref."', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par de Pâques";
                $txt = $txt."('tp_".($h + 1)."_".$d."', '".$day."', '', '50', '', '', 'True', '1', 'tp_".$pref."', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");

    //Temps per annum :
    $txt = "";
    for($h = 0; $h < 34; $h++){
        for($d = 0; $d < 7; $d++){
            if($d == 0){
                $day = $latine_num[$h].($h == 0 ? "\\\\textsuperscript{er}" : "\\\\textsuperscript{e}")." dimanche\\\\par du Temps Ordinaire";
                $pref_dim = $h % 8 != 0 ? $h % 8 : 8;
                $txt = $txt."('pa_".($h + 1)."_0', '".$day."', '', '80', '', '', 'True', '3', 'pa_dim_".$pref_dim."', '', ''),";
            }
            else{
                $day = $weekdays_fr[$d]." de la\\\\par ".$latine_num[$h].($h == 0 ? "\\\\textsuperscript{re}" : "\\\\textsuperscript{e}")." semaine\\\\par du Temps Ordinaire";
                $pref_fer = ($h + 1) % 6 != 0 ? ($h + 1) % 6 : 6;
                $txt = $txt."('pa_".($h + 1)."_".$d."', '".$day."', '', '10', '', '', 'True', '2', 'com_".$pref_fer."', '', ''),";
            }
        }
    }
    $txt = substr($txt, 0, -1);
    $connect->query("INSERT INTO Days (Ref, Day, Rang, Precedence, Tierce, Oraisons, Lect_propres, Lect_cycle, Pref, Pref_name_la, Pref_name_fr) VALUES ".$txt.";");
?>

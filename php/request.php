<?php
    include(__DIR__.'/calculate.php');

    $data_in = $_POST;
    $nombre_jours = count($data_in["days"]);
    $mode = $data_in["mode"];

    $weekdays_fr = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    $months_fr = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');

    $secret = json_decode(file_get_contents(__DIR__."/../.secret/config.json"), true);
    $db_name = $secret["db_name"];
    $db_login = $secret["db_login"];
    $db_pass = $secret["db_pass"];
    $connect = new PDO("mysql:host=localhost; dbname=$db_name; charset=utf8", $db_login, $db_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    $data_out = array();
    $data_out["days"] = array();
    $data_out["mode"] = $mode;
    
    // Pour chaque jour de la retraite, création d'un array qui contiendra les retours de la base de données :
    for($i = 0; $i < $nombre_jours; $i++){
        $out = array();
        $in = $data_in["days"][$i];
        $timestamp = $in['timestamp'] / 1000;
        $out['timestamp'] = $timestamp;

        // Données générales sur l'année :
        $year = date('Y', $timestamp);
        $lit_year = $year;
        // Calcul du 1er dim. de l'Avent de l'année civile courante :
        $current_adv = calculate_adv($year);
        // Le timestamp est peut-être dans l'année liturgique suivante (année civile courante + 1) :
        if($timestamp >= $current_adv){
            $lit_year++;
        }
        $year_even = $lit_year % 2 == 0 ? '2' : '1';
        $out['even'] = $year_even;
        $year_letters = array('A', 'B', 'C');
        $year_letter = $year_letters[($lit_year - 2011) % 3];
        $out['year'] = $year_letter;

        // Jour civil :
        $weekday = $weekdays_fr[(int) date('w', $timestamp)];
        $out['weekday'] = $weekday;
        $day = date('j', $timestamp);
        if($day == 1){
            $day = '1\\textsuperscript{er}';
        }
        $month = $months_fr[date('m', $timestamp) - 1];
        $out['civil_day'] = $weekday.' '.$day.' '.$month.' '.$year;

        // Page de Tierce :
        switch($weekday){
            case "Dimanche":
                $tierce = 2;
                break;
            case "Lundi":
                $tierce = 4;
                break;
            case "Mardi":
                $tierce = 6;
                break;
            default:
                if(((($timestamp - mktime(0, 0, 0, 1, 6, 2019)) / 3600 / 24) / 7) % 2 == 0){
                    switch($weekday){
                        case "Mercredi":
                            $tierce = 9;
                            break;
                        case "Jeudi":
                            $tierce = 12;
                            break;
                        case "Vendredi":
                            $tierce = 15;
                            break;
                        case "Samedi":
                            $tierce = 17;
                            break;
                    }
                }
                else{
                    $tierce = 6;
                }
                break;
        }
        $out["tierce_page"] = $tierce;

        // Jour liturgique :
        // On remplit le out comme s'il n'y avait que le Temporal :
        $calc_tempo = calculate_tempo($timestamp);
        $tempo = $calc_tempo[0];
        $first_in_month = $calc_tempo[1];
        $out['tempo'] = $tempo;
        $out['first_in_month'] = $first_in_month;
        $liturg_time = explode('_', $tempo)[0];
        $back_tempo = $connect->query('SELECT * FROM Days WHERE Ref = "'.$tempo.'";');
        if($rep_tempo = $back_tempo->fetch()){
            $out['lit_day'] = $rep_tempo['Day'];
            // 1er vendredi du mois :
            if($liturg_time == 'pa' && $first_in_month && $weekday == 'Vendredi'){
                $out['lit_day'] = 'Premier vendredi du mois';
            }
            $force_tempo = $rep_tempo['Precedence'];
            $out['rang'] = $rep_tempo['Rang'];
            
            // Antienne de Tierce :
            if($rep_tempo['Tierce'] == NULL){
                if($liturg_time == 'adv'){
                    $adv_hebd = explode('_', $tempo)[1];
                    if($adv_hebd == '1'){
                        $out['tierce_ant'] = 'jucundare';
                    }
                    else if($adv_hebd == '2'){
                        $out['tierce_ant'] = 'urbs_fortitudinis';
                    }
                    else if($adv_hebd == '3'){
                        $out['tierce_ant'] = 'jerusalem_gaude';
                    }
                }
                else if($liturg_time == 'quadr'){
                    $out['tierce_ant'] = 'advenerunt_nobis';
                }
                else if($liturg_time == 'pass'){
                    $out['tierce_ant'] = 'judicasti_domine';
                }
                else if($liturg_time == 'tp'){
                    $out['tierce_ant'] = ($weekday == 'Dimanche') ? 'alleluia_dim_tp' : 'alleluia_feries_tp';
                }
                else{
                    $back_tierce = $connect->query('SELECT * FROM Tierce WHERE Page = "'.$out['tierce_page'].'";');
                    if($rep_tierce = $back_tierce->fetch()){
                        $out['tierce_ant'] = $rep_tierce['Antienne'];
                    }
                    $back_tierce->closeCursor();
                }
            }
            else{
                $out['tierce_ant'] = $rep_tempo['Tierce'];
            }

            // Oraisons :
            if($rep_tempo['Oraisons_MG'] != NULL && $mode == "Missel grégorien"){
                $out['orationes'] = array('source' => 'MG', 'ref' => explode('/', $rep_tempo['Oraisons_MG']));
                // 1er vendredi du mois :
                if($liturg_time == 'pa' && $first_in_month && $weekday == 'Vendredi'){
                    $out['orationes'] = array('source' => 'Files', 'ref' => 'pvm');
                }
            }
            else{
                // Temps de Noël :
                if($liturg_time == 'noel' && explode('_', $tempo)[1] == 'time'){
                    if(explode('_', $tempo)[2] == '2'){ // Temps avant l'Épiphanie.
                        $out['orationes'] = array('source' => 'Files', 'ref' => 'noel_time_before_ep_'.date('w', $timestamp));
                    }
                    else if(explode('_', $tempo)[2] == '3'){ // Temps après l'Épiphanie.
                        $out['orationes'] = array('source' => 'Files', 'ref' => 'noel_time_after_ep_'.date('w', $timestamp));
                    }
                }
                // Temps per annum :
                else if($liturg_time == 'pa'){ // Ne concerne que la 1ère semaine Per annum (toutes les autres sont dans le MG).
                    $out['orationes'] = array('source' => 'Files', 'ref' => 'pa_'.explode('_', $tempo)[1]);
                }
                // Tout le reste (carême, tp, etc.) :
                else{
                    $out['orationes'] = array('source' => 'Files', 'ref' => $rep_tempo['Ref']);
                }
            }
            
            // Lectures :
            $out['lectures_propres'] = False;
            if($rep_tempo['Lect_cycle'] == '3'){
                $out['readings'] = $rep_tempo['Ref'].'_'.$year_letter;
            }
            else{
                if($liturg_time == 'noel' && explode('_', $tempo)[1] == 'time'){
                    $out['readings'] = date('m', $timestamp).date('d', $timestamp);
                }
                else{
                    $out['readings'] = $rep_tempo['Ref'];
                }
            }
            // Séquence :
            $out['sequence'] = $rep_tempo['Sequence'];

            // Préface :
            if($rep_tempo['Pref'] != NULL){
                $pref = $rep_tempo['Pref'];
            }
            else{
                // Avent : préface I ou II de l'Avent, selon que avant ou après 17/12 :
                if($liturg_time == 'adv'){
                    $pref = $day < 17 ? 'adv_1' : 'adv_2';
                }
            }
            $back_pref = $connect->query('SELECT * FROM Prefaces WHERE Ref = "'.$pref.'";');
            if($rep_pref = $back_pref->fetch()){
                $out['pref'] = array('ref' => $rep_tempo['Pref'], 'name' => $rep_pref['Name'], 'page' => $rep_pref['Page'], 'name_la' => $rep_tempo['Pref_name_la'], 'name_fr' => $rep_tempo['Pref_name_fr']);
            }
            else{
                $out['pref'] = $pref;
            }
            $back_pref->closeCursor();
        }
        $back_tempo->closeCursor();
        
        // On cherche s'il y a un Sancto :
        $sancto = date('m', $timestamp).date('d', $timestamp);
        // Sancto spécial :
        if($sancto == '0202'){
            if($weekday == 'Dimanche'){
                $sancto = '0202_dim';
            }
            else{
                $sancto = '0202_fer';
            }
        }
        if($sancto == '0716'){
            if($weekday == 'Samedi'){
                $sancto = '0716_sam';
            }
            else{
                $sancto = '0716_fer';
            }
        }
        if($sancto == '0806'){
            if($weekday == 'Dimanche'){
                $sancto = '0806_dim';
            }
            else{
                $sancto = '0806_fer';
            }
        }
        if($sancto == '1109'){
            if($weekday == 'Dimanche'){
                $sancto = '1109_dim';
            }
            else{
                $sancto = '1109_fer';
            }
        }
        if($sancto == '1209' && date('l', $timestamp) == "Monday"){
            $sancto = '1208';
        }
        $out['sancto'] = $sancto;
        $force_sancto = 0;
        $back_sancto = $connect->query('SELECT * FROM Days WHERE Ref = "'.$sancto.'";');
        if($rep_sancto = $back_sancto->fetch()){
            $force_sancto = $rep_sancto['Precedence'];
            // Si le Sancto est plus fort, on fait les remplacements dans le out :
            if($force_sancto > $force_tempo){
                $out['lit_day'] = $rep_sancto['Day'];
                $out['rang'] = $rep_sancto['Rang'];
                if($rep_sancto['Tierce'] != NULL){
                    $out['tierce_ant'] = $rep_sancto['Tierce'];
                }
                if($rep_sancto['Oraisons_MG'] != NULL){
                    $out['orationes'] = array('source' => 'MG', 'ref' => explode('/', $rep_sancto['Oraisons_MG']));
                }
                else{
                    $out['orationes'] = array('source' => 'Files', 'ref' => $rep_sancto['Ref']);
                }
                if($rep_sancto['Lect_propres'] == 'True'){
                    $out['lectures_propres'] = True;
                    $out['readings'] = $rep_sancto['Ref'];
                    $out['cycle_lectures'] = $rep_sancto['Lect_cycle'];
                }
                if($rep_sancto['Sequence'] != NULL){
                    $out['sequence'] = $rep_sancto['Sequence'];
                }
                $back_pref = $connect->query('SELECT * FROM Prefaces WHERE Ref = "'.$rep_sancto['Pref'].'";');
                if($rep_pref = $back_pref->fetch()){
                    $out['pref'] = array('ref' => $rep_sancto['Pref'], 'name' => $rep_pref['Name'], 'page' => $rep_pref['Page'], 'name_la' => $rep_sancto['Pref_name_la'], 'name_fr' => $rep_sancto['Pref_name_fr']);
                }
                $back_pref->closeCursor();
            }
        }
        else{
            // Préface du 1er vendredi du mois (seulement si férie = s'il n'y a pas de saint):
            if($liturg_time == 'pa' && $first_in_month && $weekday == 'Vendredi'){
                $out['pref'] = array('ref' => 'pvm', 'name' => 'Préface');
            }
        }
        $back_sancto->closeCursor();

        // On cherche s'il y a une mémoire de la Ste Vierge :
        $out['tempo'] = $tempo;
        if($weekday == 'Samedi' and $force_tempo < 30 and $force_sancto < 30 and !preg_match("/^noel_.*/", $tempo) and !preg_match("/^tp_.*/", $tempo)){
            $bmv = date('j', $timestamp) < 8 ? 'icm' : date('n', $timestamp).'_'.ceil(date('j', $timestamp) / 7);
            $back = $connect->query('SELECT * FROM BMV WHERE Ref = "'.$bmv.'";');
            if($rep = $back->fetch()){
                $out['lit_day'] = $rep['Title'];
                $out['rang'] = 'Mémoire majeure';
                $out['tierce_ant'] = $timestamp < (mktime(0, 0, 0, 2, 2, $year)) ? 'quando_natus_es' : 'laeva_ejus';
                $out['orationes'] = array('source' => 'Files', 'ref' => 'bmv_'.$rep['CM']);
                $back_pref = $connect->query('SELECT * FROM Prefaces WHERE Ref = "'.$rep['Preface'].'";');
                if($rep_pref = $back_pref->fetch()){
                    $out['pref'] = array('ref' => $rep['Preface'], 'name' => $rep_pref['Name'], 'page' => $rep_pref['Page'], 'name_la' => NULL, 'name_fr' => NULL);
                }
                $back_pref->closeCursor();
            }
            $back->closeCursor();
        }

        // Asperges me :
        $out['asp'] = '';
        if($weekday == 'Dimanche'){
            if($liturg_time == 'tp'){
                $out['asp'] = '\\TitreB{Vidi aquam}\\Normal{(p. 71).}'; // Vidi aquam.
            }
            else if($liturg_time == 'adv' or $liturg_time == 'qua'){
                $out['asp'] = '\\TitreB{Asperges me II}\\Normal{(p. 71).}'; // Avent et Carême.
            }
            else if($day < 8 or $out['rang'] == 'Fête' or $out['rang'] == 'Solennité'){
                $out['asp'] = '\\TitreB{Asperges me}\\Normal{(p. 70).}';
            }
            else{
                $out['asp'] = '\\TitreB{Asperges me I}\\Normal{(p. 71).}';
            }
        }

        // Séquence :
        if($out['sequence'] != NULL){
            $back_seq = $connect->query('SELECT * FROM Sequences WHERE Ref = "'.$out['sequence'].'";');
            if($rep_seq = $back_seq->fetch()){
                $out['sequence'] = $rep_seq['Page'] == NULL ? array('source'=>'files', 'ref'=>$out['sequence']) : array('source'=>'mg', 'name'=>$rep_seq['Name'], 'page'=>$rep_seq['Page']); // TODO: Le 2e cas n'a pas été testé (séquences du MG).
            }
            $back_seq->closeCursor();
        }
        
        // Traitement des 5 grilles ('IN', 'GR', etc.) :
        $grid = array('IN', 'GR', 'AL', 'OF', 'CO', 'KY', 'GL', 'SA', 'CR');
        for($g = 0; $g < count($grid); $g++){
            $label = $grid[$g];
            $grid_ref = $in[$label];
            if($label == 'SA'){
                $back = $connect->query('SELECT * FROM Scores WHERE Type = "SA" AND Ref = "'.$grid_ref.'";');
                if($rep = $back->fetch()){
                    $out['SA'] = array($grid_ref, $rep['Page']);
                }
                else{
                    $out['SA'] = array('', '');
                }
                $back->closeCursor();
                $back = $connect->query('SELECT * FROM Scores WHERE Type = "AG" AND Ref = "'.$grid_ref.'";');
                if($rep = $back->fetch()){
                    $out['AG'] = array($grid_ref, $rep['Page']);
                }
                else{
                    $out['AG'] = array('', '');
                }
                $back->closeCursor();
            }
            else{
                if($label == 'GR' && $liturg_time == 'tp'){
                    $back = $connect->query('SELECT * FROM Scores WHERE Type = "AL" AND Ref = "'.$grid_ref.'";');
                }
                else{
                    $back = $connect->query('SELECT * FROM Scores WHERE Type = "'.$label.'" AND Ref = "'.$grid_ref.'";');
                }
                if(($rep = $back->fetch()) && $mode == "Missel grégorien"){
                    $out[$label] = array($rep['Name'], $rep['Page']);
                }
                else{
                    $out[$label] = array(str_replace(',', '_', $grid_ref), '');
                }
                $back->closeCursor();
            }
        }

        $data_out["days"][$i] = $out;
    }
    print(json_encode($data_out));
?>


<!DOCTYPE html>
<html>
	<head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="styles.css" />
        <title>Livrets retraites</title>
	</head>
	<body>
        <div id="page">
            <div id="data">
                <h1>†</h1>
                <h2>
                Livrets de messe</br>
                pour les retraitants
                </h2>

                <div id="input">
                    <label for="date_debut">Retraite du : </label><input id="date_debut" type="date" name="date_debut" />
                </div>

                <div id="output">
                    <?php
                        $tab_grid = array("IN", "GR", "AL", "OF", "CO", "KY", "GL", "SA", "CR");
                        for($i = 0; $i < 5; $i++){
                            $code = "";
                            $code = $code."<div class='jour' id='jour_".$i."'></div>";
                            $code = $code."<table>";
                            
                            // Saint :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Saint : </td>";
                            $code = $code."<td><input type='text' id='saint_".$i."' style='width:300px'></td>";
                            $code = $code."</tr>";
                            
                            //Rang :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Rang : </td>";
                            $code = $code."<td><select id='rang_".$i."' style='width:200px'><option value=''></option><option value='Solennité'>Solennité</option><option value='Fête'>Fête</option><option value='Mémoire majeure'>Mémoire majeure</option><option value='Mémoire mineure'>Mémoire mineure</option></select></td>";
                            $code = $code."</tr>";
                            
                            // Tierce :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Tierce : </td>";
                            $code = $code."<td><input type='text' id='tierce_".$i."' style='width:300px' value='AM/'></td>";
                            $code = $code."<td class='label'>Page : </td><td><input type='text' id='tierce_page_".$i."' style='width:30px'></td>";
                            $code = $code."</tr>";
                            
                            // Oraisons dans le MG :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Oraisons : </td>";
                            $code = $code."<td><input type='radio' class='radio' name='oraisons_".$i."' value='mg' checked id='or_mg_radio_".$i."' /><label class='label' for='or_mg_radio_".$i."'>Missel Grégo pp. :</label><input class='input_margin' type='text' id='or_mg_".$i."' style='width:30px' enabled><input class='input_margin' type='text' id='so_mg_".$i."' style='width:30px' enabled><input class='input_margin' type='text' id='pc_mg_".$i."' style='width:30px' enabled></td>";
                            $code = $code."</tr>";

                            // Oraisons dans fichiers textes :
                            $code = $code."<tr>";
                            $code = $code."<td></td>";
                            $code = $code."<td><input type='radio' class='radio' name='oraisons_".$i."' value='files' id='or_files_radio_".$i."' /><label class='label' for='or_files_radio_".$i."'>Fichiers :</label><input class='input_margin' type='text' id='oraisons_files_".$i."' style='width:200px' disabled></td>";
                            $code = $code."</tr>";

                            //Préface normale :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Préface : </td>";
                            $code = $code."<td><input type='radio' class='radio' name='pref_".$i."' value='mg' checked id='pref_norm_radio_".$i."' /><label class='label' for='pref_norm_radio_".$i."'>Missel Grégo :</label><select class='input_margin' id='pref_norm_".$i."' style='width:200px' enabled>";
                            $code = $code."<option value=''></option>";
                            $code = $code."<option value='adv_1'>Avent I</option>";
                            $code = $code."<option value='adv_2'>Avent II</option>";
                            $code = $code."<option value='noel_1'>Noël I</option>";
                            $code = $code."<option value='noel_2'>Noël II</option>";
                            $code = $code."<option value='noel_3'>Noël III</option>";
                            $code = $code."<option value='epiph'>Épiphanie</option>";
                            $code = $code."<option value='bapt'>Baptême de N.S.</option>";
                            $code = $code."<option value='quadr_dim_1'>1er dimanche de Carême</option>";
                            $code = $code."<option value='quadr_dim_2'>2e dimanche de Carême</option>";
                            $code = $code."<option value='quadr_dim_3'>3e dimanche de Carême</option>";
                            $code = $code."<option value='quadr_dim_4'>4e dimanche de Carême</option>";
                            $code = $code."<option value='quadr_dim_5'>5e dimanche de Carême</option>";
                            $code = $code."<option value='quadr_1'>Carême I</option>";
                            $code = $code."<option value='quadr_2'>Carême II</option>";
                            $code = $code."<option value='quadr_3'>Carême III</option>";
                            $code = $code."<option value='quadr_4'>Carême IV</option>";
                            $code = $code."<option value='passion_1'>Passion I</option>";
                            $code = $code."<option value='rameaux'>Rameaux</option>";
                            $code = $code."<option value='tp_1'>Temps Pascal I</option>";
                            $code = $code."<option value='tp_2'>Temps Pascal II</option>";
                            $code = $code."<option value='tp_3'>Temps Pascal III</option>";
                            $code = $code."<option value='tp_4'>Temps Pascal IV</option>";
                            $code = $code."<option value='tp_5'>Temps Pascal V</option>";
                            $code = $code."<option value='ascension_1'>Ascension I</option>";
                            $code = $code."<option value='ascension 2'>Ascension II</option>";
                            $code = $code."<option value='pent'>Pentecôte</option>";
                            $code = $code."<option value='trinite'>Trinité</option>";
                            $code = $code."<option value='euch_1'>Eucharistie I</option>";
                            $code = $code."<option value='euch_2'>Eucharistie II</option>";
                            $code = $code."<option value='sacre_coeur'>Sacré-Cœur</option>";
                            $code = $code."<option value='christ_roi'>Christ-Roi</option>";
                            $code = $code."<option value='pa_dim_1'>Dimanches Per Annum I</option>";
                            $code = $code."<option value='pa_dim_2'>Dimanches Per Annum II</option>";
                            $code = $code."<option value='pa_dim_3'>Dimanches Per Annum III</option>";
                            $code = $code."<option value='pa_dim_4'>Dimanches Per Annum IV</option>";
                            $code = $code."<option value='pa_dim_5'>Dimanches Per Annum V</option>";
                            $code = $code."<option value='pa_dim_6'>Dimanches Per Annum VI</option>";
                            $code = $code."<option value='pa_dim_7'>Dimanches Per Annum VII</option>";
                            $code = $code."<option value='pa_dim_8'>Dimanches Per Annum VIII</option>";
                            $code = $code."<option value='com_1'>Commune I</option>";
                            $code = $code."<option value='com_2'>Commune II</option>";
                            $code = $code."<option value='com_3'>Commune III</option>";
                            $code = $code."<option value='com_4'>Commune IV</option>";
                            $code = $code."<option value='com_5'>Commune V</option>";
                            $code = $code."<option value='com_6'>Commune VI</option>";
                            $code = $code."<option value='marie_1'>BMV I</option>";
                            $code = $code."<option value='marie_2'>BMV II</option>";
                            $code = $code."<option value='ap_1'>Apôtres I</option>";
                            $code = $code."<option value='ap_2'>Apôtres II</option>";
                            $code = $code."<option value='virg'>Vierges et religieux</option>";
                            $code = $code."<option value='saints_1'>Saints I</option>";
                            $code = $code."<option value='saints_2'>Saints II</option>";
                            $code = $code."<option value='presentation'>Présentation</option>";
                            $code = $code."<option value='saint_joseph'>Saint Joseph</option>";
                            $code = $code."<option value='annonciation'>Annonciation</option>";
                            $code = $code."<option value='saint_jean_bapt'>Saint Jean-Baptiste</option>";
                            $code = $code."<option value='sts_pierre_paul'>Saints Pierre et Paul</option>";
                            $code = $code."<option value='marie_mad'>Sainte Marie-Madeleine</option>";
                            $code = $code."<option value='transfig'>Transfiguration</option>";
                            $code = $code."<option value='assomption'>Assomption</option>";
                            $code = $code."<option value='sainte_croix'>Sainte Croix</option>";
                            $code = $code."<option value='toussaint'>Toussaint</option>";
                            $code = $code."<option value='imm_conc'>Immaculée Conception</option>";
                            $code = $code."<option value='dedic'>Dédicace</option>";
                            $code = $code."<option value='def_1'>Défunts I</option>";
                            $code = $code."<option value='def_2'>Défunts II</option>";
                            $code = $code."<option value='def_3'>Défunts III</option>";
                            $code = $code."<option value='def_4'>Défunts IV</option></select></td>";
                            $code = $code."</tr>";

                            // Préface avec nom de saint :
                            $code = $code."<tr>";
                            $code = $code."<td></td>";
                            $code = $code."<td><input type='radio' class='radio' name='pref_".$i."' value='file' id='pref_saint_radio_".$i."' /><label class='label' for='pref_saint_radio_".$i."'>Avec nom de saint :</label><select class='input_margin' id='pref_saint_".$i."' style='width:172px' disabled><option value=''></option><option value='marie_mad'>Sainte Marie-Madeleine</option><option value='marie_1'>Marie 1</option><option value='saints_1'>Saints 1</option><option value='careme_1'>Carême 1</option></select></td>";
                            $code = $code."</tr>";

                            // Lectures :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Lectures : </td>";
                            $code = $code."<td><input type='text' id='lectures_".$i."' style='width:200px'><select style='width:85px' class='input_margin' id='nb_lect_".$i."'><option value='1'>1 lecture</option><option value='2'>2 lectures</option></select></td>";
                            $code = $code."</tr>";

                            // Grille des pièces :
                            $code = $code."</table>";
                            $code = $code."<table class='grid'>";
                            $code = $code."<tr>";
                            for($j = 0; $j < 9; $j++){
                                $code = $code."<td class='grid_elem'><div class='label_cap' id='grid_label_".$i.$j."'>".$tab_grid[$j]."</div><input type='text' id='grid_value_".$i.$j."' style='width:30px'></input></td>";}
                            $code = $code."</tr>";
                            $code = $code."</table>";
                            print($code);}
                    ?>
                </div>
            </div>
            <div id="tex">
                <textarea id="tex_area">Fichier LaTeX</textarea>
                <input type="button" id="go" value="Voir le pdf" style="width:300px; font-size:24px"></input>
            </div>
            <div id="overlay_wait">
                <img src="wait.gif" style="margin-bottom:20px"></br>
                <p>Votre livret est en cours de préparation…</p>
                <p>Veuillez patienter quelques instants…</p>
            </div>
            <form id="overlay_download" action="livret.pdf" target="_blank">
                <p>Votre livret est prêt !</p>
                <input id="view" type="submit" value="Afficher" style="margin-top:20px" />
            </form>
        </div>
	</body>
    <script src="jquery-3.2.1.js"></script>
    <script src="script.js"></script>
</html>


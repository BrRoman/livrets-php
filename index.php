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
                            //Jour civil :
                            $code = $code."<div class='jour_civil' id='jour_civil_".$i."'></div>";
                            $code = $code."<table>";
                            
                            // Saint :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Jour : </td>";
                            $code = $code."<td class='label' id='jour_lit_'".$i."'></td>";
                            $code = $code."</tr>";
                            
                            //Rang :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Rang : </td>";
                            $code = $code."<td class='label' id='rang_'".$i."'></td>";
                            $code = $code."</tr>";
                            
                            // Tierce :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Tierce : </td>";
                            $code = $code."<td class='label' id='tierce_'".$i."'></td>";
                            $code = $code."</tr>";
                            
                            // Oraisons :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Oraisons : </td>";
                            $code = $code."<td class='label' id='oraisons_'".$i."'></td>";
                            $code = $code."</tr>";

                            //Préface :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Préface : </td>";
                            $code = $code."<td class='label' id='preface_'".$i."'></td>";
                            $code = $code."</tr>";

                            // Lectures :
                            $code = $code."<tr>";
                            $code = $code."<td class='label'>Lectures : </td>";
                            $code = $code."<td class='label' id='lectures_'".$i."'></td>";
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
    <script src="scripts/jquery-3.2.1.js"></script>
    <script src="scripts/calendar.js"></script>
    <script src="scripts/latex.js"></script>
    <script src="scripts/main.js"></script>
</html>


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
                        $tab_grid = array("IN", "GR", "AL", "OF", "CO", "KY", "GL", "SA", "CR", "TI");
                        for($i = 0; $i < 5; $i++){
                            $code = "";
                            //Jour civil :
                            $code = $code."<div class='civil_day' id='civil_day_".$i."'></div>";
                            
                            // Grille des pièces :
                            $code = $code."<table class='grid'>";
                            $code = $code."<tr>";
                            for($j = 0; $j < 9; $j++){
                                $code = $code."<td class='grid_elem'><div class='label_cap' id='grid_label_".$i.$j."'>".$tab_grid[$j]."</div><input type='text' id='grid_value_".$i.$j."' style='width:30px'></input></td>";
                            }
                            $code = $code."<td class='grid_elem'><div class='label_cap' id='grid_label_".$i."'>".$tab_grid[9]."</div><select id='tierce_page_".$i."'><option value=''></option><option value='2'>2</option><option value='4'>4</option><option value='6'>6</option><option value='9'>9</option><option value='12'>12</option><option value='15'>15</option><option value='17'>17</option></select></td>";
                            $code = $code."</tr>";
                            $code = $code."</table>";
                            print($code);}
                    ?>
                </div>
            </div>
            <div id="tex">
                <textarea id="tex_area"></textarea>
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
    <script src="latex.js"></script>
    <script src="main.js"></script>
</html>


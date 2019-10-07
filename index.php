<!DOCTYPE html>
<html>
	<head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="static/jquery-ui-1.12.1/jquery-ui.min.css">
        <link rel="stylesheet" href="static/fontawesome-free-5.7.1-web/css/all.min.css">
        <link rel="stylesheet" href="static/bootstrap-4.0.0-dist/css/bootstrap.min.css">
        <link rel="stylesheet/less" type="text/css" href="static/css/livrets.less">
        <style>@font-face{font-family: "LucidaHW"; src: url("static/fonts/TR Lucida Handwriting Italic.ttf")}</style>
        <title>Livrets retraites</title>
	</head>
	<body>
        <div id="page" class="d-flex flex-column justify-content-center align-items-center">
            <div id="data" class="col-lg-7 d-flex flex-column align-items-center">
                <h1>†</h1>
                <h2>
                Livret de messe
                </h2>

                <div id="input" class="row justify-content-center">
                    <table>
                        <tr>
                            <td><label>Date de départ : </label></td>
                            <td><input id="date_depart" type="text"></td>
                        </tr>
                        <tr>
                            <td><label>Nombre de jours : </label></td>
                            <td><input id="nombre_jours" type="number" min="1" max="15" value="5"></td>
                        </tr>
                        <tr>
                            <td><label>Mode : </label></td>
                            <td>
                                <select id="select_mode">
                                    <option>Missel grégorien</option>
                                    <option>Livret complet</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div id="output">
                        <?php
                            $tab_grid = array("IN", "GR", "AL", "OF", "CO", "KY", "GL", "SA", "CR");
                            for($i = 0; $i < 15; $i++){
                                $code = "<div id='line_".$i."' class='line'>";
                                //Jour civil :
                                $code = $code."";
                                
                                // Grille des pièces :
                                $code = $code."<table>";
                                $code = $code."<thead><tr><th class='civil_day' id='civil_day_".$i."' colspan='9'></th></tr></thead><tbody class='grid d-flex justify-content-center align-items-center'><tr>";
                                for($j = 0; $j < 9; $j++){
                                    $code = $code."<td class='grid_elem'><div class='label_cap' id='grid_label_".$i.$j."'>".$tab_grid[$j]."</div><input type='text' id='grid_value_".$i.$j."'></input></td>";
                                }
                                $code = $code."</tr></tbody>";
                                $code = $code."</table></div>";
                                print($code);}
                        ?>
                    </div>
                </div>
            </div>
            <div id="tex">
                <div class="d-flex flex-column align-items-center">
                    <textarea id="tex_area" class="align-self-center mt-5 mb-5"></textarea>
                    <input type="button" id="go" class="btn btn-primary" value="Voir le PDF"></input>
                </div>
            </div>
            <div id="overlay_wait" class="flex-column justify-content-center align-items-center">
                <img src="static/images/wait.gif" style="margin-bottom:20px"></br>
                <p>Votre livret est en cours de préparation…</p>
                <p>Veuillez patienter quelques instants…</p>
            </div>
            <form id="overlay_download" class="flex-column justify-content-center align-items-center" action="livret/livret.pdf" target="_blank">
                <p>Votre livret est prêt !</p>
                <input id="view" type="submit" value="Afficher" style="margin-top:20px">
            </form>
        </div>

        <script src="static/jquery-3.3.1.min.js"></script>
        <script src="static/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        <script src="static/bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
        <script src="static/js/less.min.js"></script>
        <script src="static/js/latex.js"></script>
        <script src="static/js/livrets.js"></script>
	</body>
</html>


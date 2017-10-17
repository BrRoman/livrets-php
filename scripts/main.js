$(document).ready(function(){
    var jours_fr = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    var months_fr = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

    // Quand une date est sélectionnée, le cas échéant faire apparaître le formulaire et afficher l'entête du LaTeX :
    $("#date_debut").change(function(e){
        var date_timestamp = e.target.valueAsNumber;
        for(var i = 0; i < 5; i++){
            var date = new Date(date_timestamp + ((i + 1) * 24 * 3600 * 1000));
            var weekday = jours_fr[date.getDay()];
            var day = date.getDate();
            if(day == 1){
                day = "1er";
            }
            var month = months_fr[date.getMonth()];
            var year = date.getFullYear();
            $("#jour_civil_" + i).text(weekday + " " + day + " " + month + " " + year + " :");
            var liturgical_day = calculate_day(date);
            fill_day(liturgical_day);
        }
        $("#output").css("display", "flex");
        update_latex();
    });

    // Mise à jour du LaTeX à chaque changement de pièce :
    for(var i = 0; i < 5; i++){
        for(var j = 0; j < 9; j++){
            $("#grid_value_" + i + j).keyup(function(){
                update_latex();
            });
        }
    }

    // Clic sur les boutons "Compiler le pdf" et "Voir le pdf" des 2 overlays :
    $("#go").click(function(){
        $("#overlay_wait").css("display", "flex");
        $.post(
        "script.php",
        {"tex": $("#tex_area").val()},
        function(retour){
            $("#overlay_wait").css("display", "none");
            $("#overlay_download").css("display", "flex");
        },
        "text"
        );
    });
    $("#view").click(function(){
        $("#overlay_download").css("display", "none");
    });
});

function update_latex(){
    var grid_json = {};
    for(var i = 0; i < 5; i++){
        grid_json["#pref_norm_" + i] = ["PR", $("#pref_norm_" + i).val()];
        for(var j = 0; j < 9; j++){
            grid_json["#grid_value_" + i + j] = [$("#grid_label_" + i + j).text(), $("#grid_value_" + i + j).val()];
        }
    }
    request(grid_json);
}

function request(grid_json){
    $.post(
        "request.php",
        grid_json,
        function(data){
            write_tex(data);
        },
        "json"
    );
}

function fill_day(lit){
    // Remplit les champs "Saint", "Rang" etc. en fonction du jour liturgique.
}


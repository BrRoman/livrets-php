$(document).ready(function(){
    // Quand une date est sélectionnée, rafraîchir le formulaire et mettre à jour le LaTeX :
    $("#date_debut").change(function(e){
        $("#output").css("display", "flex");
        update();
    });

    // Quand un chant 'pièce' (Introït, Kyrie, etc.) est modifié, mettre à jour le LaTeX :
    for(var i = 0; i < 5; i++){
        for(var j = 0; j < 9; j++){
            $("#grid_value_" + i + j).keyup(function(){
                update();
            });
        }
        $("#tierce_page_" + i).change(function(){
            update();
        });
    }

    // Fonctions associées au clic sur les boutons "Compiler le pdf" et "Voir le pdf" des 2 overlays :
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

function update(){
    var days_fr = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    var months_fr = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
    var data = {};
    var start_date = $("#date_debut")[0].valueAsNumber;
    for(var i = 0; i < 5; i++){
        var date_timestamp = start_date + ((i + 1) * 24 * 3600 * 1000);
        var date = new Date(date_timestamp);

        // Update jour civil :
        var weekday = days_fr[date.getDay()];
        var num_day = date.getDate();
        if(num_day == 1){
            num_day = "1er";
        }
        var month = months_fr[date.getMonth()];
        var year = date.getFullYear();
        var civil_day = weekday + " " + num_day + " " + month + " " + year + " :";
        $("#civil_day_" + i).text(civil_day);
        
        // Mise en objet de toutes les données du formulaire :
        var grid = {};
        grid["timestamp"] = date_timestamp;
        for(var j = 0; j < 9; j++){
            grid[$("#grid_label_" + i + j).text()] = $("#grid_value_" + i + j).val();
        }
        grid["tierce_page"] = $("#tierce_page_" + i).val();
        data[i] = grid;
    }
    request(data);
}

function request(data_json){
    console.log("Input =", data_json);
    $.post(
        "scripts/request.php",
        data_json,
        function(data){
            console.log("Back =", data);
            write_latex(data);
        },
        "json"
    );
}


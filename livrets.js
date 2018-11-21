$(document).ready(function(){
    // Lier le datepicker à l'input date :
    $(function(){
        $("#date_debut").datepicker({
            dateFormat: "dd/mm/yy",
            minDate: null,
            dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
            monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthNamesShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc"]
        });
    });

    // Quand une date est sélectionnée, rafraîchir le formulaire et mettre à jour le LaTeX :
    $("#date_debut").change(function(e){
        $("#output").css("display", "flex");
        update();
    });

    // Quand un chant 'pièce' (Introït, Kyrie, etc.) est modifié, mettre à jour le LaTeX :
    for(var i = 0; i < 5; i++){
        for(var j = 0; j < 9; j++){
            $("#grid_value_" + i + j).keyup(function(){
                var page = $(this).val();
                page = page.replace(".", ",");
                $(this).val(page);
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
            console.log("Back from compilation:", retour);
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
    var start_date_split = $("#date_debut")[0].value.split("/");
    var start_date = new Date(start_date_split[2], start_date_split[1] - 1, start_date_split[0]).getTime();
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
    console.log("js =", data_json);
    $.post(
        "request.php",
        data_json,
        function(data){
            console.log("php =", data);
            write_latex(data);
        },
        "json"
    );
}


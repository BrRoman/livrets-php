$(document).ready(function(){
    // Lier le datepicker à l'input date :
    $(function(){
        var values = {
            dateFormat: "dd/mm/yy",
            minDate: null,
            dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
            monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthNamesShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc"]
        }
        $("#date_jour").datepicker(values);
        $("#date_retraite").datepicker(values);
    });

    // Activer ou désactiver les input date en fonction du radio cliqué :
    $("#radio_jour").click(function(){
        $("#date_jour").attr("disabled", false);
        $("#date_retraite").attr("disabled", true);
        $("#date_retraite").val("");
    });
    $("#radio_retraite").click(function(){
        $("#date_retraite").attr("disabled", false);
        $("#date_jour").attr("disabled", true);
        $("#date_jour").val("");
    });

    // Quand une date est sélectionnée, rafraîchir le formulaire et mettre à jour le LaTeX :
    $("#date_jour").change(function(){
        $("#output").css("display", "block");
        $("#line_1").css("display", "none");
        $("#line_2").css("display", "none");
        $("#line_3").css("display", "none");
        $("#line_4").css("display", "none");
        $("#tex").css("display", "block");
        update();
    });
    $("#date_retraite").change(function(){
        $("#output").css("display", "block");
        $("#line_1").css("display", "block");
        $("#line_2").css("display", "block");
        $("#line_3").css("display", "block");
        $("#line_4").css("display", "block");
        $("#tex").css("display", "block");
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
    if($("#radio_retraite").prop("checked")){
        var start_date_split = $("#date_retraite")[0].value.split("/");
        var start_date = new Date(start_date_split[2], start_date_split[1] - 1, start_date_split[0]).getTime();
        for(var i = 0; i < 5; i++){
            var date_timestamp = start_date + ((i + 1) * 24 * 3600 * 1000);
            var date = new Date(date_timestamp);

            // Update jour civil :
            var weekday = days_fr[date.getDay()];
            var num_day = date.getDate();
            if(num_day == 1){
                num_day = "1<sup>er</sup>";
            }
            var month = months_fr[date.getMonth()];
            var year = date.getFullYear();
            var civil_day = weekday + " " + num_day + " " + month + " " + year + " :";
            $("#civil_day_" + i).html(civil_day);
            
            // Mise en objet de toutes les données du formulaire :
            var grid = {};
            grid["timestamp"] = date_timestamp;
            for(var j = 0; j < 9; j++){
                grid[$("#grid_label_" + i + j).text()] = $("#grid_value_" + i + j).val();
            }
            grid["tierce_page"] = $("#tierce_page_" + i).val();
            data[i] = grid;
        }
    }
    else if($("#radio_jour").prop("checked")){
        var date_split = $("#date_jour")[0].value.split("/");
        var date_timestamp = new Date(date_split[2], date_split[1] - 1, date_split[0]).getTime();
        var date = new Date(date_timestamp);

        // Update jour civil :
        var weekday = days_fr[date.getDay()];
        var num_day = date.getDate();
        if(num_day == 1){
            num_day = "1<sup>er</sup>";
        }
        var month = months_fr[date.getMonth()];
        var year = date.getFullYear();
        var civil_day = weekday + " " + num_day + " " + month + " " + year + " :";
        $("#civil_day_0").html(civil_day);
        
        // Mise en objet de toutes les données du formulaire :
        var grid = {};
        grid["timestamp"] = date_timestamp;
        for(var i = 0; i < 9; i++){
            grid[$("#grid_label_0" + i).text()] = $("#grid_value_0" + i).val();
        }
        grid["tierce_page"] = $("#tierce_page_0").val();
        data[0] = grid;
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


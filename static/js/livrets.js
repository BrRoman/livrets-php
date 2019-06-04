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
        $("#date_depart").datepicker(values);
    });

    // Si action sur les éléments d'affichage, rafraîchir le formulaire et mettre à jour le LaTeX :
    $("#date_depart").change(function(){
        refresh();
    });
    $("#nombre_jours").change(function(){
        if($("#date_depart").val() != ""){
            refresh();
        }
    });
    $("#select_mode").change(function(){
        refresh();
    });

    // Quand un chant 'pièce' (Introït, Kyrie, etc.) est modifié, mettre à jour le LaTeX :
    for(var i = 0; i < $("#nombre_jours").val(); i++){
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
            "php/compile_latex.php",
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

// Fonction pour rafraîchir le formulaire et updater le LaTeX :
function refresh(){
    $("#output").css("display", "block");
    $(".line").css("display", "none");
    for(var i = 0; i < $("#nombre_jours").val(); i++){
        $("#line_" + i).css("display", "block");
    }
    $("#tex").css("display", "block");
    update();
}

function update(){
    var days_fr = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    var months_fr = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
    var data = {};
    data["mode"] = $("#select_mode option:selected").text();
    data["days"] = {};
    var start_date_split = $("#date_depart")[0].value.split("/");

    // Calcul des changements d'heure pour ensuite ajouter 1 heure aux timestamps compris entre les deux (cf. infra).
    // Changement d'heure de printemps (dernier dimanche de mars) :
    for(var i = 25; i <= 31; i++){
        var date = new Date(parseInt(start_date_split[2]), 2, i);
        if(date.getDay() == 0){
            var changement_heure_printemps = date.getTime() + (3600 * 1000);
        }
    }
    // Changement d'heure d'automne (dernier dimanche d'octobre) :
    for(var i = 24; i < 30; i++){
        var date = new Date(parseInt(start_date_split[2]), 9, i);
        if(date.getDay() == 0){
            var changement_heure_automne = date.getTime() + (3600 * 1000);
        }
    }

    var start_date = new Date(start_date_split[2], start_date_split[1] - 1, start_date_split[0]).getTime();
    for(var i = 0; i < $("#nombre_jours").val(); i++){
        var date_timestamp = start_date + (i * 24 * 3600 * 1000);

        // On rajoute 3600 secondes en été :
        if(date_timestamp >= changement_heure_printemps && date_timestamp < changement_heure_automne){
            date_timestamp = date_timestamp + (3600 * 1000);
        }

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
        data["days"][i] = grid;
    }

    console.log("js =", data);
    $.post(
        "php/request.php",
        data,
        function(back){
            console.log("php =", back);
            write_latex(back);
        },
        "json"
    );
}


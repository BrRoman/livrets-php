$(document).ready(function(){
    // Quand une date est sélectionnée, rafraîchir les dates, calculer le contenu des 5 jours et mettre à jour le LaTeX :
    $("#date_debut").change(function(e){
        var data = {}
        var date_timestamp = e.target.valueAsNumber;
        for(var i = 0; i < 5; i++){
            var date = new Date(date_timestamp + ((i + 1) * 24 * 3600 * 1000));
            var day_data = calculate_day(date);// Renvoie un objet contenant les données du jour.
            data["day_" + i] = day_data;
            $("#civil_day_" + i).text(data["day_" + i]["civil_day"]);
        }
        $("#output").css("display", "flex");
        console.log(data);
        write_latex(data);
    });

    // À chaque changement de pièce, mise à jour du LaTeX :
    for(var i = 0; i < 5; i++){
        for(var j = 0; j < 9; j++){
            $("#grid_value_" + i + j).keyup(function(){
                write_latex(data);
            });
        }
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


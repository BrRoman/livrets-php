$(document).ready(function(){
    var jours_fr = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    var months_fr = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

    // Quand une date est sélectionnée, le cas échéant faire apparaître le formulaire et afficher l'entête du LaTeX :
    $("#date_debut").change(function(e){
        var date_timestamp = e.target.valueAsNumber;
        for(var i = 0; i < 5; i++){
            date = new Date(date_timestamp + ((i + 1) * 24 * 3600 * 1000));
            var weekday = jours_fr[date.getDay()];
            var day = date.getDate();
            if(day == 1){
                day = "1er";
            }
            var month = months_fr[date.getMonth()];
            var year = date.getFullYear();
            $("#jour_" + i).text(weekday + " " + day + " " + month + " " + year + " :");
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
            console.log("retour =", retour);
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

function write_tex(pages_json){
    console.log(pages_json);
    var page = "";
    var tex = tex_header(Date.parse($("#date_debut").val()));
    for(var i = 0; i < 5; i++){
        // Jour liturgique :
        tex += "\n\n\\section{" + $("#jour_" + i).text().substring(0, $("#jour_" + i).text().lastIndexOf(" :")) + "}\n\n";
        var saint = "#saint_" + i;
        if($(saint).val() != ""){
            tex += "\\Saint{" + $(saint).val() + "}{";
            // Rang liturgique :
            var rang = "#rang_" + i;
            if($(rang).val() != ""){
                tex += $(rang).val() + "}\n\n";
                // Ouverture de la célébration :
                tex += "\\TitreB{Ouverture de la célébration~:}\\Normal{p. 7.}\n\n"
            }
            else{
                tex += "}\n\n"
            }
        }

        // Introït :
        var introit = "#grid_value_" + i + "0";
        if($(introit).val() != ""){
            var page = pages_json[introit];
            if(page[1] != ""){
                tex += "\\TitreB{Antienne d'introït~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Antienne d'introït~:}\\par\n";
                tex += "\\Partoche{GR/in_" + $(introit).val().replace(",", "_") + "}\n\n";
            }
        }
        
        // Tierce :
        var tierce = "#tierce_" + i;
        if($(tierce).val() != "AM/"){
            tex += "\\Tierce{" + $(tierce).val() + "}"
            var tierce_page = "#tierce_page_" + i;
            if($(tierce_page).val() != ""){
                tex += "{" + $(tierce_page).val() + "}\n\n";
            }
        }
        
        // Kyrie :
        var kyrie = "#grid_value_" + i + "5";
        if($(kyrie).val() != ""){
            page = pages_json[kyrie];
            tex += "\\TitreB{Kyrie " + $(kyrie).val() + "}\\Normal{(p. " + page[1] + ")}\n\n";
        }
        
        // Gloria :
        var gloria = "#grid_value_" + i + "6";
        if($(gloria).val() != ""){
            page = pages_json[gloria];
            tex += "\\TitreB{Gloria " + $(gloria).val() + "}\\Normal{(p. " + page[1] + ")}\n\n";
        }
        
        // Oraison :
        if($("#or_mg_radio_" + i)[0].checked){
            var or = "#or_mg_" + i;
            if($(or).val() != ""){
                tex += "\\TitreB{Oraison~:}\\Normal{p. " + $(or).val() + ".}\n\n";
            }
        }
        if($("#or_files_radio_" + i)[0].checked){
            var or = "#oraisons_files_" + i;
            if($(or).val() != ""){
                tex += "\\Oraison{Oraison}{or}{" + $(or).val() + "}\n\n";
            }
        }

        // 1ère lecture :
        if($("#lectures_" + i).val() != ""){
            tex += "\\Lecture{Première lecture}{" + $("#lectures_" + i).val() + "_1}\n\n";
        }

        // Graduel :
        var graduel = "#grid_value_" + i + "1";
        if($(graduel).val() != ""){
            page = pages_json[graduel];
            if(page[1] != ""){
                tex += "\\TitreB{Graduel~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Graduel~:}\\par\n";
                tex += "\\Partoche{GR/gr_" + $(graduel).val().replace(",", "_") + "}\n\n";
            }
        }

        // 2e lecture :
        if($("#nb_lect_" + i).val() == 2){
            if($("#lectures_" + i).val() != ""){
                tex += "\\Lecture{Deuxième lecture}{" + $("#lectures_" + i).val() + "_2}\n\n";
            }
        }

        // Alleluia :
        var alleluia = "#grid_value_" + i + "2";
        if($(alleluia).val() != ""){
            page = pages_json[alleluia];
            if(page[1] != ""){
                tex += "\\TitreB{Alleluia~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Alleluia~:}\\par\n";
                tex += "\\Partoche{GR/al_" + $(alleluia).val().replace(",", "_") + "}\n\n";
            }
        }

        // Évangile :
        if($("#lectures_" + i).val() != ""){
            tex += "\\Lecture{Évangile}{" + $("#lectures_" + i).val() + "_ev}\n\n";
        }

        // Credo :
        var credo = "#grid_value_" + i + "8";
        if($(credo).val() != ""){
            page = pages_json[credo];
            tex += "\\TitreB{Credo " + $(credo).val() + "} \\Normal{(p. " + page[1] + ").}\n\n"
        }

        // Antienne d'offertoire :
        var ant_off = "#grid_value_" + i + "3";
        if($(ant_off).val() != ""){
            page = pages_json[ant_off];
            if(page[1] != ""){
                tex += "\\TitreB{Antienne d'offertoire~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Antienne d'offertoire~:}\\par\n";
                tex += "\\Partoche{GR/of_" + $(ant_off).val().replace(",", "_") + "}\n\n";
            }
        }

        // Super oblata :
        if($("#or_mg_radio_" + i)[0].checked){
            var so = "#so_mg_" + i;
            if($(so).val() != ""){
                tex += "\\TitreB{Prière sur les offrandes~:}\\Normal{p. " + $(so).val() + ".}\n\n";
            }
        }
        if($("#or_files_radio_" + i)[0].checked){
            var so = "#oraisons_files_" + i;
            if($(so).val() != ""){
                tex += "\\Oraison{Prière sur les offrandes}{so}{" + $(so).val() + "}\n\n";
            }
        }

        // Préface :
        if($("#pref_norm_radio_" + i)[0].checked){
            var pref = "#pref_norm_" + i;
            if($(pref).val() != ""){
                page = pages_json[pref];
                if(page[1] != ""){
                    tex += "\\TitreB{" + page[0] + "~:}\\Normal{p. " + page[1] + "}\n\n";
                }
                else{
                    tex += "\\Preface{" + page[0] + "}{" + $(pref).val() + "}\n\n";
                }
            }
        }
        /*
        if($("#pref_saint_radio_" + i)[0].checked){
            var or = "#pref_saint_" + i;
            if($(or).val() != ""){
                tex += "\\Preface{Pref}{" + $(pref).val() + "}\n\n";
            }
        }
        */
        
        // Sanctus-Agnus :
        var sanctus_agnus = "#grid_value_" + i + "7";
        if($(sanctus_agnus).val() != ""){
            page = pages_json[sanctus_agnus]["SA"];
            tex += "\\TitreB{Sanctus " + $(sanctus_agnus).val() + "}\\Normal{(p. " + page[1] + ")}\n\n";
            tex += "\\TitreB{Prière eucharistique n. 1}\\Normal{(p. 22)}\n\n";
            tex += "\\TitreB{Rites de communion~:}\\Normal{(p. 41)}\n\n";
            page = pages_json[sanctus_agnus]["AG"];
            tex += "\\TitreB{Agnus Dei " + $(sanctus_agnus).val() + "}\\Normal{(p. " + page[1] + ")}\n\n";
        }
        
        // Antienne de communion :
        var comm = "#grid_value_" + i + "4";
        if($(comm).val() != ""){
            var page = pages_json[comm];
            if(page[1] != ""){
                tex += "\\TitreB{Antienne de communion~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Antienne de communion~:}\\par\n";
                tex += "\\Partoche{GR/co_" + $(comm).val().replace(",", "_") + "}\n\n";
            }
        }

        // Postcommunion :
        if($("#or_mg_radio_" + i)[0].checked){
            var pc = "#pc_mg_" + i;
            if($(pc).val() != ""){
                tex += "\\TitreB{Prière après la communion~:}\\Normal{p. " + $(pc).val() + ".}\n\n";
                tex += "\\TitreB{Conclusion :}{\\Normal{p. 47.}}\n\n";
            }
        }
        if($("#or_files_radio_" + i)[0].checked){
            var pc = "#oraisons_files_" + i;
            if($(pc).val() != ""){
                tex += "\\Oraison{Prière après la communion}{pc}{" + $(pc).val() + "}\n\n";
                tex += "\\TitreB{Conclusion :}{\\Normal{p. 47.}}\n\n";
            }
        }
    }
    tex += "\n\n\n\n\\vspace{7cm}\n\n";
    tex += "\\begin{center}\n\n";
    tex += "\\makebox[12.35cm][c]{\\textit{Vous pouvez emporter ce livret à la fin de la retraite si vous le souhaitez.}}\n\n";
    tex += "\\makebox[12.35cm][c]{\\textit{Merci de rendre le Missel grégorien bleu au Fr. assistant.}}\n\n";
    tex += "\\end{center}\n\n";

    tex += "\\end{document}\n\n";
    $("#tex_area").val(tex);
}

function tex_header(timestamp_start){
    var months_fr = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

    var date_start = new Date(timestamp_start);
    var day_start = date_start.getDate();
    if(day_start == 1){
        day_start = "1\\textsuperscript{er}";
    }
    var month_start = months_fr[date_start.getMonth()];

    var date_end = new Date(timestamp_start + (5 * 24 * 3600 * 1000));
    var day_end = date_end.getDate();
    if(day_end == 1){
        day_end = "1\\textsuperscript{er}";
    }
    var month_end = months_fr[date_end.getMonth()];
    var year_end = date_end.getFullYear();
    
    if(month_start == month_end){
        var date_debut = day_start;
    }
    else{
        var date_debut = day_start + " " + month_start;
    }
    var date_fin = day_end + " " +  month_end + " " + year_end;

    var tex_header = "\\input{config.tex}\n\n";
	tex_header += "\\begin{document}\n\n";
	tex_header += "\\thispagestyle{empty}\n\n";
	tex_header += "\\begin{center}\n\n";
	tex_header += "+\\par\n\n";
	tex_header += "PAX\\par\n\n";
	tex_header += "\\end{center}\n\n";
	tex_header += "\\vspace{.5cm}\n\n";
	tex_header += "\\TitreA{Retraite du}\n\n";
	tex_header += "\\TitreA{" + date_debut + " au " + date_fin + "}\n\n";
    tex_header += "\\vspace{.2cm}\n\n";
	tex_header += "\\TitreA{Messe conventuelle}\n\n";

    return(tex_header);
}


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
        calculate_paques(date_timestamp);
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
            console.log("Retour JSON =", data);
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


    tex += "Communion spirituelle\n"
    tex += "Ô Jésus, mon aimable Sauveur, combien je voudrais en ce moment, m’approcher de votre Table sainte, plein de confiance, non en mes propres mérites, mais en votre infinie bonté! Que je voudrais aller à vous, Source de miséricorde ; être guéri par vous, divin Médecin de mon âme ; chercher en vous mon appui, en vous, Seigneur, qui serez un jour mon Juge, mais qui ne voulez être, maintenant, que mon Sauveur ! Je vous aime, ô Jésus, Agneau divin, innocente Victime, immolée par amour sur la Croix, pour moi et pour le salut du genre humain. Ô mon Dieu, souvenez-vous de votre humble créature, rachetée par votre Sang ! Je me repens de vous avoir offensé, et je désire réparer mes fautes par les efforts que je ferai pour obéir à votre sainte volonté. Ô bon Jésus, qui, par votre grâce tout-puissante, me fortifiez contre les ennemis de mon âme et de mon corps, faites que bientôt, purifié de toute souillure, j’aie le bonheur de vous recevoir dans la Sainte Eucharistie, afin de travailler avec une constante générosité à l’œuvre de mon salut. Ainsi soit-il.\n"
    tex += "Prières avant la Communion\n"
    tex += "Acte de Foi. – Ô Seigneur Jésus, je crois que vous êtes réellement et substantiellement présent dans la Sainte Hostie, avec votre Corps, votre Sang, votre Âme et votre Divinité. Je le crois fermement parce que vous l’avez dit, vous qui êtes la vérité même. Je crois que dans ce Sacrement, vous, mon Sauveur, vrai Dieu et vrai homme, vous vous donnez à moi, pour me faire vivre plus abondamment de votre vie divine ; je le crois, mais fortifiez et augmentez ma foi.\n"
    tex += "Acte d’humilité. – Je reconnais, ô mon Dieu, que je suis une humble créature, sortie de vos mains et de plus, un pauvre pécheur, très indigne de vous recevoir, vous qui êtes le Tout-Puissant, l’Éternel, le Dieu infiniment saint. Je devrais vous dire, comme votre apôtre Pierre, et avec bien plus de raison que lui: «Éloignez-vous de moi, parce que je suis un pécheur»; mais souffrez que je répète avec le Centurion : « Seigneur, dites seulement une parole, et mon âme sera guérie ».\n"
    tex += "Acte de contrition. – Mon Dieu, je déteste toutes les fautes de ma vie ; je les déteste de tout mon cœur, parce qu’elles vous ont offensé, vous, ô mon Dieu, qui êtes si bon. Je vous en supplie, effacez-les par votre sang. Avec l’aide de votre grâce, je prends la résolution de ne plus commettre le péché, et d’en faire une sincère pénitence.\n"
    tex += "Acte de désir et d’amour. – Ô Seigneur Jésus, le Dieu de mon cœur, mon bonheur et ma force, vous, le Pain vivant, qui descendez du ciel pour être la nourriture de mon âme, j’ai un grand désir de vous recevoir. Je me réjouis à la pensée que vous allez venir habiter en moi. Venez, Seigneur Jésus, venez posséder mon cœur ; qu’il soit à vous pour toujours! Vous qui m’aimez tant, faites que je vous aime de toute mon âme, et par-dessus toutes choses. Recours à la Très Sainte Vierge et aux Saints. – Sainte Vierge Marie, Mère de Jésus, le Dieu d’amour qui va s’unir à mon âme dans la Sainte Eucharistie, obtenez-moi la grâce de le recevoir dignement. Saint Joseph, Saints et Bienheureux, et vous, mon bon Ange gardien, intercédez pour moi.\n"
    tex += "Prières après la Communion\n"
    tex += "Acte de Foi et d’Adoration. – Ô Jésus, je le crois, c’est vous que je viens de recevoir, vous, mon Dieu, mon Créateur et mon Maître, vous qui, par amour pour moi, avez été, à votre naissance, couché sur la paille de la crèche, vous qui avez voulu mourir pour moi sur la Croix. J’ai été tiré du néant par votre toute-puissance, et vous venez habiter en moi ! Ô mon Dieu, saisi d’un profond respect, je me prosterne devant votre souveraine majesté, je vous adore, et je vous offre mes plus humbles louanges.\n"
    tex += "Acte de Reconnaissance et d’Amour. – Très doux Jésus, Dieu d’infinie bonté, je vous remercie de tout mon cœur, pour la grâce insigne que vous venez de me faire. Que vous rendrai-je pour un tel bienfait ? Je voudrais vous aimer, autant que vous êtes aimable, et vous servir, autant que vous méritez de l’être. Ô Dieu, qui êtes tout amour, apprenez-moi à vous aimer, d’une affection véritable et fidèle, et enseignez-moi à faire votre sainte volonté. Je m’offre tout entier à vous: mon corps, afin qu’il soit chaste; mon âme, afin qu’elle soit pure de tout péché; mon cœur, afin qu’il ne cesse de vous aimer. Vous vous êtes donné à moi, je me donne à vous pour toujours.\n"
    tex += "Acte de Demande. – Vous êtes en moi, ô Jésus, vous qui avez dit: «Demandez et vous recevrez». Vous y êtes, rempli de bonté pour moi, les mains pleines de grâces ; daignez les répandre sur mon âme, qui en a tant besoin. Ôtez de mon cœur tout ce qui vous déplaît, mettez-y tout ce qui peut le rendre agréable à vos yeux. Appliquez-moi les mérites de votre vie et de votre mort, unissez-moi à vous, vivez en moi, faites que je vive par vous et pour vous. Accordez aussi, Dieu infiniment bon, les mêmes grâces à toutes les personnes pour lesquelles j’ai le devoir de prier, ou à qui j’ai promis particulièrement de le faire. – Cœur miséricordieux de Jésus, ayez pitié des pauvres âmes du purgatoire, et donnez-leur le repos éternel.\n"







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

function calculate_paques(date_timestamp){
    var date = new Date(date_timestamp);
    var year = date.getFullYear();
    var var_1 = year - 1900;
    var var_2 = var_1 % 19;
    var var_3 = Math.floor((7 * var_2 + 1) / 19);
    var var_4 = (11 * var_2 + 4 - var_3) % 29;
    var var_5 = Math.floor(var_1 / 4);
    var var_6 = (var_1 + var_5 + 31 - var_4) % 7;
    var var_7 = 25 - var_4 - var_6;
    if(var_7 > 0){
        mois_paques = 4;
        jour_paques = var_7;
    }
    else{
        mois_paques = 3;
        jour_paques = var_7 + 31;
    }
    console.log(jour_paques, mois_paques, year);
}









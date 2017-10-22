// Fonctions concernant le LaTeX.

function write_tex(data){
    var page = "";
    var tex = tex_header(Date.parse($("#date_debut").val()));
    for(var i = 0; i < 5; i++){
        var data_day = data["day_" + i];
        
        // Jour civil :
        tex += "\n\n\\section{" + day_data["civil_day"] + "}\n\n";
        
        // Jour liturgique :
        var jour_lit = day_data["lit_day"];
        tex += "\\Saint{" + jour_lit + "}";

        // Rang liturgique :
        var rang = day_data["rang"];
        if(rang != ""){
            tex += "{" + rang + "}\n\n";
        }
        else{
            tex += "{}\n\n";
        }

        // Ouverture de la célébration :
        tex += "\\TitreB{Ouverture de la célébration~:}\\Normal{p. 7.}\n\n"

        // Introït :
        var introit = "#grid_value_" + i + "0";
        if($(introit).val() != ""){
            page = day_data["pages"]["IN"];
            if(page[1] != ""){
                tex += "\\TitreB{Antienne d'introït~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Antienne d'introït~:}\\par\n";
                tex += "\\Partoche{GR/in_" + $(introit).val().replace(",", "_") + "}\n\n";
            }
        }
        
        // Tierce :
        var tierce = day_data["tierce"];
        tex += "\\Tierce{" + tierce + "}"
        var tierce_page = day_data["tierce_page"];
        tex += "{" + tierce_page + "}\n\n";
        
        // Kyrie :
        var kyrie = "#grid_value_" + i + "5";
        if($(kyrie).val() != ""){
            page = day_data["pages"]["KY"];
            tex += "\\TitreB{Kyrie " + kyrie + "}\\Normal{(p. " + page[1] + ")}\n\n";
        }
        
        // Gloria :
        var gloria = "#grid_value_" + i + "6";
        if($(gloria).val() != ""){
            page = day_data["pages"]["GL"];
            tex += "\\TitreB{Gloria " + gloria + "}\\Normal{(p. " + page[1] + ")}\n\n";
        }
        
        // Oraison :
        var or = day_data["or"];
        if(or[1] != ""){
            tex += "\\TitreB{Oraison~:}\\Normal{p. " + or + ".}\n\n";
        }
        else{
            tex += "";
        }

        // 1ère lecture :
        var lect_1 = day_data["reading_1"];
        tex += "\\Lecture{Première lecture}{" + reading_1 + "\n\n";

        // Graduel :
        var graduel = "#grid_value_" + i + "1";
        if($(graduel).val() != ""){
            page = day_data["pages"]["GR"];
            if(page[1] != ""){
                tex += "\\TitreB{Graduel~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Graduel~:}\\par\n";
                tex += "\\Partoche{GR/gr_" + $(graduel).val().replace(",", "_") + "}\n\n";
            }
        }

        // 2e lecture :
        var lect_2 = day_data["reading_2"];
        tex += "\\Lecture{Deuxième lecture}{" + reading_2 + "\n\n";

        // Alleluia :
        var alleluia = "#grid_value_" + i + "2";
        if($(alleluia).val() != ""){
            page = day_data["pages"]["AL"];
            if(page[1] != ""){
                tex += "\\TitreB{Alleluia~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Alleluia~:}\\par\n";
                tex += "\\Partoche{GR/al_" + $(alleluia).val().replace(",", "_") + "}\n\n";
            }
        }

        // Évangile :
        var evg = day_data["evg"];
        tex += "\\Lecture{Évangile}{" + $("#readings_" + i).val() + "_ev}\n\n";

        // Credo :
        var credo = "#grid_value_" + i + "8";
        if($(credo).val() != ""){
            page = day_data["pages"]["CR"];
            tex += "\\TitreB{Credo " + $(credo).val() + "} \\Normal{(p. " + page[1] + ").}\n\n"
        }

        // Antienne d'offertoire :
        var ant_off = "#grid_value_" + i + "3";
        if($(ant_off).val() != ""){
            page = day_data["pages"]["OF"];
            if(page[1] != ""){
                tex += "\\TitreB{Antienne d'offertoire~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Antienne d'offertoire~:}\\par\n";
                tex += "\\Partoche{GR/of_" + $(ant_off).val().replace(",", "_") + "}\n\n";
            }
        }

        // Super oblata :
        var so = day_data["so"];
        tex += "\\TitreB{Prière sur les offrandes~:}\\Normal{p. " + so + ".}\n\n";

        // Préface :
        var pref = day_data["pref"];
        if(pref[1] != ""){
            tex += "\\TitreB{" + page[0] + "~:}\\Normal{p. " + page[1] + "}\n\n";
        }
        else{
            tex += "\\Preface{" + page[0] + "}{" + $(pref).val() + "}\n\n";
        }
        
        // Sanctus-Agnus :
        var sanctus_agnus = "#grid_value_" + i + "7";
        if($(sanctus_agnus).val() != ""){
            page = day_data["pages"]["SA"];
            tex += "\\TitreB{Sanctus " + $(sanctus_agnus).val() + "}\\Normal{(p. " + page[1] + ")}\n\n";
            tex += "\\TitreB{Prière eucharistique n. 1}\\Normal{(p. 22)}\n\n";
            tex += "\\TitreB{Rites de communion~:}\\Normal{(p. 41)}\n\n";
            page = pages_json[sanctus_agnus]["AG"];
            tex += "\\TitreB{Agnus Dei " + $(sanctus_agnus).val() + "}\\Normal{(p. " + page[1] + ")}\n\n";
        }
        
        // Antienne de communion :
        var comm = "#grid_value_" + i + "4";
        if($(comm).val() != ""){
            var page = day_data["pages"]["CO"];
            if(page[1] != ""){
                tex += "\\TitreB{Antienne de communion~:}\\Normal{\\textit{" + page[0] + "} (p. " + page[1] + ").}\n\n"
            }
            else{
                tex += "\\TitreB{Antienne de communion~:}\\par\n";
                tex += "\\Partoche{GR/co_" + $(comm).val().replace(",", "_") + "}\n\n";
            }
        }

        // Postcommunion :
        var pc = day_data["pc"];
        tex += "\\TitreB{Prière après la communion~:}\\Normal{p. " + pc + ".}\n\n";

        // Conclusion :
        tex += "\\TitreB{Conclusion :}{\\Normal{p. 47.}}\n\n";
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





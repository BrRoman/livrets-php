// Fonctions écrivant le contenu du fichier tex à partir du retour de request.php.

function write_latex(data){
    var start_date_split = $('#date_debut')[0].value.split('/');
    var start_date = new Date(start_date_split[2], start_date_split[1] - 1, start_date_split[0]).getTime();
    var tex = tex_header(start_date);
    for(var i = 0; i < 5; i++){
        var day_data = data[i];

        // Oraisons :
        var orationes = day_data['orationes'];
        
        // Jour civil :
        tex += '\n\n%--------------------\n\n\\section{' + day_data['civil_day'] + '}\n\n';

        // Jour liturgique :
        tex += '\\JourLiturgique{' + day_data['lit_day'] + '}';

        // Rang liturgique :
        var rang = day_data['rang'];
        if(rang != '' && rang != null){
            tex += '{' + rang + '}\n\n';
        }
        else{
            tex += '{}\n\n';
        }

        // Introït :
        if($('#grid_value_' + i + '0').val() != ''){
            var introit = day_data['IN'];
            if(introit[1] != ''){
                tex += '\\TitreB{Antienne d\'introït~:}\\Normal{\\textit{' + introit[0] + '} (p. ' + introit[1] + ').}\n\n'
            }
            else{
                tex += '\\TitreB{Antienne d\'introït~:}\\par\n';
                tex += '\\Partoche{GR/in_' + introit[0] + '}\n\n';
            }
        }
        
        // Ouverture de la célébration :
        tex += '\\TitreB{Ouverture de la célébration~:}\\Normal{p. 7.}\n\n'

        // Asperges me :
        if(day_data['asp'] != ''){
            tex += day_data['asp'] + '\n\n';
        }
        
        // Tierce :
        if($('#tierce_page_' + i).val() != ''){
            tex += '\\Tierce{' + day_data['tierce_ant'] + '}{' + day_data['tierce_page']+ '}\n\n';
        }
        
        // Kyrie :
        if($('#grid_value_' + i + '5').val()!= ''){
            var kyrie = day_data['KY'];
            tex += '\\TitreB{' + kyrie[0] + '}\\Normal{(p. ' + kyrie[1] + ').}\n\n';
        }
        
        // Gloria :
        if($('#grid_value_' + i + '6').val() != ''){
            var gloria = day_data['GL'];
            tex += '\\TitreB{' + gloria[0] + '}\\Normal{(p. ' + gloria[1] + ').}\n\n';
        }
        
        // Oraison :
        if(orationes['source'] == 'MG'){
            tex += '\\TitreB{Oraison~:}\\Normal{p. ' + orationes['ref'][0] + '.}\n\n';
        }
        else{
            tex += '\\Oraison{Oraison}{1}{' + orationes['ref'] + '}\n\n';
        }

        // 1ère lecture :
        tex += '\\Lecture{Première lecture}{' + day_data['readings'];
        if(day_data['lectures_propres']){
            if(day_data['cycle_lectures'] == 6){
                tex += '_1_' + day_data['year'] + '_' + day_data['even'];
            }
            else if(day_data['cycle_lectures'] == 3){
                tex += '_1_' + day_data['year'];
            }
            else if(day_data['cycle_lectures'] == 2){
                tex += '_1_' + day_data['even'];
            }
            else{
                tex += '_1';
            }
        }
        else{
            if(day_data['readings'].startsWith('pa_') && day_data['weekday'] != 'Dimanche'){
                tex += '_1_' + day_data['even'];
            }
            else{
                tex += '_1';
            }
        }
        tex += '}\n\n';

        // Graduel :
        if($('#grid_value_' + i + '1').val() != ''){
            var graduel = day_data['GR'];
            var score = day_data['tempo'].startsWith('tp_') ? 'Alleluia' : 'Graduel';
            var score_abbr = day_data['tempo'].startsWith('tp_') ? 'al' : 'gr';
            if(graduel[1] != ''){
                tex += '\\TitreB{' + score + '~:}\\Normal{\\textit{' + graduel[0] + '} (p. ' + graduel[1] + ').}\n\n'
            }
            else{
                tex += '\\TitreB{' + score + '~:}\\par\n';
                tex += '\\Partoche{GR/' + score_abbr + '_' + graduel[0] + '}\n\n';
            }
        }
        
        // 2e lecture :
        if(($('#grid_value_' + i + '1').val() != '') & ($('#grid_value_' + i + '2').val() != '')){
            tex += '\\Lecture{Deuxième lecture}{' + day_data['readings'];
            if(day_data['lectures_propres']){
                if(day_data['cycle_lectures'] == 6){
                    tex += '_2_' + day_data['year'] + '_' + day_data['even'];
                }
                else if(day_data['cycle_lectures'] == 3){
                    tex += '_2_' + day_data['year'];
                }
                else if(day_data['cycle_lectures'] == 2){
                    tex += '_2_' + day_data['even'];
                }
                else{
                    tex += '_2';
                }
            }
            else{
                if(day_data['readings'].startsWith('pa_') && day_data['weekday'] != 'Dimanche'){
                    tex += '_2_' + day_data['even'];
                }
                else{
                    tex += '_2';
                }
            }
            tex += '}\n\n';
        }

        // Alleluia :
        if($('#grid_value_' + i + '2').val() != ''){
            var alleluia = day_data['AL'];
            var score = day_data['tempo'].startsWith('qua_') ? 'Trait' : 'Alleluia';
            if(alleluia[1] != ''){
                tex += '\\TitreB{' + score + '~:}\\Normal{\\textit{' + alleluia[0] + '} (p. ' + alleluia[1] + ').}\n\n'
            }
            else{
                tex += '\\TitreB{' + score + '~:}\\par\n';
                tex += '\\Partoche{GR/al_' + alleluia[0] + '}\n\n';
            }
        }

        // Séquence :
        if(day_data['sequence'] != null){
            if(day_data['sequence']['source'] == 'files'){
                tex += '\\TitreB{Séquence~:}\\par\n';
                tex += '\\Partoche{sequences/' + day_data['sequence']['ref'] + '}\n\n';
            }
            else{ // TODO: Ce 2e cas n'a pas encore été testé.
                tex += '\\TitreB{Séquence~:}\\Normal{\\textit{' + day_data['sequence']['name'] + '} (p. ' + day_data['sequence']['page'] + ').}\n\n';
            }
        }

        // Évangile :
        tex += '\\Lecture{Évangile}{' + day_data['readings'];
        if(day_data['lectures_propres']){
            if(day_data['cycle_lectures'] == 6){
                tex += '_ev_' + day_data['year'] + '_' + day_data['even'];
            }
            else if(day_data['cycle_lectures'] == 3){
                tex += '_ev_' + day_data['year'];
            }
            else if(day_data['cycle_lectures'] == 2){
                tex += '_ev_' + day_data['even'];
            }
            else{
                tex += '_ev';
            }
        }
        else{
            tex += '_ev';
        }
        tex += '}\n\n';


        // Credo :
        if($('#grid_value_' + i + '8').val()!= ''){
            var credo = day_data['CR'];
            tex += '\\TitreB{' + credo[0] + '}\\Normal{(p. ' + credo[1] + ').}\n\n'
        }

        // Antienne d'offertoire :
        if($('#grid_value_' + i + '3').val() != ''){
            var ant_off = day_data['OF'];
            if(ant_off[1] != ''){
                tex += '\\TitreB{Antienne d\'offertoire~:}\\Normal{\\textit{' + ant_off[0] + '} (p. ' + ant_off[1] + ').}\n\n'
            }
            else{
                tex += '\\TitreB{Antienne d\'offertoire~:}\\par\n';
                tex += '\\Partoche{GR/of_' + ant_off[0] + '}\n\n';
            }
        }

        // Super oblata :
        if(orationes['source'] == 'MG'){
            tex += '\\TitreB{Prière sur les offrandes~:}\\Normal{p. ' + orationes['ref'][1] + '.}\n\n';
        }
        else{
            tex += '\\Oraison{Prière sur les offrandes}{2}{' + orationes['ref'] + '}\n\n';
        }

        // Préface :
        var pref = day_data['pref'];
        if(pref['page'] != null){
            tex += '\\TitreB{' + pref['name'] + '~:}\\Normal{p. ' + pref['page'] + '.}\n\n';
        }
        else{
            if(pref['name_la'] != null){
                tex += '\\PrefaceWithName{' + pref['name'] + '}{' + pref['ref'] + '}{' + pref['name_la'] + '}{' + pref['name_fr'] + '}\n\n';
            }
            else{
                tex += '\\Preface{' + pref['name'] + '}{' + pref['ref'] + '}\n\n';
            }
        }
        
        // Sanctus :
        if($('#grid_value_' + i + '7').val() != ''){
            var sanctus = day_data['SA'];
            tex += '\\TitreB{' + sanctus[0] + '}\\Normal{(p. ' + sanctus[1] + ').}\n\n';
        }

        // Canon :
        tex += '\\TitreB{Prière eucharistique n. 1}\\Normal{(p. 22).}\n\n';
        tex += '\\TitreB{Rites de communion~:}\\Normal{p. 41.}\n\n';
        
        // Agnus Dei :
        if($('#grid_value_' + i + '7').val() != ''){
            var agnus = day_data['AG'];
            tex += '\\TitreB{' + agnus[0] + '}\\Normal{(p. ' + agnus[1] + ').}\n\n';
        }
        
        // Antienne de communion :
        if($('#grid_value_' + i + '4').val() != ''){
            var comm = day_data['CO'];
            if(comm[1] != ''){
                tex += '\\TitreB{Antienne de communion~:}\\Normal{\\textit{' + comm[0] + '} (p. ' + comm[1] + ').}\n\n'
            }
            else{
                tex += '\\TitreB{Antienne de communion~:}\\par\n';
                tex += '\\Partoche{GR/co_' + comm[0] + '}\n\n';
            }
        }

        // Postcommunion :
        if(orationes['source'] == 'MG'){
            tex += '\\TitreB{Prière après la communion~:}\\Normal{p. ' + orationes['ref'][2] + '.}\n\n';
        }
        else{
            tex += '\\Oraison{Prière après la Communion}{3}{' + orationes['ref'] + '}\n\n';
        }

        // Conclusion :
        tex += '\\TitreB{Conclusion~:}{\\Normal{p. 47.}}\n\n';
    }
    tex += '\n\n\n\n\\vspace{7cm}\n\n';
    tex += '\\begin{center}\n\n';
    tex += '\\makebox[12.35cm][c]{\\textit{Vous pouvez emporter ce livret à la fin de la retraite si vous le souhaitez.}}\n\n';
    tex += '\\makebox[12.35cm][c]{\\textit{Merci de rendre le Missel grégorien bleu au Fr. assistant.}}\n\n';
    tex += '\\end{center}\n\n';


    tex += '\\newpage\n'
    tex += '\\pagestyle{plain}\n' // No header please, only page number.
    tex += '\\fontsize{11.5}{13}\\selectfont\n'
    tex += '\\begin{center}\\Normal{\\textbf{Communion spirituelle}}\\end{center}\n\n'
    tex += 'Ô Jésus, mon aimable Sauveur, combien je voudrais en ce moment, m’approcher de votre Table sainte, plein de confiance, non en mes propres mérites, mais en votre infinie bonté! Que je voudrais aller à vous, Source de miséricorde~; être guéri par vous, divin Médecin de mon âme~; chercher en vous mon appui, en vous, Seigneur, qui serez un jour mon Juge, mais qui ne voulez être, maintenant, que mon Sauveur~! Je vous aime, ô Jésus, Agneau divin, innocente Victime, immolée par amour sur la Croix, pour moi et pour le salut du genre humain. Ô mon Dieu, souvenez-vous de votre humble créature, rachetée par votre Sang~! Je me repens de vous avoir offensé, et je désire réparer mes fautes par les efforts que je ferai pour obéir à votre sainte volonté. Ô bon Jésus, qui, par votre grâce tout-puissante, me fortifiez contre les ennemis de mon âme et de mon corps, faites que bientôt, purifié de toute souillure, j’aie le bonheur de vous recevoir dans la Sainte Eucharistie, afin de travailler avec une constante générosité à l’œuvre de mon salut. Ainsi soit-il.\\par\\vspace{0.2cm}\n'
    tex += '\\begin{center}\\Normal{\\textbf{Prières avant la Communion}}\\end{center}\n\n'
    tex += '\\textbf{Acte de Foi.} – Ô Seigneur Jésus, je crois que vous êtes réellement et substantiellement présent dans la Sainte Hostie, avec votre Corps, votre Sang, votre Âme et votre Divinité. Je le crois fermement parce que vous l’avez dit, vous qui êtes la vérité même. Je crois que dans ce Sacrement, vous, mon Sauveur, vrai Dieu et vrai homme, vous vous donnez à moi, pour me faire vivre plus abondamment de votre vie divine~; je le crois, mais fortifiez et augmentez ma foi.\n\n'
    tex += '\\textbf{Acte d’humilité.} – Je reconnais, ô mon Dieu, que je suis une humble créature, sortie de vos mains et de plus, un pauvre pécheur, très indigne de vous recevoir, vous qui êtes le Tout-Puissant, l’éternel, le Dieu infiniment saint. Je devrais vous dire, comme votre apôtre Pierre, et avec bien plus de raison que lui: «~éloignez-vous de moi, parce que je suis un pécheur~»; mais souffrez que je répète avec le Centurion~: «~Seigneur, dites seulement une parole, et mon âme sera guérie.~»\n\n'
    tex += '\\textbf{Acte de contrition.} – Mon Dieu, je déteste toutes les fautes de ma vie~; je les déteste de tout mon cœur, parce qu’elles vous ont offensé, vous, ô mon Dieu, qui êtes si bon. Je vous en supplie, effacez-les par votre sang. Avec l’aide de votre grâce, je prends la résolution de ne plus commettre le péché, et d’en faire une sincère pénitence.\n\n'
    tex += '\\textbf{Acte de désir et d’amour.} – Ô Seigneur Jésus, le Dieu de mon cœur, mon bonheur et ma force, vous, le Pain vivant, qui descendez du ciel pour être la nourriture de mon âme, j’ai un grand désir de vous recevoir. Je me réjouis à la pensée que vous allez venir habiter en moi. Venez, Seigneur Jésus, venez posséder mon cœur~; qu’il soit à vous pour toujours! Vous qui m’aimez tant, faites que je vous aime de toute mon âme, et par-dessus toutes choses.\n\n'
    tex += '\\textbf{Recours à la Très Sainte Vierge et aux Saints.} – Sainte Vierge Marie, Mère de Jésus, le Dieu d’amour qui va s’unir à mon âme dans la Sainte Eucharistie, obtenez-moi la grâce de le recevoir dignement. Saint Joseph, Saints et Bienheureux, et vous, mon bon Ange gardien, intercédez pour moi.\\par\\vspace{0.2cm}\n'
    tex += '\\begin{center}\\Normal{\\textbf{Prières après la Communion}}\\end{center}\n\n'
    tex += '\\textbf{Acte de Foi et d’Adoration.} – Ô Jésus, je le crois, c’est vous que je viens de recevoir, vous, mon Dieu, mon Créateur et mon Maître, vous qui, par amour pour moi, avez été, à votre naissance, couché sur la paille de la crèche, vous qui avez voulu mourir pour moi sur la Croix. J’ai été tiré du néant par votre toute-puissance, et vous venez habiter en moi~! Ô mon Dieu, saisi d’un profond respect, je me prosterne devant votre souveraine majesté, je vous adore, et je vous offre mes plus humbles louanges.\n\n'
    tex += '\\textbf{Acte de Reconnaissance et d’Amour.} – Très doux Jésus, Dieu d’infinie bonté, je vous remercie de tout mon cœur, pour la grâce insigne que vous venez de me faire. Que vous rendrai-je pour un tel bienfait~? Je voudrais vous aimer, autant que vous êtes aimable, et vous servir, autant que vous méritez de l’être. Ô Dieu, qui êtes tout amour, apprenez-moi à vous aimer, d’une affection véritable et fidèle, et enseignez-moi à faire votre sainte volonté. Je m’offre tout entier à vous: mon corps, afin qu’il soit chaste; mon âme, afin qu’elle soit pure de tout péché; mon cœur, afin qu’il ne cesse de vous aimer. Vous vous êtes donné à moi, je me donne à vous pour toujours.\n\n'
    tex += '\\textbf{Acte de Demande.} – Vous êtes en moi, ô Jésus, vous qui avez dit: «~Demandez et vous recevrez~». Vous y êtes, rempli de bonté pour moi, les mains pleines de grâces~; daignez les répandre sur mon âme, qui en a tant besoin. Ôtez de mon cœur tout ce qui vous déplaît, mettez-y tout ce qui peut le rendre agréable à vos yeux. Appliquez-moi les mérites de votre vie et de votre mort, unissez-moi à vous, vivez en moi, faites que je vive par vous et pour vous. Accordez aussi, Dieu infiniment bon, les mêmes grâces à toutes les personnes pour lesquelles j’ai le devoir de prier, ou à qui j’ai promis particulièrement de le faire. – Cœur miséricordieux de Jésus, ayez pitié des pauvres âmes du purgatoire, et donnez-leur le repos éternel.\n\n'

    tex += '\\end{document}\n\n';
    $('#tex_area').val(tex);
}

function tex_header(timestamp_start){
    var months_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

    var date_start = new Date(timestamp_start);
    var day_start = date_start.getDate();
    if(day_start == 1){
        day_start = '1\\textsuperscript{\\lowercase{er}}';
    }
    var month_start = months_fr[date_start.getMonth()];

    var date_end = new Date(timestamp_start + (5 * 24 * 3600 * 1000));
    var day_end = date_end.getDate();
    if(day_end == 1){
        day_end = '1\\textsuperscript{\\lowercase{er}}';
    }
    var month_end = months_fr[date_end.getMonth()];
    var year_end = date_end.getFullYear();
    
    if(month_start == month_end){
        var date_debut = day_start;
    }
    else{
        var date_debut = day_start + ' ' + month_start;
    }
    var date_fin = day_end + ' ' +  month_end + ' ' + year_end;

    var tex_header = '\\input{config.tex}\n\n';
	tex_header += '\\begin{document}\n\n';
	tex_header += '\\thispagestyle{empty}\n\n';
	tex_header += '\\begin{center}\n\n';
	tex_header += '+\\par\n\n';
	tex_header += 'PAX\\par\n\n';
	tex_header += '\\end{center}\n\n';
	tex_header += '\\vspace{.5cm}\n\n';
	tex_header += '\\TitreA{Retraite du}\n\n';
	tex_header += '\\TitreA{' + date_debut + ' au ' + date_fin + '}\n\n';
    tex_header += '\\vspace{.2cm}\n\n';
	tex_header += '\\TitreA{Messe conventuelle}\n\n';

    return(tex_header);
}


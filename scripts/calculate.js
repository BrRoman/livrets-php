// Calcul du jour liturgique demandé (date = jour civil),
// renvoyé sous forme de référencee : "pa_30_0", "adv_3_2", etc.

function calculate_day_lit(date){
    var days_fr = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    var latine_numbers = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX", "XXXI", "XXXII", "XXXXIII", "XXXIV"];
    var day_lit = {};

    // Données liturgiques :
    var year = date.getFullYear();
    var even = year % 2 == 0 ? "2" : "1";
    var year_letters = ["A", "B", "C"];
    var year_letter = year_letters[(year - 2011) % 3];
    var day = 24 * 3600 * 1000;

    // Calcul des grands jours liturgiques de l'année à laquelle appartient le jour concerné :
    var paques = calculate_paques(year);
    var noel = new Date(year - 1, 11, 25);
    var noel_day = noel.getDay();
    if(noel_day == 0){
        var bapteme = new Date(noel.getTime() + (14 * day));
    }
    else{
        var bapteme = new Date(noel.getTime() + (14 * day) + ((7 - noel_day) * day));
    }
    var cendres = new Date(paques.getTime() - (45 * day));
    var pentecote = new Date(paques.getTime() + (49 * day));
    var next_noel = new Date(year, 11, 25);
    noel_day = next_noel.getDay();
    if(noel_day == 0){
        var adv_dim_1 = new Date(next_noel.getTime() - (28 * day));
    }
    else{
        var adv_dim_1 = new Date(next_noel.getTime() - ((21 + noel_day) * day));
    }
    var christ_roi = new Date(adv_dim_1 - (7 * day));

    // Temps per Annum après la Pentecôte :
    if(date > pentecote && date < christ_roi){
        days_before_christ_roi = Math.ceil((christ_roi.getTime() - date.getTime()) / day);
        dim_per_annum = 34 - Math.ceil(days_before_christ_roi / 7);
        var weekday = 7 - (days_before_christ_roi % 7);
        weekday = weekday == 7 ? 0 : weekday;
        day_lit["day_lit_ref"] = "pa_" + dim_per_annum + "_" + weekday;
        if(weekday != 0){
            day_lit["day_lit_letters"] = days_fr[weekday] + " de la\\par " + latine_numbers[dim_per_annum] + "\\textsuperscript{e} semaine\\par du Temps Ordinaire";
        }
        else{
            day_lit["day_lit_letters"] = latine_numbers[dim_per_annum] + "\\textsuperscript{e} Dimanche\\par du Temps Ordinaire";
        }
    }

    return(day_lit);
}

function calculate_paques(year){
    var var_1 = year - 1900;
    var var_2 = var_1 % 19;
    var var_3 = Math.floor((7 * var_2 + 1) / 19);
    var var_4 = (11 * var_2 + 4 - var_3) % 29;
    var var_5 = Math.floor(var_1 / 4);
    var var_6 = (var_1 + var_5 + 31 - var_4) % 7;
    var var_7 = 25 - var_4 - var_6;
    if(var_7 > 0){
        mois_paques = 3;// Attention, en JS, les mois vont de 0 à 11.
        jour_paques = var_7;
    }
    else{
        mois_paques = 2;// Attention, en JS, les mois vont de 0 à 11.
        jour_paques = var_7 + 31;
    }
    var paques = new Date(year, mois_paques, jour_paques);
    return(paques);
}


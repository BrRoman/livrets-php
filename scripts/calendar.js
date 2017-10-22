// Fonctions liées au calendrier liturgique.

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

function calculate_day(date, paques, year_letter, even){
    var days_fr = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    var latine_numbers = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX", "XXXI", "XXXII", "XXXXIII", "XXXIV"];
    var day = 24 * 3600 * 1000;
    var year = date.getFullYear();
    var liturgical_day = "";
    var readings = "";
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
        var weekday_fr = days_fr[weekday];
        if(weekday_fr == "Dimanche"){
            liturgical_day = latine_numbers[dim_per_annum] + "\\textsuperscript{e} Dimanche\\par du Temps Ordinaire"
        }
        else{
            liturgical_day = weekday_fr + " de la\\par " + latine_numbers[dim_per_annum] + "\\textsuperscript{e} semaine\\par du Temps Ordinaire"
        }
        if(weekday == 0){
            readings = "pa_" + dim_per_annum + "_" + weekday + "_" + year_letter;
        }
        else{
            readings = "pa_" + dim_per_annum + "_" + even + "_" + weekday;
        }
    }

    return({"liturgical_day": liturgical_day, "readings": readings});
}











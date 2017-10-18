// Fonctions liées au calendrier liturgique.

function calculate_day(date){
    var day = 24 * 3600 * 1000;
    var year = date.getFullYear();
    var noel = new Date(year - 1, 11, 25);
    var noel_day = noel.getDay();
    if(noel_day == 0){
        var bapteme = new Date(noel.getTime() + (14 * day));
    }
    else{
        var bapteme = new Date(noel.getTime() + (14 * day) + ((7 - noel_day) * day));
    }
    var paques = calculate_paques(year);
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


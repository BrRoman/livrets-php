<?php
    // This script writes in file "readings_missing" the list of missing readings.
    // It has to be put in the readings' dir.

    if(is_file(__DIR__."/readings_missing")){
        // Empty the result file:
        system("echo '' > ".__DIR__."/readings_missing");
    }

    $f = fopen(__DIR__."/readings_missing", "w");

    // Readings of feriæ per annum:
    for($i = 1; $i <= 34; $i++){
        for($j = 1; $j <= 6; $j++){
            for($k = 1; $k <= 2; $k++){
                $file = "pa_${i}_${j}_1_${k}.txt";
                if(!is_file(__DIR__."/../static/data/lectures/$file")){
                    fwrite($f, $file."\n");
                }
            }
        }
    }

    fclose($f);
?>

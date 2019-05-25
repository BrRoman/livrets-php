<?php
    // This script writes in file "result" the list of missing readings.
    // It has to be put in the readings' dir.

    if(is_file(__DIR__."/result")){
        // Empty the result file:
        system("echo '' > ".__DIR__."/result");
    }

    $f = fopen(__DIR__."/result", "w");

    // Readings of feriæ per annum:
    for($i = 1; $i <= 34; $i++){
        for($j = 1; $j <= 6; $j++){
            for($k = 1; $k <= 2; $k++){
                $file = "pa_${i}_${j}_1_${k}.txt";
                if(!is_file(__DIR__."/$file")){
                    fwrite($f, $file."\n");
                }
            }
        }
    }

    fclose($f);
?>

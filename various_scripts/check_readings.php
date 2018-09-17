<?php
    $count = 0;
    for($i = 1; $i <= 34; $i++){
        for($j = 1; $j <= 6; $j++){
            for($k = 1; $k <= 2; $k++){
                $reading = dirname(dirname(__FILE__)).'/data/Lectures/pa_'.$i.'_'.$j.'_1_'.$k.'.txt';
                if(! is_file($reading)){
                    $count++;
                    print('Absent : '.$reading.'<br>');
                }
            }
        }
    }
    print('Total : '.$count.' fichiers manquants.');
?>


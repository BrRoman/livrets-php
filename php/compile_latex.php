<?php
    file_put_contents(__DIR__."/../livret/livret.tex", $_POST["tex"]);
    if(is_file(__DIR__."/../livret/livret.pdf")){
        shell_exec("rm ".__DIR__."/../livret/livret.pdf");
    }
    $LATEX = 'export PATH="/usr/bin/lualatex:$PATH"; ';
    $retour = shell_exec($LATEX."cd ../livret; lualatex --shell-escape livret.tex");
    print($retour);
?>


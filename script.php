<?php
    file_put_contents("livret.tex", $_POST["tex"]);
    shell_exec("rm livret.pdf");
    //$LATEX = 'export PATH="/usr/bin/lualatex:$PATH"; ';// Serveur
    $LATEX = 'export PATH="/Library/TeX/texbin:$PATH"; ';// Local
    $retour = shell_exec($LATEX."lualatex livret.tex");
    print($retour);
?>


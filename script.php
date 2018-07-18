<?php
    file_put_contents("livret.tex", $_POST["tex"]);
    shell_exec("rm livret.pdf");
    $LATEX = 'export PATH="/usr/bin/lualatex:$PATH"; ';
    $retour = shell_exec($LATEX."lualatex --shell-escape livret.tex");
    print($retour);
?>


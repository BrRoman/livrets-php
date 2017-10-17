<?php
    $connect = new PDO("mysql:host=localhost; dbname=livrets; charset=utf8", "root", "marie2017", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $data = $_POST;
    $pages = array();
    for($i = 0; $i < 5; $i++){
        $id = "#pref_norm_".$i;
        $pages[$id] = array("", "");
        $type = "PR";
        $ref = $data[$id][1];
        $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$type."' AND Ref = '".$ref."';");
        if($rep = $back->fetch()){
            $pages[$id] = array($rep["Name"], $rep["Page"]);
        }
        $back->closeCursor();

        for($j = 0; $j < 9; $j++){
            $id = "#grid_value_".$i.$j;
            $pages[$id] = array("", "");
            $type = $data[$id][0];
            $ref = $data[$id][1];
            if($type == "SA"){
                $back = $connect->query("SELECT * FROM Scores WHERE Type = 'SA' AND Ref = '".$ref."';");
                if($rep = $back->fetch()){
                    $pages[$id]["SA"] = array($rep["Name"], $rep["Page"]);
                }
                $back = $connect->query("SELECT * FROM Scores WHERE Type = 'AG' AND Ref = '".$ref."';");
                if($rep = $back->fetch()){
                    $pages[$id]["AG"] = array($rep["Name"], $rep["Page"]);
                }
            }
            else{
                $back = $connect->query("SELECT * FROM Scores WHERE Type = '".$type."' AND Ref = '".$ref."';");
                if($rep = $back->fetch()){
                    $pages[$id] = array($rep["Name"], $rep["Page"]);
                }
            }
            $back->closeCursor();
        }
    }
    print(json_encode($pages));
?>


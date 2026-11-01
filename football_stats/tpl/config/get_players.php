<?php
    header("Content-Type: text/html; charset=UTF-8");
    if(isset($_POST['id_team'])){

        $id_team = (int) $_POST['id_team'];

        
        define("SERVER", "localhost");
        define("ROOT", "sportanalytic212");
        define("PASSWORD", "sportanalytic212");
        define("NAMEBD", "sportanalytic212");

        $dbconnect = mysql_connect(SERVER, ROOT, PASSWORD) or die("Error! Could not connect to database!");
        mysql_select_db(NAMEBD, $dbconnect);
        mysql_query('SET NAMES `utf8`');

        $getplayers = mysql_query("SELECT * FROM 2z_football_players WHERE id_team=".$id_team." OR id_team_country=".$id_team." ORDER BY name");
        if(mysql_num_rows($getplayers) > 0){
            $players = "<option value='0'>-</option>";
            $resplayers = mysql_fetch_array($getplayers);
            do{
                $player_name = $resplayers['name'];
                $player_id = (int) $resplayers['id'];
                $players .= "<option value='".$player_id."'>".$player_name."</option>";
            }
            while($resplayers = mysql_fetch_array($getplayers));

            echo $players;
        }
        else{
            echo "<option value='0'>-</option>";
        }
    }
?>
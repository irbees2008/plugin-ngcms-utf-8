<?php
    header("Content-Type: text/html; charset=UTF-8");
    if(isset($_POST['id_champo'])){

        $id_champo = (int) $_POST['id_champo'];

        
        define("SERVER", "localhost");
        define("ROOT", "sportanalytic212");
        define("PASSWORD", "sportanalytic212");
        define("NAMEBD", "sportanalytic212");

        $dbconnect = mysql_connect(SERVER, ROOT, PASSWORD) or die("Error! Could not connect to database!");
        mysql_select_db(NAMEBD, $dbconnect);
        mysql_query('SET NAMES `utf8`');

        $get_teams = mysql_query("SELECT id, name FROM 2z_football_teams WHERE type = 0 AND id_country=".$id_champo." ORDER BY name");
        if(mysql_num_rows($get_teams) > 0){
            $teams = "<option value='0'>-</option>";
            $res_teams = mysql_fetch_array($get_teams);
            do{
                $team_name = $res_teams['name'];
                $team_id = (int) $res_teams['id'];
                $teams .= "<option value='".$team_id."'>".$team_name."</option>";
            }
            while($res_teams = mysql_fetch_array($get_teams));

            echo $teams;
        }
        else{
            echo "<option value='0'>-</option>";
        }
    }
?>
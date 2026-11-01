<?php
    header("Content-Type: text/html; charset=UTF-8");
    if(isset($_POST['id_team']) && isset($_POST['id_game'])){

        $id_team = (int) $_POST['id_team'];
        $id_game = (int) $_POST['id_game'];

        
        define("SERVER", "localhost");
        define("ROOT", "sportanalytic212");
        define("PASSWORD", "sportanalytic212");
        define("NAMEBD", "sportanalytic212");

        $dbconnect = mysql_connect(SERVER, ROOT, PASSWORD) or die("Error! Could not connect to database!");
        mysql_select_db(NAMEBD, $dbconnect);
        mysql_query('SET NAMES `utf8`');

        $get_coach = mysql_query("SELECT coach_name FROM 2z_coaches WHERE id_team=".$id_team." AND id_game=".$id_game);
        if(mysql_num_rows($get_coach) > 0){
            $res_coach = mysql_fetch_array($get_coach);
            do{
                $coach = $res_coach['coach_name'];
                break;
            }
            while($res_coach = mysql_fetch_array($get_coach));

            echo trim($coach);
        }
        else{
            echo "-";
        }
    }
?>
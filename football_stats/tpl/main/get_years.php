<?php
    if(isset($_POST['alias']) && isset($_POST['year'])){
        
        $alias = $_POST['alias'];
        $year = $_POST['year'];

        define("SERVER", "localhost");
        define("ROOT", "sportanalytic212");
        define("PASSWORD", "sportanalytic212");
        define("NAMEBD", "sportanalytic212");

        $dbconnect = mysql_connect(SERVER, ROOT, PASSWORD) or die("Error! Could not connect to database!");
        mysql_select_db(NAMEBD, $dbconnect);

        $max_year = 0;

        if($year == ""){
            $getData = mysql_query("SELECT MAX(start) FROM 2z_football_championats WHERE alias=\"".$alias."\"");
            $resData = mysql_fetch_array($getData);
            do{
                $max_year = $resData['MAX(start)'];
                break;
            }
            while($resData = mysql_fetch_array($getData));
        }
        else{
            $max_year = $year;
        }

        $years = "";

        $get_years = mysql_query("SELECT start, finish FROM 2z_football_championats WHERE alias=\"".$alias."\" GROUP BY start ORDER BY start");
        if(mysql_num_rows($get_years) > 0){
            $res_years = mysql_fetch_array($get_years);
            do{
                if($res_years['start'] == $max_year){
                    $years .= "<option value='".$res_years['start']."' selected>".$res_years['start']." - ".$res_years['finish']."</option>";
                }
                else{
                    $years .= "<option value='".$res_years['start']."'>".$res_years['start']." - ".$res_years['finish']."</option>";
                }
            }
            while($res_years = mysql_fetch_array($get_years));

            echo $years;
        }
        else{
            echo "";
        }
    }
?>
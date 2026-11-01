<?php
	$getData = mysql_query("SELECT ".$field." FROM ".$table.$where);
    if(mysql_num_rows($getData) > 0){
        $resData = mysql_fetch_array($getData);
        do{
            $result = $resData[$field];
            break;
        }
        while($resData = mysql_fetch_array($getData));

        return $result;
    }
    else{
        return "";
    }
?>
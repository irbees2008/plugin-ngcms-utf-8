<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'holidays');

// Get content [ array - content and deferred elements ]
function holidays() {
    global $template;

    // Configuration
    $klvmsg = 5; // Number of dates to display
    $klvdays = 15; // Maximum distant event, in days
    $datafile = root . "plugins/holidays/dat_holidays/holidays.dat"; // Database file name
    $months = array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
    $date = date("d ".$months[date('n')]." Y"); // Day.month.year
    $time = date("H:i:s"); // Hours:minutes:seconds
    $holidays = "";
    $day = date("d"); // Day
    $month = date("n"); // Month
    $year = date("Y"); // Year
    if ($month == 12) {
        $year++;
    } // To correctly calculate January holidays

    $vchera = $day - 1;
    $klvchasov = $klvdays * 30;

    // Read the data file
    $lines = file_get_contents($datafile);
    $lines = explode("\n", $lines);

    // Loop through each line of the file
    foreach ($lines as $line) {
        $dt = explode("|", $line);

        // Calculate the event date
        $todaydate = date("d ".$months[date('n')]." Y");
        $tekdt = new DateTime();
        $newdate = new DateTime();
        $newdate->setDate($year, $dt[1], intval($dt[0]));
        $dayx = $newdate->format("d ".$months[$newdate->format('n')]." Y");
        $hdate = ceil(($newdate->getTimestamp() - $tekdt->getTimestamp()) / 3600);
        $ddate = ceil($hdate / 24);

        // Format the "dney" variable
        $dney = "дней";
        if ($ddate == 1) {
            $dney = "день";
        } elseif ($ddate == 2 || $ddate == 3 || $ddate == 4) {
            $dney = "дня";
        }
        if (($dt[0] == $day) && ($dt[1] == $month)) {
            $holidays .= "<B>Сегодня</B> :<i class='fa fa-arrow-down' aria-hidden='true'></i><br/> $dt[2]<br/>";
        }
        
        if (($dt[0] == $vchera) && ($dt[1] == $month)) {
            $holidays .= "Вчера :<i class='fa fa-arrow-down' aria-hidden='true'></i> <br/>$dt[2]<br/>";
        }
        
        if ($klvmsg > 1) {
            if (($hdate > 1) && ($hdate < $klvchasov)) {
                if (!isset($m1)) {
                    $holidays .= "<h6><span>Скоро</span></h6>";
                    $m1 = 1;
                }
                $klvmsg--;
                $holidays .= " <B>$dayx</B> через <B>$ddate</B> $dney<br/><i class='fa fa-arrow-down' aria-hidden='true'></i> $dt[2]<hr/>";
            }
        }
$i++;
}
while($i<$itogo);

$holidays .= "";


$output = $holidays;

$template['vars']['plugin_holidays'] = $output;
}

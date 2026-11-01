<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


add_act('index', 'holidays');

// Get content [ array - content and deferred elements ]
function holidays() {
global $template;


// †онфигураци§
$klvmsg="5";  // Чколько выводить дат?
$klvdays="15";  // максимальное удалённое событие, дней
$datafile=root."plugins/holidays/dat_holidays/holidays.dat"; // имя файла базы данных
$months = array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
$date=date("d ".$months[date('n')]." Y"); // число.мес¤ц.год
$time=date("H:i:s"); // часы:минуты:секунды

$holidays .= "";
$day=$date=date("d"); // день
$month=$date=date("n"); // мес¤ц
$year=$date=date("Y"); // год
if ($month==12) {$year++;} // Дтобы верно считал ¤нварские праздники
$vchera=$day-1;
$klvchasov=$klvdays*30;
$lines=file($datafile);
$itogo=count($lines); $i=0;

do {$dt=explode("|",$lines[$i]);

$todaydate=date("d ".$months[date('n')]." Y");
$tekdt=mktime();

$newdate=mktime(0,0,0,$dt[1],$dy[0],$year);
$dayx=date("d ".$months[date('n')]." Y",$newdate); // конверируем дни до праздника в человеческий формат
$hdate=ceil(($newdate-$tekdt)/3600); // через сколько ДЉЧЭђ наступит событие
$ddate=ceil($hdate/24); // считаем сколько дней до событи§

// приводим слово ??Нє/?Н¤/?Н?Е к нужному типу
$dney="дней";
if ($ddate=="1") {$dney="день";}
if ($ddate=="2" or $ddate=="3" or $ddate=="4") {$dney="дня";}
if (($dt[0]==$day) and ($dt[1]==$month)) {$holidays .= "<B>Сегодня</B> :<i class='fa fa-arrow-down' aria-hidden='true'></i><br/> $dt[2]<br/>";}
if (($dt[0]==$vchera) and ($dt[1]==$month)) {$holidays .= "Вчера :<i class='fa fa-arrow-down' aria-hidden='true'></i> <br/>$dt[2]<br/>";}

if ($klvmsg>1) {
if (($hdate>1) and ($hdate<$klvchasov)) {
if (!isset($m1)) {$holidays .= "<h6><span>Скоро</span></h6>"; $m1=1;}
$klvmsg--;
$holidays .=" <B>$dayx</B> через <B>$ddate</B> $dney<br/><i class='fa fa-arrow-down' aria-hidden='true'></i> $dt[2]<hr/>";} }
$i++;
}
while($i<$itogo);

$holidays .= "";


$output = $holidays;

$template['vars']['plugin_holidays'] = $output;
}

<?php
if (!defined('NGCMS')) die ('HAL');
add_act('index', 'kurskz');
function kurskz(){
  global $tvars, $template;

$url = "http://www.nationalbank.kz/rss/rates_all.xml";
$dataKZ = simplexml_load_file($url);
if ($dataKZ){
 foreach ($dataKZ->channel->item as $item){
 echo "<li>Валюта: ".$item->title."
";
 echo " - ".$item->description."
";;
 echo " за ".$item->quant."
";
 echo "тенге ".$item->change."  |</li>
";
}}

		
			$template['vars']['kurskz'] = $dataKZ;
;

}
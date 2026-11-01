<?php
plugins_load_config();

$cfg = array();
array_push($cfg, array('title' => 'Плагин для размещения рекламных ссылок от <a href="http://www.mainlink.ru/?partnerid=11893" target="_blank">mainlink.ru</a>', 'descr' => '1. Скачиваем <a href="http://mainlink.ru/my/xscript/php/getscript.aspx">скрипт</a> (необходимо быть залогиненным в системе mainlink)<br />2. Создаем в корневой директории сайта новую папку с произвольным названием (пр.: putslinkshere)<br />3. Распаковываем в нее скачанный архив<br />4. На папку /putslinkshere/data ставим права 777 (chmod) - для записи кеша<br />5. Идем в редактирование шаблона сайта (Настройки - Управление шаблонами - Шаблоны сайта - main.tpl)<br />6. В нужное место вставляем <b>[isplugin mainlink]{mainlink}[/isplugin]</b><br />7. Если выводим ссылки в двух блоках (см. ниже), то и <b>[isplugin mainlink]{mainlink2}[/isplugin]</b><br />Конструкция [isplugin mainlink][/isplugin] позволяет "не париться" при отключении плагина.'));
array_push($cfg, array('type' => 'input', 'name' => 'folder', 'title' => 'Имя папки со скриптом', 'descr' => 'No comments..', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param($plugin, 'folder') ? extra_get_param($plugin, 'folder') : 'putslinkshere'));
array_push($cfg, array('type' => 'input', 'name' => 'count_links', 'title' => 'Кол-во ссылок в первом блоке:', 'descr' => 'Если стоит <b>0</b> - то все ссылки выводятся в одном блоке <b>{mainlink}</b>, иначе, следует в шаблон вставить <b>{mainlink}</b> и <b>{mainlink2}</b>. В первом блоке будет выводится указанное кол-во ссылок, во втором - все остальные', 'html_flags' => 'style="width: 25px;"', 'value' => extra_get_param($plugin, 'count_links') ? extra_get_param($plugin, 'count_links') : '0'));
array_push($cfg, array('type' => 'select', 'name' => 'charset', 'title' => 'Кодировка', 'descr' => 'Это непонятно, это все не здорово, это все не так.', 'html_flags' => 'style="width: 250px;"', 'values' => array('win' => 'windows-1251', 'utf' => 'utf-8', 'koi' => 'koi8-r'), value => extra_get_param($plugin, 'charset')));
array_push($cfg, array('type' => 'select', 'name' => 'debugmode', 'title' => 'Режим отладки скрипта', 'descr' => 'Всё пройдёт, только верить надо...', 'values' => array('0' => 'Выкл.', '1' => 'Вкл.'), value => extra_get_param($plugin, 'debugmode')));
array_push($cfg, array('type' => 'input', 'name' => 'splitter', 'title' => 'Разделитель ссылок', 'descr' => '', 'html_flags' => 'style="width: 25px;text-align:center"', 'value' => extra_get_param($plugin, 'splitter') ? extra_get_param($plugin, 'splitter') : ' | '));
array_push($cfg, array('type' => 'input', 'name' => 'htmlbefore', 'title' => 'Текст (html) до каждой ссылки', 'descr' => '&lt;img src="begin.jpg" /&gt;', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param($plugin, 'htmlbefore') ? extra_get_param($plugin, 'htmlbefore') : ''));
array_push($cfg, array('type' => 'input', 'name' => 'htmlafter', 'title' => 'Текст (html) после каждой ссылки', 'descr' => '&lt;img src="end.jpg" /&gt;', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param($plugin, 'htmlafter') ? extra_get_param($plugin, 'htmlafter') : ''));
array_push($cfg, array('type' => 'input', 'name' => 'style', 'title' => 'CSS-стиль ссылки', 'descr' => 'color:red;font-size:15px', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param($plugin, 'style') ? extra_get_param($plugin, 'style') : ''));
array_push($cfg, array('type' => 'input', 'name' => 'class_name', 'title' => 'Имя класса ссылки', 'descr' => 'myclass', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param($plugin, 'class_name') ? extra_get_param($plugin, 'class_name') : ''));

if ($_REQUEST['action'] == 'commit') {
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}

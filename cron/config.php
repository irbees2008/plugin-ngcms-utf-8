<?php

if (!defined('NGCMS')) {
    exit('HAL');
}

LoadPluginLang('cron', 'config', '', '', '#');

$target = admin_url . '/admin.php?mod=cron';
$title = htmlspecialchars($lang['cron']['manager_title'], ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($lang['cron']['manager_description'], ENT_QUOTES, 'UTF-8');
$linkTitle = htmlspecialchars($lang['cron']['manager_link'], ENT_QUOTES, 'UTF-8');
$target = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');

echo '<div class="alert alert-info"><h4>' . $title . '</h4><p>' . $description . '</p><a class="btn btn-primary" href="' . $target . '">' . $linkTitle . '</a></div>';

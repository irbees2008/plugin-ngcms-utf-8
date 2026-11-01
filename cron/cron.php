<?php

if (!defined('NGCMS')) {
    exit('HAL');
}

function cron_manager()
{
    global $cron;

    if (isset($cron) && $cron instanceof cronManager) {
        return $cron;
    }

    $cron = new cronManager();
    return $cron;
}

function cron_load()
{
    global $CRONFILE_LOADED, $CRONFILE, $CRONDATA;

    $CRONFILE_LOADED = 1;
    $CRONFILE = [];
    $CRONDATA = [];
    foreach (cron_manager()->getConfig() as $task) {
        if (!is_array($task)) {
            continue;
        }

        $fields = ['min', 'hour', 'day', 'month', 'dow', 'plugin', 'handler'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $task)) {
                continue 2;
            }
        }

        $line = implode("\t", [
            $task['min'],
            $task['hour'],
            $task['day'],
            $task['month'],
            $task['dow'],
            $task['plugin'],
            $task['handler'],
        ]);
        $CRONFILE[] = $line;
        $CRONDATA[] = [
            $line,
            $task['min'],
            $task['hour'],
            $task['day'],
            $task['month'],
            $task['dow'],
            $task['plugin'],
            $task['handler'],
        ];
    }

    return $CRONDATA;
}

function cron_save()
{
    global $CRONDATA;

    if (!isset($CRONDATA) || !is_array($CRONDATA)) {
        return false;
    }

    $tasks = [];
    foreach ($CRONDATA as $line) {
        if (!is_array($line) || count($line) < 8) {
            return false;
        }

        $tasks[] = [
            'min' => (string) $line[1],
            'hour' => (string) $line[2],
            'day' => (string) $line[3],
            'month' => (string) $line[4],
            'dow' => (string) $line[5],
            'plugin' => (string) $line[6],
            'handler' => (string) $line[7],
        ];
    }

    return cron_manager()->setConfig($tasks);
}

function cron_register_task($plugin, $pluginCMD, $min = '*', $hour = '*', $day = '*', $month = '*', $DOW = '*')
{
    $result = cron_manager()->registerTask($plugin, $pluginCMD, $min, $hour, $day, $month, $DOW);
    if ($result) {
        cron_load();
    }
    return $result;
}

function cron_unregister_task($plugin, $pluginCMD = '', $min = '', $hour = '', $day = '', $month = '', $DOW = '')
{
    $result = cron_manager()->unregisterTask($plugin, $pluginCMD, $min, $hour, $day, $month, $DOW);
    if ($result) {
        cron_load();
    }
    return $result;
}

function cron_run()
{
    return cron_manager()->run();
}

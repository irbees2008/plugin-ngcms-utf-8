<?php

if (!defined('NGCMS')) {
    exit('HAL');
}

function dbcache_report_error($message)
{
    static $reported = false;
    if (!$reported) {
        error_log('NGCMS dbcache: ' . $message);
        $reported = true;
    }
}

function dbcache_cache_directory()
{
    $directory = get_plugcache_dir('dbcache');
    if (!$directory || !is_dir($directory) || !is_writable($directory)) {
        dbcache_report_error('cache directory is unavailable or not writable');
        return false;
    }

    return $directory;
}

function dbcache_write_locked($filename, $contents)
{
    $handle = @fopen($filename, 'c+b');
    if (!$handle) {
        dbcache_report_error('unable to open cache file for writing');
        return false;
    }

    if (!@flock($handle, LOCK_EX)) {
        fclose($handle);
        dbcache_report_error('unable to lock cache file for writing');
        return false;
    }

    $written = @ftruncate($handle, 0) && @rewind($handle)
        ? @fwrite($handle, $contents)
        : false;
    $success = ($written === strlen($contents)) && @fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    if (!$success) {
        dbcache_report_error('unable to write cache file');
    }

    return $success;
}

function dbcache_read_locked($filename)
{
    $handle = @fopen($filename, 'rb');
    if (!$handle) {
        return false;
    }

    if (!@flock($handle, LOCK_SH)) {
        fclose($handle);
        return false;
    }

    $contents = stream_get_contents($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return is_string($contents) ? $contents : false;
}

function dbcache_generation($directory)
{
    $filename = $directory . '.generation';
    if (!is_file($filename)) {
        dbcache_write_locked($filename, '');
    }

    $generation = dbcache_read_locked($filename);
    if ($generation === false) {
        dbcache_report_error('unable to read cache generation');
        return false;
    }

    if ($generation === '') {
        $generation = sprintf('%.6F-%d', microtime(true), mt_rand());
        if (!dbcache_write_locked($filename, $generation)) {
            return false;
        }
    }

    return $generation;
}

function dbcache_can_cache($operation, $sql)
{
    if (!in_array($operation, ['select', 'record', 'result'], true)) {
        return false;
    }

    if (!preg_match('/^\s*SELECT\b/i', $sql) || strpos($sql, ';') !== false) {
        return false;
    }

    if (preg_match('/\b(?:FOR\s+UPDATE|LOCK\s+IN\s+SHARE\s+MODE|SQL_NO_CACHE|SQL_CALC_FOUND_ROWS|INTO\s+(?:OUTFILE|DUMPFILE))\b/i', $sql)) {
        return false;
    }

    if (preg_match('/\b(?:NOW|CURRENT_TIMESTAMP|CURRENT_DATE|CURRENT_TIME|LOCALTIME|LOCALTIMESTAMP|UTC_TIMESTAMP|SYSDATE|CURDATE|CURTIME|UNIX_TIMESTAMP|RAND|RANDOM_BYTES|UUID|UUID_SHORT|CONNECTION_ID|LAST_INSERT_ID|FOUND_ROWS|GET_LOCK|RELEASE_LOCK|SLEEP|LOAD_FILE)\b/i', $sql) || strpos($sql, '@') !== false) {
        return false;
    }

    return !preg_match('/(?:^|[^a-z0-9_])(?:[a-z0-9]+_)*(?:users?|user_?id|uid|author_?id|owner_?id|admins?|sessions?|auth|permissions?|tokens?|captcha|api_keys?|passwords?|ipban|login_attempts?|ratelimit)(?:_[a-z0-9]+)*(?:$|[^a-z0-9_])/i', $sql);
}

function dbcache_may_modify_data($sql)
{
    return !preg_match('/^\s*(?:SELECT|SET|SHOW|USE|DESCRIBE|DESC|EXPLAIN|START|BEGIN|COMMIT|ROLLBACK)\b/i', $sql);
}

function dbcache_intercept_query($operation, $sql, $execute)
{
    $configuredTimeout = pluginGetVariable('dbcache', 'timeout');
    $timeout = ($configuredTimeout === false || $configuredTimeout === null || $configuredTimeout === '')
        ? 10
        : (int) $configuredTimeout;
    if ($timeout <= 0 || !dbcache_can_cache($operation, $sql)) {
        $result = $execute();
        if (dbcache_may_modify_data($sql)) {
            $directory = dbcache_cache_directory();
            if ($directory) {
                $generation = dbcache_generation($directory);
                if ($generation !== false) {
                    $generation = sprintf('%.6F-%d', microtime(true), mt_rand());
                    dbcache_write_locked($directory . '.generation', $generation);
                }
            }
        }
        return $result;
    }

    $directory = dbcache_cache_directory();
    if (!$directory) {
        return $execute();
    }

    $generation = dbcache_generation($directory);
    if ($generation === false) {
        return $execute();
    }

    $key = md5($operation . "\0" . trim($sql));
    $filename = $directory . $key . '.cache';
    $contents = dbcache_read_locked($filename);
    if ($contents !== false && $contents !== '') {
        $entry = @unserialize($contents, ['allowed_classes' => false]);
        if (
            is_array($entry) &&
            isset($entry['generation'], $entry['expires']) &&
            $entry['generation'] === $generation &&
            $entry['expires'] >= time() &&
            array_key_exists('value', $entry)
        ) {
            return $entry['value'];
        }
    }

    $result = $execute();
    if ($result !== null && (is_array($result) || is_scalar($result))) {
        $entry = [
            'generation' => $generation,
            'expires' => time() + min($timeout, 1440) * 60,
            'value' => $result,
        ];
        dbcache_write_locked($filename, serialize($entry));
    }

    return $result;
}

global $mysql;
if (!($mysql instanceof NGLegacyDB)) {
    throw new RuntimeException('DBCache requires the current NGLegacyDB adapter');
}
$mysql->setQueryCache('dbcache_intercept_query');

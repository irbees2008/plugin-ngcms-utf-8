<?php
if (!defined('NGCMS')) {
    die('HAL');
}

register_plugin_page('showpic', '', 'plugin_showpic', 0);
add_act('index_post', 'plugin_showpic_redirect');

function showpic_requested_image()
{
    $folder = $_REQUEST['f'] ?? '';
    $filename = $_REQUEST['p'] ?? '';

    if (!is_string($folder) || !is_string($filename) || trim($filename) === '') {
        return false;
    }

    return [
        'folder' => trim($folder),
        'filename' => trim($filename),
    ];
}

function showpic_resolve_image($folder, $filename)
{
    if (!is_string($folder) || !is_string($filename)
        || $filename === '' || strpos($filename, "\0") !== false
        || strpos($filename, '/') !== false || strpos($filename, '\\') !== false
        || basename($filename) !== $filename) {
        return false;
    }

    $folder = str_replace('\\', '/', $folder);
    $folder = trim($folder, '/');
    $folderParts = $folder === '' ? [] : explode('/', $folder);
    foreach ($folderParts as $part) {
        if ($part === '' || $part === '.' || $part === '..' || strpos($part, "\0") !== false) {
            return false;
        }
    }

    $imageRoot = realpath(images_dir);
    if ($imageRoot === false) {
        return false;
    }

    $relativeParts = array_merge($folderParts, [$filename]);
    $imagePath = realpath($imageRoot . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $relativeParts));
    if ($imagePath === false || !is_file($imagePath)) {
        return false;
    }

    $rootPrefix = rtrim($imageRoot, "\\/") . DIRECTORY_SEPARATOR;
    $isInsideImageRoot = DIRECTORY_SEPARATOR === '\\'
        ? strncasecmp($imagePath, $rootPrefix, strlen($rootPrefix)) === 0
        : strncmp($imagePath, $rootPrefix, strlen($rootPrefix)) === 0;
    if (!$isInsideImageRoot) {
        return false;
    }

    $encodedPath = implode('/', array_map('rawurlencode', $relativeParts));
    return rtrim(images_url, '/') . '/' . $encodedPath;
}

function plugin_showpic()
{
    global $lang, $template, $tpl;

    LoadPluginLang('showpic', 'site');
    $result = $lang['showpic:invalid_request'];
    $image = showpic_requested_image();

    if ($image !== false) {
        $imageUrl = showpic_resolve_image($image['folder'], $image['filename']);
        if ($imageUrl === false) {
            $result = $lang['showpic:image_not_found'];
        } else {
            $result = '<img class="showpic-image" src="'
                . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8')
                . '" alt="' . htmlspecialchars($image['filename'], ENT_QUOTES, 'UTF-8') . '">';
        }
    }

    $templatePath = locatePluginTemplates(['pic'], 'showpic', 0);
    if (empty($templatePath['pic'])) {
        throw new RuntimeException('The showpic template could not be located.');
    }

    $tpl->template('pic', $templatePath['pic']);
    $tpl->vars('pic', ['vars' => ['result' => $result]]);
    if (!isset($template['vars']['mainblock'])) {
        $template['vars']['mainblock'] = '';
    }
    $template['vars']['mainblock'] .= $tpl->show('pic');
}

function plugin_showpic_redirect()
{
    if (!isset($_REQUEST['action']) || $_REQUEST['action'] !== 'showpic') {
        return;
    }

    $image = showpic_requested_image();
    $folder = $image === false ? '' : $image['folder'];
    $filename = $image === false ? '' : $image['filename'];
    $url = generateLink(
        'core',
        'plugin',
        ['plugin' => 'showpic'],
        ['f' => $folder, 'p' => $filename]
    );

    header('Location: ' . $url, true, 303);
    exit;
}

<?php
if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('social', 'config', '', '', '#');
global $config, $lang, $twig;
$socialLang = $lang['social']['config'] ?? [];
$twigLang = [];
foreach ($socialLang as $key => $value) {
    $twigLang['social#' . $key] = $value;
}

function social_config_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function social_config_skins()
{
    $skins = [];
    $skinRoot = __DIR__ . '/tpl/skins';
    foreach (scandir($skinRoot) as $entry) {
        if ($entry !== '.' && $entry !== '..' && is_dir($skinRoot . DIRECTORY_SEPARATOR . $entry)
            && preg_match('/^[a-zA-Z0-9_-]+$/', $entry)) {
            $skins[] = $entry;
        }
    }

    return $skins;
}

function social_config_image_directories($localsource, $skin)
{
    global $config;

    $directories = [];
    if (!$localsource) {
        $theme = (string) ($config['theme'] ?? 'default');
        $themeImages = site_root . 'templates/' . $theme . '/plugins/social/images';
        if (is_dir($themeImages)) {
            $directories[] = $themeImages;
        }
    }

    $pluginImages = __DIR__ . '/tpl/skins/' . $skin . '/images';
    if (!is_dir($pluginImages)) {
        $pluginImages = __DIR__ . '/tpl/skins/default/images';
    }

    if (is_dir($pluginImages)) {
        $directories[] = $pluginImages;
    }

    return $directories;
}

function social_config_image_names($directories)
{
    $images = [];
    foreach ($directories as $directory) {
        foreach (scandir($directory) as $file) {
            if (preg_match('/^[a-zA-Z0-9_.-]+\.(?:gif|jpe?g|png|webp)$/i', $file)
                && is_file($directory . DIRECTORY_SEPARATOR . $file)) {
                $images[] = $file;
            }
        }
    }

    return array_values(array_unique($images));
}

function social_config_clear_cache()
{
    $directory = get_plugcache_dir('social');
    if (!$directory || !is_dir($directory)) {
        return;
    }

    foreach (scandir($directory) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $path = rtrim($directory, '\\/') . DIRECTORY_SEPARATOR . $file;
        if (is_file($path) && !unlink($path)) {
            throw new RuntimeException('Unable to remove a social plugin cache file.');
        }
    }
}

function social_config_default_services()
{
    global $socialLang;

    return [
        ['name' => 'Facebook', 'title' => $socialLang['default_facebook'], 'active' => 0, 'link' => 'https://www.facebook.com/sharer/sharer.php?u=%link%', 'img' => 'facebook.png'],
        ['name' => 'X (Twitter)', 'title' => $socialLang['default_x'], 'active' => 0, 'link' => 'https://twitter.com/intent/tweet?text=%title%&url=%link%', 'img' => 'twitter.png'],
        ['name' => $socialLang['default_vk_name'], 'title' => $socialLang['default_vk'], 'active' => 0, 'link' => 'https://vk.com/share.php?url=%link%&title=%title%&description=%content%', 'img' => 'vk.png'],
        ['name' => $socialLang['default_ok_name'], 'title' => $socialLang['default_ok'], 'active' => 0, 'link' => 'https://connect.ok.ru/offer?url=%link%&title=%title%', 'img' => 'odnoklassniki.png'],
        ['name' => 'LinkedIn', 'title' => $socialLang['default_linkedin'], 'active' => 0, 'link' => 'https://www.linkedin.com/sharing/share-offsite/?url=%link%', 'img' => 'linkedin.png'],
        ['name' => 'Reddit', 'title' => $socialLang['default_reddit'], 'active' => 0, 'link' => 'https://www.reddit.com/submit?url=%link%&title=%title%', 'img' => 'reddit.png'],
        ['name' => 'Telegram', 'title' => $socialLang['default_telegram'], 'active' => 0, 'link' => 'https://t.me/share/url?url=%link%&text=%title%', 'img' => ''],
        ['name' => 'WhatsApp', 'title' => $socialLang['default_whatsapp'], 'active' => 0, 'link' => 'https://api.whatsapp.com/send?text=%title%+%link%', 'img' => ''],
    ];
}

$settings = [
    'localsource' => (int) pluginGetVariable('social', 'localsource'),
    'skin' => (string) pluginGetVariable('social', 'skin'),
    'cache' => (int) pluginGetVariable('social', 'cache'),
    'cacheExpire' => (int) pluginGetVariable('social', 'cacheExpire'),
];
$settings['localsource'] = $settings['localsource'] === 1 ? 1 : 0;
$availableSkins = social_config_skins();
$settings['skin'] = in_array($settings['skin'], $availableSkins, true) ? $settings['skin'] : 'default';
$settings['cache'] = $settings['cache'] === 1 ? 1 : 0;
$settings['cacheExpire'] = $settings['cacheExpire'] > 0 ? $settings['cacheExpire'] : 3600;

$services = pluginGetVariable('social', 'services');
$services = is_array($services) ? $services : [];
$error = '';
$notice = '';
$editId = isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT) !== false
    ? (int) $_GET['id']
    : null;
$editService = ['name' => '', 'title' => '', 'link' => '', 'img' => '', 'active' => 0];
if ($editId !== null && isset($services[$editId]) && is_array($services[$editId])) {
    $editService = array_merge($editService, $services[$editId]);
} else {
    $editId = null;
}

if (isset($_POST['operation'])) {
    if (!isset($_REQUEST['action'], $_POST['token'])
        || $_REQUEST['action'] !== 'commit'
        || !hash_equals(genUToken('admin.extra-config'), (string) $_POST['token'])) {
        throw new RuntimeException('Invalid social plugin configuration request token.');
    }

    if (!is_string($_POST['operation'])) {
        throw new RuntimeException('Invalid social plugin configuration operation.');
    }
    $operation = $_POST['operation'];
    if ($operation === 'save_settings') {
        $localsource = filter_var($_POST['localsource'] ?? null, FILTER_VALIDATE_INT);
        $cache = filter_var($_POST['cache'] ?? null, FILTER_VALIDATE_INT);
        $cacheExpire = filter_var($_POST['cacheExpire'] ?? null, FILTER_VALIDATE_INT);
        $skin = $_POST['skin'] ?? '';

        if (!is_string($skin)
            || !in_array($localsource, [0, 1], true)
            || !in_array($cache, [0, 1], true)
            || $cacheExpire === false || $cacheExpire < 1
            || !in_array($skin, $availableSkins, true)) {
            $error = $socialLang['config_invalid_settings'];
        } else {
            pluginSetVariable('social', 'localsource', $localsource);
            pluginSetVariable('social', 'skin', $skin);
            pluginSetVariable('social', 'cache', $cache);
            pluginSetVariable('social', 'cacheExpire', $cacheExpire);
            if (!pluginsSaveConfig()) {
                throw new RuntimeException('Unable to save social plugin settings.');
            }
            $settings = [
                'localsource' => $localsource,
                'skin' => $skin,
                'cache' => $cache,
                'cacheExpire' => $cacheExpire,
            ];
            social_config_clear_cache();
            $notice = $socialLang['msg_settings_saved'];
        }
    } elseif ($operation === 'save_service') {
        $id = isset($_POST['id']) && $_POST['id'] !== ''
            ? filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])
            : null;
        $nameInput = $_POST['name'] ?? '';
        $titleInput = $_POST['title'] ?? '';
        $urlInput = $_POST['link'] ?? '';
        $imageInput = $_POST['img'] ?? '';
        $name = is_string($nameInput) ? trim($nameInput) : '';
        $title = is_string($titleInput) ? trim($titleInput) : '';
        $serviceUrl = is_string($urlInput) ? trim($urlInput) : '';
        $image = is_string($imageInput) ? $imageInput : '';
        $active = isset($_POST['active']) && $_POST['active'] === '1' ? 1 : 0;
        $imageNames = social_config_image_names(social_config_image_directories($settings['localsource'], $settings['skin']));
        $scheme = parse_url($serviceUrl, PHP_URL_SCHEME);
        $host = parse_url($serviceUrl, PHP_URL_HOST);

        if (!is_string($nameInput) || !is_string($titleInput) || !is_string($urlInput) || !is_string($imageInput)
            || $name === '' || strlen($name) > 100 || strlen($title) > 255
            || strlen($serviceUrl) > 2048
            || !in_array(strtolower((string) $scheme), ['http', 'https'], true)
            || !$host
            || ($image !== '' && (!in_array($image, $imageNames, true) || basename($image) !== $image))
            || ($id !== null && ($id === false || !isset($services[$id])))) {
            $error = $socialLang['config_invalid_service'];
            $editService = ['name' => $name, 'title' => $title, 'link' => $serviceUrl, 'img' => $image, 'active' => $active];
            $editId = $id === false ? null : $id;
        } else {
            if ($id === null) {
                $id = empty($services) ? 0 : max(array_map('intval', array_keys($services))) + 1;
            }
            $services[$id] = [
                'name' => $name,
                'title' => $title,
                'link' => $serviceUrl,
                'img' => $image,
                'active' => $active,
            ];
            pluginSetVariable('social', 'services', $services);
            if (!pluginsSaveConfig()) {
                throw new RuntimeException('Unable to save social service configuration.');
            }
            social_config_clear_cache();
            $editId = null;
            $editService = ['name' => '', 'title' => '', 'link' => '', 'img' => '', 'active' => 0];
            $notice = $socialLang['msg_service_saved'];
        }
    } elseif ($operation === 'delete_service') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($id === false || !isset($services[$id])) {
            $error = $socialLang['config_invalid_service'];
        } else {
            unset($services[$id]);
            $services = array_values($services);
            pluginSetVariable('social', 'services', $services);
            if (!pluginsSaveConfig()) {
                throw new RuntimeException('Unable to delete social service.');
            }
            social_config_clear_cache();
            $notice = $socialLang['msg_service_deleted'];
        }
    } elseif ($operation === 'move_service') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $direction = is_string($_POST['direction'] ?? null) ? $_POST['direction'] : '';
        $keys = array_keys($services);
        $position = $id === false ? false : array_search($id, $keys, true);
        $offset = $direction === 'up' ? -1 : ($direction === 'down' ? 1 : 0);
        $target = $position === false ? false : $position + $offset;
        if ($position === false || $offset === 0 || !isset($keys[$target])) {
            $error = $socialLang['config_invalid_service'];
        } else {
            $temporary = $services[$keys[$position]];
            $services[$keys[$position]] = $services[$keys[$target]];
            $services[$keys[$target]] = $temporary;
            $services = array_values($services);
            pluginSetVariable('social', 'services', $services);
            if (!pluginsSaveConfig()) {
                throw new RuntimeException('Unable to reorder social services.');
            }
            social_config_clear_cache();
            $notice = $socialLang['msg_order_saved'];
        }
    } elseif ($operation === 'restore_services') {
        $services = social_config_default_services();
        pluginSetVariable('social', 'services', $services);
        if (!pluginsSaveConfig()) {
            throw new RuntimeException('Unable to restore default social services.');
        }
        social_config_clear_cache();
        $notice = $socialLang['msg_services_restored'];
    } elseif ($operation === 'clear_cache') {
        social_config_clear_cache();
        $notice = $socialLang['msg_cache_cleared'];
    } else {
        throw new RuntimeException('Unknown social plugin configuration operation.');
    }
}

$imageNames = social_config_image_names(social_config_image_directories($settings['localsource'], $settings['skin']));
$serviceRows = [];
foreach ($services as $id => $service) {
    if (!is_array($service)) {
        continue;
    }
    $serviceRows[] = [
        'id' => social_config_escape($id),
        'name' => social_config_escape($service['name'] ?? ''),
        'title' => social_config_escape($service['title'] ?? ''),
        'link' => social_config_escape($service['link'] ?? ''),
        'active' => !empty($service['active']),
        'position' => count($serviceRows),
        'is_first' => count($serviceRows) === 0,
        'is_last' => count($serviceRows) === count($services) - 1,
    ];
}

$skinOptions = [];
foreach ($availableSkins as $skinName) {
    $skinOptions[] = ['value' => social_config_escape($skinName), 'label' => social_config_escape($skinName)];
}
$imageOptions = [['value' => '', 'label' => $socialLang['no_image']]];
foreach ($imageNames as $imageName) {
    $imageOptions[] = ['value' => social_config_escape($imageName), 'label' => social_config_escape($imageName)];
}

$editService['name'] = social_config_escape($editService['name']);
$editService['title'] = social_config_escape($editService['title']);
$editService['link'] = social_config_escape($editService['link']);
$editService['img'] = social_config_escape($editService['img']);
$templatePath = locatePluginTemplates(['config'], 'social', 1, '', 'config');
if (empty($templatePath['config'])) {
    throw new RuntimeException('The social plugin configuration template could not be located.');
}

$view = $twig->loadTemplate($templatePath['config'] . 'config.tpl');
print $view->render([
    'settings' => $settings,
    'services' => $serviceRows,
    'service' => $editService,
    'service_id' => $editId === null ? '' : social_config_escape($editId),
    'editing' => $editId !== null,
    'skin_options' => $skinOptions,
    'image_options' => $imageOptions,
    'notice' => $notice,
    'error' => $error,
    'token' => social_config_escape(genUToken('admin.extra-config')),
    'lang' => $twigLang,
]);

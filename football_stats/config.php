<?php

if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('football_stats', 'config', '', 'fs', ':');

global $lang, $twig;

$definitions = [
    ['name' => 'pagination', 'post' => 'pagination', 'label' => 'pagination', 'type' => 'number', 'min' => 1],
    ['name' => 'championats', 'post' => 'championats', 'label' => 'championats', 'type' => 'text'],
    ['name' => 'photos.url', 'post' => 'photos_url', 'label' => 'photos.url', 'type' => 'text'],
    ['name' => 'photos.dir', 'post' => 'photos_dir', 'label' => 'photos.dir', 'type' => 'text'],
    ['name' => 'goals', 'post' => 'goals', 'label' => 'goals', 'type' => 'number', 'min' => 0],
    ['name' => 'uefa_main', 'post' => 'uefa_main', 'label' => 'uefa_main', 'type' => 'number', 'min' => 0],
    ['name' => 'count_card', 'post' => 'count_card', 'label' => 'count_card', 'type' => 'number', 'min' => 0],
    ['name' => 'scorers_in_admin_panel', 'post' => 'scorers_in_admin_panel', 'label' => 'scorers_in_admin_panel', 'type' => 'number', 'min' => 0],
    ['name' => 'scorers_on_site', 'post' => 'scorers_on_site', 'label' => 'scorers_on_site', 'type' => 'number', 'min' => 0],
    ['name' => 'count_scorers_in_main_page', 'post' => 'count_scorers_in_main_page', 'label' => 'count_scorers_in_main_page', 'type' => 'number', 'min' => 0],
    ['name' => 'path_to_upload_photo', 'post' => 'path_to_upload_photo', 'label' => 'path_to_upload_photo', 'type' => 'text'],
    ['name' => 'day_minus', 'post' => 'day_minus', 'label' => 'day_minus', 'type' => 'number', 'min' => 0],
    ['name' => 'begin_day', 'post' => 'begin_day', 'label' => 'begin_day_and_hour', 'type' => 'date'],
    ['name' => 'begin_hour', 'post' => 'begin_hour', 'label' => 'begin_day_and_hour', 'type' => 'time'],
    ['name' => 'end_day', 'post' => 'end_day', 'label' => 'end_day_and_hour', 'type' => 'date'],
    ['name' => 'end_hour', 'post' => 'end_hour', 'label' => 'end_day_and_hour', 'type' => 'time'],
    ['name' => 'cacheExpire', 'post' => 'cacheExpire', 'label' => 'label_cacheExpire', 'type' => 'number', 'min' => 0],
    ['name' => 'country_views', 'post' => 'country_views', 'label' => 'country_views', 'help' => 'country_views_help', 'type' => 'json'],
    ['name' => 'league_views', 'post' => 'league_views', 'label' => 'league_views', 'help' => 'league_views_help', 'type' => 'json'],
];

$errors = [];
$success = false;
$values = [];

foreach ($definitions as $definition) {
    $value = pluginGetVariable('football_stats', $definition['name']);
    if ($definition['type'] === 'json') {
        $decoded = football_stats_config_decode_array($value, $definition['name'], $errors);
        $json = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $errors[] = 'Не удалось подготовить настройку ' . $definition['name'] . ' для отображения.';
            $json = '[]';
        }
        $values[$definition['name']] = $json;
    } elseif (is_scalar($value) || $value === null) {
        $values[$definition['name']] = (string) $value;
    } else {
        $errors[] = 'Настройка ' . $definition['name'] . ' имеет неподдерживаемый формат и не может быть отображена.';
        $values[$definition['name']] = '';
    }
}

$values['cache'] = (string) pluginGetVariable('football_stats', 'cache') === '1';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save'])) {
    $errors = [];
    $submitted = [];

    foreach ($definitions as $definition) {
        $postName = $definition['post'];
        $raw = $_POST[$postName] ?? '';

        if (!is_string($raw)) {
            $errors[] = 'Некорректное значение настройки ' . $definition['name'] . '.';
            continue;
        }

        if ($definition['type'] === 'json') {
            $values[$definition['name']] = $raw;
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                $errors[] = 'Настройка ' . $definition['name'] . ' должна содержать корректный JSON-массив или объект.';
                continue;
            }
            foreach ($decoded as $item) {
                if (!is_array($item)) {
                    $errors[] = 'Каждый элемент настройки ' . $definition['name'] . ' должен быть объектом.';
                    continue 2;
                }
            }
            $submitted[$definition['name']] = serialize($decoded);
            $values[$definition['name']] = $raw;
            continue;
        }

        if ($definition['type'] === 'number') {
            if ($raw === '') {
                $submitted[$definition['name']] = '';
                $values[$definition['name']] = '';
                continue;
            }
            $number = filter_var($raw, FILTER_VALIDATE_INT);
            if ($number === false || $number < $definition['min']) {
                $errors[] = 'Для настройки ' . $definition['name'] . ' укажите целое число не меньше ' . $definition['min'] . '.';
                $values[$definition['name']] = $raw;
                continue;
            }
            $submitted[$definition['name']] = (string) $number;
        } elseif ($definition['type'] === 'date' && $raw !== '') {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
                $errors[] = 'Укажите дату в формате ГГГГ-ММ-ДД для настройки ' . $definition['name'] . '.';
                $values[$definition['name']] = $raw;
                continue;
            }
            [$year, $month, $day] = array_map('intval', explode('-', $raw));
            if (!checkdate($month, $day, $year)) {
                $errors[] = 'Укажите существующую дату для настройки ' . $definition['name'] . '.';
                $values[$definition['name']] = $raw;
                continue;
            }
            $submitted[$definition['name']] = $raw;
        } elseif ($definition['type'] === 'time' && $raw !== '') {
            if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $raw)) {
                $errors[] = 'Укажите время в формате ЧЧ:ММ для настройки ' . $definition['name'] . '.';
                $values[$definition['name']] = $raw;
                continue;
            }
            $submitted[$definition['name']] = $raw;
        } else {
            $submitted[$definition['name']] = trim($raw);
        }

        $values[$definition['name']] = $raw;
    }

    $submitted['cache'] = isset($_POST['cache']) ? '1' : '0';
    $values['cache'] = isset($_POST['cache']);

    if (!$errors) {
        foreach ($submitted as $name => $value) {
            pluginSetVariable('football_stats', $name, $value);
        }
        pluginsSaveConfig();
        $success = true;
    }
}

$fields = [];
foreach ($definitions as $definition) {
    $label = $lang['fs:' . $definition['label']] ?? $definition['label'];
    $fields[] = [
        'name' => $definition['post'],
        'label' => html_entity_decode(strip_tags($label), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'help' => isset($definition['help']) ? ($lang['fs:' . $definition['help']] ?? '') : '',
        'type' => $definition['type'],
        'value' => $values[$definition['name']],
    ];
}

$tpath = locatePluginTemplates(['config_settings'], 'football_stats', 1, '', 'config');
$template = $twig->loadTemplate($tpath['config_settings'] . 'config_settings.tpl');
echo $template->render([
    'fields' => $fields,
    'cache_label' => $lang['fs:to_use_caching'] ?? 'Кэширование',
    'cache_value' => $values['cache'],
    'save_label' => $lang['fs:button_save'] ?? 'Сохранить',
    'errors' => $errors,
    'success' => $success,
]);

function football_stats_config_decode_array($value, $name, array &$errors)
{
    if ($value === null || $value === '' || $value === false) {
        return [];
    }
    if (!is_string($value)) {
        if (is_array($value)) {
            return $value;
        }
        $errors[] = 'Настройка ' . $name . ' имеет неподдерживаемый формат.';

        return [];
    }

    set_error_handler(static function ($severity, $message) {
        throw new ErrorException($message, 0, $severity);
    });
    try {
        $decoded = unserialize($value, ['allowed_classes' => false]);
    } catch (Throwable $exception) {
        $errors[] = 'Не удалось прочитать сохранённую настройку ' . $name . ': ' . $exception->getMessage();

        return [];
    } finally {
        restore_error_handler();
    }

    if (!is_array($decoded)) {
        $errors[] = 'Сохранённая настройка ' . $name . ' не является массивом.';

        return [];
    }

    return $decoded;
}

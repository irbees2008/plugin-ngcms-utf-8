<?php

if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('make_links', 'config', '', '', ':');

global $lang, $twig;

$definitions = [
    [
        'name' => 'if_main',
        'label' => 'if_main',
        'type' => 'select',
        'options' => ['0' => 'option_no', '1' => 'option_yes'],
        'value_type' => 'boolean',
    ],
    [
        'name' => 'if_category',
        'label' => 'if_category',
        'type' => 'select',
        'options' => ['0' => 'option_no', '1' => 'option_yes'],
        'value_type' => 'boolean',
    ],
    [
        'name' => 'if_full_news',
        'label' => 'if_full_news',
        'type' => 'select',
        'options' => ['0' => 'option_no', '1' => 'option_yes'],
        'value_type' => 'boolean',
    ],
    [
        'name' => 'max_link_count',
        'label' => 'max_link_count',
        'type' => 'number',
        'help' => 'max_link_count_help',
        'value_type' => 'number',
    ],
    [
        'name' => 'mode_short',
        'label' => 'mode_short',
        'type' => 'select',
        'options' => ['0' => 'short_only', '1' => 'short_and_full'],
        'value_type' => 'boolean',
    ],
    [
        'name' => 'mode_full',
        'label' => 'mode_full',
        'type' => 'select',
        'options' => ['0' => 'full_only', '1' => 'short_and_full'],
        'value_type' => 'boolean',
    ],
    [
        'name' => 'if_rand_word',
        'label' => 'if_rand_word',
        'type' => 'select',
        'options' => ['0' => 'option_no', '1' => 'option_yes'],
        'value_type' => 'boolean',
    ],
    [
        'name' => 'words',
        'label' => 'words',
        'type' => 'textarea',
        'help' => 'words_help',
        'value_type' => 'text',
    ],
];

$values = [];
$errors = [];
$success = false;

foreach ($definitions as $definition) {
    $value = pluginGetVariable('make_links', $definition['name']);
    if ($definition['value_type'] === 'text') {
        $values[$definition['name']] = is_string($value) ? $value : '';
    } elseif ($definition['value_type'] === 'number') {
        $values[$definition['name']] = is_numeric($value) ? (string) max(0, (int) $value) : '0';
    } else {
        $values[$definition['name']] = (string) ((int) $value === 1);
        $values[$definition['name']] = $values[$definition['name']] === '1' ? '1' : '0';
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save'])) {
    $submitted = [];

    foreach ($definitions as $definition) {
        $name = $definition['name'];
        $raw = $_POST[$name] ?? null;
        if (!is_string($raw)) {
            $errors[] = $lang['make_links:config_invalid'];
            continue;
        }

        if ($definition['value_type'] === 'boolean') {
            if ($raw !== '0' && $raw !== '1') {
                $errors[] = $lang['make_links:config_invalid'];
                $values[$name] = $raw;
                continue;
            }
            $submitted[$name] = (int) $raw;
        } elseif ($definition['value_type'] === 'number') {
            $number = filter_var($raw, FILTER_VALIDATE_INT);
            if ($number === false || $number < 0) {
                $errors[] = $lang['make_links:config_invalid'];
                $values[$name] = $raw;
                continue;
            }
            $submitted[$name] = $number;
        } else {
            $submitted[$name] = trim($raw);
        }

        $values[$name] = $raw;
    }

    if (!$errors) {
        foreach ($submitted as $name => $value) {
            pluginSetVariable('make_links', $name, $value);
        }
        pluginsSaveConfig();
        $success = true;
    }
}

$fields = [];
foreach ($definitions as $definition) {
    $options = [];
    foreach ($definition['options'] ?? [] as $value => $label) {
        $options[] = [
            'value' => $value,
            'label' => $lang['make_links:' . $label],
        ];
    }
    $fields[] = [
        'name' => $definition['name'],
        'label' => $lang['make_links:' . $definition['label']],
        'type' => $definition['type'],
        'help' => isset($definition['help']) ? $lang['make_links:' . $definition['help']] : '',
        'options' => $options,
        'value' => $values[$definition['name']],
    ];
}

$tpath = locatePluginTemplates(['config'], 'make_links', 1, '', 'config');
$template = $twig->loadTemplate($tpath['config'] . 'config.tpl');
echo $template->render([
    'title' => $lang['make_links:config_title'],
    'description' => $lang['make_links:config_description'],
    'group_title' => $lang['make_links:group_title'],
    'save_label' => $lang['make_links:save'],
    'saved_message' => $lang['make_links:saved'],
    'fields' => $fields,
    'errors' => $errors,
    'success' => $success,
]);

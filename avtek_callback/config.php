<?php
if (!defined('NGCMS')) {
    die('HAL');
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/inc/export.php';

if (function_exists('loadPluginLang')) {
    loadPluginLang('avtek_callback', 'config', '', '', ':');
}

$canAdmin = !function_exists('isAdmin') || isAdmin();
if (!$canAdmin) {
    $msg = isset($lang['avtek_callback:no_access']) ? $lang['avtek_callback:no_access'] : 'Нет доступа';
    echo '<div class="alert alert-danger">' . avtek_cb_h($msg) . '</div>';
    return;
}

function avtek_cb_admin_snippet_box(int $formId): string
{
    $twigEmbed = "{{ callPlugin('avtek_callback.show', {'id': " . $formId . ", 'mode': 'embed'})|raw }}";
    $twigModal = "{{ callPlugin('avtek_callback.show', {'id': " . $formId . ", 'mode': 'modal'})|raw }}";
    $serverInject = "{{ plugin_avtek_cb|raw }}";
    $escape = function (string $value): string {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    return '<div class="form-group">'
        . '<label for="avtekSnippetEmbed">Twig: встроенная форма</label>'
        . '<textarea id="avtekSnippetEmbed" class="form-control mb-3" rows="2" readonly onclick="this.select()">' . $escape($twigEmbed) . '</textarea>'
        . '<label for="avtekSnippetModal">Twig: модальное окно</label>'
        . '<textarea id="avtekSnippetModal" class="form-control mb-3" rows="2" readonly onclick="this.select()">' . $escape($twigModal) . '</textarea>'
        . '<label for="avtekSnippetNews">Вставка в полную новость</label>'
        . '<textarea id="avtekSnippetNews" class="form-control mb-3" rows="2" readonly onclick="this.select()">' . $escape($serverInject) . '</textarea>'
        . '<small class="form-text text-muted">Проверить HTML: <code>/plugin/avtek_callback/?sub=render&amp;form_id=' . $formId . '&amp;mode=embed</code></small>'
        . '</div>';
}

function avtek_cb_admin_render(string $activeTab, string $action, string $bodyTemplate, array $bodyVars): void
{
    global $tpl, $lang;

    echo '<link rel="stylesheet" href="' . home . '/lib/notify.css?v=1">';
    echo '<script>(function(){ if(!window.showToast){ try{ var s=document.createElement("script"); s.src="' . home . '/lib/notify.js"; document.head.appendChild(s); }catch(e){} } })();</script>';

    $templatePaths = locatePluginTemplates(['conf.main', $bodyTemplate], 'avtek_callback', 1);
    $token = function_exists('genUToken') ? (string)genUToken('admin.extra-config') : '';
    $bodyVars['token'] = htmlspecialchars($token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $tpl->template($bodyTemplate, $templatePaths[$bodyTemplate]);
    $tpl->vars($bodyTemplate, ['vars' => $bodyVars]);

    $adminLang = LoadLang('extra-config', 'admin');
    $pluginTitle = isset($lang['cfg_title']) ? (string)$lang['cfg_title'] : 'AVTEK Callback';
    $tabs = ['forms' => '', 'leads' => '', 'settings' => ''];
    if (isset($tabs[$activeTab])) {
        $tabs[$activeTab] = 'active';
    }

    $documentationVars = ['documentation_button' => '', 'documentation_modal' => ''];
    if (function_exists('plugin_documentation_markup')) {
        list($documentationVars['documentation_button'], $documentationVars['documentation_modal']) = plugin_documentation_markup('avtek_callback');
    }

    $mainVars = [
        'entries' => (string)$tpl->show($bodyTemplate),
        'action' => $action,
        'plugin_title' => $pluginTitle,
        'extras_label' => isset($adminLang['extras']) ? $adminLang['extras'] : 'Управление плагинами',
        'tab_forms_active' => $tabs['forms'],
        'tab_leads_active' => $tabs['leads'],
        'tab_settings_active' => $tabs['settings'],
        'lang' => $lang,
    ];
    $mainVars = array_merge($mainVars, $documentationVars);

    $tpl->template('conf.main', $templatePaths['conf.main']);
    $tpl->vars('conf.main', ['vars' => $mainVars]);
    print $tpl->show('conf.main');
}

function avtek_cb_admin_require_token(): void
{
    $expected = function_exists('genUToken') ? (string)genUToken('admin.extra-config') : '';
    $provided = (string)($_POST['token'] ?? '');
    if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
        http_response_code(403);
        echo '<div class="alert alert-danger">Недействительный токен запроса. Обновите страницу и повторите действие.</div>';
        exit;
    }
}

$mode = (string)avtek_cb_req('mode', 'forms');

if ($mode === 'settings' && !empty($_POST['save_settings'])) {
    avtek_cb_admin_require_token();
    avtek_cb_set_setting('default_emails', avtek_cb_trim((string)($_POST['default_emails'] ?? ''), 2000));
    avtek_cb_set_setting('mail_from', avtek_cb_trim((string)($_POST['mail_from'] ?? ''), 200));
    avtek_cb_set_setting('telegram_token', avtek_cb_trim((string)($_POST['telegram_token'] ?? ''), 300));
    avtek_cb_set_setting('telegram_chat_ids', avtek_cb_trim((string)($_POST['telegram_chat_ids'] ?? ''), 2000));
    avtek_cb_set_setting('crm_type', avtek_cb_trim((string)($_POST['crm_type'] ?? ''), 50));
    avtek_cb_set_setting('crm_settings', avtek_cb_trim((string)($_POST['crm_settings'] ?? ''), 20000));
    avtek_cb_set_setting('auto_form_id', avtek_cb_trim((string)($_POST['auto_form_id'] ?? '1'), 10));
    $autoMode = (string)($_POST['auto_mode'] ?? 'embed');
    avtek_cb_set_setting('auto_mode', $autoMode === 'modal' ? 'modal' : 'embed');
    $saved = isset($lang['avtek_callback:saved']) ? $lang['avtek_callback:saved'] : 'Сохранено';
    echo '<script>try{ if(window.showToast){ window.showToast("' . addslashes((string)$saved) . '", {type:"success", title:"AVTEK Callback"}); } }catch(e){} </script>';
}

if ($mode === 'form_edit' && !empty($_POST['save_form'])) {
    avtek_cb_admin_require_token();
    $id = (int)($_POST['id'] ?? 0);
    $title = (string)($_POST['title'] ?? '');
    $slug = (string)($_POST['slug'] ?? '');
    $emails = (string)($_POST['emails'] ?? '');
    $labels = [
        'name' => (string)($_POST['label_name'] ?? 'Имя'),
        'email' => (string)($_POST['label_email'] ?? 'Электронный адрес'),
        'phone' => (string)($_POST['label_phone'] ?? 'Номер телефона +3'),
        'city' => (string)($_POST['label_city'] ?? 'Город'),
        'variants' => (string)($_POST['label_variants'] ?? 'Варианты ответа'),
        'calendar' => (string)($_POST['label_calendar'] ?? 'Календарь'),
        'comment' => (string)($_POST['label_comment'] ?? 'Комментарий'),
    ];

    $fields = [];
    $addField = function (string $name, string $label, string $type, bool $required, string $placeholder = '', array $options = []) use (&$fields) {
        $fields[] = [
            'name' => $name,
            'label' => $label,
            'type' => $type,
            'required' => $required ? 1 : 0,
            'placeholder' => $placeholder,
            'options' => $options,
        ];
    };

    if (!empty($_POST['f_name'])) {
        $addField('name', $labels['name'], 'text', !empty($_POST['r_name']), (string)($_POST['ph_name'] ?? ''));
    }
    if (!empty($_POST['f_email'])) {
        $addField('email', $labels['email'], 'email', !empty($_POST['r_email']), (string)($_POST['ph_email'] ?? ''));
    }
    if (!empty($_POST['f_phone'])) {
        $addField('phone', $labels['phone'], 'phone', !empty($_POST['r_phone']), (string)($_POST['ph_phone'] ?? '+3'));
    }
    if (!empty($_POST['f_city'])) {
        $addField('city', $labels['city'], 'city', !empty($_POST['r_city']), (string)($_POST['ph_city'] ?? ''));
    }
    if (!empty($_POST['f_variants'])) {
        $options = preg_split('~\r?\n~', (string)($_POST['variants_list'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $options = array_map('trim', $options ?: []);
        $addField('variants', $labels['variants'], 'select', !empty($_POST['r_variants']), '', $options);
    }
    if (!empty($_POST['f_calendar'])) {
        $addField('calendar', $labels['calendar'], 'date', !empty($_POST['r_calendar']));
    }
    if (!empty($_POST['f_comment'])) {
        $addField('comment', $labels['comment'], 'textarea', !empty($_POST['r_comment']), (string)($_POST['ph_comment'] ?? ''));
    }

    $customLines = preg_split('~\r?\n~', (string)($_POST['custom_fields'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $customIndex = 1;
    foreach ($customLines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        $label = $parts[0] ?? ('Поле ' . $customIndex);
        $type = $parts[1] ?? 'text';
        $required = !empty($parts[2]) && $parts[2] !== '0';
        $options = [];
        if ($type === 'select' && !empty($parts[3])) {
            $options = array_map('trim', preg_split('~\s*,\s*~', $parts[3]) ?: []);
        }
        $addField('custom_' . $customIndex, $label, $type, $required, '', $options);
        $customIndex++;
    }

    $settings = [
        'button_text' => (string)($_POST['button_text'] ?? 'Отправить'),
        'success_mode' => (string)($_POST['success_mode'] ?? 'inline'),
        'success_text' => (string)($_POST['success_text'] ?? 'Спасибо! Заявка отправлена.'),
        'success_redirect_url' => (string)($_POST['success_redirect_url'] ?? ''),
        'telegram_chat_ids' => (string)($_POST['telegram_chat_ids_form'] ?? ''),
        'mail_subject' => (string)($_POST['mail_subject'] ?? ''),
        'conversion_js' => (string)($_POST['conversion_js'] ?? ''),
        'crm_type' => (string)($_POST['crm_type_form'] ?? ''),
        'crm_settings' => (string)($_POST['crm_settings_form'] ?? ''),
        'skin_embed' => (string)($_POST['skin_embed'] ?? ''),
    ];
    $savedId = avtek_cb_save_form([
        'id' => $id,
        'title' => $title,
        'slug' => $slug,
        'emails' => $emails,
        'fields' => $fields,
        'settings' => $settings,
    ]);
    $savedMessage = isset($lang['avtek_callback:form_saved']) ? $lang['avtek_callback:form_saved'] : 'Форма сохранена';
    echo '<script>try{ if(window.showToast){ window.showToast("' . addslashes((string)$savedMessage) . ' (ID: ' . (int)$savedId . ')", {type:"success", title:"AVTEK Callback"}); } }catch(e){} </script>';
}

if ($mode === 'form_delete') {
    avtek_cb_admin_require_token();
    $id = (int)avtek_cb_req('id', 0);
    if ($id > 0) {
        avtek_cb_delete_form($id);
        $deletedMessage = isset($lang['avtek_callback:form_deleted']) ? $lang['avtek_callback:form_deleted'] : 'Форма удалена';
        echo '<script>try{ if(window.showToast){ window.showToast("' . addslashes((string)$deletedMessage) . '", {type:"success", title:"AVTEK Callback"}); } }catch(e){} </script>';
    }
    $mode = 'forms';
}

if ($mode === 'export') {
    avtek_cb_export_xls((int)avtek_cb_req('form_id', 0));
}

$bodyVars = [];
if ($mode === 'settings') {
    $activeTab = 'settings';
    $action = 'Настройки';
    $bodyTemplate = 'conf.settings';
    $bodyVars = [
        'default_emails' => avtek_cb_get_setting('default_emails', ''),
        'mail_from' => avtek_cb_get_setting('mail_from', ''),
        'telegram_token' => avtek_cb_get_setting('telegram_token', ''),
        'telegram_chat_ids' => avtek_cb_get_setting('telegram_chat_ids', ''),
        'crm_type' => avtek_cb_get_setting('crm_type', ''),
        'crm_settings' => avtek_cb_get_setting('crm_settings', ''),
        'auto_form_id' => avtek_cb_get_setting('auto_form_id', '1'),
        'auto_mode' => avtek_cb_get_setting('auto_mode', 'embed') === 'modal' ? 'modal' : 'embed',
    ];
} elseif ($mode === 'leads') {
    $activeTab = 'leads';
    $action = 'Заявки';
    $bodyTemplate = 'conf.leads';
    $rows = avtek_cb_db_getall(
        'SELECT l.*, f.title AS form_title FROM `' . avtek_cb_leads_table() . '` l '
        . 'LEFT JOIN `' . avtek_cb_forms_table() . '` f ON f.id=l.form_id '
        . 'ORDER BY l.id DESC LIMIT 200'
    );
    $items = [];
    foreach ($rows as $row) {
        $data = avtek_cb_json_decode($row['data'] ?? '');
        $values = [];
        $leadEmail = '';
        foreach ($data as $label => $value) {
            if (is_array($value)) {
                $value = implode(', ', array_map('strval', $value));
            }
            $value = (string)$value;
            $values[] = ['label' => (string)$label, 'value' => $value];
            if ($leadEmail === '' && avtek_cb_is_valid_email($value)) {
                $leadEmail = $value;
            }
        }
        $items[] = [
            'id' => (int)$row['id'],
            'created_at' => (string)($row['created_at'] ?? ''),
            'form_title' => (string)($row['form_title'] ?? ''),
            'ip' => (string)($row['ip'] ?? ''),
            'page_url' => (string)($row['page_url'] ?? ''),
            'data' => $values,
            'email' => $leadEmail,
            'email_sent' => !empty($row['email_sent']),
            'telegram_sent' => !empty($row['telegram_sent']),
        ];
    }
    $bodyVars = ['items' => $items];
} elseif ($mode === 'form_edit') {
    $activeTab = 'forms';
    $id = (int)avtek_cb_req('id', 0);
    $form = $id > 0 ? avtek_cb_get_form($id) : null;
    $storedFields = $form ? avtek_cb_json_decode($form['fields'] ?? '') : [];
    $fieldMap = [];
    foreach ($storedFields as $field) {
        if (is_array($field) && isset($field['name'])) {
            $fieldMap[(string)$field['name']] = $field;
        }
    }

    $fieldDefinitions = [
        ['key' => 'name', 'title' => 'Имя', 'label' => 'Имя', 'placeholder' => ''],
        ['key' => 'email', 'title' => 'Электронный адрес', 'label' => 'Электронный адрес', 'placeholder' => ''],
        ['key' => 'phone', 'title' => 'Номер телефона +3', 'label' => 'Номер телефона +3', 'placeholder' => '+3'],
        ['key' => 'city', 'title' => 'Город', 'label' => 'Город', 'placeholder' => ''],
        ['key' => 'variants', 'title' => 'Варианты ответа (select)', 'label' => 'Варианты ответа', 'placeholder' => ''],
        ['key' => 'calendar', 'title' => 'Календарь (date)', 'label' => 'Календарь', 'placeholder' => ''],
        ['key' => 'comment', 'title' => 'Комментарий (textarea)', 'label' => 'Комментарий', 'placeholder' => ''],
    ];
    $fields = [];
    foreach ($fieldDefinitions as $definition) {
        $saved = $fieldMap[$definition['key']] ?? [];
        $fields[] = [
            'key' => $definition['key'],
            'title' => $definition['title'],
            'label' => (string)($saved['label'] ?? $definition['label']),
            'placeholder' => (string)($saved['placeholder'] ?? $definition['placeholder']),
            'enabled' => isset($fieldMap[$definition['key']]),
            'required' => !empty($saved['required']),
        ];
    }

    $variants = $fieldMap['variants']['options'] ?? [];
    $customLines = [];
    foreach ($fieldMap as $name => $field) {
        if (strpos($name, 'custom_') !== 0) {
            continue;
        }
        $options = $field['options'] ?? [];
        $customLines[] = (string)($field['label'] ?? '')
            . '|' . (string)($field['type'] ?? 'text')
            . '|' . (!empty($field['required']) ? '1' : '0')
            . '|' . ((($field['type'] ?? '') === 'select' && is_array($options)) ? implode(',', array_map('strval', $options)) : '');
    }

    $formSettings = $form ? avtek_cb_json_decode($form['settings'] ?? '') : [];
    $bodyVars = [
        'id' => $id,
        'is_existing' => (bool)$form,
        'title' => (string)($form['title'] ?? ''),
        'slug' => (string)($form['slug'] ?? ''),
        'emails' => (string)($form['emails'] ?? ''),
        'fields' => $fields,
        'variants_list' => is_array($variants) ? implode("\n", array_map('strval', $variants)) : '',
        'custom_fields' => implode("\n", $customLines),
        'form_settings' => [
            'button_text' => (string)($formSettings['button_text'] ?? 'Отправить'),
            'success_text' => (string)($formSettings['success_text'] ?? 'Спасибо! Заявка отправлена.'),
            'success_mode' => ($formSettings['success_mode'] ?? '') === 'redirect' ? 'redirect' : 'inline',
            'success_redirect_url' => (string)($formSettings['success_redirect_url'] ?? ''),
            'telegram_chat_ids' => (string)($formSettings['telegram_chat_ids'] ?? ''),
            'mail_subject' => (string)($formSettings['mail_subject'] ?? ''),
            'conversion_js' => (string)($formSettings['conversion_js'] ?? ''),
            'skin_embed' => (string)($formSettings['skin_embed'] ?? ''),
            'crm_type' => (string)($formSettings['crm_type'] ?? ''),
            'crm_settings' => (string)($formSettings['crm_settings'] ?? ''),
        ],
        'snippet_box' => $form ? avtek_cb_admin_snippet_box($id) : '',
        'delete_confirm' => isset($lang['avtek_callback:delete_confirm']) ? $lang['avtek_callback:delete_confirm'] : 'Удалить форму?',
    ];
    $action = $id > 0 ? 'Редактирование формы' : 'Добавление формы';
    $bodyTemplate = 'conf.form';
} else {
    $activeTab = 'forms';
    $action = 'Список форм';
    $bodyTemplate = 'conf.forms';
    $items = [];
    foreach (avtek_cb_list_forms() as $form) {
        $items[] = [
            'id' => (int)$form['id'],
            'title' => (string)$form['title'],
            'slug' => (string)$form['slug'],
            'emails' => (string)$form['emails'],
        ];
    }
    $bodyVars = ['items' => $items];
}

avtek_cb_admin_render($activeTab, $action, $bodyTemplate, $bodyVars);

<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

/**
 * Внедрение кода Web Push в шаблон
 */
function webpush_inject_code(): void
{
    global $template, $tpl;

    // Проверяем, включен ли плагин
    $enabled = extra_get_param('webpush', 'enabled');
    if (!$enabled) {
        $template['vars']['webpush'] = '<!-- WebPush: disabled -->';
        return;
    }

    // Проверяем, нужно ли показывать кнопку
    $showButton = extra_get_param('webpush', 'show_button');
    if (!$showButton) {
        $template['vars']['webpush'] = '<!-- WebPush: button hidden -->';
        return;
    }

    // Загружаем локализацию
    LoadPluginLang('webpush', 'site', '', '', ':');

    // Находим шаблон
    $tpath = locatePluginTemplates(['webpush'], 'webpush', 1, 0);

    if (!isset($tpath['webpush'])) {
        $template['vars']['webpush'] = '<!-- WebPush: template not found -->';
        return;
    }

    // Подготавливаем переменные для шаблона
    $tvars = [];
    $tvars['vars']['endpoint'] = '/engine/plugins/webpush/endpoint.php';
    $tvars['vars']['subscribe_text'] = extra_get_param('webpush', 'subscribe_text') ?: 'Включить уведомления';
    $tvars['vars']['unsubscribe_text'] = $GLOBALS['lang']['webpush:unsubscribe_text'] ?? 'Отключить уведомления';
    $tvars['vars']['js_path'] = '/engine/plugins/webpush/js/webpush.js';

    // Регистрируем CSS если есть
    if (isset($tpath['url::webpush.css'])) {
        register_stylesheet($tpath['url::webpush.css']);
    }

    // Генерируем HTML
    $tpl->template('webpush', $tpath['webpush']);
    $tpl->vars('webpush', $tvars);

    $template['vars']['webpush'] = $tpl->show('webpush');
}

/**
 * Получение статистики подписок
 */
function webpush_get_stats(): array
{
    global $mysql;

    $stats = [
        'total' => 0,
        'today' => 0,
        'week' => 0,
    ];

    // Общее количество
    $rec = $mysql->record("SELECT COUNT(*) as cnt FROM " . prefix . "_webpush_subscriptions");
    $stats['total'] = (int)($rec['cnt'] ?? 0);

    // За сегодня
    $today = strtotime('today');
    $rec = $mysql->record("SELECT COUNT(*) as cnt FROM " . prefix . "_webpush_subscriptions WHERE created >= " . $today);
    $stats['today'] = (int)($rec['cnt'] ?? 0);

    // За неделю
    $week = strtotime('-7 days');
    $rec = $mysql->record("SELECT COUNT(*) as cnt FROM " . prefix . "_webpush_subscriptions WHERE created >= " . $week);
    $stats['week'] = (int)($rec['cnt'] ?? 0);

    return $stats;
}

/**
 * NewsFilter для автоматической отправки уведомлений о новых новостях
 */
class WebPushNewsFilter extends NewsFilter
{
    public function showNews($newsID, $SQLnews, &$tvars, $mode = [])
    {
        // Здесь можно добавить логику автоматической отправки уведомлений
        // о новых материалах, если это требуется
    }
}

// Регистрация фильтра новостей
if (class_exists('NewsFilter')) {
    register_filter('news', 'webpush', new WebPushNewsFilter());
}

// Добавляем хук для внедрения кода на страницы
// Регистрируем для index_post - срабатывает на всех страницах после генерации контента
if (function_exists('add_act')) {
    add_act('index_post', 'webpush_inject_code');
}

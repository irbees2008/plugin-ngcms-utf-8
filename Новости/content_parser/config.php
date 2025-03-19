<?php

// Защита от прямого доступа
if (!defined('NGCMS')) {
    exit('HAL');
}

// Подключаем конфигурацию плагина
pluginsLoadConfig();

// Сохранение настроек плагина
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обновляем параметры плагина
    extra_set_param('content_parser', 'rss_url', trim($_POST['rss_url']));
    extra_set_param('content_parser', 'rss_limit', intval($_POST['rss_limit']));
    extra_set_param('content_parser', 'cache_enabled', isset($_POST['cache_enabled']) ? 1 : 0);
    extra_set_param('content_parser', 'cache_expire', intval($_POST['cache_expire']));

    // Сообщение об успешном сохранении
    msg(['type' => 'info', 'message' => 'Настройки успешно сохранены']);
}

// Основная функция для отображения интерфейса автоматизации
function automation()
{
    global $twig, $PHP_SELF;

    // Определяем пути к шаблонам
    $tpath = locatePluginTemplates(
        ['config/main', 'config/automation'],
        'content_parser',
        1
    );

    // Проверяем существование шаблонов
    if (empty($tpath['config/main']) || empty($tpath['config/automation'])) {
        die('Ошибка: Не найдены необходимые шаблоны.');
    }

    try {
        // Загружаем основной шаблон
        $mainTemplate = $twig->loadTemplate($tpath['config/main'] . 'config/main.tpl');

        // Загружаем шаблон автоматизации
        $automationTemplate = $twig->loadTemplate($tpath['config/automation'] . 'config/automation.tpl');

        // Получаем текущие настройки плагина
        $rssUrl = extra_get_param('content_parser', 'rss_url', '');
        $rssLimit = extra_get_param('content_parser', 'rss_limit', 10);
        $cacheEnabled = extra_get_param('content_parser', 'cache_enabled', 0);
        $cacheExpire = extra_get_param('content_parser', 'cache_expire', 3600);

        // Переменные для шаблона автоматизации
        $tVarsAutomation = [
            'rss_url' => $rssUrl,
            'rss_limit' => $rssLimit,
            'cache_enabled' => $cacheEnabled,
            'cache_expire' => $cacheExpire,
            'php_self' => $PHP_SELF,
            'plugin_url' => admin_url . '/admin.php?mod=extra-config&plugin=content_parser',
        ];

        // Рендерим шаблон автоматизации
        $renderedAutomation = $automationTemplate->render($tVarsAutomation);

        // Переменные для основного шаблона
        $tVarsMain = [
            'entries' => $renderedAutomation,
            'php_self' => $PHP_SELF,
            'plugin_url' => admin_url . '/admin.php?mod=extra-config&plugin=content_parser',
            'skins_url' => skins_url,
            'admin_url' => admin_url,
            'home' => home,
            'current_title' => 'Настройки парсера RSS',
        ];

        // Выводим основной шаблон
        echo $mainTemplate->render($tVarsMain);
    } catch (Exception $e) {
        // Обработка ошибок Twig
        die('Ошибка шаблонизатора: ' . $e->getMessage());
    }
}

// Основной обработчик запросов
switch ($_REQUEST['action'] ?? '') {
    default:
        automation();
        break;
}
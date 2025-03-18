<?php

// Защита от прямого доступа
if (!defined('NGCMS')) {
    exit('HAL');
}

// Подключаем конфигурацию плагина
pluginsLoadConfig();

// Основная функция для отображения интерфейса автоматизации
function automation()
{
    global $twig, $PHP_SELF;

    // Определяем пути к шаблонам
    $tpath = locatePluginTemplates(
        ['config/main', 'config/automation'],
        'content_generator',
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

        // Переменные для шаблона автоматизации
        $tVarsAutomation = [];

        // Рендерим шаблон автоматизации
        $renderedAutomation = $automationTemplate->render($tVarsAutomation);

        // Переменные для основного шаблона
        $tVarsMain = [
            'entries' => $renderedAutomation,
            'php_self' => $PHP_SELF,
            'plugin_url' => admin_url . '/admin.php?mod=extra-config&plugin=content_generator',
            'skins_url' => skins_url,
            'admin_url' => admin_url,
            'home' => home,
            'current_title' => 'Automation',
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
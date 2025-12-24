<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');
function plugin_ai_rewriter_install($action)
{
    switch ($action) {
        case 'confirm':
            generate_install_page('ai_rewriter', 'Плагин рерайта новостей ИИ будет установлен. Таблицы БД не требуются.');
            break;
        case 'autoapply':
        case 'apply':
            // Defaults
            pluginSetVariable('ai_rewriter', 'provider', '');
            pluginSetVariable('ai_rewriter', 'model', 'gpt-4o-mini');
            pluginSetVariable('ai_rewriter', 'api_key', '');
            pluginSetVariable('ai_rewriter', 'api_base', '');
            pluginSetVariable('ai_rewriter', 'originality', 60);
            pluginSetVariable('ai_rewriter', 'enable_on_add', 0);
            pluginSetVariable('ai_rewriter', 'enable_on_edit', 0);
            pluginSetVariable('ai_rewriter', 'tone', '');
            pluginSetVariable('ai_rewriter', 'temperature', 0.7);
            pluginSetVariable('ai_rewriter', 'timeout', 20);
            pluginsSaveConfig();
            // --- Автоматически добавляем ai_rewriter в секции RPC и CORE файла plugins.php ---
            $confFile = __DIR__ . '/../../conf/plugins.php';
            if (is_file($confFile) && is_writable($confFile)) {
                // include создаст переменную $array в текущем scope
                include $confFile;
                if (isset($array) && is_array($array)) {
                    $changed = false;
                    if (!isset($array['actions']['rpc'])) {
                        $array['actions']['rpc'] = [];
                        $changed = true;
                    }
                    if (!isset($array['actions']['rpc']['ai_rewriter'])) {
                        $array['actions']['rpc']['ai_rewriter'] = 'ai_rewriter/ai_rewriter.php';
                        $changed = true;
                    }
                    if (!isset($array['actions']['core'])) {
                        $array['actions']['core'] = [];
                        $changed = true;
                    }
                    if (!isset($array['actions']['core']['ai_rewriter'])) {
                        $array['actions']['core']['ai_rewriter'] = 'ai_rewriter/ai_rewriter.php';
                        $changed = true;
                    }
                    if ($changed) {
                        $export = var_export($array, true);
                        file_put_contents($confFile, "<?php \$array = $export; ?>");
                    }
                }
            }
            plugin_mark_installed('ai_rewriter');
            // После установки вернуть в список плагинов (только для ручного apply)
            if ($action === 'apply') {
                global $PHP_SELF;
                header('Location: ' . $PHP_SELF . '?mod=extras');
                exit;
            }
            break;
    }
    return true;
}

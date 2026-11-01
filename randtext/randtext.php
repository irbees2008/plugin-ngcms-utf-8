<?php
if (!defined('NGCMS')) {
     die("Доступ запрещён");
}

add_act('index', 'randtext');

function randtext()
{
     global $template, $config;

     // Параметры плагина
     $pluginName = 'randtext';
     $cacheEnabled = extra_get_param($pluginName, 'cache');
     $cacheTtl = extra_get_param($pluginName, 'cacheExpire') ?: 3600; // 1 час по умолчанию

     // Генерация уникального ключа кеша
     $cacheKey = md5(
          $pluginName .
               $config['theme'] .
               $config['default_lang'] .
               (defined('LANG') ? LANG : '')
     );
     $cacheFile = $cacheKey . '.txt';

     // Попытка чтения из кеша
     if ($cacheEnabled) {
          $cacheData = cacheRetrieveFile($cacheFile, $cacheTtl, $pluginName);
          if ($cacheData !== false) {
               $template['vars']['randtext'] = $cacheData;
               return;
          }
     }

     // Основная логика при промахе кеша
     $dir = root . 'plugins/' . $pluginName . '/';
     $filename = $dir . 'texts.txt';

     // Проверка файла
     if (!is_file($filename) || !is_readable($filename)) {
          error_log("[$pluginName] Файл не найден: $filename");
          return;
     }

     // Чтение и обработка файла с поддержкой UTF-8
     $fileContent = file_get_contents($filename);

     // Удаление BOM
     if (substr($fileContent, 0, 3) == "\xEF\xBB\xBF") {
          $fileContent = substr($fileContent, 3);
     }

     // Разделение на строки
     $lines = preg_split('/\R/u', $fileContent, -1, PREG_SPLIT_NO_EMPTY);
     $lines = array_map('trim', $lines);
     $lines = array_filter($lines);

     if (empty($lines)) {
          error_log("[$pluginName] Файл пуст: $filename");
          return;
     }

     // Получение случайной строки
     $randomLine = $lines[array_rand($lines)];

     // Сохранение в кеш
     if ($cacheEnabled) {
          cacheStoreFile($cacheFile, $randomLine, $pluginName);
     }

     // Передача в шаблон
     $template['vars']['randtext'] = $randomLine;
}
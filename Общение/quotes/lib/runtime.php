<?php

function quotes_log(string $event, array $context = []): void
{
    $record = [
        'plugin' => 'quotes',
        'event' => $event,
        'time' => gmdate(DATE_ATOM),
    ] + $context;

    $json = json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    error_log('[quotes] ' . ($json === false ? '{"event":"log_encode_failed"}' : $json));
}

function quotes_parse_import_content(string $content, int $maxChar): array
{
    if (str_starts_with($content, "\xEF\xBB\xBF")) {
        $content = substr($content, 3);
    }
    if (!mb_check_encoding($content, 'UTF-8')) {
        return ['error' => 'invalid_encoding'];
    }

    $lines = preg_split('/\R/u', $content);
    if ($lines === false) {
        return ['error' => 'read_failed'];
    }

    $quotes = [];
    $skippedEmpty = 0;
    $truncated = 0;
    foreach ($lines as $line) {
        $quote = trim($line);
        if ($quote === '') {
            $skippedEmpty++;
            continue;
        }
        if (count($quotes) >= 5000) {
            return ['error' => 'too_many_quotes'];
        }
        if ($maxChar > 0 && mb_strlen($quote, 'UTF-8') > $maxChar) {
            $quote = mb_substr($quote, 0, $maxChar, 'UTF-8');
            $truncated++;
        }
        $quotes[] = $quote;
    }

    return [
        'quotes' => $quotes,
        'skipped_empty' => $skippedEmpty,
        'truncated' => $truncated,
    ];
}

function quotes_filter_hidden_blocks(string $content): string
{
    global $config, $lang, $userROW;

    if (empty($config['blocks_for_reg'])) {
        return $content;
    }

    if (is_array($userROW)) {
        return preg_replace_callback('/\[hide\]\s*(.*?)\s*\[\/hide\]/is', static function ($matches) {
            return $matches[1];
        }, $content);
    }

    $replacement = str_replace('{text}', $lang['quotes_not_logged'] ?? '', $lang['quotes_not_logged_html'] ?? '');
    return preg_replace_callback('/\[hide\]\s*(.*?)\s*\[\/hide\]/is', static function () use ($replacement) {
        return $replacement;
    }, $content);
}

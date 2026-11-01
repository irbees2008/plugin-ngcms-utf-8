<?php
if (!defined('NGCMS')) {
    die('HAL');
}

add_act('news_full', 'scn');

function scn()
{
    global $mysql, $SYSTEM_FLAGS, $tvars;

    $currentNewsId = (int) ($SYSTEM_FLAGS['news']['db.id'] ?? 0);
    $currentAltName = (string) ($SYSTEM_FLAGS['news']['db.alt'] ?? (defined('altname') ? altname : ''));
    $currentNews = $SYSTEM_FLAGS['news']['db.record'] ?? null;
    if (!is_array($currentNews) && $currentAltName !== '') {
        $currentNews = $mysql->record(
            'SELECT id, catid, alt_name FROM ' . prefix . '_news'
            . ' WHERE alt_name = ' . db_squote($currentAltName)
            . ' LIMIT 1'
        );
    }

    $categoryIds = [];
    if (is_array($currentNews) && !empty($currentNews['catid'])) {
        $currentNewsId = (int) ($currentNews['id'] ?? $currentNewsId);
        $currentAltName = (string) ($currentNews['alt_name'] ?? $currentAltName);
        foreach (explode(',', (string) $currentNews['catid']) as $categoryId) {
            if ((int) $categoryId > 0) {
                $categoryIds[] = (int) $categoryId;
            }
        }
    } else {
        foreach (($SYSTEM_FLAGS['news']['db.categories'] ?? []) as $categoryId) {
            if ((int) $categoryId > 0) {
                $categoryIds[] = (int) $categoryId;
            }
        }
    }
    $categoryIds = array_values(array_unique($categoryIds));

    if (!$categoryIds) {
        $tvars['vars']['plugin_scn'] = '';
        return;
    }

    $number = (int) extra_get_param('scn', 'number');
    if ($number < 1) {
        $number = 5;
    }

    $orderOptions = [
        0 => 'RAND()',
        1 => 'postdate DESC',
        2 => 'postdate ASC',
        3 => 'id DESC',
        4 => 'id ASC',
    ];
    $orderBy = $orderOptions[(int) extra_get_param('scn', 'orderby')] ?? $orderOptions[1];

    $categoryConditions = [];
    foreach ($categoryIds as $categoryId) {
        $categoryConditions[] = 'FIND_IN_SET(' . $categoryId . ', catid)';
    }

    $excludeCurrentNews = $currentNewsId > 0
        ? 'id <> ' . $currentNewsId
        : 'alt_name <> ' . db_squote($currentAltName);
    $query = 'SELECT * FROM ' . prefix . '_news'
        . ' WHERE approve = 1'
        . ' AND ' . $excludeCurrentNews
        . ' AND (' . implode(' OR ', $categoryConditions) . ')'
        . ' ORDER BY ' . $orderBy
        . ' LIMIT ' . $number;

    $output = '';
    $useCustomTemplate = (int) extra_get_param('scn', 'mantemplate') === 1;
    $customTemplate = (string) extra_get_param('scn', 'template');

    foreach ($mysql->select($query) as $row) {
        $title = htmlspecialchars((string) $row['title'], ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars((string) newsGenerateLink($row), ENT_QUOTES, 'UTF-8');

        if (!$useCustomTemplate) {
            $output .= '<a href="' . $link . '">' . $title . '</a><br />';
            continue;
        }

        $date = date('d.m.Y', (int) $row['postdate']);
        $output .= strtr($customTemplate, [
            '{link}' => $link,
            '{title}' => $title,
            '{date}' => htmlspecialchars($date, ENT_QUOTES, 'UTF-8'),
            '{author}' => htmlspecialchars((string) $row['author'], ENT_QUOTES, 'UTF-8'),
            '{com}' => (string) (int) $row['com'],
            '{views}' => (string) (int) $row['views'],
        ]);
    }

    $tvars['vars']['plugin_scn'] = $output;
}

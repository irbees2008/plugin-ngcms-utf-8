<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

pluginsLoadConfig();
loadPluginLang('freelance_market', 'main', '', '', ':');

$db_update = [
    [
        'table'  => 'freelance_jobs',
        'action' => 'cmodify',
        'engine' => 'MyISAM',
        'key'    => 'primary key(id), KEY `user_id` (`user_id`), KEY `status` (`status`), FULLTEXT(title), FULLTEXT(description)',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'],
            ['action' => 'cmodify', 'name' => 'date', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'user_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'title', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'description', 'type' => 'text', 'params' => 'NOT NULL'],
            ['action' => 'cmodify', 'name' => 'price', 'type' => 'decimal(12,2)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'location', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'contacts', 'type' => 'text', 'params' => 'NOT NULL'],
            ['action' => 'cmodify', 'name' => 'status', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default 1'],
            ['action' => 'cmodify', 'name' => 'views', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
        ]
    ],
    [
        'table'  => 'freelance_bids',
        'action' => 'cmodify',
        'engine' => 'MyISAM',
        'key'    => 'primary key(id), KEY `job_id` (`job_id`), KEY `user_id` (`user_id`)',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'],
            ['action' => 'cmodify', 'name' => 'job_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'user_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'message', 'type' => 'text', 'params' => 'NOT NULL'],
            ['action' => 'cmodify', 'name' => 'price_offer', 'type' => 'decimal(12,2)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'date', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'status', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default 0'],
        ]
    ],
    [
        'table'  => 'freelance_user_access',
        'action' => 'cmodify',
        'engine' => 'MyISAM',
        'key'    => 'primary key(user_id)',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'user_id', 'type' => 'int(10)', 'params' => 'NOT NULL'],
            ['action' => 'cmodify', 'name' => 'access_until', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
        ]
    ],
    [
        'table'  => 'freelance_pay_order',
        'action' => 'cmodify',
        'engine' => 'MyISAM',
        'key'    => 'primary key(id), KEY `user_id` (`user_id`), KEY `status` (`status`), KEY `order_id` (`order_id`)',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'],
            ['action' => 'cmodify', 'name' => 'dt', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'user_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'merchant_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'order_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'amount', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'currency', 'type' => 'varchar(50)', 'params' => 'NOT NULL default "RUB"'],
            ['action' => 'cmodify', 'name' => 'description', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'paymode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'trans_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'status', 'type' => 'varchar(50)', 'params' => 'NOT NULL default "new"'],
            ['action' => 'cmodify', 'name' => 'error_msg', 'type' => 'varchar(255)', 'params' => 'NOT NULL default ""'],
            ['action' => 'cmodify', 'name' => 'test_mode', 'type' => 'varchar(10)', 'params' => 'NOT NULL default ""'],
        ]
    ],
    [
        'table'  => 'freelance_rating',
        'action' => 'cmodify',
        'engine' => 'MyISAM',
        'key'    => 'primary key(id), KEY `ratee_id` (`ratee_id`), KEY `rater_id` (`rater_id`), KEY `job_id` (`job_id`)',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'],
            ['action' => 'cmodify', 'name' => 'rater_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'ratee_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'job_id', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'score', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default 0'],
            ['action' => 'cmodify', 'name' => 'comment', 'type' => 'text', 'params' => 'NOT NULL'],
            ['action' => 'cmodify', 'name' => 'dt', 'type' => 'int(10)', 'params' => 'NOT NULL default 0'],
        ]
    ],
];

if ($_REQUEST['action'] == 'commit') {
    if (fixdb_plugin_install('freelance_market', $db_update)) {
        // Параметры по умолчанию
        $params = array(
            'price_10' => '199',
            'price_30' => '499',
            'robokassa_login' => '',
            'robokassa_pass1' => '',
            'robokassa_pass2' => '',
            'robokassa_is_test' => '1',
        );
        foreach ($params as $k => $v) {
            extra_set_param('freelance_market', $k, $v);
        }
        extra_commit_changes();
        plugin_mark_installed('freelance_market');
    }
} else {
    $text = $lang['freelance_market:install_desc'];
    generate_install_page('freelance_market', $text);
}

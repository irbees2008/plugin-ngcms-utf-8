<?php
if (!defined('NGCMS'))
{
    die ('HAL');
}

include_once(dirname(__FILE__).'/functions.php');

function plugin_eshop_install($action) {
    global $lang, $mysql;

    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/thumb/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products/thumb'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products/temp'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp/thumb/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products/temp/thumb'), 1);

    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/categories'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/thumb/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/categories/thumb'), 1);

    if ($action != 'autoapply')
        loadPluginLang('eshop', 'config', '', '', ':');
    $db_update = array(
    array(
        'table'     => 'eshop_products',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `name` (`name`), KEY `brand_id` (`brand_id`), KEY `position` (`position`), KEY `featured` (`featured`), KEY `active` (`active`), KEY `likes` (`likes`), KEY `comments` (`comments`), KEY `stocked` (`stocked`), KEY `views` (`views`), UNIQUE `url` (`url`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'code', 'type' => 'char(255)', 'params' => 'NOT NULL DEFAULT \'\''),
            array('action'  => 'cmodify', 'name' => 'url', 'type' => 'char(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'brand_id', 'type' => 'INT(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'annotation', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'body', 'type' => 'longtext', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'active', 'type' => 'tinyint(1)', 'params' => 'NOT NULL DEFAULT \'1\''),
            array('action'  => 'cmodify', 'name' => 'featured', 'type' => 'tinyint(1)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'  => 'cmodify', 'name' => 'stocked', 'type' => 'tinyint(1)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'meta_title', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'meta_keywords', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'meta_description', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'date', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'  => 'cmodify', 'name' => 'editdate', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'views', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'  => 'cmodify', 'name' => 'likes', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'  => 'cmodify', 'name' => 'comments', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
        )
    ),

    array(
        'table'  => 'eshop_products_comments',
        'action' => 'cmodify',
        'key'      => 'primary key(id), KEY `product_id` (`product_id`)',
        'fields' => array(
            array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(11)', 'params' => 'not null auto_increment'),
            array('action' => 'cmodify', 'name' => 'postdate', 'type' => 'INT(11)', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'product_id', 'type' => 'int', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(255)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'author', 'type' => 'char(100)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'mail', 'type' => 'char(100)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'text', 'type' => 'text'),
            array('action' => 'cmodify', 'name' => 'answer', 'type' => 'text'),
            array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'reg', 'type' => 'tinyint(1)', 'params'=> "default '0'"),
            array('action' => 'cmodify', 'name' => 'status', 'type' => 'tinyint(1)', 'params'=> "default '1'"),
        )
    ),
    
    array(
        'table' => 'eshop_products_likes',
        'action' => 'cmodify',
        'key' => 'primary key (`id`), KEY `product_id` (`product_id`)',
        'fields' => array(
            array('action' => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'not null auto_increment'),
            array('action' => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'cookie', 'type' => 'char(50)', 'params' => 'default ""'),
            array('action' => 'cmodify', 'name' => 'state', 'type' => 'tinyint(2)', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'host_ip', 'type' => 'varchar(100)', 'params' => "NOT NULL default ''"),
        )
    ),

    array(
        'table'     => 'eshop_products_view',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL'),
            array('action'  => 'cmodify', 'name' => 'cnt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'     => 'eshop_features',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `position` (`position`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'in_filter', 'type' => 'int(1)', 'params' => 'NOT NULL default \'0\''),
        )
    ),
        
    array(
        'table'     => 'eshop_options',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(`product_id`, `feature_id`), KEY `product_id` (`product_id`), KEY `feature_id` (`feature_id`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'feature_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'value', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
        )
    ),

    array(
        'table'     => 'eshop_related_products',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(`product_id`, `related_id`), KEY `product_id` (`product_id`), KEY `related_id` (`related_id`), KEY `position` (`position`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'related_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),
        
    array(
        'table'     => 'eshop_categories',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `name` (`name`), KEY `parent_id` (`parent_id`), UNIQUE `url` (`url`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'url', 'type' => 'char(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'image', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'parent_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'description', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'meta_title', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'meta_keywords', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'meta_description', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'active', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'1\''),
        )
    ),

    array(
        'table'     => 'eshop_products_categories',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(`category_id`, `product_id`), KEY `product_id` (`product_id`), KEY `category_id` (`category_id`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'category_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'     => 'eshop_categories_features',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(`category_id`, `feature_id`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'category_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'feature_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),
        
    array(
        'table'     => 'eshop_brands',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `url` (`url`), KEY `name` (`name`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'url', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'image', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),

            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'description', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'meta_title', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'meta_keywords', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'meta_description', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),

        )
    ),
        
    array(
        'table'     => 'eshop_purchases',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `order_id` (`order_id`), KEY `product_id` (`product_id`), KEY `variant_id` (`variant_id`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'dt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'order_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'variant_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),

            array('action'  => 'cmodify', 'name' => 'product_name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'variant_name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),

            array('action'  => 'cmodify', 'name' => 'amount', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'sku', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            /*
            array('action'  => 'cmodify', 'name' => 'merchant_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'order_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'amount', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'currency', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'description', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'paymode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'trans_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'status', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'error_msg', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'test_mode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            */
        )
    ),

    array(
        'table'     => 'eshop_orders',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `uniqid` (`uniqid`), KEY `author_id` (`author_id`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'author_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'uniqid', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'dt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'paid', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'type', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'0\''),

            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'address', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'phone', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'email', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'comment', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),

            array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)', 'params' => "default ''"),
            array('action'  => 'cmodify', 'name' => 'total_price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),
        )
    ),
    
    array(
        'table'     => 'eshop_order_basket',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `order_id` (`order_id`), KEY `linked_id` (`linked_id`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'order_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'linked_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action' => 'cmodify', 'name' => 'title', 'type' => 'varchar(500)', 'params' => 'default ""'),
            array('action' => 'cmodify', 'name' => 'count', 'type' => 'int', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'price', 'type' => 'decimal(12,2)', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'linked_fld', 'type' => 'text'),
        )
    ),
    
    array(
        'table'  => 'eshop_compare',
        'action' => 'cmodify',
        'key'    => 'primary key(id)',
        'fields' => array(
            array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
            array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'cookie', 'type' => 'char(50)', 'params' => 'default ""'),
            array('action' => 'cmodify', 'name' => 'linked_fld', 'type' => 'text'),
        )
    ),
        
    array(
        'table'     => 'eshop_variants',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `product_id` (`product_id`), KEY `position` (`position`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),

            array('action'  => 'cmodify', 'name' => 'sku', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),
            array('action'  => 'cmodify', 'name' => 'compare_price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),
            
            array('action'  => 'cmodify', 'name' => 'stock', 'type' => 'mediumint(9)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'  => 'cmodify', 'name' => 'amount', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'attachment', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
        )
    ),
        
    array(
        'table'     => 'eshop_images',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `product_id` (`product_id`), KEY `position` (`position`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'filepath', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),
    
    array(
        'table'     => 'eshop_currencies',
        'action'    => 'cmodify',
        'engine'    => 'MyISAM',
        'key'       => 'primary key(id), KEY `position` (`position`)',
        'fields'    => array(
            array('action'  => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'  => 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'sign', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'  => 'cmodify', 'name' => 'code', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'  => 'cmodify', 'name' => 'rate_from', 'type' => 'decimal(10,4)', 'params' => 'NOT NULL default \'1.00\''),
            array('action'  => 'cmodify', 'name' => 'rate_to', 'type' => 'decimal(10,4)', 'params' => 'NOT NULL default \'1.00\''),
            array('action'  => 'cmodify', 'name' => 'cents', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'1\''),
            array('action'  => 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'  => 'cmodify', 'name' => 'enabled', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'1\''),
        )
    ),

    array(
        'table'  => 'eshop_ebasket',
        'action' => 'cmodify',
        'key'    => 'primary key(id), KEY `linked_id` (`linked_id`)',
        'fields' => array(
            array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
            array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'cookie', 'type' => 'char(50)', 'params' => 'default ""'),
            array('action' => 'cmodify', 'name' => 'dt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action' => 'cmodify', 'name' => 'linked_ds', 'type' => 'int', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'linked_id', 'type' => 'int', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'title', 'type' => 'char(120)', 'params' => 'default ""'),
            array('action' => 'cmodify', 'name' => 'linked_fld', 'type' => 'text'),
            array('action' => 'cmodify', 'name' => 'price', 'type' => 'decimal(12,2)', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'count', 'type' => 'int', 'params' => 'default 0'),
        )
    ),    

);

    switch ($action)
    {
        case 'confirm':
            generate_install_page('eshop', file_get_contents(''));break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('eshop', $db_update, 'install', ($action=='autoapply')?true:false)) {
                $mysql->query("insert into ".prefix."_eshop_currencies values (1,'доллары','$','USD','1.0000','1.0000',1,0,1), (2,'рубли','руб','RUB','0.0133','1.0000',1,1,1), (3,'гривна','грн','UAH','0.0428','1.0000',1,2,1)");
                
                if(!$mysql->record('SHOW INDEX FROM '.prefix.'_eshop_products WHERE Key_name = \'name\''))
                    $mysql->query('alter table '.prefix.'_eshop_products add FULLTEXT (name)');

                if(!$mysql->record('SHOW INDEX FROM '.prefix.'_eshop_products WHERE Key_name = \'annotation\''))
                    $mysql->query('alter table '.prefix.'_eshop_products add FULLTEXT (annotation)');

                if(!$mysql->record('SHOW INDEX FROM '.prefix.'_eshop_products WHERE Key_name = \'body\''))
                    $mysql->query('alter table '.prefix.'_eshop_products add FULLTEXT (body)');
                
                plugin_mark_installed('eshop');
                //create_urls();
                
                $rootpath = $_SERVER['DOCUMENT_ROOT'];
                $rewrite_filepath_src = $rootpath."/engine/plugins/eshop/install_tmp/rewrite.php";
                $urlconf_filepath_src = $rootpath."/engine/plugins/eshop/install_tmp/urlconf.php";
                $rewrite_filepath_dst = $rootpath."/engine/conf/rewrite.php";
                $urlconf_filepath_dst = $rootpath."/engine/conf/urlconf.php";
                
                if (!file_exists($rootpath."/engine/plugins/eshop/install_tmp/backup")) {
                    mkdir($rootpath."/engine/plugins/eshop/install_tmp/backup", 0777, true);
                }
                
                copy($rewrite_filepath_dst, $rootpath."/engine/plugins/eshop/install_tmp/backup/rewrite.php");
                copy($urlconf_filepath_dst, $rootpath."/engine/plugins/eshop/install_tmp/backup/urlconf.php");
                
                copy($rewrite_filepath_src, $rewrite_filepath_dst);
                copy($urlconf_filepath_src, $urlconf_filepath_dst);
                
            } else {
                return false;
            }
            
            $params = array(
                'count' => '8',
                'count_search' => '8',
                'count_stocks' => '8',
                
                'views_count' => '1',
                'bidirect_linked_products' => '0',
                
                'approve_comments' => '0',
                'sort_comments' => '0',
                'integrate_gsmg' => '0',
                
                'url' => '1',
                
                'max_image_size' => '5',
                'width' => '2000',
                'height' => '2000',
                'width_thumb' => '350',
                'ext_image' => 'jpg, jpeg, gif, png',
                
                'pre_width' => '0',
                
                'catz_max_image_size' => '5',
                'catz_width' => '2000',
                'catz_height' => '2000',
                'catz_width_thumb' => '350',
                'catz_ext_image' => 'jpg, jpeg, gif, png',
                
                'email_notify_orders' => '',
                'email_notify_comments' => '',
                'email_notify_back' => '',
                
                'description_delivery' => '<ul>
    <li>Новая Почта</li>
    <li>Другие транспортные службы</li>
    <li>Курьером по Киеву</li>
    <li>Самовывоз</li>
</ul>',
                'description_order' => '<ul>
    <li>Наличными при получении</li>
    <li>Безналичный перевод</li>
    <li>Приват 24</li>
    <li>WebMoney</li>
</ul>',
                'description_phones' => '<div class="frame-ico">
    <span class="icon_work">
    </span>
</div>
<div>
    <div>
        Работаем: 
        <span class="text-el">
        Пн–Пт 09:00–20:00,
        <br>
        Сб 09:00–17:00, Вс выходной
        </span>
    </div>
</div>',

            );
            foreach ($params as $k => $v) {
                extra_set_param('eshop', $k, $v);
            }
            extra_commit_changes();
            break;
    }
    return true;
}

<?php

if(!defined('NGCMS'))
{
    exit('HAL');
}

rpcRegisterFunction('eshop_linked_products', 'linked_prd');
rpcRegisterFunction('eshop_change_img_pos', 'change_img_pos');
rpcRegisterFunction('eshop_change_price', 'change_price');

rpcRegisterFunction('eshop_compare', 'compare_prd');
rpcRegisterFunction('eshop_viewed', 'viewed_prd');

rpcRegisterFunction('eshop_likes_result', 'likes_result');

rpcRegisterFunction('eshop_comments_add', 'comments_add');
rpcRegisterFunction('eshop_comments_show', 'comments_show');

rpcRegisterFunction('eshop_ebasket_manage', 'ebasket_rpc_manage');

function linked_prd($params){
    global $userROW, $mysql;

    //$req = iconv( "windows-1251", "utf-8", urldecode($_REQUEST["q"]) );
    $req = urldecode(filter_var( $_REQUEST["q"], FILTER_SANITIZE_STRING ));
    $mode = filter_var( $_REQUEST["mode"], FILTER_SANITIZE_STRING );
    $id = filter_var( $_REQUEST["id"], FILTER_SANITIZE_STRING );

    $conditions = array();
    if ($req) {
        array_push($conditions, "p.name LIKE '%".$req."%' ");
    }
    
    if ($mode == "edit") {
        array_push($conditions, "p.id != '".$id."' ");
    }

    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.name AS category, i.filepath AS image_filepath ".$sqlQPart;

    foreach ($mysql->select(iconv( "utf-8", "windows-1251", $sqlQ )) as $row)
    {

        $tEntry[] = array (
            'id'                   => $row['id'],
            'name'                 => iconv("windows-1251", "utf-8", $row['name'] ),
            'image_filepath'       => $row['image_filepath'],
            'code'                 => iconv("windows-1251", "utf-8", $row['code'] ),
            'category'             => iconv("windows-1251", "utf-8", $row['category'] ),
        );
    }

    return $tEntry;
}

function change_img_pos($params){
    global $userROW, $mysql;

    $results = array();
    $params = arrayCharsetConvert(1, $params);

    $img_id = intval($params['img_id']);
    $position = intval($params['position']);
    $prevPosition = intval($params['prevPosition']);

    if($userROW['status'] < 3) {
        $img_row = $mysql->record("select * from ".prefix."_eshop_images where id = ".db_squote($img_id)."");
        if(isset($img_row)) {
            $mysql->query("update ".prefix."_eshop_images set position = ".db_squote($img_row['position'])." where (product_id = ".db_squote($img_row['product_id']).") and (position = ".db_squote($position).") ");
            //$prev_img_row = $mysql->record("select * from ".prefix."_eshop_images where (product_id = ".db_squote($img_row['product_id']).") and (position = ".db_squote($prevPosition).")");
            $mysql->query("update ".prefix."_eshop_images set position = ".db_squote($position)." where (id = ".db_squote($img_row['id']).") ");
            return array('status' => 1, 'errorCode' => 0, 'data' => 'Images swaped success', 'update' => '');
        }
    }

    return array('status' => 0, 'errorCode' => 0, 'data'     => 'OK, '.var_export($params, true));
}

function change_price($params){
    global $userROW, $mysql;

    $results = array();
    $params = arrayCharsetConvert(1, $params);

    $qid = intval($params['id']);
    $mode = filter_var( $params['mode'], FILTER_SANITIZE_STRING );
    $price = floatval(filter_var( $params['price'], FILTER_SANITIZE_STRING ));
    
    if($price < 0) {
        $price = 0;
    }

    if($userROW['status'] < 3) {
        
        $SQL = array();
        switch ($mode) {
            case 'price':
                $SQL['price'] = $price;
            break;
            case 'compare_price':
                $SQL['compare_price'] = $price;
            break;
        }
        
        if(isset($qid)) {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            //var_dump('UPDATE '.prefix.'_eshop_variants SET '.implode(', ',$vnames).' WHERE product_id = \''.intval($qid).'\'  ');
            $mysql->query('UPDATE '.prefix.'_eshop_variants SET '.implode(', ',$vnames).' WHERE product_id = \''.intval($qid).'\'  ');
            return array('status' => 1, 'errorCode' => 0, 'data' => 'Price success changed', 'update' => '');
        }

    }

    return array('status' => 0, 'errorCode' => 0, 'data'     => 'OK, '.var_export($params, true));
}

function compare_prd($params){
    global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $twigLoader;

    $results = array();
    $params = arrayCharsetConvert(1, $params);
    
    $id = intval($params['id']);

    if ($id < 1) {
        return array('status' => 0, 'errorCode' => 2, 'errorText' => 'ID should be positive');
    }

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO compare ****
        case 'add':

            // Check if now we're logged in and earlier we started filling ebasket before logging in
            if (is_array($userROW)) {
                $mysql->query("update ".prefix."_eshop_compare set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
            }

            $mysql->query("insert into ".prefix."_eshop_compare (user_id, cookie, linked_fld) values (".(is_array($userROW)?db_squote($userROW['id']):0).", ".db_squote($_COOKIE['ngTrackID']).", ".db_squote($id).")");

            // ======== Prepare update of totals informer ========
            $filter = array();
            if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
            if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }

            $tCount = 0;
            
            if (count($filter) && is_array($res = $mysql->record("select count(*) as count from ".prefix."_eshop_compare where ".join(" or ", $filter), 1))) {
                $tCount = $res['count'];
            }

            $tVars = array(
                'count' => $tCount,
            );

            $tpath = locatePluginTemplates(array('compare_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
            $xt = $twig->loadTemplate($tpath['compare_block_eshop'].'compare_block_eshop.tpl');

            return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into compare', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));

            break;
        case 'remove':
        
            // Check if now we're logged in and earlier we started filling ebasket before logging in
            if (is_array($userROW)) {
                $mysql->query("update ".prefix."_eshop_compare set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
            }
            
            // ======== Prepare update of totals informer ========
            $filter = array();
            if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
            if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }

            $mysql->query("delete from ".prefix."_eshop_compare where (".join(" or ", $filter).") and linked_fld = ".db_squote($id)." ");

            $tCount = 0;
            
            if (count($filter) && is_array($res = $mysql->record("select count(*) as count from ".prefix."_eshop_compare where ".join(" or ", $filter), 1))) {
                $tCount = $res['count'];
            }

            $tVars = array(
                'count' => $tCount,
            );

            $tpath = locatePluginTemplates(array('compare_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
            $xt = $twig->loadTemplate($tpath['compare_block_eshop'].'compare_block_eshop.tpl');

            return array('status' => 1, 'errorCode' => 0, 'data' => 'Item removed from compare', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));

            break;
    }
    
    return array('status' => 1, 'errorCode' => 0, 'data'     => 'OK, '.var_export($params, true));
}

function viewed_prd($params){
    global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $twigLoader;

    $results = array();
    $params = arrayCharsetConvert(1, $params);
    $page_stack = filter_var( $params['page_stack'], FILTER_SANITIZE_STRING );

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO compare ****
        case 'show':

            if(!empty($page_stack)) {

                $conditions = array();
                array_push($conditions, "p.active = 1 ");
                array_push($conditions, "p.id IN (".$page_stack.") ");

                $fSort = " GROUP BY p.id ORDER BY p.id DESC";
                $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
                $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.url as curl, c.name AS category ".$sqlQPart;

                $cmp_array = array();
                foreach ($SYSTEM_FLAGS["eshop"]["compare"]["entries"] as $cmp_row)
                {
                    $cmp_array[] = $cmp_row['linked_fld'];
                }
                
                foreach ($mysql->select($sqlQ) as $row)
                {
                    $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
                    $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $row['curl'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));
                        
                    $cmp_flag = in_array($row['id'], $cmp_array);

                    $entries[$row['id']] = array (
                        'id' => $row['id'],
                        'code' => $row['code'],
                        'name' => $row['name'],
                        
                        'annotation' => $row['annotation'],
                        'body' => $row['body'],
                        
                        'active' => $row['active'],
                        'featured' => $row['featured'],

                        'meta_title' => $row['meta_title'],
                        'meta_keywords' => $row['meta_keywords'],
                        'meta_description' => $row['meta_description'],
                        
                        'fulllink' => $fulllink,

                        'date' => (empty($row['date']))?'':$row['date'],
                        'editdate' => (empty($row['editdate']))?'':$row['editdate'],
                        
                        'views'     =>  $row['views'],
                        
                        'cat_name' => $row['category'],
                        'cid' => $row['cid'],
                        'catlink' => $catlink,
                        
                        'compare' => $cmp_flag,
                        
                        'home' => home,
                        'tpl_url' => home.'/templates/'.$config['theme'],
                    );

                }
                
            }
            
            $entries_array_ids = array_keys($entries);
            
            if(isset($entries_array_ids) && !empty($entries_array_ids)) {
                
                $entries_string_ids = implode(',', $entries_array_ids);
            
                foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_images i WHERE i.product_id IN ('.$entries_string_ids.') ORDER BY i.position, i.id') as $irow)
                {
                    $entries[$irow['product_id']]['images'][] = $irow;
                }
                
                foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_variants v WHERE v.product_id IN ('.$entries_string_ids.') ORDER BY v.position, v.id') as $vrow)
                {
                    $entries[$vrow['product_id']]['variants'][] = $vrow;
                }
                
            }
            
            $tVars = array(
                'info' =>   isset($info)?$info:'',
                'entries' => isset($entries)?$entries:'',
                'tpl_url' => home.'/templates/'.$config['theme'],
                'tpl_home' => admin_url,
            );
            
            $tpath = locatePluginTemplates(array('viewed_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
            $xt = $twig->loadTemplate($tpath['viewed_block_eshop'].'viewed_block_eshop.tpl');
    
            return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into compare', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));

            break;
    }
    
    return array('status' => 1, 'errorCode' => 0, 'data'     => 'OK, '.var_export($params, true));
}

function likes_result($params){
    global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $twigLoader;

    $results = array();
    $params = arrayCharsetConvert(1, $params);
    $id = filter_var( $params['id'], FILTER_SANITIZE_STRING );

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO compare ****
        case 'do_like':

            if(!empty($id)) {
                
                // Check if now we're logged in and earlier we started filling ebasket before logging in
                if (is_array($userROW)) {
                    $mysql->query("update ".prefix."_eshop_products_likes set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
                }
                
                $filter = array();
                if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
                if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }

                $tCount = 0;
                
                if (count($filter) && is_array($check_like = $mysql->record("select count(*) as count from ".prefix."_eshop_products_likes where (".join(" or ", $filter).") AND product_id = ".db_squote($id)." ", 1))) {
                    $tCount = $check_like['count'];
                }
                
                if($tCount == 0) {
            
                    $mysql->query("insert into ".prefix."_eshop_products_likes (user_id, cookie, product_id) values (".(is_array($userROW)?db_squote($userROW['id']):0).", ".db_squote($_COOKIE['ngTrackID']).", ".db_squote($id).")");
                    
                    $mysql->query("update ".prefix."_eshop_products set likes = likes + 1 where id = ".db_squote($id)." ");

                }
                else {
                    
                    $mysql->query("delete from ".prefix."_eshop_products_likes where (".join(" or ", $filter).") AND product_id = ".db_squote($id)." ");
                    
                    $mysql->query("update ".prefix."_eshop_products set likes = likes - 1 where id = ".db_squote($id)." ");
                }

                $likes = $mysql->record("SELECT COUNT(*) as count FROM ".prefix."_eshop_products_likes l WHERE l.product_id='".$id."'");
                
                $likes_tVars = array (
                    'count' => $likes['count'],
                    'id' => $id,
                );
                        
                //var_dump($likes_tVars);

                $tpath = locatePluginTemplates(array('likes_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
                $xt = $twig->loadTemplate($tpath['likes_eshop'].'likes_eshop.tpl');

                return array('status' => 1, 'errorCode' => 0, 'data' => 'Likes updated', 'update' => arrayCharsetConvert(0, $xt->render($likes_tVars)));
                
                
            }



            break;
        }
    
    return array('status' => 1, 'errorCode' => 0, 'data'     => 'OK, '.var_export($params, true));
}

function comments_add($params) {
    global $tpl, $template, $twig, $ip, $SYSTEM_FLAGS, $config, $userROW, $mysql, $TemplateCache;

    // Prepare basic reply array
    $results = array();

    if (isset($userROW)) {
        $SQL['name']            = $userROW['name'];
        $SQL['author']          = $userROW['name'];
        $SQL['author_id']       = $userROW['id'];
        $SQL['mail']            = $userROW['mail'];
        $is_member              = 1;
        $memberRec              = $userROW;
    }
    else {
        $SQL['name']            = secure_html(convert(trim($params['comment_author'])));
        $SQL['author']          = secure_html(convert(trim($params['comment_author'])));
        $SQL['author_id']       = 0;
        $SQL['mail']            = secure_html(convert(trim($params['comment_email'])));
        $is_member              = 0;
        $memberRec              = "";
    }
    $SQL['text']    =   secure_html(convert(trim($params['comment_text'])));
    $SQL['product_id'] =  $params['product_id'];
    $SQL['postdate'] = time() + ($config['date_adjust'] * 60);

    $SQL['text']    = str_replace("\r\n", "<br />", $SQL['text']);
    $SQL['ip']      = $ip;
    $SQL['reg']     = ($is_member) ? '1' : '0';
    
    $approve_comments = pluginGetVariable('eshop', 'approve_comments');
    if($approve_comments == "1") {
        $SQL['status']      = '0';
    }
    else {
        $SQL['status']      = '1';
    }

    if(empty($SQL['name']))
    {
        $error_text[] = 'Вы не ввели имя!';
    }

    if(empty($SQL['mail']))
    {
        $error_text[] = 'Вы не ввели email!';
    }

    if(empty($SQL['text']))
    {
        $error_text[] = 'Вы не написали комментарий!';
    }

    if( empty($error_text) )
    {

        // Create comment
        $vnames = array(); $vparams = array();
        foreach ($SQL as $k => $v) { $vnames[]  = $k; $vparams[] = db_squote($v); }

        $mysql->query("insert into ".prefix."_eshop_products_comments (".implode(",",$vnames).") values (".implode(",",$vparams).")");

        // Update comment counter
        $mysql->query("update ".prefix."_eshop_products set comments = comments + 1 where id = ".db_squote($SQL['product_id'])." ");

        $notify_tpath = locatePluginTemplates(array('mail/lfeedback_comment'), 'eshop', pluginGetVariable('eshop', 'localsource'));
        $notify_xt = $twig->loadTemplate($notify_tpath['mail/lfeedback_comment'].'mail/'.'lfeedback_comment.tpl');

        $prd = $mysql->record("SELECT * FROM ".prefix."_eshop_products WHERE id=".db_squote($SQL['product_id'])." ");

        $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $prd['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $prd['url']));
        $prd['fulllink'] = $fulllink;

        $pVars = array(
            'vproduct' => $prd,
            'vnames'   => $SQL,
        );
    
        $mailBody = $notify_xt->render($pVars);
        $mailSubject = "Новый комментарий с сайта";
        $mailTo = pluginGetVariable('eshop', 'email_notify_comments');
        $mail_from = pluginGetVariable('eshop', 'email_notify_back');
        
        if($mail_from == "") {
            $mail_from = false;
        }
            
        if($mailTo != "") {
            sendEmailMessage($mailTo, $mailSubject, $mailBody, $filename = false, $mail_from, $ctype = 'text/html');
        }

        $results = array(
            'eshop_comments'    => 100,
            'eshop_comments_text' => iconv('Windows-1251', 'UTF-8', 'Комментарий успешно добавлен!'),
            'eshop_comments_show' => iconv('Windows-1251', 'UTF-8', comments_show_handler($params))
        );
        
    }
    else {

        $results = array(
            'eshop_comments' => 2,
            'eshop_comments_text' => iconv('Windows-1251', 'UTF-8', implode('<br />', $error_text)),
            'eshop_comments_show' => iconv('Windows-1251', 'UTF-8', comments_show_handler($params))
        );

    }

    return array('status' => 1, 'errorCode' => 0, 'data' => $results);
}

function comments_show($params){
    global $mysql, $tpl, $template, $config, $userROW, $parse, $lang, $PFILTERS, $TemplateCache, $twig, $mysql;

    // Prepare basic reply array
    $results = array();

    $results = array(
            'eshop_comments'    => 100,
            'eshop_comments_text' => iconv('Windows-1251', 'UTF-8', 'Комментарии'),
            'eshop_comments_show' => iconv('Windows-1251', 'UTF-8', comments_show_handler($params))
    );
    
    return array('status' => 1, 'errorCode' => 0, 'data' => $results);

}

function comments_show_handler($params){
    global $mysql, $tpl, $template, $config, $userROW, $parse, $lang, $PFILTERS, $TemplateCache, $twig, $mysql;

    // Preload template configuration variables
    templateLoadVariables();

    // Use default <noavatar> file
    // - Check if noavatar is defined on template level
    $tplVars = $TemplateCache['site']['#variables'];
    $noAvatarURL = (isset($tplVars['configuration']) && is_array($tplVars['configuration']) && isset($tplVars['configuration']['noAvatarImage']) && $tplVars['configuration']['noAvatarImage'])?(tpl_url."/".$tplVars['configuration']['noAvatarImage']):(avatars_url."/noavatar.gif");

    $tpath = locatePluginTemplates(array('comments.show_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $xt = $twig->loadTemplate($tpath['comments.show_eshop'].'comments.show_eshop.tpl');

    $conditions = array();
    if ($params['product_id']) {
        array_push($conditions, "c.product_id = ".db_squote($params['product_id']));
    }
    
    $approve_comments = pluginGetVariable('eshop', 'approve_comments');
    if($approve_comments == "1") {
        array_push($conditions, "c.status = 1");
    }
    
    $sort_comments = pluginGetVariable('eshop', 'sort_comments');
    if($sort_comments == "0") {
        $dSort = " ASC";
    }
    else {
        $dSort = " DESC";
    }

    $fSort = "ORDER BY c.postdate".$dSort;

    $sqlQPart = "from ".prefix."_eshop_products_comments c LEFT JOIN ".prefix."_users u ON c.author_id = u.id ".(count($conditions)?"where ".implode(" AND ", $conditions):'').' '.$fSort;
    $sqlQ = "select *, c.id as cid, u.id as uid, u.name as uname, c.name as name ".$sqlQPart;

    $output = '';
    foreach ($mysql->select($sqlQ) as $row) {
        // Add [hide] tag processing
        $text   = $row['text'];

        if ($config['use_bbcodes'])         { $text = $parse -> bbcodes($text); }
        if ($config['use_htmlformatter'])   { $text = $parse -> htmlformatter($text); }
        if ($config['use_smilies'])         { $text = $parse -> smilies($text); }

        if ($config['use_avatars']) {
            if ($row['avatar']) {
                $avatar = avatars_url."/".$row['avatar'];
            } else {
                    $avatar = $noAvatarURL;
            }
        } else {
            $avatar = '';
        }
        
        
        $Entries[] = array (
                'id' => $row['cid'],
                'mail' =>   $row['mail'],
                'author' => $row['name'],
                'date' => $row['postdate'],
                'profile_link' => checkLinkAvailable('uprofile', 'show')?
                    generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
                    generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id'])),
                'avatar' => $avatar,
                'name' => $row['uname'],
                'commenttext' => $text
                );
                
        //$output .= $xt->render($tVars);
    }

    $tVars = array(
        'entries' => isset($Entries)?$Entries:'',
        'tpl_url' => home.'/templates/'.$config['theme'],
        'tpl_home' => admin_url
    );
        
    $output .=  $xt->render($tVars);

    return $output;
}

function ebasket_add_item($linked_ds, $linked_id, $title, $price, $count, $xfld = array()) {
    global $mysql, $userROW, $twig, $template, $config;

    // Check if now we're logged in and earlier we started filling ebasket before logging in
    if (is_array($userROW)) {
        $mysql->query("update ".prefix."_eshop_ebasket set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
    }
    
    $current_time = time() + ($config['date_adjust'] * 60);

    $mysql->query("insert into ".prefix."_eshop_ebasket (user_id, cookie, dt, linked_ds, linked_id, title, linked_fld, price, count) values (".(is_array($userROW)?db_squote($userROW['id']):0).", ".db_squote($_COOKIE['ngTrackID']).", ".db_squote($current_time).", ".db_squote($linked_ds).", ".db_squote($linked_id).", ".db_squote($title).", ".db_squote(serialize($xfld)).", ".db_squote($price).", ".db_squote($count).") on duplicate key update price=".db_squote($price).", count = count+".db_squote($count));

    // ======== Prepare update of totals informer ========
    $filter = array();
    if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }

    $tCount = 0;
    $tPrice = 0;

    if (count($filter) && is_array($res = $mysql->record("select count(*) as count, sum(price*count) as price from ".prefix."_eshop_ebasket where ".join(" or ", $filter), 1))) {
        $tCount = $res['count'];
        $tPrice = $res['price'];
    }
    
    $basket_link = checkLinkAvailable('eshop', 'ebasket_list')?
            generateLink('eshop', 'ebasket_list', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'ebasket_list'), array());

    $tVars = array(
        'count'         => $tCount,
        'price'         => $tPrice,
        'basket_link'   => $basket_link,
    );

    $tpath = locatePluginTemplates(array('ebasket/total'), 'eshop', pluginGetVariable('eshop', 'localsource'));

    $xt = $twig->loadTemplate($tpath['ebasket/total'].'ebasket/'.'total.tpl');
    
    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into ebasket', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));
}

function ebasket_add_fast_order($linked_ds, $linked_id, $title, $price, $count, $type, $order, $xfld = array()) {
    global $mysql, $userROW, $twig, $template, $ip;

    $SQL['name'] = $order['name'];
    $SQL['email'] = $order['email'];
    $SQL['phone'] = $order['phone'];
    $SQL['address'] = $order['address'];
    $SQL['comment'] = "";

    $SQL['dt'] = time() + ($config['date_adjust'] * 60);
    $SQL['ip'] =  $ip;
    
    $SQL['type'] =  $type;
    
    $SQL['paid'] = 0;
    $SQL['total_price'] = round($price * $count, 2);
    
    if(isset($userROW)) {
        $SQL['author_id'] = $userROW['id'];
    }
        
    $SQL['uniqid'] = substr(str_shuffle(MD5(microtime())), 0, 10);
    
    $vnames = array();
    foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
    $mysql->query('INSERT INTO '.prefix.'_eshop_orders SET '.implode(', ',$vnames).' ');
    $qid = $mysql->lastid('eshop_orders');
    
    $mysql->query("INSERT INTO ".prefix."_eshop_order_basket (`order_id`, `linked_id`, `title`, `count`, `price`, `linked_fld`) VALUES (".db_squote($qid).", ".db_squote($linked_id).", ".db_squote($title).", ".db_squote($count).", ".db_squote($price).", ".db_squote(serialize($xfld))." )");
    
    // mail notify

    $filter = array();
    if ($qid) {
        $filter []= '(order_id = '.db_squote($qid).')';
    }

    foreach ($mysql->select("select * from ".prefix."_eshop_order_basket where ".join(" or ", $filter), 1) as $rec) {
                $total += round($rec['price'] * $rec['count'], 2);

                $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
                $rec['xfields'] = unserialize($rec['linked_fld']);
                unset($rec['linked_fld']);
                $basket []= $rec;
    }
    
    $notify_tpath = locatePluginTemplates(array('mail/lfeedback'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $notify_xt = $twig->loadTemplate($notify_tpath['mail/lfeedback'].'mail/'.'lfeedback.tpl');

    $pVars = array(
        'recs'      => count($basket),
        'entries'   => $basket,
        'total'     => sprintf('%9.2f', $total),
        'vnames'   => $SQL,
    );

    $mailBody = $notify_xt->render($pVars);
    $mailSubject = "Новый заказ с сайта";
    $mailTo = pluginGetVariable('eshop', 'email_notify_orders');
    $mail_from = pluginGetVariable('eshop', 'email_notify_back');

    if($mail_from == "") {
        $mail_from = false;
    }

    if($mailTo != "") {
        sendEmailMessage($mailTo, $mailSubject, $mailBody, $filename = false, $mail_from, $ctype = 'text/html');
    }

    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into ebasket', 'update' => '');
}

function basket_update_item_count($id, $linked_ds, $linked_id, $count) {
    global $mysql, $userROW, $twig;

    $mysql->query("update ".prefix."_eshop_ebasket set count = ".db_squote($count)." where (linked_id = ".db_squote($linked_id).") and (linked_ds = ".db_squote($linked_ds).") and (id = ".db_squote($id).")");

    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item count updated');
}

function basket_delete_item($id, $linked_ds, $linked_id) {
    global $mysql, $userROW, $twig;

    $mysql->query("delete from ".prefix."_eshop_ebasket where id = ".db_squote($id)." and linked_id = ".db_squote($linked_id)." and linked_ds = ".db_squote($linked_ds)." limit 1");

    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item deleted');
}

function ebasket_rpc_manage($params){
    global $userROW, $DSlist, $mysql, $twig;

    LoadPluginLibrary('xfields', 'common');

    if (!is_array($params) || !isset($params['action']))
        return array('status' => 0, 'errorCode' => 1, 'errorText' => 'Activity mode is not set');

    $params = arrayCharsetConvert(1, $params);

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO ebasket ****
        case 'add':
            $linked_ds = intval($params['ds']);
            $linked_id = intval($params['id']);
            $count     = intval($params['count']);

            // Check available DataSources
            if (!(in_array($linked_ds, array($DSlist['news'])))) {
                return array('status' => 0, 'errorCode' => 2, 'errorText' => 'ebasket can be used only for NEWS');
            }

            // Check available DataSources
            if ($count < 1) {
                return array('status' => 0, 'errorCode' => 2, 'errorText' => 'Count should be positive');
            }

            // Check if linked item is available
            switch ($linked_ds) {
                case $DSlist['news']:
                
                    $conditions = array();
                    if ($linked_id) {
                        array_push($conditions, "p.id = ".db_squote($linked_id));
                    }
                
                    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
                    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
                    $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.url as curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
                
                    // Retrieve news record
                    $rec = $mysql->record($sqlQ);
                    if (!is_array($rec)) {
                        return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
                    }

                    $btitle = $rec['name'];
                    $price = $rec['price'];
                    
                    $view_link = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $rec['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $rec['url']));
            
                    $rec['view_link'] = $view_link;

                    // Add data into basked
                    return ebasket_add_item($linked_ds, $linked_id, $btitle, $price, $count, array('item' => $rec));

                    break;
                    
            }
            break;
        case 'update_count':
            $id = intval($params['id']);
            $linked_ds = intval($params['linked_ds']);
            $linked_id = intval($params['linked_id']);
            $count     = intval($params['count']);
            return basket_update_item_count($id, $linked_ds, $linked_id, $count);
            break;
        case 'delete':
            $id = intval($params['id']);
            $linked_ds = intval($params['linked_ds']);
            $linked_id = intval($params['linked_id']);
            return basket_delete_item($id, $linked_ds, $linked_id);
            break;
        case 'add_fast':
            $linked_ds = intval($params['ds']);
            $linked_id = intval($params['id']);
            $count     = intval($params['count']);
            $type     = intval($params['type']);
            
            $order['name'] = filter_var( $params['name'], FILTER_SANITIZE_STRING );
            if(empty($order['name']))
            {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }

            $order['email'] = "";

            $order['phone'] = filter_var( $params['phone'], FILTER_SANITIZE_STRING );
            if(empty($order['phone']))
            {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }
            
            $order['address'] = filter_var( $params['address'], FILTER_SANITIZE_STRING );
            if(empty($order['address']))
            {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }
            
            // Check available DataSources
            if (!(in_array($linked_ds, array($DSlist['news'])))) {
                return array('status' => 0, 'errorCode' => 2, 'errorText' => 'ebasket can be used only for NEWS');
            }

            // Check available DataSources
            if ($count < 1) {
                $count = 1;
            }
            
            $conditions = array();
            if ($linked_id) {
                array_push($conditions, "p.id = ".db_squote($linked_id));
            }

            $fSort = " GROUP BY p.id ORDER BY p.id DESC";
            $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
            $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.url as curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;

            // Retrieve news record
            $rec = $mysql->record($sqlQ);
            if (!is_array($rec)) {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }

            $btitle = $rec['name'];
            $price = $rec['price'];

            $view_link = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));

            $rec['view_link'] = $view_link;

            // Add data into basked
            return ebasket_add_fast_order($linked_ds, $linked_id, $btitle, $price, $count, $type, $order, array('item' => $rec));

            break;
    }
    return array('status' => 1, 'errorCode' => 0, 'data'     => 'OK, '.var_export($params, true));

}

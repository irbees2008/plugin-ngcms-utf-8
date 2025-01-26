<?php
if (!defined('NGCMS')) die ('HAL');

function generate_catz_cache($load = false)
{global $mysql, $config, $twig, $cat_tt;

    $eshop_dir = get_plugcfg_dir('eshop');
    
    if(!file_exists($eshop_dir.'/cache_catz.php') or $load){
        
        $levels = array();
        $tree = array();
        $cur = array();
        
        $cat_cnt_arr = array();
        foreach ($mysql->select("SELECT category_id, COUNT(*) as cnt FROM ".prefix."_eshop_products_categories pc LEFT JOIN ".prefix."_eshop_products p ON p.id = pc.product_id WHERE p.active = 1 GROUP BY category_id") as $crow)
        {
            $cat_cnt_arr[$crow['category_id']] = $crow;
        }

        $catz_arr = array();
        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id") as $rows)
        {
            $catz_arr[] = $rows;
        }
        
        foreach ($catz_arr as $rows)
        {
            
            $cur = &$levels[$rows['id']];
            $cur['id'] = $rows['id'];
            $cur['url'] = $rows['url'];
            $cur['image'] = $rows['image'];
            
            $cur['parent_id'] = $rows['parent_id'];
            $cur['position'] = $rows['position'];
            
            $cur['name'] = $rows['name'];
            $cur['description'] = $rows['description'];
            
            $cur['meta_title'] = $rows['meta_title'];
            $cur['meta_keywords'] = $rows['meta_keywords'];
            $cur['meta_description'] = $rows['meta_description'];
            
            $cur['active'] = $rows['active'];
            
            $cat_link = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $rows['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $rows['url']));
            
            $cur['cat_link'] = $cat_link;
                          
            if($rows['parent_id'] == 0){
                $tree[$rows['id']] = &$cur;
            }
            else{
                $levels[$rows['parent_id']]['children'][$rows['id']] = &$cur;
            }
        }

        $cnt_arr = array();
        foreach ($catz_arr as $rows)
        {
            $cat_tt = array();
            reCategory($tree, $rows['url']);
            
            $sum_cnt = 0;
            foreach ($cat_tt as $ctt)
            {
                if(isset($cat_cnt_arr[$ctt])) {
                    $sum_cnt += $cat_cnt_arr[$ctt]['cnt'];
                }
                else {
                    $sum_cnt += 0;
                }
            }

            $cnt_arr[$rows['id']] = $sum_cnt;
        }

        $tVars = array(
            'tree' => $tree,
            'cnt' => $cnt_arr,
        );
        
        file_put_contents($eshop_dir.'/cache_catz.php', serialize($tVars));
    }

}

function generate_currency_cache($load = false)
{global $mysql, $config;

    $eshop_dir = get_plugcfg_dir('eshop');
    
    if(!file_exists($eshop_dir.'/cache_currency.php') or $load){
        
        $currency_link = checkLinkAvailable('eshop', 'currency')?
            generateLink('eshop', 'currency', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'currency'), array());
            
        $currency_tEntry = array();
        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_currencies WHERE enabled = 1 ORDER BY position, id") as $row)
        {
            $row['currency_link'] = $currency_link."?id=".$row['id'];
            $currency_tEntry[] = $row;
        }
        
        file_put_contents($eshop_dir.'/cache_currency.php', serialize($currency_tEntry));
    }
    
}

function reCategory($arr, $flg){
    global $cat_tt;
    foreach($arr as $k=>$v){
 
        if($v['url']==$flg) { 
            $cat_tt[] = $k;
            if(!empty($v['children'])){     
                $cat_tt = array_merge($cat_tt, array_keys($v['children']));
            }
        }
    
        if(!empty($v['children'])){     
            reCategory($v['children'], $flg);
        }
    }
}


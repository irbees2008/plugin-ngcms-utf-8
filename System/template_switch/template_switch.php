<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');
add_act('core', 'plugin_template_switch');
add_act('index', 'plugin_template_switch_menu');
register_plugin_page('template_switch', '', 'template_switch_redirector', 0);
register_htmlvar('index', 'plugin_template_switch_menu');
if (isset($_GET['get_description'])) {
	$template_id = intval($_GET['get_description']);
	$description_link = pluginGetVariable('template_switch', 'profile' . $template_id . '_description_link');
	if ($description_link) {
		if (($_GET['format'] ?? '') === 'json') {
			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-store');
			$response = json_encode(['url' => $description_link], JSON_UNESCAPED_SLASHES);
			if ($response === false) {
				error_log('template_switch: failed to encode description link for profile ' . $template_id);
				http_response_code(500);
				exit;
			}
			echo $response;
		} else {
			header("Location: " . $description_link);
		}
		exit;
	}
	header("HTTP/1.0 404 Not Found");
	exit;
}
function plugin_template_switch()
{
	global $config;
	$sw_count = intval(pluginGetVariable('template_switch', 'count'));
	if (!$sw_count) {
		$sw_count = 3;
	}
	$sw_template = intval($_COOKIE['sw_template'] ?? 0);
	$is_preview = isset($_GET['template_switch_frame']);

	if ($is_preview) {
		$preview_template = intval($_GET['template'] ?? 0);
		if (
			$preview_template > 0
			&& $preview_template <= $sw_count
			&& pluginGetVariable('template_switch', 'profile' . $preview_template . '_active')
		) {
			$sw_template = $preview_template;
		}
	} elseif (isset($_GET['profile']) && pluginGetVariable('template_switch', 'selfpage')) {
		$template_id = (string)$_GET['profile'];
		for ($i = 1; $i <= $sw_count; $i++) {
			$profile_id = pluginGetVariable('template_switch', 'profile' . $i . '_id');
			if (empty($profile_id)) {
				$template_name = pluginGetVariable('template_switch', 'profile' . $i . '_template');
				$profile_id = strtolower(preg_replace('/[^a-z0-9]/', '', $template_name));
			}
			if ((string)$profile_id === $template_id && pluginGetVariable('template_switch', 'profile' . $i . '_active')) {
				$sw_template = $i;
				setcookie('sw_template', (string)$i, 0, '/');
				break;
			}
		}
	}
	// If template is not selected, we can show default value
	if (!$sw_template) {
		// Check if we have default profile for this domain
		for ($i = 0; $i <= $sw_count; $i++) {
			$dlist = pluginGetVariable('template_switch', 'profile' . $i . '_domains');
			if (!$dlist) continue;
			if (!is_array($darr = explode("\n", $dlist))) continue;
			$is_catched = 0;
			foreach ($darr as $dname) {
				$dname = trim($dname);
				if (!$dname) continue;
				// Check if domain fits our domain
				if (($_SERVER['SERVER_NAME'] == $dname) || ($_SERVER['HTTP_HOST'] == $dname)) {
					$is_catched = 1;
				}
			}
			if ($is_catched) {
				$sw_template = $i;
				break;
			}
		}
	}
	if (($sw_template > 0) && ($sw_template <= $sw_count) && pluginGetVariable('template_switch', 'profile' . $sw_template . '_active')) {
		if (pluginGetVariable('template_switch', 'profile' . $sw_template . '_template')) {
			$config['theme'] = pluginGetVariable('template_switch', 'profile' . $sw_template . '_template');
		}
		if (pluginGetVariable('template_switch', 'profile' . $sw_template . '_lang')) {
			$config['default_lang'] = pluginGetVariable('template_switch', 'profile' . $sw_template . '_lang');
		}
		// If theme was actually switched — update main template path and Twig loader so that
		// index.php picks up the correct main.tpl and all {% include %} resolve to the right theme.
		// Without this, main.tpl stays from the original (tpl_site) theme while tpl_url points
		// to the switched theme → CSS/JS 403/404 errors.
		if (defined('tpl_site') && (site_root . 'templates/' . $config['theme'] . '/') !== tpl_site) {
			global $twigLoader, $SYSTEM_FLAGS, $TemplateCache;
			$newTplSite = site_root . 'templates/' . $config['theme'] . '/';
			// Tell index.php to load main.tpl from the switched theme
			$SYSTEM_FLAGS['template.main.path'] = $newTplSite;
			// Update Twig loader: switched theme first, original theme as fallback for missing templates,
			// engine/plugins (root) always last
			if (is_dir($newTplSite)) {
				$twigLoader->setPaths([$newTplSite, tpl_site, root]);
			}
			// Reset variables.ini cache so templateLoadVariables() re-reads it from the switched theme.
			// Without this, pagination/messages/etc. keep using the original theme's variables.ini.
			unset($TemplateCache['site']['#variables']);
		}
	}
}
function plugin_template_switch_menu()
{
	global $template, $tpl, $lang;
	if (isset($_GET['template_switch_frame'])) {
		return;
	}
	if (isset($GLOBALS['template_switch_rendered']) || isset($_GET['template_switch_frame'])) {
		return;
	}
	static $executed = false;
	if ($executed) return;
	$executed = true;
	$GLOBALS['template_switch_rendered'] = true;
	$list = '';
	$sw_count = intval(pluginGetVariable('template_switch', 'count'));
	if (!$sw_count) {
		$sw_count = 3;
	}
	// The template query parameter selects the preview without changing the visitor's cookie.
	$current_template = intval($_COOKIE['sw_template'] ?? 0);
	$requested_template = intval($_GET['template'] ?? 0);
	if (
		$requested_template > 0
		&& $requested_template <= $sw_count
		&& pluginGetVariable('template_switch', 'profile' . $requested_template . '_active')
	) {
		$current_template = $requested_template;
	}
	if (
		$current_template < 1
		|| $current_template > $sw_count
		|| !pluginGetVariable('template_switch', 'profile' . $current_template . '_active')
	) {
		$current_template = 0;
		for ($i = 1; $i <= $sw_count; $i++) {
			if (pluginGetVariable('template_switch', 'profile' . $i . '_active')) {
				$current_template = $i;
				break;
			}
		}
	}
	for ($i = 1; $i <= $sw_count; $i++) {
		if (pluginGetVariable('template_switch', 'profile' . $i . '_active')) {
			$profileName = pluginGetVariable('template_switch', 'profile' . $i . '_name');
			if (empty($profileName)) {
				$profileName = pluginGetVariable('template_switch', 'profile' . $i . '_template');
			}
			$selected = ($i === $current_template) ? ' selected' : '';
			$list .= "<option value='$i'$selected>" . htmlspecialchars($profileName) . "</option>\n";
		}
	}
	LoadPluginLang('template_switch', 'main', '', 'template_switch');
	$tpath = locatePluginTemplates(array('template_switch'), 'template_switch', pluginGetVariable('template_switch', 'localsource'));
	$tpl->template('template_switch', $tpath['template_switch']);
	$tvars['vars']['current_template'] = $current_template;
	$tvars['vars']['is_frame'] = isset($_GET['template_switch_frame']);
	$tvars['vars']['list'] = $list;
	$tpl->vars('template_switch', $tvars);
	$template['vars']['template_switch'] = $tpl->show('template_switch');
	register_htmlvar('plain', '{template_switch}');
}
function template_switch_redirector()
{
	$templateID = $_REQUEST['profile'];
	// Scan for template with this ID
	$sw_count = intval(pluginGetVariable('template_switch', 'count'));
	if (!$sw_count) {
		$sw_count = 3;
	}
	$templateNum = 0;
	for ($i = 1; $i <= $sw_count; $i++) {
		if (pluginGetVariable('template_switch', 'profile' . $i . '_id') == $templateID) {
			$templateNum = $i;
			break;
		}
	}
	// Set cookie with template ID
	@setcookie('sw_template', $i, 0, '/'); // 0 - значит до закрытия браузера
	// Redirect user:
	// if `redirect` is set - to specified URL
	// if `redirect` is not set - to root directory of the site
	@header("Location: " . (pluginGetVariable('template_switch', 'profile' . $i . '_redirect') ? pluginGetVariable('template_switch', 'profile' . $i . '_redirect') : home));
}

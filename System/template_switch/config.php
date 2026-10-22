<?php

// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

pluginsLoadConfig();
LoadPluginLang('template_switch', 'config', '', '', ':');

$cfg = array();
array_push($cfg, array('descr' => $lang['template_switch:description']));

$tpl_list = array();
$tpl_list = array_merge($tpl_list, ListFiles('../templates', ''));

$cfgX = array();
$profile_count = intval(pluginGetVariable('template_switch', 'count'));
if (!$profile_count) {
	$profile_count = 3;
}

array_push($cfgX, array(
	'name' => 'count',
	'title' => $lang['template_switch:count'],
	'descr' => $lang['template_switch:count_desc'],
	'type' => 'input',
	'html_flags' => ' size="5"',
	'value' => $profile_count
));

array_push($cfgX, array(
	'name' => 'selfpage',
	'title' => $lang['template_switch:selfpage'],
	'descr' => $lang['template_switch:selfpage_desc'],
	'type' => 'select',
	'values' => array('1' => $lang['yesa'], '0' => $lang['noa']),
	'value' => pluginGetVariable('template_switch', 'selfpage')
));

array_push($cfg, array(
	'mode' => 'group',
	'title' => '<b>' . $lang['template_switch:commonconfig'] . '</b>',
	'entries' => $cfgX
));

for ($i = 1; $i <= $profile_count; $i++) {
	$cfgX = array();

	// Основные поля профиля
	array_push($cfgX, array(
		'name' => 'profile' . $i . '_active',
		'title' => $lang['template_switch:flagactive'],
		'type' => 'select',
		'values' => array('1' => $lang['yesa'], '0' => $lang['noa']),
		'value' => pluginGetVariable('template_switch', 'profile' . $i . '_active')
	));

	array_push($cfgX, array(
		'name' => 'profile' . $i . '_template',
		'title' => $lang['template_switch:template'],
		'descr' => $lang['template_switch:template_desc'],
		'type' => 'select',
		'values' => $tpl_list,
		'value' => pluginGetVariable('template_switch', 'profile' . $i . '_template'),
		'onchange' => "updateProfileFields($i, this)"
	));

	array_push($cfgX, array(
		'name' => 'profile' . $i . '_name',
		'title' => '',
		'type' => 'hidden',
		'value' => pluginGetVariable('template_switch', 'profile' . $i . '_name'),
		'id' => 'profile' . $i . '_name'
	));

	array_push($cfgX, array(
		'name' => 'profile' . $i . '_id',
		'title' => '',
		'type' => 'hidden',
		'value' => pluginGetVariable('template_switch', 'profile' . $i . '_id'),
		'id' => 'profile' . $i . '_id'
	));

	// Новое поле: ссылка на описание шаблона
	array_push($cfgX, array(
		'name' => 'profile' . $i . '_description_link',
		'title' => $lang['template_switch:description_link'],
		'descr' => $lang['template_switch:description_link_desc'],
		'type' => 'input',
		'html_flags' => ' size="50"',
		'value' => pluginGetVariable('template_switch', 'profile' . $i . '_description_link')
	));

	// Поле для предпросмотра
	$profile_id = pluginGetVariable('template_switch', 'profile' . $i . '_id');
	if (empty($profile_id)) {
		$template_name = pluginGetVariable('template_switch', 'profile' . $i . '_template');
		$profile_id = $template_name;
	}
	$preview_url = home . '/?template=' . $i . '&profile=' . rawurlencode((string)$profile_id);

	array_push($cfgX, array(
		'name' => 'profile' . $i . '_preview_link',
		'title' => $lang['template_switch:preview_link'],
		'descr' => $lang['template_switch:preview_link_desc'],
		'type' => 'input',
		'html_flags' => ' size="50" readonly id="preview_link_' . $i . '" class="form-control preview-link-input"',
		'value' => $preview_url
	));

	array_push($cfg, array(
		'mode' => 'group',
		'title' => '<b>' . $lang['template_switch:profile'] . ' #' . $i . '</b>',
		'toggle' => true,
		'entries' => $cfgX
	));
}
// Добавляем JavaScript для обновления полей
$copyLinkTitle = json_encode($lang['template_switch:copy_link'], JSON_UNESCAPED_UNICODE);
$openLinkTitle = json_encode($lang['template_switch:open_link'], JSON_UNESCAPED_UNICODE);
$linkCopiedMessage = json_encode($lang['template_switch:link_copied'], JSON_UNESCAPED_UNICODE);
echo <<<HTML
<script>
function updateProfileFields(profileNum, selectElement) {
    var profileName = selectElement.options[selectElement.selectedIndex].text;
    var profileId = profileName.toLowerCase();

    document.getElementById('profile' + profileNum + '_name').value = profileName;
    document.getElementById('profile' + profileNum + '_id').value = profileId;

    // Обновляем ссылку предпросмотра
    var previewField = document.querySelector('[name="profile' + profileNum + '_preview_link"]');
    if (previewField) {
        previewField.value = window.location.origin + '/?template=' + profileNum + '&profile=' + encodeURIComponent(profileId);
    }
}
document.addEventListener("DOMContentLoaded", function() {
        var hiddenFields = document.querySelectorAll(
            'input[type="hidden"][name^="profile"][name$="_name"], input[type="hidden"][name^="profile"][name$="_id"]'
        );
        hiddenFields.forEach(function(hiddenField) {
            var row = hiddenField;
            while (row && row.nodeType === 1 && row.tagName !== "TR") {
                row = row.parentNode;
            }
            if (row && row.nodeType === 1) {
                row.style.display = "none";
            }
        });

        // Находим все поля с ссылками предпросмотра
        var inputs = document.querySelectorAll(".preview-link-input");

        inputs.forEach(function(input) {
            var inputGroup = document.createElement("div");
            inputGroup.className = "input-group mb-2";

            var copyContainer = document.createElement("div");
            copyContainer.className = "input-group-prepend";

            var openContainer = document.createElement("div");
            openContainer.className = "input-group-append";

            // Кнопка "Копировать"
            var copyBtn = document.createElement("button");
            copyBtn.type = "button";
            copyBtn.className = "btn btn-success";
            copyBtn.innerHTML = "<i class=\"fa fa-copy\"></i>";
            copyBtn.setAttribute("data-bs-toggle", "tooltip");
            copyBtn.setAttribute("data-original-title", {$copyLinkTitle});
            copyBtn.onclick = function() {
                input.select();
                document.execCommand("copy");
                alert({$linkCopiedMessage});
            };

            // Кнопка "Перейти"
            var openBtn = document.createElement("button");
            openBtn.type = "button";
            openBtn.className = "btn btn-info";
            openBtn.innerHTML = "<i class=\"fa fa-external-link\"></i>";
            openBtn.setAttribute("data-bs-toggle", "tooltip");
            openBtn.setAttribute("data-original-title", {$openLinkTitle});
            openBtn.onclick = function() {
                window.open(input.value, "_blank", "noopener,noreferrer");
            };

            var parent = input.parentNode;
            parent.insertBefore(inputGroup, input);
            copyContainer.appendChild(copyBtn);
            openContainer.appendChild(openBtn);
            inputGroup.appendChild(copyContainer);
            inputGroup.appendChild(input);
            inputGroup.appendChild(openContainer);
        });
    });
</script>
HTML;

// RUN
if ($_REQUEST['action'] == 'commit') {
	// Генерируем ID для профилей, где он не указан
	for ($i = 1; $i <= $profile_count; $i++) {
		$id = $_REQUEST['profile' . $i . '_id'];
		if (empty($id)) {
			$template_name = $_REQUEST['profile' . $i . '_template'];
			$_REQUEST['profile' . $i . '_id'] =  $template_name;
		}
	}

	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
}
if ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

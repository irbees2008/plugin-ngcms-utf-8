<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load LIBRARY
loadPluginLibrary('comments', 'lib');

// Filter class
class clFilterComments extends FilterComments {
	function addComments($userRec, $newsRec, &$tvars, &$SQL) {
		global $lang;

		// Manage filtering
		$flagUpdated = false;
		$c = $SQL['text'];

		foreach (explode("\n", extra_get_param('filter', 'replace')) as $line) {
			$line = rtrim($line, "\r");
			$parts = explode('|', $line, 2);
			if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
				$c = str_ireplace($parts[0], $parts[1], $c);
				$flagUpdated = true;
			}
		}

		// Manage blocking
		foreach (explode("\n", extra_get_param('filter', 'block')) as $line) {
			$line = trim($line);
			if ($line !== '' && stripos($c, $line) !== false) {
				loadPluginLang('filter', 'filter', '', '', ':');
				msg([
					'type' => 'error',
					'text' => str_replace('%lock%', $line, $lang['filter:block']),
				]);
				return 0;
			}
		}

		if ($flagUpdated)
			$SQL['text'] = $c;

		return 1;
	}
}

// Activate interceptor
register_filter('comments','filter', new clFilterComments);

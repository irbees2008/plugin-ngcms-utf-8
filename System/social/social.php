<?php
if (!defined('NGCMS')) {
    die('HAL');
}

class SocialNewsFilter extends NewsFilter
{
    public function showNews($newsID, $SQLnews, &$tvars, $mode = [])
    {
        global $config, $twig;

        $services = pluginGetVariable('social', 'services');
        if (!is_array($services) || !$services) {
            $tvars['vars']['plugin_social'] = '';
            return 1;
        }

        $localsource = (int) pluginGetVariable('social', 'localsource');
        $skin = (string) pluginGetVariable('social', 'skin');
        $skin = preg_match('/^[a-zA-Z0-9_-]+$/', $skin) ? $skin : 'default';
        $theme = (string) ($config['theme'] ?? 'default');
        $link = newsGenerateLink($SQLnews, false, 0, true);
        $title = (string) ($SQLnews['title'] ?? '');
        $newsContent = (string) ($SQLnews['short_story'] ?? '');
        if ($newsContent === '') {
            $newsContent = (string) ($SQLnews['full_story'] ?? ($SQLnews['content'] ?? ''));
        }
        $content = trim(strip_tags($newsContent));
        $content = function_exists('mb_substr') ? mb_substr($content, 0, 200, 'UTF-8') : substr($content, 0, 200);

        $cacheEnabled = (int) pluginGetVariable('social', 'cache') === 1;
        $cacheExpire = max(1, (int) pluginGetVariable('social', 'cacheExpire'));
        $cacheKey = hash('sha256', serialize([
            'news_id' => (int) $newsID,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'services' => $services,
            'localsource' => $localsource,
            'skin' => $skin,
            'theme' => $theme,
            'language' => $config['default_lang'] ?? '',
        ])) . '.cache';

        if ($cacheEnabled) {
            $cacheData = cacheRetrieveFile($cacheKey, $cacheExpire, 'social');
            if (is_string($cacheData)) {
                $tvars['vars']['plugin_social'] = $cacheData;
                return 1;
            }
        }

        $templatePath = locatePluginTemplates(['social'], 'social', $localsource, $skin);
        if (empty($templatePath['social'])) {
            throw new RuntimeException('The social display template could not be located.');
        }

        $themeIconDirectory = '';
        $themeIconUrl = '';
        if (!$localsource) {
            $themeIconDirectory = site_root . 'templates/' . $theme . '/plugins/social/images';
            if (is_dir($themeIconDirectory)) {
                $themeIconUrl = rtrim(home, '/') . '/templates/' . rawurlencode($theme) . '/plugins/social/images';
            }
        }
        $pluginIconDirectory = __DIR__ . '/tpl/skins/' . $skin . '/images';
        if (!is_dir($pluginIconDirectory)) {
            $pluginIconDirectory = __DIR__ . '/tpl/skins/default/images';
            $skin = 'default';
        }
        $pluginIconUrl = rtrim(home, '/') . '/engine/plugins/social/tpl/skins/' . rawurlencode($skin) . '/images';

        $entries = [];
        foreach ($services as $service) {
            if (!is_array($service) || empty($service['active'])) {
                continue;
            }
            $serviceTitle = (string) ($service['title'] ?? '');
            $serviceUrl = html_entity_decode((string) ($service['link'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $serviceUrl = strtr($serviceUrl, [
                '%title%' => rawurlencode($title),
                '%content%' => rawurlencode($content),
                '%link%' => rawurlencode($link),
            ]);
            $imageName = (string) ($service['img'] ?? '');
            $safeImageName = $imageName !== '' && basename($imageName) === $imageName;
            $hasThemeImage = $safeImageName
                && $themeIconDirectory !== ''
                && is_file($themeIconDirectory . DIRECTORY_SEPARATOR . $imageName);
            $hasPluginImage = $safeImageName
                && is_file($pluginIconDirectory . DIRECTORY_SEPARATOR . $imageName);
            $hasImage = $hasThemeImage || $hasPluginImage;
            $imageUrl = $hasThemeImage ? $themeIconUrl : $pluginIconUrl;

            $entries[] = [
                'url' => htmlspecialchars($serviceUrl, ENT_QUOTES, 'UTF-8'),
                'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                'desc' => htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'),
                'img' => $hasImage
                    ? htmlspecialchars($imageUrl . '/' . rawurlencode($imageName), ENT_QUOTES, 'UTF-8')
                    : '',
                'has_img' => $hasImage,
            ];
        }

        $tVars = [
            'home' => $config['home_url'] ?? '',
            'entries' => $entries,
        ];
        $tvars['vars']['plugin_social'] = $twig->render($templatePath['social'] . 'social.tpl', $tVars);

        if ($cacheEnabled) {
            cacheStoreFile($cacheKey, $tvars['vars']['plugin_social'], 'social');
        }

        return 1;
    }
}

register_filter('news', 'social', new SocialNewsFilter());

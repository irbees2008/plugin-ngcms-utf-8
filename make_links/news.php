<?php

// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

class MakeLinkNewsFilter extends NewsFilter
{
    private $newsCount = 0;
    private $ifFull = 0;
    private $ifShort = 0;
    private $ifView = 0;
    private $limitLink = 0;
    private $words = [];

    public function onBeforeShow($mode)
    {
        global $CurrentHandler, $config;
        $handlerName = $CurrentHandler['handlerName'] ?? '';

        if ($handlerName === 'main') {
            $this->ifView = (int) pluginGetVariable('make_links', 'if_main');
        } elseif ($handlerName === 'by.category') {
            $this->ifView = (int) pluginGetVariable('make_links', 'if_category');
        } elseif ($handlerName === 'news') {
            $this->ifView = (int) pluginGetVariable('make_links', 'if_full_news');
        }

        if (!$this->ifView) {
            return 1;
        }

        if ($mode === 'full') {
            $this->ifFull = 1;
            $this->ifShort = (int) pluginGetVariable('make_links', 'mode_full');
            $this->newsCount = 1;
        } elseif ($mode === 'short') {
            $this->ifFull = (int) pluginGetVariable('make_links', 'mode_short');
            $this->ifShort = 1;
            $configuredNewsCount = (int) ($config['number'] ?? 0);
            $this->newsCount = $configuredNewsCount >= 1 ? $configuredNewsCount : 5;
        } else {
            $this->ifView = 0;
            return 1;
        }

        $this->limitLink = max(0, (int) pluginGetVariable('make_links', 'max_link_count'));
        $configuredWords = pluginGetVariable('make_links', 'words');
        $this->words = is_string($configuredWords)
            ? array_values(array_filter(array_map('trim', explode(',', $configuredWords)), static function ($word) {
                return $word !== '';
            }))
            : [];

        return 1;
    }

    public function showNews($newsID, $SQLnews, &$tvars, $mode = [])
    {
        if (!$this->limitLink || !$this->ifView || !$this->words) {
            return 1;
        }

        if ((int) pluginGetVariable('make_links', 'if_rand_word') === 1) {
            shuffle($this->words);
        }

        $newsOrder = is_array($mode) ? (int) ($mode['nCount'] ?? 0) : 0;
        $delta = $newsOrder > 0 ? $newsOrder - 1 : 0;
        $remainingNews = $this->newsCount - $delta;
        if ($remainingNews < 1) {
            return 1;
        }

        $linksForNews = (int) ceil($this->limitLink / $remainingNews);
        while ($linksForNews > 0) {
            $changed = false;
            foreach ($this->words as $word) {
                if ($linksForNews < 1) {
                    break;
                }

                if (
                    $this->ifShort
                    && isset($tvars['vars']['short-story'])
                    && $this->replace($tvars['vars']['short-story'], $word)
                ) {
                    $linksForNews--;
                    $this->limitLink--;
                    $changed = true;
                } elseif (
                    $this->ifFull
                    && isset($tvars['vars']['full-story'])
                    && $this->replace($tvars['vars']['full-story'], $word)
                ) {
                    $linksForNews--;
                    $this->limitLink--;
                    $changed = true;
                }
            }

            if (!$changed) {
                break;
            }
        }

        return 1;
    }

    private function replace(&$text, $word, $start = 0)
    {
        if (!is_string($text) || $word === '') {
            return false;
        }

        $pos = strpos($text, ' ' . $word, $start);
        if ($pos === false) {
            return false;
        }

        $before = substr($text, 0, $pos);
        $imageStart = strrpos($before, '<img ');
        if ($imageStart !== false) {
            $imageEnd = strpos($text, '>', $imageStart);
            if ($imageEnd !== false && $imageEnd > $pos) {
                return $this->replace($text, $word, $pos + 1);
            }
        }

        $anchorStart = strrpos($before, '<a ');
        if ($anchorStart !== false) {
            $anchorEnd = strpos($text, '</a>', $anchorStart);
            if ($anchorEnd !== false && $anchorEnd > $pos) {
                return $this->replace($text, $word, $pos + 1);
            }
        }

        $url = htmlspecialchars((string) home, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $label = htmlspecialchars($word, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $text = substr_replace($text, ' <a href="' . $url . '">' . $label . '</a>', $pos, strlen(' ' . $word));

        return true;
    }
}
register_filter('news', 'make_links', new MakeLinkNewsFilter);

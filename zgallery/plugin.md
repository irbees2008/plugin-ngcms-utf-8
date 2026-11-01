---
id: "zgallery"
name: "zgallery"
version: "0.01"
acts: "index, news_short, news_full"
file: "zgallery.php"
type: "plugin"
description: "Управление галереями"
author: "Rostunov Sergey"
author_uri: "http://ngcms.ru/"
title: "Управление галереями"
information: "Добавляет возможность выводить галереи в шаблонах"
---

# =========================================================================== #
# NG CMS // Плагины // Управление галереями                                   #
# =========================================================================== #

[TWIG]{{ callPlugin('zgallery.show', { 'number' : 10, 'mode' : 'date desc', 'cat': 'default', 'template' : 'block1'}) }}[/TWIG]

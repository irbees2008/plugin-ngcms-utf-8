---
id: "turbo_yandex"
name: "RSS поток для Yandex Turbo"
name_en: "RSS export for Yandex Turbo"
version: "0.6.0"
acts: "ppages"
file: "turbo_yandex.php"
config: "config.php"
install: "install.php"
deinstall: "deinstall.php"
library:
  - "lib; lib/TurboYandex.php"
  - "helpers; lib/helpers.php"
type: "plugin"
description: "RSS поток для Yandex Turbo"
description_en: "RSS export for Yandex Turbo"
author: "irbees2008"
author_uri: "http://ngcmshak.ru/"
preinstall_vars: "feed_title_format=\"site_title\"; news_title=\"1\"; news_count=\"30\"; use_hide=\"1\""
title: "RSS поток для Yandex Turbo"
preinstall: "default_no"
information: "Генерирует специальный RSS поток для Yandex Turbo"
minenginebuild: "3740369"
icons: "<i class=\"fa fa-rss fa-3x\" aria-hidden=\"true\"></i>"
---

# =========================================================================== #
# NG CMS // Плагины // RSS экспорт страниц для Турбо Яндекса                  #
# =========================================================================== #

Плагин экспорта страниц в формате RSS, обладает расширенными настройками.
При передаче в параметрах ID категори(и/ий) позволяет генерировать
RSS ленту для заданных категорий.

По умолчанию - создаёт основную RSS ленту сайта по всем страницам, отображаемым
на главной странице.

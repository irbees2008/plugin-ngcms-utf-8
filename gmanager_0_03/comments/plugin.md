---
id: "comments"
name: "User comments"
version: "0.05b modif"
acts: "news_short, news_full, ppages, editnews, editnews_form, news, admin:mod:news, admin:mod:categories"
file: "comments.php"
config: "config.php"
install: "install.php"
deinstall: "uninstall.php"
type: "plugin"
description: "Комментарии пользователей"
author: "Vitaly A. Ponomarev"
author_uri: "http://ngcms.ru/"
title: "Комментарии пользователей"
information: "Реализует функционал работы с комментариями"
preinstall: "default_yes"
preinstall_vars: "cache=\"1\"; cacheExpire=\"3600\";"
library: "lib; inc/comments.lib.php"
---

# =========================================================================== #
# NG CMS // Плагины // Комментарии                                            #
# =========================================================================== #

 Плагин comments обеспечивает работу функционала работы с комментариями.
На текущий момент поддерживаются только комментарии к новостям.
В процессе работы над версией 0.9.1 функционал работы над комментариями был
перенесён из ядра в этот плагин.

 В своей работе плагин comments выводит свои информационные блоки в:
 * шаблоне news.short.tpl
 * шаблоне news.full.tpl
 * собственной странице плагина (используется для постраничного отображения комментариев)

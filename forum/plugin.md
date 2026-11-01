---
id: "forum"
name: "FORUM_NG_CMS"
version: "0.1"
actions:
  - "ppages; forum.php"
  - "core; forum_core.php"
  - "admin:mod:news; forum_news.php"
  - "news_short, news_full; show_news_forum.php"
  - "comments; forum_news_post.php"
  - "index, plugin.forum; forum_block.php"
  - "forum:core; forum_ban.php"
config: "config.php"
install: "install.php"
deinstall: "uninstall.php"
type: "plugin"
title: "Форум"
description: ""
information: ""
author: "Nail' R. Davydov (ROZARD)"
author_uri: "http://ngcms.ru/"
preinstall: "no"
---

# =========================================================================== #
# NG CMS // Плагины // Форум                                    #
# =========================================================================== #

Плагин отображает на сайте информационный блок с RSS канала других сайтов.

Используемые шаблоны:
--------------------------------------------------------------------
В /engine/skins/default/tpl/news/add.tpl
После:
{% if flags['multicat.show'] %}
  <tr>
   <td></td>
   <td><span class="f15">{{ lang['editor.extcat'] }}</span></td>
  </tr>
  <tr>
   <td></td><td>
   <div style="overflow: auto; height: 150px;" class="list">{{ extcat }}</div>
   </td>
  </tr>
  <tr><td colspan=2>&nbsp;</td></tr>
{% endif %}
Добавляем:
<tr>
	<td width="20"></td>
	<td><span class="f15">Форум</span></td>
</tr>
<tr>
	<td></td><td><div class="list">
	<select name="forum_id" id="catmenu">
		<option>Выберите форум</option>
		{{ options_forum }}
	</select></div></td>
</tr>

Находим:
<label><input type="checkbox" name="mainpage" value="1" class="check" id="mainpage" {% if (flags.mainpage) %}checked="checked" {% endif %}{% if flags['mainpage.disabled'] %}disabled {% endif %}  /> {{ lang.addnews['mainpage'] }}</label><br />
Перед ней добавляем:
<label><input type="checkbox" name="create_forum" value="1" class="check" id="mainpage" /> Создать на форуме</label><br />

В шаблонах news.short.tpl, news.full.tpl доступен блок:
  . {topic_forum_url}          - Отображает ссылку на форум (показывается если у этой новости есть тема на форуме)
  .  [topic_show][/topic_show] - Отображает внутри если есть темы на форуме 

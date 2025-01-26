<form action="{{php_self}}?mod=extra-config&plugin=eshop&action=modify_comment" method="post" name="check_comment">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="10%">Дата</td>
<td width="20%">Имя</td>
<td width="30%">Комментарий</td>
<td width="25">Страница</td>
<td width="5%">Статус</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" onclick="javascript:check_uncheck_all(check_comment)" /></td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="10%" class="contentEntry1">{{entry.date|date('d.m.Y H:i')}}</td>
<td width="20%" class="contentEntry1">{% if (entry.reg) %}<a href="{{ entry.profile_link }}" >{{ entry.author }}</a>{% else %}{{ entry.author }}{% endif %}<br/>
        <small>
            {{ entry.mail }}
        </small>&nbsp;</td>
<td width="30%" class="contentEntry1">{{ entry.commenttext }}</td>
<td width="25%" class="contentEntry1"><a href="{{ entry.product_edit_link }}" >{{ entry.title }}</a><br/>
        <small>
            <a href="{{home}}{{ entry.view_link }}" target="_blank">{{home}}{{ entry.view_link }}</a>
        </small>&nbsp;</td>
<td width="5%" class="contentEntry1"><img src="{{home}}/engine/skins/default/images/{% if (entry.status == 1) %}yes.png{% else %}no.png{% endif %}" alt=""></td>
<td width="5%" class="contentEntry1"><input name="selected_comment[]" value="{{ entry.id }}" class="check" type="checkbox" /></td>
</tr>
{% else %}
<tr align="left">
<td colspan="8" class="contentEntry1">По вашему запросу ничего не найдено.</td>
</tr>
{% endfor %}
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>

<tr align="center">
<td colspan="9" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">
Действие: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
<option value="">-- Действие --</option>
<option value="mass_delete">Удалить</option>
<option value="" style="background-color: #E0E0E0;" disabled="disabled">===================</option>
<option value="mass_active_add">Опубликовать</option>
<option value="mass_active_remove">Запретить публикацию</option>
</select>
<input type="submit" value="Выполнить.." class="button" />
<br/>
</div>
</td>
</tr>

<tr>
<td align="center" colspan="10" class="contentHead">{{ pagesss }}</td>
</tr>

</table>
</form>

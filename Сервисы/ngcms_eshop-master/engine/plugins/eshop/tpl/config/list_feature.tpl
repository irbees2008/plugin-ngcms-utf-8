<form action="{{php_self}}?mod=extra-config&plugin=eshop&action=modify_feature" method="post" name="check_feature">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="70%">Название</td>
<td width="10%">Позиция</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" onclick="javascript:check_uncheck_all(check_feature)" /></td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="70%" class="contentEntry1"><a href="{{ entry.edit_link }}" >{{ entry.name }}</a></td>
<td width="10%" class="contentEntry1">{{ entry.position }}</td>
<td width="5%" class="contentEntry1"><input name="selected_feature[]" value="{{ entry.id }}" class="check" type="checkbox" /></td>
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
</select>
<input type="submit" value="Выполнить.." class="button" />


<input class="button" style="float:right; width: 105px;" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=eshop&action=add_feature'" value="Добавить свойство" />

<br/>
</div>
</td>
</tr>

</table>
</form>

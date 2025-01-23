<form action="{{php_self}}?mod=extra-config&plugin=eshop&action=list_cat" method="post" name="catz_bar">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="15%">Изображение</td>
<td width="72%">Название</td>
<td width="5%">Порядок</td>
<td width="5%">Действие</td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="10%" class="contentEntry1">{% if entry.image %}<a href="{{ entry.edit_link }}" ><img src="{{home}}/uploads/eshop/categories/thumb/{{ entry.image }}" width="100px" height="100px"/></a>{% endif %}</td>
<td width="40%" class="contentEntry1">
    <div style="float: left; margin: 0px;">
        {{ entry.prefix }} <a href="{{ entry.edit_link }}" >{{ entry.cat_name }}</a><br/>
        <small>
            <a href="{{ home }}{{ entry.view_link }}" target="_blank">{{ home }}{{ entry.view_link }}</a>
        </small>&nbsp;
    </div>
    
</td>
<td width="15%" class="contentEntry1" style="text-align:center;vertical-align:middle">{{ entry.position }}</td>
<td width="15%" class="contentEntry1" style="text-align:center;vertical-align:middle"><a href="{{ entry.del_link }}"  /><img src="/engine/skins/default/images/delete.gif"></a></td>
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
<td colspan="8" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">

<input class="button" style="float:right; width: 105px;" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=eshop&action=add_cat'" value="Добавить категорию" />
</div>
</td>
</tr>

</table>
</form>

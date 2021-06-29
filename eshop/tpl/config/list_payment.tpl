<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
    <tr class="contHead" align="left">
        <td width="5%">ID</td>
        <td width="80%">Название системы</td>
    </tr>
    {% for entry in entries %}
        <tr align="left">
            <td width="5%" class="contentEntry1">{{ loop.index }}</td>
            <td width="80%" class="contentEntry1"><a href="{{ entry.edit_link }}">{{ entry.name }}</a></td>
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
        </td>
    </tr>
</table>
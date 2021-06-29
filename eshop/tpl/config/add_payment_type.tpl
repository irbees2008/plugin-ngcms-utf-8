{{ entries.error }}
<form method="post" action="" enctype="multipart/form-data">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">Название<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ entries.name }}"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Описание<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <textarea rows="10" cols="45" name="description">{{ entries.description }}</textarea>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Позиция<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="1" name="position"
                                                         value="{{ entries.position }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Активная?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="checkbox" name="active"
                                                         {% if entries.mode == 'add' %}checked{% else %}{% if entries.active == '1' %}checked{% endif %}{% endif %}
                                                         value="1"></td>
        </tr>

    </table>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100%" colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" class="contentEdit" align="center">
                <input type="submit" name="submit" value="Сохранить" class="button"/>
            </td>
        </tr>
    </table>
</form>

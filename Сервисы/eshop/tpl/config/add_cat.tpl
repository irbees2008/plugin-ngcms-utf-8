{{ entries.error }}
<form method="post" action="" enctype="multipart/form-data">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">Имя<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="cat_name"
                                                         value="{{ entries.cat_name }}"/></td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">Описание<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="description"
                                                         value="{{ entries.description }}"/></td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">URL<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="url" value="{{ entries.url }}"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Meta title<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="meta_title"
                                                         value="{{ entries.meta_title }}"/></td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">Meta keywords<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="meta_keywords"
                                                         value="{{ entries.meta_keywords }}"/></td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">Meta description<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="meta_description"
                                                         value="{{ entries.meta_description }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Родительская категория<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <select name="parent">
                    <option value="0">Выберите категорию</option>
                    {{ entries.catz }}
                </select>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Позиция<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="position"
                                                         value="{{ entries.position }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Изображение<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                {% if entries.image %}
                    <div id="previewImage">
                        <img src="{{ home }}/uploads/eshop/categories/thumb/{{ entries.image }}" width="100px"
                             height="100px"/>
                        <br/>
                        <input type="checkbox" name="image_del" value="1"> <label for="image_del">удалить иконку</label>
                    </div>
                    <br/>
                {% else %}
                    <input type="file" size="40" name="image"/>
                {% endif %}
            </td>
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

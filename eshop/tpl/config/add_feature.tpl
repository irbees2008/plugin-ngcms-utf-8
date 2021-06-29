<script src="{{ admin_url }}/plugins/eshop/tpl/config/tinymce/tinymce.min.js" type="text/javascript"></script>
<script>
    tinymce.init({
        selector: 'textarea[name=html_default]',
        height: 100,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
    });

</script>
{{ entries.error }}

<form method="post" action="">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">Название<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ entries.name }}"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Категории<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <select multiple="" name="feature_categories[]">
                    {{ entries.catz }}
                </select>
            </td>
        </tr>
    </table>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">Тип поля<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <select size="5" id="xfSelectType" name="ftype" onclick="clx(this.value);" onchange="clx(this.value);">
                    <option value="text" {% if (entries.ftype == 0) %}selected{% endif %}>Текстовый</option>
                    <option value="checkbox" {% if (entries.ftype == 1) %}selected{% endif %}>Флажок (checkbox)</option>
                    <option value="select" {% if (entries.ftype == 2) %}selected{% endif %}>Выбор значения</option>
                    <option value="html" {% if (entries.ftype == 3) %}selected{% endif %}>HTML</option>
                </select>
            </td>
        </tr>
    </table>

    <!-- FIELD TYPE: TEXT -->
    <div id="type_text">
        <table border="0" cellspacing="1" cellpadding="1" class="content">
            <tr class="contRow1">
                <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">текст</td>
                <td width="45%">Значение по умолчанию:</td>
                <td><input type="text" name="text_default"
                           value="{% if (entries.ftype == 0) %}{{ entries.fdefault }}{% endif %}" size=40></td>
            </tr>
        </table>
    </div>

    <!-- FIELD TYPE: CHECKBOX -->
    <div id="type_checkbox">
        <table border="0" cellspacing="1" cellpadding="1" class="content">
            <tr class="contRow1">
                <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">флаг</td>
                <td width="45%">Значение по умолчанию:</td>
                <td><input type="checkbox" name="checkbox_default" value="1"
                           {% if (entries.fdefault == 1) and (entries.ftype == 1) %}checked{% endif %} ></td>
            </tr>
        </table>
    </div>

    <!-- FIELD TYPE: SELECT -->
    <div id="type_select">
        <table border="0" cellspacing="1" cellpadding="1" class="content">
            <tr class="contRow1">
                <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">выбор</td>
                <td valign="top">Список значений:</td>
                <td>
                    <table id="xfSelectTable" width="100%" cellspacing="0" cellpadding="0" border="0" class="content"
                           style="padding: 0px;">
                        <thead>
                        <tr class="contRow1">
                            <td>Код</td>
                            <td>Значение</td>
                            <td>&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody id="xfSelectRows">
                        {% if (mode == "add") %}
                            <tr>
                                <td><input size="12" name="so_data[1][0]" type="text" value=""/></td>
                                <td><input type="text" size="55" name="so_data[1][1]" value=""/></td>
                                <td><a href="#" onclick="return false;"><img
                                                src="{{ admin_url }}/skins/default/images/delete.gif" alt="DEL"
                                                width="12" height="12"/></a></td>
                            </tr>
                        {% else %}
                            {{ entries.sOpts }}
                        {% endif %}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3"><input type="button" id="xfBtnAdd" style="width: 300px;"
                                                   value=" + Добавить строку"/></td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr class="contRow1">
                <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">выбор</td>
                <td>Значение по умолчанию: <br/>
                    <small><i>При сохранении кодов</i>: код
                </td>
                <td><input type="text" name="select_default"
                           value="{% if (entries.ftype == 2) %}{{ entries.fdefault }}{% endif %}" size=40></td>
            </tr>
        </table>
    </div>

    <!-- FIELD TYPE: HTML -->
    <div id="type_html">
        <table border="0" cellspacing="1" cellpadding="1" class="content">
            <tr class="contRow1">
                <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">HTML</td>
                <td width="45%">Значение по умолчанию:</td>
                <td>
                    <textarea
                            name="html_default">{% if (entries.ftype == 3) %}{{ entries.fdefault }}{% endif %}</textarea>
                </td>
            </tr>
        </table>
    </div>

    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">Позиция<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="position"
                                                         value="{{ entries.position }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">В фильтре?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="checkbox" name="in_filter"
                                                         {% if entries.in_filter == '1' %}checked{% endif %} value="1">
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

<script language="javascript">
    function clx(mode) {
        document.getElementById('type_text').style.display = (mode == 'text') ? 'block' : 'none';
        document.getElementById('type_checkbox').style.display = (mode == 'checkbox') ? 'block' : 'none';
        document.getElementById('type_select').style.display = (mode == 'select') ? 'block' : 'none';
        document.getElementById('type_html').style.display = (mode == 'html') ? 'block' : 'none';
    }

    clx('{% if (entries.ftype == 0) %}text{% elseif(entries.ftype == 1) %}checkbox{% elseif(entries.ftype == 2) %}select{% elseif(entries.ftype == 3) %}html{% endif %}');


    var soMaxNum = $('#xfSelectTable >tbody >tr').length + 1;

    $('#xfSelectTable a').click(function () {
        if ($('#xfSelectTable >tbody >tr').length > 1) {
            $(this).parent().parent().remove();
        } else {
            $(this).parent().parent().find("input").val('');
        }
    });

    // jQuery - INIT `select` configuration
    $("#xfBtnAdd").click(function () {
        var xl = $('#xfSelectTable tbody>tr:last').clone();
        xl.find("input").val('');
        xl.find("input").eq(0).attr("name", "so_data[" + soMaxNum + "][0]");
        xl.find("input").eq(1).attr("name", "so_data[" + soMaxNum + "][1]");
        soMaxNum++;

        xl.insertAfter('#xfSelectTable tbody>tr:last');
        $('#xfSelectTable a').click(function () {
            if ($('#xfSelectTable >tbody >tr').length > 1) {
                $(this).parent().parent().remove();
            } else {
                $(this).parent().parent().find("input").val('');
            }
        });
    });

</script>

<script src="{{ admin_url }}/plugins/eshop/tpl/config/jq/jquery-1.7.2.min.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/upload/uploadifive/uploadifive.css">
<script src="{{ admin_url }}/plugins/eshop/upload/uploadifive/jquery.uploadifive.min.js"
        type="text/javascript"></script>

<link rel="stylesheet" href="{{ admin_url }}/plugins/eshop/tpl/config/jq_capty/jquery.capty.css" type="text/css"/>
<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/config/jq_capty/jquery.capty.min.js"></script>

<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/tpl/config/jq_jesse/jquery-jesse.css">
<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/config/jq_jesse/jquery-jesse.js"></script>

<script type="text/javascript"
        src="{{ admin_url }}/plugins/eshop/tpl/config/jq_tokeninput/js/jquery.tokeninput.js"></script>
<link rel="stylesheet" href="{{ admin_url }}/plugins/eshop/tpl/config/jq_tokeninput/css/token-input.css"
      type="text/css"/>

<script src="{{ admin_url }}/plugins/eshop/tpl/config/tinymce/tinymce.min.js" type="text/javascript"></script>
<script>

    tinymce.init({
        selector: 'textarea[class=html_textarea]',
        height: 100,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
    });

</script>

<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/js/eshop.js"></script>

{{ entries.error }}
<form method="post" action="" id="product_form" enctype="multipart/form-data">
    <input type="hidden" name="handler" value="1"/>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">Название<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ entries.name }}"/>
            </td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">Код<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="code" value="{{ entries.code }}"/>
            </td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">URL<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="url" value="{{ entries.url }}"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Meta title<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_title"
                                                         value="{{ entries.meta_title }}"/></td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">Meta keywords<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_keywords"
                                                         value="{{ entries.meta_keywords }}"/></td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">Meta description<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_description"
                                                         value="{{ entries.meta_description }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Внешний ID<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="external_id"
                                                         value="{{ entries.external_id }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Категория<br/>
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
            <td width="50%" class="contentEntry1">Анотация<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><textarea rows="10" cols="45"
                                                            name="annotation"
                                                            class=html_textarea>{{ entries.annotation }}</textarea></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Полное описание<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><textarea rows="10" cols="45"
                                                            name="body"
                                                            class=html_textarea>{{ entries.body }}</textarea></td>
        </tr>


        <tr>
            <td width="50%" class="contentEntry1">Прикрепить изображения<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">

                <script type="text/javascript">
                    $(document).ready(function () {

                        var i = 0;
                        $('#file_upload').uploadifive({
                            'auto': false,
                            'formData': {},
                            'queueID': 'queue',
                            'uploadScript': '/engine/plugins/eshop/upload/libs/upload_product_images.php',
                            'onUpload': function (filesToUpload) {
                                i = 0;
                            },
                            'onUploadComplete': function (file, data) {
                                console.log(file);
                                console.log(data);
                                if (data == "1") {
                                    var form = document.forms['product_form'];
                                    var el = document.createElement("input");
                                    el.type = "hidden";
                                    el.name = "data[images][" + i + "]";
                                    el.value = file.name;
                                    form.appendChild(el);
                                    i++;
                                }
                            },
                            'onQueueComplete': function (uploads) {
                                document.getElementById('product_form').submit();
                            }
                        });


                        $('.fix').capty({
                            cWrapper: 'capty-tile',
                            height: 36,
                            opacity: .6
                        });


                    });

                </script>

                <div id="queue">
                </div>
                <input id="file_upload" name="file_upload" type="file" multiple="true">

            </td>
        </tr>

        <tr>

            <td width="50%" class="contentEntry1">Прикрепленные изображения<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">

                <script type="text/javascript">
                    $(function () {
                        $('#list').jesse({
                            onStop: function (position, prevPosition, item) {
                                if (position != prevPosition) {

                                    var img_id = item.find('img').attr('data-id');
                                    rpcEshopRequest('eshop_change_img_pos', {
                                        'img_id': img_id,
                                        'position': position,
                                        'prevPosition': prevPosition
                                    }, function (resTX) {
                                        eshop_indication('success', 'Порядок изображений изменен');
                                    });

                                }
                            }
                        });

                        $('.del_img').mouseup(function (e) {
                            return false;
                        });

                    });
                </script>
                <style>
                    #list {
                        max-width: 640px;
                        margin: 20px auto;
                    }
                </style>

                <ul class="jq-jesse" id="list">
                    {% for img in entries.entriesImg %}
                        <li class="jq-jesse__item"><a
                                    href='{{ home }}/uploads/eshop/products/{{ entries.id }}/{{ img.filepath }}'
                                    target='_blank'><img class="fix" name="#content-target-{{ img.id }}"
                                                         data-id="{{ img.id }}"
                                                         src='{{ home }}/uploads/eshop/products/{{ entries.id }}/thumb/{{ img.filepath }}'
                                                         width='100' height='100'></a>
                            <div id="content-target-{{ img.id }}">
                                <a href="{{ img.del_link }}" class="del_img" data-id="{{ img.id }}">[x]</a>&nbsp;&nbsp;&nbsp;
                            </div>
                        </li>
                    {% endfor %}
                </ul>


            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Активировать объявление?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="checkbox" name="active"
                                                         {% if entries.mode == 'add' %}checked{% else %}{% if entries.active == '1' %}checked{% endif %}{% endif %}
                                                         value="1"></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Рекомендованный?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="checkbox" name="featured"
                                                         {% if entries.featured == '1' %}checked{% endif %} value="1">
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Акционный?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="checkbox" name="stocked"
                                                         {% if entries.stocked == '1' %}checked{% endif %} value="1">
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Связанные товары?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <input type="text" id="linked-products" name="linked-products"/>
            </td>
        </tr>

        <tr class="contRow1">
            <td width="50%" class="contentEntry1">Варианты<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <table id="variantsTable" width="100%" cellspacing="0" cellpadding="0" border="0" class="content"
                       style="padding: 0px;">
                    <thead>
                    <tr class="contRow1">
                        <td>SKU</td>
                        <td>Название варианта</td>
                        <td>Текущая цена&nbsp;{{ system_flags.eshop.currency[0].sign }}</td>
                        <td>Старая цена&nbsp;{{ system_flags.eshop.currency[0].sign }}</td>
                        <td>Количество</td>
                        <td>Наличие</td>
                        <td>&nbsp;</td>
                    </tr>
                    </thead>
                    <tbody id="variantsRows">
                    {% if (entries.mode == "add") %}
                        <tr>
                            <td><input size="12" name="so_data[1][0]" type="text" value=""/></td>
                            <td><input size="45" name="so_data[1][1]" type="text" value=""/></td>
                            <td><input size="12" name="so_data[1][2]" type="text" value=""/></td>
                            <td><input size="12" name="so_data[1][3]" type="text" value=""/></td>
                            <td><input size="12" name="so_data[1][4]" type="text" value=""/></td>
                            <td>
                                <select name="so_data[1][5]" style="width: 100px;">
                                    <option selected="selected" value="5">Есть</option>
                                    <option value="0">Нет</option>
                                    <option value="1">На заказ</option>
                                </select>
                            </td>
                            <td><a href="#" onclick="return false;"><img
                                            src="{{ admin_url }}/skins/default/images/delete.gif" alt="DEL" width="12"
                                            height="12"/></a></td>
                        </tr>
                    {% else %}
                        {{ entries.sOpts }}
                    {% endif %}
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"><input type="button" id="variantsBtnAdd" style="width: 300px;"
                                               value=" + Добавить строку"/></td>
                    </tr>
                    </tfoot>
                </table>
            </td>
        </tr>

        <script>

            var cat_features = {% if (entries.cat_features == 'null') %} "" {% else %} {{ entries.cat_features }} {% endif %};

            $(function () {
                $('select[name=parent]').change(function () {
                    var selected_cat_id = $(this).val();
                    var find = false;
                    $.each(cat_features, function (i, l) {
                        if (i.toString() == selected_cat_id) {
                            $('tr[class=features]').css('display', 'none');
                            $.each(eval(l), function (iv, lv) {
                                $("tr[data-fid=" + lv + "]").css('display', 'table-row');
                            });
                            find = true;
                        }
                    });

                    if (!find) {
                        $("tr[class=features]").css('display', 'none');
                    }

                });
            });

            {% if entries.mode == 'edit' %}

            $(function () {

                var selected_cat_id = "{{ entries.category_id }}";
                var find = false;
                $.each(cat_features, function (i, l) {
                    if (i.toString() == selected_cat_id) {
                        $('tr[class=features]').css('display', 'none');
                        $.each(eval(l), function (iv, lv) {
                            $("tr[data-fid=" + lv + "]").css('display', 'table-row');
                        });
                        find = true;
                    }
                });

                if (!find) {
                    $("tr[class=features]").css('display', 'none');
                }

            });

            {% endif %}

        </script>

        {% if (entries.features) %}
            <tr>
                <td width="50%" class="contentEntry1">Дополнительные поля<br/>
                    <small></small>
                </td>
                <td width="50%" class="contentEntry2"></td>
            </tr>
            {% for feature in entries.features %}
                <tr data-fid="{{ feature.id }}" class="features">
                    <td width="50%" class="contentEntry1">{{ feature.name }}<br/>
                        <small></small>
                    </td>
                    <td width="50%" class="contentEntry2">
                        {% if feature.ftype == 0 %}<input type="text" size="80" name="data[features][{{ feature.id }}]"
                                                          value="{% if not(feature.value) and (entries.mode == 'add') %}{{ feature.fdefault }}{% else %}{{ feature.value }}{% endif %}" >
                        {% elseif feature.ftype == 1 %}<input type="checkbox" name="data[features][{{ feature.id }}]"
                                                              value="1"
                                                              {% if (entries.mode == 'add') %}{% if(feature.fdefault) %}checked{% endif %}{% else %}{% if(feature.value) %}checked{% endif %}{% endif %}>
                        {% elseif feature.ftype == 2 %}
                            <select name="data[features][{{ feature.id }}]">
                                <option value="" style="background-color: #E0E0E0;"></option>
                                {% for k,v in feature.foptions %}
                                    <option value="{{ k }}"
                                            {% if (entries.mode == 'add') %}{% if (feature.fdefault == k) %}selected
                                            {% endif %}{% else %}{% if (feature.value == k) %}selected {% endif %}{% endif %}>{{ v }}</option>
                                {% endfor %}
                            </select>
                        {% elseif feature.ftype == 3 %}
                            <textarea class="html_textarea"
                                      name="data[features][{{ feature.id }}]">{% if not(feature.value) and (entries.mode == 'add') %}{{ feature.fdefault }}{% else %}{{ feature.value }}{% endif %}</textarea>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
        {% endif %}

    </table>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100%" colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" class="contentEdit" align="center">
                <input class="button" style="float:center; width: 55px;"
                       onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" value="Сохранить"/>
                <!--
                <input type="submit" name="submit" value="Сохранить" onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" />
                -->
            </td>
        </tr>
    </table>
</form>

<script>
    $(document).ready(function () {

        $("#linked-products").tokenInput("{{ home }}/engine/rpc.php?methodName=eshop_linked_products", {
            crossDomain: false,
            preventDuplicates: true,
            {% if (entries.related) %}
            prePopulate: [
                {% for rel in entries.related %}
                {id: "{{ rel.related_id }}", name: `"{{ rel.name }}"`},
                {% endfor %}
            ],
            {% endif %}
        });

    });

</script>


<script language="javascript">

    var soMaxNum = $('#variantsTable >tbody >tr').length + 1;

    $('#variantsTable a').click(function () {
        if ($('#variantsTable >tbody >tr').length > 1) {
            $(this).parent().parent().remove();
        } else {
            $(this).parent().parent().find("input").val('');
        }
    });

    // jQuery - INIT `select` configuration
    $("#variantsBtnAdd").click(function () {
        var xl = $('#variantsTable tbody>tr:last').clone();
        xl.find("input").val('');
        xl.find("input").eq(0).attr("name", "so_data[" + soMaxNum + "][0]");
        //xl.find("span").eq(0).text(soMaxNum);
        xl.find("input").eq(1).attr("name", "so_data[" + soMaxNum + "][1]");
        xl.find("input").eq(2).attr("name", "so_data[" + soMaxNum + "][2]");
        xl.find("input").eq(3).attr("name", "so_data[" + soMaxNum + "][3]");
        xl.find("input").eq(4).attr("name", "so_data[" + soMaxNum + "][4]");
        xl.find("select").eq(0).attr("name", "so_data[" + soMaxNum + "][5]");
        soMaxNum++;

        xl.insertAfter('#variantsTable tbody>tr:last');
        $('#variantsTable a').click(function () {
            if ($('#variantsTable >tbody >tr').length > 1) {
                $(this).parent().parent().remove();
            } else {
                $(this).parent().parent().find("input").val('');
            }
        });
    });

</script>

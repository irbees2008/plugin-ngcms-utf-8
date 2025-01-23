<script src="{{ admin_url }}/plugins/eshop/tpl/config/jq/jquery-1.7.2.min.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/upload/uploadifive/uploadifive.css">
<script src="{{ admin_url }}/plugins/eshop/upload/uploadifive/jquery.uploadifive.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="{{ admin_url }}/plugins/eshop/tpl/config/jq_capty/jquery.capty.css" type="text/css" />
<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/config/jq_capty/jquery.capty.min.js"></script>

<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/tpl/config/jq_jesse/jquery-jesse.css">
<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/config/jq_jesse/jquery-jesse.js"></script>

<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/config/jq_tokeninput/js/jquery.tokeninput.js"></script>
<link rel="stylesheet" href="{{ admin_url }}/plugins/eshop/tpl/config/jq_tokeninput/css/token-input.css" type="text/css" />

<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/tpl/js/eshop.js"></script>

{{entries.error}}
<form method="post" action="" id="product_form" enctype="multipart/form-data">
<input type="hidden" name="handler" value="1" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">Название<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{entries.name}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Код<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="code" value="{{entries.code}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">URL<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="url" value="{{entries.url}}" /></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Meta title<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_title" value="{{entries.meta_title}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Meta keywords<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_keywords" value="{{entries.meta_keywords}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Meta description<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_description" value="{{entries.meta_description}}" /></td>
    </tr>


    <tr>
        <td width="50%" class="contentEntry1">Категория<br /><small></small></td>
        <td width="50%" class="contentEntry2">
            <select name="parent">
                <option value="0">Выберите категорию</option>
                {{entries.catz}}
            </select>
        </td>
    </tr>
    
    <tr>
        <td width="50%" class="contentEntry1">Анотация<br /><small></small></td>
        <td width="50%" class="contentEntry2"><textarea rows="10" cols="45" name="annotation">{{entries.annotation}}</textarea></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Полное описание<br /><small></small></td>
        <td width="50%" class="contentEntry2"><textarea rows="10" cols="45" name="body">{{entries.body}}</textarea></td>
    </tr>    
    
    
    <tr>
    <td width="50%" class="contentEntry1">Прикрепить изображения<br /><small></small></td>
    <td width="50%" class="contentEntry2">

    <script type="text/javascript">
    $(document).ready(function() {

        var i = 0;
        $('#file_upload').uploadifive({
            'auto'             : false,
            'formData'         : {},
            'queueID'          : 'queue',
            'uploadScript'     : '/engine/plugins/eshop/upload/libs/upload_product_images.php',
            'onUpload' : function(filesToUpload) {
                     i = 0;
                },
            'onUploadComplete' : function(file, data) {
                    console.log(file);
                    console.log(data);
                    if(data == "1") {
                        var form = document.forms['product_form'];
                        var el = document.createElement("input");
                        el.type = "hidden";
                        el.name = "data[images]["+i+"]";
                        el.value = file.name;
                        form.appendChild(el);
                        i++;
                    }
                },
            'onQueueComplete' : function(uploads) {
                document.getElementById('product_form').submit();
            }
        });


    $('.fix').capty({
       cWrapper:  'capty-tile',
       height:   36,
       opacity:  .6
     });


    });

    </script>

    <div id="queue">
    </div>
    <input id="file_upload" name="file_upload" type="file" multiple="true">

    </td>
    </tr>

    <tr>
        
    <td width="50%" class="contentEntry1">Прикрепленные изображения<br /><small></small></td>
    <td width="50%" class="contentEntry2">

    <script type="text/javascript">
    $(function(){
        $('#list').jesse({
            onStop: function(position, prevPosition, item) {
                if(position != prevPosition) {

                    var img_id = item.find('img').attr('data-id');
                    rpcEshopRequest('eshop_change_img_pos', {'img_id':img_id, 'position':position, 'prevPosition':prevPosition}, function (resTX) {
                    });
                    
                }
            }
        });
        
        $('.del_img').mouseup(function(e) {
            return false;
        });
        
    });
    </script>
    <style>
    #list {max-width:640px; margin:20px auto;}
    </style>

    <ul class="jq-jesse" id="list">
        {% for img in entries.entriesImg %}
        <li class="jq-jesse__item"><a href='{{home}}/uploads/eshop/products/{{img.filepath}}' target='_blank'><img class="fix" name="#content-target-{{img.id}}"  data-id="{{img.id}}" src='{{home}}/uploads/eshop/products/thumb/{{img.filepath}}' width='100' height='100'></a>
        <div id="content-target-{{img.id}}">
           <a href="{{img.del_link}}" class="del_img" data-id="{{img.id}}">[x]</a>&nbsp;&nbsp;&nbsp;
        </div></li>
        {% endfor %}
    </ul>

    
    </td>
    </tr>

    <tr>
    <td width="50%" class="contentEntry1">Активировать объявление?<br /><small></small></td>
    <td width="50%" class="contentEntry2"><input type="checkbox" name="active" {% if entries.mode == 'add' %}checked{% else %}{% if entries.active == '1' %}checked{% endif %}{% endif %} value="1" > </td>
    </tr>
    
    <tr>
    <td width="50%" class="contentEntry1">Рекомендованный?<br /><small></small></td>
    <td width="50%" class="contentEntry2"><input type="checkbox" name="featured" {% if entries.featured == '1' %}checked{% endif %} value="1" ></td>
    </tr>
    
    <tr>
    <td width="50%" class="contentEntry1">Акционный?<br /><small></small></td>
    <td width="50%" class="contentEntry2"><input type="checkbox" name="stocked" {% if entries.stocked == '1' %}checked{% endif %} value="1" ></td>
    </tr>
    
    <tr>
    <td width="50%" class="contentEntry1">Связанные товары?<br /><small></small></td>
    <td width="50%" class="contentEntry2">
        <input type="text" id="linked-products" name="linked-products" />
    </td>
    </tr>
    
    <tr>
        <td width="50%" class="contentEntry1">Цены<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="6" name="price" value="{{entries.prices[0].price}}" >&nbsp;{{ system_flags.eshop.currency[0].sign }} &nbsp;&nbsp;&nbsp;<input type="text" size="6" name="compare_price" value="{{entries.prices[0].compare_price}}" >&nbsp;{{ system_flags.eshop.currency[0].sign }}&nbsp;&nbsp;&nbsp;
        <select name="stock" style="width: 200px;">
            <option {% if entries.mode == 'add' %}selected="selected"{% else %}{% if entries.prices[0].stock == '5' %}selected="selected"{% endif %}{% endif %} value="5">Есть</option>
            <option {% if entries.prices[0].stock == '0' %}selected="selected"{% endif %}value="0">Нет</option>
            <option {% if entries.prices[0].stock == '1' %}selected="selected"{% endif %}value="1">На заказ</option>
        </select></td>
    </tr>

    {% if (entries.features) %}
        <tr>
            <td width="50%" class="contentEntry1">Дополнительные поля<br /><small></small></td>
            <td width="50%" class="contentEntry2"></td>
        </tr>
        {% for feature in entries.features %}
            <tr>
                <td width="50%" class="contentEntry1">{{feature.name}}<br /><small></small></td>
                <td width="50%" class="contentEntry2"><input type="text" size="80" name="data[features][{{feature.id}}]" value="{{feature.value}}" ></td>
            </tr>
        {% endfor %}
    {% endif %}

</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input class="button" style="float:center; width: 55px;" onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" value="Сохранить" />
<!--
<input type="submit" name="submit" value="Сохранить" onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" />
-->
</td>
</tr>
</table>
</form>

<script>
$(document).ready(function() {

    $("#linked-products").tokenInput("{{home}}/engine/rpc.php?methodName=eshop_linked_products", {
        crossDomain: false,
        preventDuplicates: true,
        {% if (entries.related) %}
        prePopulate: [
            {% for rel in entries.related %}
                {id: "{{rel.related_id}}", name: `"{{rel.name}}"`},
            {% endfor %}
        ],
        {% endif %}
    });

});

</script>

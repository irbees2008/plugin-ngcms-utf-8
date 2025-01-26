<table border="0" cellspacing="0" cellpadding="0" class="content">
<tbody>
<tr>
    
    <td width="50%" valign="top" class="contentEntry1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
                <tr>
                    <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">Экспорт YML</td>
                </tr>
                <tr>
                    <td><br/>
                        URL: <a href="{{ home }}{{ yml_export_link }}">{{ home }}{{ yml_export_link }}</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>

    <td width="50%" class="contentEntry1" valign="top">
        <form action="" method="post">
        <input type="hidden" name="import" value="1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
            <tr>
                <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">Импорт YML</td>
            </tr>
            <tr>
                <td>
                <table>
                <tbody>
                    <tr id="row">
                        <br/>
                        <td>URL: </td><td><input type="text" size="60" name="yml_url"></td>
                    </tr>
                </tbody>
                </table>
                
                <div class="list">
                    Внимание, существующие данные могут быть удалены! 
                    <!--
                    <input type="checkbox" name="replace" value="replace" id="replace2" class="check">
                    <label for="replace2">Перезаписать, если уже есть</label><br>
                    <input type="checkbox" name="rand" value="rand" id="rand2" class="check">
                    <label for="rand2">Подставить случайное число</label><br>
                    <input type="checkbox" name="thumb" value="thumb" id="thumb2" class="check">
                    <label for="thumb2">Создать уменьшенную копию</label><br>
                    <input type="checkbox" name="shadow" value="shadow" id="shadow2" class="check"><label for="shadow2">Добавить тень</label><br>
                    <input type="checkbox" name="stamp" value="stamp" id="stamp2" class="check"><label for="stamp2">Добавить штамп-картинку</label>
                    -->
                </div>
                
                </td>
            </tr>
            <tr align="center">
            <td width="100%" class="contentEdit" align="center" valign="top">
                <input type="submit" value="Загрузить!" class="button">
            </td>
            </tr>
            </tbody>
        </table>
        </form>
    </td>
</tr>
<tr>
    
    <td width="50%" valign="top" class="contentEntry1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
                <tr>
                    <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">Массовое изменение цен</td>
                </tr>
                <tr>
                    <td>
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </td>

    <td width="50%" class="contentEntry1" valign="top">
        <form action="" method="post">
        <input type="hidden" name="currency" value="1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
            <tr>
                <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">Обновление валют</td>
            </tr>
            <tr>
                <td>

                <div class="list">
                    Источник данных: CurrencyConverterApi.com 
                </div>
                
                </td>
            </tr>
            <tr align="center">
            <td width="100%" class="contentEdit" align="center" valign="top">
                <input type="submit" value="Загрузить!" class="button">
            </td>
            </tr>
            </tbody>
        </table>
        </form>
    </td>
</tr>

</tbody>
</table>

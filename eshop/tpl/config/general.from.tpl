<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=options">
    <tr>
        <td colspan=2>
            <fieldset class="admGroup">
                <legend class="title">Настройки</legend>
                <table width="100%" border="0" class="content">
                    <tr>
                        <td class="contentEntry1" valign=top>Количество продуктов на странице<br/></td>
                        <td class="contentEntry2" valign=top><input name="count" type="text"
                                                                    title="Количество продуктов на странице" size="4"
                                                                    value="{{ entries.count }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Количество продуктов на странице поиска<br/></td>
                        <td class="contentEntry2" valign=top><input name="count_search" type="text"
                                                                    title="Количество продуктов на странице поиска"
                                                                    size="4" value="{{ entries.count_search }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Количество продуктов на странице c акциями<br/></td>
                        <td class="contentEntry2" valign=top><input name="count_stocks" type="text"
                                                                    title="Количество продуктов на странице c акциями"
                                                                    size="4" value="{{ entries.count_stocks }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Учёт просмотра объявлений?<br/></td>
                        <td class="contentEntry2" valign=top><select
                                    name="views_count">{{ entries.views_count }}</select></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Двухсторонние связанные товары?<br/></td>
                        <td class="contentEntry2" valign=top><select
                                    name="bidirect_linked_products">{{ entries.bidirect_linked_products }}</select></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Премодерация отзывов?<br/></td>
                        <td class="contentEntry2" valign=top><select
                                    name="approve_comments">{{ entries.approve_comments }}</select></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Порядок сортировки отзывов?<br/></td>
                        <td class="contentEntry2" valign=top><select
                                    name="sort_comments">{{ entries.sort_comments }}</select></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Добавлять страницы категорий и продукции в GSMG?<br/></td>
                        <td class="contentEntry2" valign=top><select
                                    name="integrate_gsmg">{{ entries.integrate_gsmg }}</select></td>
                    </tr>
                </table>
            </fieldset>
        </td>
    </tr>

    <tr>
        <td colspan=2>
            <fieldset class="admGroup">
                <legend class="title">Изображения товаров</legend>
                <table width="100%" border="0" class="content">
                    <tr>
                        <td class="contentEntry1" valign=top>Ширина уменьшенной копии<br/></td>
                        <td class="contentEntry2" valign=top><input name="width_thumb" type="text"
                                                                    title="Ширина уменьшенной копии" size="20"
                                                                    value="{{ entries.width_thumb }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Разрешенные расширения для изображений<br/>
                            <small>Формат записи <b>jpg, jpeg, gif, png</b></small>
                        </td>
                        <td class="contentEntry2" valign=top><input name="ext_image" type="text"
                                                                    title="Разрешенные разширения для изображений"
                                                                    size="50" value="{{ entries.ext_image }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Ширина при загрузке<br/></td>
                        <td class="contentEntry2" valign=top><input name="pre_width" type="text"
                                                                    title="Ширина при загрузке" size="20"
                                                                    value="{{ entries.pre_width }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Качество при загрузке<br/></td>
                        <td class="contentEntry2" valign=top><input name="pre_quality" type="text"
                                                                    title="Качество при загрузке" size="20"
                                                                    value="{{ entries.pre_quality }}"/></td>
                    </tr>
                    <!--
<tr>
<td class="contentEntry1" valign=top>Максимальный размер загружаемого изображения<br /><small>Размер в мегабайтах</small></td>
<td class="contentEntry2" valign=top><input name="max_image_size" type="text" title="Максимальный размер загружаемого изображения" size="20" value="{{ entries.max_image_size }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Максимальный ширина загружаемого изображения<br /><small>Указывается в пикселях</small></td>
<td class="contentEntry2" valign=top><input name="width" type="text" title="Максимальный ширина загружаемого изображения" size="20" value="{{ entries.width }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Максимальный высота загружаемого изображения<br /><small>Указывается в пикселях</small></td>
<td class="contentEntry2" valign=top><input name="height" type="text" title="Максимальный высота загружаемого изображения" size="20" value="{{ entries.height }}" /></td>
</tr>
-->
                </table>
            </fieldset>
        </td>
    </tr>

    <tr>
        <td colspan=2>
            <fieldset class="admGroup">
                <legend class="title">Изображения категорий</legend>
                <table width="100%" border="0" class="content">
                    <tr>
                        <td class="contentEntry1" valign=top>Ширина уменьшенной копии<br/></td>
                        <td class="contentEntry2" valign=top><input name="catz_width_thumb" type="text"
                                                                    title="Ширина уменьшенной копии" size="20"
                                                                    value="{{ entries.catz_width_thumb }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Разрешенные расширения для изображений<br/>
                            <small>Формат записи <b>jpg, jpeg, gif, png</b></small>
                        </td>
                        <td class="contentEntry2" valign=top><input name="catz_ext_image" type="text"
                                                                    title="Разрешенные разширения для изображений"
                                                                    size="50" value="{{ entries.catz_ext_image }}"/>
                        </td>
                    </tr>
                    <!--
<tr>
<td class="contentEntry1" valign=top>Максимальный размер загружаемого изображения<br /><small>Размер в мегабайтах</small></td>
<td class="contentEntry2" valign=top><input name="catz_max_image_size" type="text" title="Максимальный размер загружаемого изображения" size="20" value="{{ entries.catz_max_image_size }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Максимальный ширина загружаемого изображения<br /><small>Указывается в пикселях</small></td>
<td class="contentEntry2" valign=top><input name="catz_width" type="text" title="Максимальный ширина загружаемого изображения" size="20" value="{{ entries.catz_width }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Максимальный высота загружаемого изображения<br /><small>Указывается в пикселях</small></td>
<td class="contentEntry2" valign=top><input name="catz_height" type="text" title="Максимальный высота загружаемого изображения" size="20" value="{{ entries.catz_height }}" /></td>
</tr>
-->
                </table>
            </fieldset>
        </td>
    </tr>

    <tr>
        <td colspan=2>
            <fieldset class="admGroup">
                <legend class="title">Оповещения</legend>
                <table width="100%" border="0" class="content">
                    <tr>
                        <td class="contentEntry1" valign=top>Оповещение о заказах<br/></td>
                        <td class="contentEntry2" valign=top><input name="email_notify_orders" type="text"
                                                                    title="Оповещение о заказах" size="100"
                                                                    value="{{ entries.email_notify_orders }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Оповещение о комментариях<br/></td>
                        <td class="contentEntry2" valign=top><input name="email_notify_comments" type="text"
                                                                    title="Оповещение о комментариях" size="100"
                                                                    value="{{ entries.email_notify_comments }}"/></td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Обратный адрес оповещений<br/></td>
                        <td class="contentEntry2" valign=top><input name="email_notify_back" type="text"
                                                                    title="Обратный адрес оповещений" size="100"
                                                                    value="{{ entries.email_notify_back }}"/></td>
                    </tr>
                </table>
            </fieldset>
        </td>
    </tr>

    <tr>
        <td colspan=2>
            <fieldset class="admGroup">
                <legend class="title">Описания</legend>
                <table width="100%" border="0" class="content">
                    <tr>
                        <td class="contentEntry1" valign=top>Описание доставки<br/></td>
                        <td class="contentEntry2" valign=top><textarea rows="10" cols="45"
                                                                       name="description_delivery">{{ entries.description_delivery }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Описание покупки<br/></td>
                        <td class="contentEntry2" valign=top><textarea rows="10" cols="45"
                                                                       name="description_order">{{ entries.description_order }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="contentEntry1" valign=top>Телефоны магазина<br/></td>
                        <td class="contentEntry2" valign=top><textarea rows="10" cols="45"
                                                                       name="description_phones">{{ entries.description_phones }}</textarea>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </td>
    </tr>

    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100%" colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" class="contentEdit" align="center">
                <input name="submit" type="submit" value="Сохранить" class="button"/>
            </td>
        </tr>
    </table>
</form>

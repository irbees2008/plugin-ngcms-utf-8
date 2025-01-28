{{ entries.error }}
<form method="post" action="">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">ID<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="name" value="{{ entries.id }}"
                                                         disabled="disabled"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Дата<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="name"
                                                         value="{{ entries.dt|date('d.m.Y H:i') }}"
                                                         disabled="disabled"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">IP<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="name" value="{{ entries.ip }}"
                                                         disabled="disabled"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Тип<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="type"
                                                         value="{% if (entries.type == 1) %}Обычный{% elseif (entries.type == 2) %}Купить в один клик{% elseif (entries.type == 3) %}Узнать о наличии{% endif %}"
                                                         disabled="disabled"/></td>
        </tr>

        {% if (entries.author_id) %}
            <tr>
                <td width="50%" class="contentEntry1">Пользователь<br/>
                    <small></small>
                </td>
                <td width="50%" class="contentEntry2"><a href="{{ entries.profile_link }}">{{ entries.author }}</a></td>
            </tr>
        {% endif %}

        <tr>
            <td width="50%" class="contentEntry1">Имя<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="name" value="{{ entries.name }}"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Телефон<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="phone"
                                                         value="{{ entries.phone }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Email<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="email"
                                                         value="{{ entries.email }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Способ оплаты<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <select name="payment_type_id" style="font: 12px Verdana, Courier, Arial; width: 230px;">
                    {% for v in entries.entriesPaymentTypes %}
                        <option value="{{ v.id }}" {% if (entries.payment_type_id == v.id) %}selected{% endif %}>{{ v.name }}</option>
                    {% endfor %}
                </select>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Способ доставки<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <select name="delivery_type_id" style="font: 12px Verdana, Courier, Arial; width: 230px;">
                    {% for v in entries.entriesDeliveryTypes %}
                        <option value="{{ v.id }}" {% if (entries.delivery_type_id == v.id) %}selected{% endif %}>{{ v.name }}</option>
                    {% endfor %}
                </select>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Адрес<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" class="form-control" size="80" name="address"
                                                         value="{{ entries.address }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Комментарий<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><textarea rows="10" cols="45"
                                                            name="comment">{{ entries.comment }}</textarea></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">Оплачен?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2">
                <select name="paid" style="font: 12px Verdana, Courier, Arial; width: 230px;">
                    <option value="0" {% if (entries.paid == 0) %}selected="selected"{% endif %}>Нет</option>
                    <option value="1" {% if (entries.paid == 1) %}selected="selected"{% endif %}>Да</option>
                </select>
            </td>
        </tr>

        <tr>

            <table border="0" cellspacing="0" width="1000" cellpadding="0" align="center">
                <tr class="contHead" align="left">
                    <td width="5%">ID</td>
                    <td width="15%">Изображение</td>
                    <td width="40%">Название</td>
                    <td width="20%">Вариант</td>
                    <td width="10%">Количество</td>
                    <td width="10%">Текущая цена</td>
                </tr>
                {% for entry in entries.basket %}
                    <tr align="left">
                        <td width="5%" class="contentEntry1">{{ entry.id }}</td>
                        <td width="15%" class="contentEntry1"><a href="{{ entry.xfields.item.view_link }}"><img alt=""
                                                                                                                src="{% if (entry.xfields.item.image_filepath) %}{{ home }}/uploads/eshop/products/{{ entry.xfields.item.id }}/thumb/{{ entry.xfields.item.image_filepath }}{% else %}{{ home }}/engine/plugins/eshop/tpl/img/img_none.jpg{% endif %}"
                                                                                                                width="100"
                                                                                                                height="100"></a>
                        </td>
                        <td width="40%" class="contentEntry1"><a
                                    href="{{ entry.xfields.item.view_link }}">{{ entry.title }}</a></td>
                        <td width="10%" class="contentEntry1">{{ entry.xfields.item.v_name }}</td>
                        <td width="10%" class="contentEntry1">{{ entry.count }} шт.</td>
                        <td width="10%"
                            class="contentEntry1">{{ entry.price }} {{ system_flags.eshop.currency[0].sign }}</td>
                    </tr>
                {% endfor %}
                <tr>
                    <td width="100%" colspan="8">&nbsp;</td>
                </tr>

                <tr align="center">
                    <td colspan="9" class="contentEdit" align="right" valign="top">
                        <div style="text-align: left;">
                            Cтоимость
                            товаров: {{ entries.basket_total|number_format(2, '.', '') }} {{ system_flags.eshop.currency[0].sign }}
                        </div>
                    </td>
                </tr>

            </table>

        </tr>
        {% if (entries.purchases) %}
            <tr>

                <table border="0" cellspacing="0" width="1000" cellpadding="0" align="center" style="
    MARGIN-TOP: 10PX;
    MARGIN-BOTTOM: 10PX;">
                    <tr class="contHead" align="left">
                        <td width="10%">ID оплаты</td>
                        <td width="15%">Дата оплаты</td>
                        <td width="75%">Информация</td>
                    </tr>
                    {% for purchase in entries.purchases %}
                        <tr align="left">
                            <td width="5%" class="contentEntry1">{{ purchase.id }}</td>
                            <td width="15%" class="contentEntry1">{{ purchase.dt|date('d.m.Y H:i') }}</td>
                            <td width="80%" class="contentEntry1">{{ purchase.info_string }}</td>
                        </tr>
                    {% endfor %}
                    <tr>
                        <td width="100%" colspan="8">&nbsp;</td>
                    </tr>
                </table>

            </tr>
        {% endif %}

    </table>
    <table border="0" width="1000" cellspacing="0" cellpadding="0" align="center">
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

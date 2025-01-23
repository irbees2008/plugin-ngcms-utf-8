<script type="text/javascript">
    document.ready=function()
    {
        var sizelimit = {about_sizelimit};
        if(sizelimit <= 0 ) { document.getElementById('sizelimit_text').style.display = "none"; }
    }
    function validate_form() {
        var f = document.getElementById('profileForm');
        // ICQ
        var icq = f.editicq.value;
        if ((icq.length > 0)&&(! icq.match(/^\d{4,10}$/))) { 
            alert("{{ lang.uprofile['wrong_icq'] }}"); 
            return false; 
        }
        // Email
        var email = f.editmail.value;
        if ((email.length > 0) && (! emailCheck(email))) {
            alert("{{ lang.uprofile['wrong_email'] }}");
            return false;
        }
        // About
        var about = f.editabout.value;
        if (({about_sizelimit} > 0) && (about.length > {about_sizelimit})) {
            alert("{{ info_sizelimit_text }}");
            return false;   
        }
        return true;
    }
</script>

<div class="content">
    <div class="frame-inside page-profile">
    <div class="container">
    <div class="f-s_0 title-profile without-crumbs">
    <div class="frame-title">
    <h1 class="title">{{ lang.uprofile['profile_of'] }}</h1>
    </div>
    </div>
    
    <div class="left-personal f-s_0">
    <ul class="tabs tabs-data">
    <li class="active"><button data-href="#my_data">Основные данные</button></li>
    <li class=""><button data-href="#history_order">История заказов</button></li>
    </ul>
    
    <div class="frame-tabs-ref frame-tabs-profile">
    
    <div id="my_data" class="visited active" style="display: block;">
    <div class="inside-padd clearfix">
    <div class="frame-change-profile">
    <div class="horizontal-form">
    <form id="profileForm" method="post" action="{{ form_action }}" enctype="multipart/form-data">
    <input type="hidden" name="token" value="{{ token }}"/>
    
    <label>
        <span class="title">{{ lang.uprofile['email'] }}:</span>
        <span class="frame-form-field">
            <input type="text" name="editmail" value="{{ user.email }}" class="input" />
        </span>
    </label>
    
    <label>
        <span class="title">{{ lang.uprofile['from'] }}:</span>
        <span class="frame-form-field">
            <input type="text" name="editfrom" value="{{ user.from }}" class="input" />
        </span>
    </label>
    
    <label>
        <span class="title">{{ lang.uprofile['new_pass'] }}:</span>
        <span class="frame-form-field">
            <input type="password" name="editpassword" class="input" />
        </span>
    </label>

    <label>
        <span class="title">{{ lang.uprofile['oldpass'] }}:</span>
        <span class="frame-form-field">
            <input type="password" name="oldpass" value="" class="input" />
        </span>
    </label>
    
    {% if pluginIsActive('xfields') %}

        <label>
            <span class="title">{{ p.xfields.fields.name.title }}:</span>
            <span class="frame-form-field">
                {{ p.xfields.fields.name.input }}
            </span>
        </label>
        <label>
            <span class="title">{{ p.xfields.fields.phone.title }}:</span>
            <span class="frame-form-field">
                
                {{ p.xfields.fields.phone.input }}
            </span>
        </label>
        <label>
            <span class="title">{{ p.xfields.fields.address.title }}:</span>
            <span class="frame-form-field">
                {{ p.xfields.fields.address.input }}
            </span>
        </label>        
    {% endif %}
        
    <div class="frame-label">
    <span class="title">&nbsp;</span>
    <span class="frame-form-field">
    <span class="btn-form">
    <input type="submit" onclick="return validate_form();" value="{{ lang.uprofile['save'] }}">
    </span>
    </span>
    </div>
    
    </form>
    </div>
    </div>

<!--
    <div class="layout-highlight info-discount">
    <div class="title">Ваша скидка</div>
    <div class="content">
    <ul class="items items-info-discount">
    <li class="inside-padd">
    <div>
    Куплено товаров на сумму:
    <span class="price-item">
    <span class="f-w_b">
    <span class="price">0</span> $
    </span>
    </span>
    </div>
                                                                                                                                        </li>

    <li class="inside-padd">
    <span class="text-el">Таблица скидок</span>
    <button type="button" class="icon_info_t isDrop" data-drop=".drop-comulativ-discounts">&nbsp;</button>
    </li>
    </ul>
    </div>
    </div>
-->    
    </div>
    </div>

    <div id="history_order" class="visited" style="display: none;">
    <div class="inside-padd">
    <table class="table-profile adaptive">
        <thead>
            <tr>
                <th>Заказ #</th>
                <th>Время покупки</th>
                <th>Сумма покупки</th>
                <th>Статус платежа</th>
            </tr>
        </thead>
        <tbody>
            {% for order in eshop.orders %}
            <tr>
                <td title="Заказ #"><a rel="nofollow" href="{{ order.order_link }}">Заказ #{{ order.id }}</a></td>
                <td title="Время покупки">{{ order.dt|date("d.m.Y H:i") }}</td>
                <td title="Сумма покупки">
                <div class="frame-prices">
                <span class="current-prices">
                <span class="price-new">
                <span>
                <span class="price">{{ (order.total_price * system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }}</span> {{ system_flags.eshop.current_currency.sign }}
                </span>
                </span>
                </span>
                </div>
                </td>
                <td title="Статус платежа">{% if (order.paid == 0) %}Не оплачен{% else %}Оплачен{% endif %}</td>
            </tr>
            {% endfor %}
        </tbody>
    </table>
    </div></div>
    
    </div>
    </div>
    </div>
    </div>
</div>

{% if (error) %}
<div class="feed-me">
{{error}}
</div>
{% endif %}

<div class="comment">
<h3><span>РћРїР»Р°С‚Р° VIP РѕР±СЉСЏРІР»РµРЅРёСЏ</span></h3>
<form method="post" action="{{pay_url}}" class="comment-form" name="form">
<input type="hidden" name="zid" value="{{zid}}">
<ul class="comment-author">
<li class="item clearfix">
    <select name="price_time_id">
        <option disabled>Р’С‹Р±РµСЂРёС‚Рµ РІСЂРµРјСЏ</option>
        {% for entry in entriesPrices %}
            <option value="{{entry.id}}">{{entry.time}} Рґ. - {{entry.price}} СЂСѓР±.</option>
        {% endfor %}
    </select>
</li>

</ul>
<span class="submit"><button name="submit" type="submit"  tabindex="5" onclick="javascript:$('#file_upload').uploadifive('upload')" >РћС‚РїСЂР°РІРёС‚СЊ</button></span>
</form>
</div>
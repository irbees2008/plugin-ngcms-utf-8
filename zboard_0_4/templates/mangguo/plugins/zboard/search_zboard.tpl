{% if (submit) %}

<div class="t-side">
<div class="hd">РџРѕРёСЃРє</div>
<div class="bd">

<form id="search" method="post" action=""> 
<label class="conl">РљР»СЋС‡РµРІС‹Рµ СЃР»РѕРІР°<br />
<input type="text" name="keywords" size="40" maxlength="100" />
<br />
</label> 

<label class="conl">РљР°С‚РµРіРѕСЂРёРё<br />
<select id="cat" name="cat_id"> 
<option value='0'>Р’СЃРµ РёРјРµСЋС‰РёРµСЃСЏ</option>
{{ options }}
</select> 
<br />
</label> 

<label class="conl">РџРѕРёСЃРє РІ<br />
<select name="search_in"> 
<option value='all' selected>Р’ С‚РµРєСЃС‚Рµ РѕР±СЉСЏРІР»РµРЅРёСЏ Рё Р·Р°РіРѕР»РѕРІРєРµ</option>
<option value='text'>РўРѕР»СЊРєРѕ РІ С‚РµРєСЃС‚Рµ РѕР±СЉСЏРІР»РµРЅРёСЏ</option>
<option value='title'>РўРѕР»СЊРєРѕ РІ Р·Р°РіРѕР»РѕРІРєРµ</option>
</select> 
<br />
</label>
<br /><br /><br />

<input type="submit" name="submit" value="РћС‚РїСЂР°РІРёС‚СЊ" accesskey="s" />
</form> 

</div>
</div>
{% else %}

<div class="t-side">
<div class="hd">Р РµР·СѓР»СЊС‚Р°С‚С‹ РїРѕРёСЃРєР°</div>
<div class="bd">

<table class="hosting">
				<tr>
					<th>Р”Р°С‚Р°</th>
					<th>РљР°С‚РµРіРѕСЂРёСЏ</th>
					<th>Р�Р·РѕР±СЂР°Р¶РµРЅРёРµ</th>
					<th>Р—Р°РіРѕР»РѕРІРѕРє</th>
					<th>РћР±СЉСЏРІР»РµРЅРёРµ</th>
					<th>РђРІС‚РѕСЂ</th>
				</tr>
				{% for entry in entries %}
				<tr>
					<td class="website">
						<a>{{entry.date|date("m-d-Y H:i")}}</a>
					</td>
					<!-- <th class="rank">1</th> -->
					<td class="price">
						<a href="{{entry.catlink}}" class="tag-{{entry.cid}}">{{entry.cat_name}}</a>
					</td>
					<td class="web-hosting">
						<a href="#" title="">{% if (entry.pid) %}<a href='{{entry.fulllink}}'><img src='{{home}}/uploads/zboard/thumb/{{entry.filepath}}' width='60' height='60'></a>{% else %}<a href='{{entry.fulllink}}'><img src='{{tpl_url}}/img/noimage.png' width='60' height='60'></a>{% endif %}</a>
					</td>

					<td class="disk-space">
						<a href="{{ entry.fulllink }}">{{ entry.announce_name }}</a>
					</td>
					<td class="bandwidth">
						{{entry.announce_description|truncateHTML(30,'...')}}
					</td>
					<td class="domain">
						{{entry.author}}
					</td>
				</tr>
				{% else %}
				<tr>
					<td colspan="6" class="website">
						РџРѕ РІР°С€РµРјСѓ Р·Р°РїСЂРѕСЃСѓ <b>{{get_url}}</b> РЅРёС‡РµРіРѕ РЅРµ РЅР°Р№РґРµРЅРѕ
					</td>
				</tr>
				{% endfor %}
				<tr>
					<td colspan="6" class="website">
						<a href='{{home}}/plugin/zboard/search/'>Р’РµСЂРЅСѓС‚СЊСЃСЏ РЅР°Р·Р°Рґ</a>
					</td>
				</tr>
				{% if (pages.true) %}
				<tr>
					<th colspan="6"><div class="pagenavi clearfix">{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}</div></th>
				</tr>
				{% endif %}
</table>

</div>
</div>
{% endif %}
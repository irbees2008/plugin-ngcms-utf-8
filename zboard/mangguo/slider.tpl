{% if isHandler('news:news') %}
	<div id="slider" class="slider">
		<ul>
			<li><img src="{{ tpl_url }}/img/slider/slide-1.jpg" alt="" title="РџСѓС‚РµС€РµСЃС‚РІРёСЏ" /></li>
			<li><a href="#"><img src="{{ tpl_url }}/img/slider/slide-2.jpg" alt="" title="#htmlcaption" /></a></li>
			<li><img src="{{ tpl_url }}/img/slider/slide-3.jpg" alt="" title="Р‘РёР·РЅРµСЃ" /></li>
			<li><img src="{{ tpl_url }}/img/slider/slide-4.jpg" alt="" /></li>
		</ul>
	</div>
	<div id="htmlcaption" class="slider-html-caption">
		<strong>This</strong> is an example of a <em>HTML</em> caption with <a href="#">a link</a>.
	</div>
{% endif %}
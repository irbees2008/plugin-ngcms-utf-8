<li class="is-photo">
<a href="{{ news.url.full }}" class="frame-photo-title">
  <span class="photo-block">
    <span class="helper">
    </span>
    {% if (news.embed.imgCount > 0 ) %}
    <img src="{{ news.embed.images[0] }}" alt="">
    {% else %}
    <img src="{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg" alt="">
    {% endif %}
  </span>
  <span class="title">
    {{ news.title|truncateHTML(70,'...') }}
  </span>
</a>
<div class="description">
  <p>
    {{ news.short }}
  </p>
  
  <div class="date f-s_0">
    <span class="icon_time">
    </span>
    <span class="text-el">
    </span>
    <span class="day">
      {{ news.dateStamp|date("d") }}
    </span>
    <span class="month">
      {{ news.dateStamp|date("m") }} 
    </span>
    <span class="year">
      {{ news.dateStamp|date("Y") }} 
    </span>
  </div>
</div>
</li>

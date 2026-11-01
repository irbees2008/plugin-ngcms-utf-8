<div class="frame-news">
<div class="title-news">
  <div class="frame-title">
    <div class="title">
      <a href="#" class="t-d_n f-s_0 s-all-d">
        <span class="text-el">
          Новости и акции
        </span>
        <span class="icon_arrow">
        </span>
      </a>
    </div>
  </div>
</div>
<ul class="items items-news">
    {% for entry in entries %}{{ entry }}{% endfor %}
</ul>
</div>

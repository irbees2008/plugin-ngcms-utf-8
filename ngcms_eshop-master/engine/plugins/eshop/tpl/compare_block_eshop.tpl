<div class="compare-list-btn tinyCompareList {% if (count > 0) %}pointer{% endif %}">
<button {% if (count == 0) %}class="isDrop"{% endif %}>
  <span class="js-empty empty" {% if (count == 0) %}style="display: block"{% else %}style="display: none;"{% endif %}>
    <span class="icon_compare_list">
    </span>
    <span class="text-el">
      Сравнение 
    </span>
  </span>
  <span class="js-no-empty no-empty" {% if (count == 0) %}style="display: none"{% else %}style="display: block;"{% endif %}>
    <span class="icon_compare_list">
    </span>
    <a href="{{home}}/eshop/compare/">
    <span class="text-el">
      Сравнение
    </span>
    <span class="compareListCount">
      {{ count }}
    </span>
    </a>
  </span>
</button>

</div>
<div class="drop drop-info drop-info-compare">
<span class="helper">
</span>
<span class="text-el">
  Ваш список 
  <br>
  “Список сравнения” пуст    
</span>
</div>

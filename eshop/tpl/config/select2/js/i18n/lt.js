/*! Select2 4.0.0 | https://github.com/select2/select2/blob/master/LICENSE.md */

(function(){if(jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var e=jQuery.fn.select2.amd;return e.define("select2/i18n/lt",[],function(){function e(e,t,n,r){return e%100>9&&e%100<21||e%10===0?e%10>1?n:r:t}return{inputTooLong:function(t){var n=t.input.length-t.maximum,r="PaЕЎalinkite "+n+" simbol";return r+=e(n,"iЕі","ius","ДЇ"),r},inputTooShort:function(t){var n=t.minimum-t.input.length,r="Д®raЕЎykite dar "+n+" simbol";return r+=e(n,"iЕі","ius","ДЇ"),r},loadingMore:function(){return"Kraunama daugiau rezultatЕівЂ¦"},maximumSelected:function(t){var n="JЕ«s galite pasirinkti tik "+t.maximum+" element";return n+=e(t.maximum,"Еі","us","Д…"),n},noResults:function(){return"AtitikmenЕі nerasta"},searching:function(){return"IeЕЎkomaвЂ¦"}}}),{define:e.define,require:e.require}})();
/*! Select2 4.0.0 | https://github.com/select2/select2/blob/master/LICENSE.md */

(function(){if(jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var e=jQuery.fn.select2.amd;return e.define("select2/i18n/bg",[],function(){return{inputTooLong:function(e){var t=e.input.length-e.maximum,n="РњРѕР»СЏ РІСЉРІРµРґРµС‚Рµ СЃ "+t+" РїРѕ-РјР°Р»РєРѕ СЃРёРјРІРѕР»";return t>1&&(n+="a"),n},inputTooShort:function(e){var t=e.minimum-e.input.length,n="РњРѕР»СЏ РІСЉРІРµРґРµС‚Рµ РѕС‰Рµ "+t+" СЃРёРјРІРѕР»";return t>1&&(n+="a"),n},loadingMore:function(){return"Р—Р°СЂРµР¶РґР°С‚ СЃРµ РѕС‰РµвЂ¦"},maximumSelected:function(e){var t="РњРѕР¶РµС‚Рµ РґР° РЅР°РїСЂР°РІРёС‚Рµ РґРѕ "+e.maximum+" ";return e.maximum>1?t+="РёР·Р±РѕСЂР°":t+="РёР·Р±РѕСЂ",t},noResults:function(){return"РќСЏРјР° РЅР°РјРµСЂРµРЅРё СЃСЉРІРїР°РґРµРЅРёСЏ"},searching:function(){return"РўСЉСЂСЃРµРЅРµвЂ¦"}}}),{define:e.define,require:e.require}})();
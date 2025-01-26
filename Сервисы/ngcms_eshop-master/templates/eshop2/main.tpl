[TWIG]
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{ lang['langcode'] }}" lang="{{ lang['langcode'] }}" dir="ltr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset={{ lang['encoding'] }}" />
    <meta http-equiv="content-language" content="{{ lang['langcode'] }}" />
    <meta name="generator" content="{{ what }} {{ version }}" />
    <meta name="document-state" content="dynamic" />
    {{ htmlvars }}
    
    <link rel="stylesheet" href="{{ tpl_url }}/css/style.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/colorscheme.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/color.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/adaptive.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/font-awesome.min.css">
    {% if pluginIsActive('rss_export') %}
    <link href="{{ home }}/rss.xml" rel="alternate" type="application/rss+xml" title="RSS" />
    {% endif %}
    <script src="{{ tpl_url }}/js/jquery-1.8.3.min.js"></script>
    
    <script type="text/javascript" src="{{ tpl_url }}/js/jquery.dropdown.min.js"></script>
    <link href="{{ tpl_url }}/css/jquery.dropdown.min.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="{{ tpl_url }}/js/cusel_original.js"></script>

    <!-- <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript"></script> -->

    <script type="text/javascript">
      var locale = "";
    </script>
    
    <script type="text/javascript">
      var curr = '$',
          cartItemsProductsId = ["1035"],
          nextCs = '€',
          nextCsCond = nextCs == '' ? false : true,
          pricePrecision = parseInt(''),
          checkProdStock = "", //use in plugin plus minus
          inServerCompare = parseInt("0"),
          inServerWishList = parseInt("0"),
          countViewProd = parseInt("6"),
          theme = "http://fluid.imagecmsdemo.net/templates/fluid/",
          siteUrl = "http://fluid.imagecmsdemo.net/",
          colorScheme = "css/color_scheme_1",
          isLogin = "0" === '1' ? true : false,
          typePage = "main",
          typeMenu = "row";
      text = {
        search: function(text) {
          return 'Введите более' + ' ' + text + ' символов';
        }
        ,
        error: {
          notLogin: 'В список желаний могут добавлять только авторизированные пользователи',
          fewsize: function(text) {
            return 'Выберите размер меньше или равно' + ' ' + text + ' пикселей';
          }
          ,
          enterName: 'Введите название'
        }
      }
        
        text.inCart = 'В корзине';
  text.pc = 'шт.';
  text.quant = 'Кол-во:';
  text.sum = 'Сумма:';
  text.toCart = 'Купить';
  text.pcs = 'Количество:';
  text.kits = 'Комплектов:';
  text.captchaText = 'Код протекции';
  text.plurProd = ['товар', 'товара', 'товаров'];
  text.plurKits = ['набор', 'набора', 'наборов'];
  text.plurComments = ['отзыв', 'отзыва', 'отзывов'];
      </script>
      
      <script src="{{ tpl_url }}/js/settings.js"></script>
      <script src="{{ tpl_url }}/js/_adaptive.js"></script>
      <script src="{{ tpl_url }}/js/jquery.cycle.all.min.js"></script>
      
      <script type="text/javascript" src="{{ scriptLibrary }}/functions.js">
      </script>
      <script type="text/javascript" src="{{ scriptLibrary }}/ajax.js">
      </script>
      <title>
        {{ titles }}
      </title>
      <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">
      <!--[if lte IE 9]>
<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js">
</script>
<![endif]-->
      <!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" href="{{ tpl_url }}/css/lte_ie_8.css" />
<![endif]-->
      <!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="{{ tpl_url }}/css/ie_7.css" />
<![endif]-->
  </head>
  
  <body class="isChrome not-js main">
    <script>
      if ('ontouchstart' in document.documentElement)
        document.body.className += ' isTouch';
      else
        document.body.className += ' notTouch';
    </script>
    
    <script>
      var langs = new Object();
      function lang(value) {
        return  langs[value];
      }
    </script>
    
    
    <div id="loading-layer">
      <img src="{{ tpl_url }}/img/loading.gif" alt="" />
    </div>
    <div class="main-body">
      <div class="fon-header">
        <header>
          <div class="top-header">
            <div class="container">
              <nav class="left-header">
                <ul class="nav nav-default-inline mq-w-768 mq-max mq-block" data-mq-max="768" data-mq-min="0" data-mq-target="#topMenuInMainMenu">
                  
                  <li>
                    <a href="{{home}}" target="_self" title="О компании">
                      О компании
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}" target="_self" title="Доставка">
                      Доставка
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}" target="_self" title="Оплата">
                      Оплата
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}" target="_self" title="Новости">
                      Новости
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}" target="_self" title="Бренды">
                      Бренды
                    </a>
                  </li>
                  
                  <li>

                    <a title="Валюты" href="#" data-jq-dropdown="#jq-dropdown-1">
                      Валюты
                    </a>
                  </li>
                  
                </ul>
                <ul class="nav nav-default-inline mq-w-768 mq-min mq-block">
                  <li>
                    <button type="button" data-drop="#topMenuInMainMenu" data-place="noinherit" data-overlay-opacity="0" class="isDrop">
                      Меню                    
                  </button>
                  <ul class="drop drop-style" id="topMenuInMainMenu">
                  </ul>
              </li>
          </ul>
      </nav>
      <ul class="items items-user-toolbar-top">
        <li>
          
          <div class="wish-list-btn tinyWishList">
            <button data-href="{{home}}/wishlist" class="isDrop">
              <span class="js-empty empty" style="display: block">
                <span class="icon_wish_list">
                </span>
                <span class="text-el">
                  Избранные 
                </span>
              </span>
              <span class="js-no-empty no-empty" style="display: none;">
                <span class="icon_wish_list">
                </span>
                <span class="text-el">
                  Избранные 
                </span>
                <span class="wishListCount">
                  0
                </span>
              </span>
            </button>
          </div>
          
          
        </li>
        <li class="compare-button">
            {{ eshop_compare }}
        </li>
        <!--Start. Top menu and authentication data block-->
        <li class="btn-personal-area">
          {% if not (global.flags.isLogged) %}
          <button type="button" onclick="location = '{{home}}/login/'">
            <span class="icon_enter">
            </span>
            <span class="text-el">
              Вход
            </span>
          </button>
          {% else %}
          <button type="button" data-jq-dropdown="#jq-dropdown-2">
            <span class="icon_profile"></span>
            <span class="text-el">Профиль</span>
          </button>
          {% endif %}
        </li>
        <!--Else show link for personal cabinet -->
        <!--End. Top menu and authentication data block-->
      </ul>
                          </div>
                      </div>
                      <div class="content-header">
                        <div class="container">
                          <div class="left-content-header t-a_j">
                            <!--        Logo-->
                            <div class="logo-out"><a href="{{home}}">
                              <span class="logo">
                                <img src="{{ tpl_url }}/img/logo.png" alt="logo">
                              </span></a>
                            </div>
          <!--                Start. contacts block-->
          <div class="top-search">
            <button class="small-search-btn">
              <span class="icon_small_search">
              </span>
            </button>
            <form name="search" method="post" action="{{home}}/eshop/search/" class="search-form">
              <input type="text" class="input-search" id="inputString" name="keywords" autocomplete="off" value="" placeholder="Поиск по сайту">
              <span class="btn-search">
                <button type="submit">
                  <span class="icon_search">
                  </span>
                </button>
              </span>
              <div class="icon_times_drop">
              </div>
              <div id="suggestions" class="drop drop-search">
              </div>
            </form>
          </div>
          <div class="phones-header">
            <!-- 
            <div class="frame-ico">
              <span class="icon_phone_header">
              </span>
            </div>
            <div>
              <div class="f-s_0">
                <span class="phone">
                  <span class="phone-number">
                    0 (800) 567-43-21
                  </span>
                  <span class="phone-number">
                    0 (800) 567-43-21
                  </span>
                </span>
              </div>
              <div class="btn-order-call">
                <button data-drop="#ordercall" data-tab="true" data-source="{{home}}/shop/callback" class="isDrop">
                  <span class="icon_order_call">
                  </span>
                  <span class="text-el d_l">
                    Заказать звонок
                  </span>
                </button>
              </div>
            </div>
          -->
          </div>
          <div class="frame-time-work">

              {{ system_flags.eshop.description_phones }}
            <!--
            <div class="frame-ico">
              <span class="icon_work">
              </span>
            </div>
            <div>
                <div>
                  Работаем: 
                  <span class="text-el">
                    Пн–Пт 09:00–20:00,
                    <br>
                    Сб 09:00–17:00, Вс выходной
                  </span>
                </div>
            </div>
            -->
          
          </div>
          <!-- End. Contacts block-->
          <!-- Start. Include cart data template-->
          <div id="tinyBask" class="frame-cleaner">
            
            {{ eshop_ebasket }}

          </div>
          <!-- End. Include cart data template-->
      </div>
  </div>
                      </div>
                  </header>
                  <div class="frame-menu-main horizontal-menu">
                    <!--    menu-row-category || menu-col-category-->
                    <div class="container">
                      <div class="menu-main menu-row-category">
                        <nav>
                          <table>
                            <tbody>
                              <tr>
                                <td class="mq-w-480 mq-min mq-table-cell">
                                  <div class="frame-item-menu-out frameItemMenu">
                                    <div class="frame-title is-sub">
                                      <span class="title title-united">
                                        <span class="helper" style="height: 49px;">
                                        </span>
                                        <span class="text-el">
                                          Каталог
                                        </span>
                                      </span>
                                      <span class="icon-is-sub">
                                      </span>
                                    </div>
                                    <div class="frame-drop-menu left-drop" id="unitedCatalog" style="display: none; left: 0px;">
                                    </div>
                                  </div>
                                </td>
                                <td data-mq-max="768" data-mq-min="0" data-mq-target="#unitedCatalog" class="mq-w-480 mq-max mq-table-cell">
                                  <table>
                                    <tbody>
                                      <tr>
                                        <td>
                                          <div class="frame-item-menu frameItemMenu">
                                            <div class="frame-title is-sub">
                                              <a class="title">
                                                <span class="helper" style="height: 49px;">
                                                </span>
                                                <span class="text-el">
                                                  Категории
                                                </span>
                                              </a>
                                            </div>

                                            <div class="frame-drop-menu left-drop">

                                                {{ callPlugin('eshop.show_catz_tree') }}

                                              <!--
                                              <ul class="items">
                                                <li class="column_0">
                                                  <a href="{{home}}/shop/category/telefoniia-pleery-gps/telefony" title="Телефоны" class="title-category-l1  is-sub">
                                                    <span class="helper">
                                                    </span>
                                                    <span class="text-el">
                                                      Телефоны
                                                    </span>
                                                  </a>
                                                  <div class="frame-l2">
                                                    <ul class="items">
                                                      <li class="column2_0">
                                                        <a href="{{home}}/shop/category/telefoniia-pleery-gps/telefony/mobilnye-telefony" title="Мобильные телефоны">
                                                          Мобильные телефоны
                                                        </a>
                                                      </li>
                                                    </ul>
                                                  </div>
                                                </li>
                                              </ul>
                                              -->
                                            
                                            </div>
                                            
                                          </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}" title="Домашнее видео" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Домашнее видео
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}" title="Детские товары" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Детские товары
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}" title="Активный отдых и туризм" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Активный отдых и туризм
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}" title="Музыкальные инструменты" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Музыкальные инструменты
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      
                                  </tr>
                              </tbody>
                          </table>
                      </td>
                  </tr>
              </tbody>
          </table>
      </nav>
  </div>
                      </div>
                      <script>
                        function beforeShowSearch(el, drop) {
                          el.closest('td').prevAll().hide();
                          el.css('position', 'static');
                          drop.removeAttr('style');
                        }
                        function afterHideSearch(el, drop) {
                          el.closest('td').find('form').hide();
                          el.closest('td').prevAll().show().css('display', '');
                          el.css('position', '');
                        }
                        function closedSearch(el, drop){
                          drop.removeAttr('style');
                        }
                      </script>
                  </div>
                {% if isHandler('news:main') %}
                  <div class="frame-baner frame-baner-start_page">
                    <section class="carousel-js-css baner container cycleFrame resize is-small" style="height: 218px;">
                      <!--remove class="resize" if not resize-->
                      <div class="p_r" style="height: 100%;">
                        <div class="content-carousel">
                          <div class="f_l">
                            <p>
                              <a href="{{home}}">
                                <img src="{{ tpl_url }}/img/1.jpg" alt="">
                              </a>
                            </p>
                          </div>
                          <div class="f_r">
                            <p>
                              <a href="{{home}}">
                                <img src="{{ tpl_url }}/img/3.jpg" alt="">
                              </a>
                            </p>
                            <p>
                              <a href="{{home}}">
                                <img src="{{ tpl_url }}/img/4.jpg" alt="">
                              </a>
                            </p>
                          </div>
                          <ul class="cycle">
                            <!--remove class="cycle" if not cycle-->
                            <li>
                              <a href="{{home}}">
                                <img src="{{ tpl_url }}/img/2.jpg" alt="" style="display: inline;">
                              </a>
                            </li>
                          </ul>
                          
                          <div class="pager">
                          </div>
                          <script>
                            langs["Message"] = 'Сообщение';
                            langs["Banner (s) successfully removed"] = 'Баннер (ы) успешно удален(ы)';
                          </script>
                          
                        </div>
                      </div>
                      <div class="group-button-carousel">
                        <button type="button" class="prev arrow">
                          <span class="icon_arrow_p">
                          </span>
                        </button>
                        <button type="button" class="next arrow">
                          <span class="icon_arrow_n">
                          </span>
                        </button>
                      </div>
                    </section>
                  </div>
                {% endif %}
            </div>
            
            
            <div class="content">
              {% if isHandler('news:main') %}
              <div class="page-main">
                <div class="container f-s_0">
                  <ul class="tabs tabs-special-proposition">
                    <li class="active">
                      <button data-href="#popular_products" type="button">
                        Хиты
                      </button>
                    </li>
                    <li>
                      <button data-href="#new_products" type="button">
                        Новинки
                      </button>
                    </li>
                    <li>
                      <button data-href="#action_products" type="button">
                        Спецпредложения
                      </button>
                    </li>
                  </ul>
                  <div class="frame-tabs-ref">
                    <div id="popular_products">
                      <div class="horizontal-carousel">
                        <section class="special-proposition">
                          <div class="big-container">
                            <div class="carousel-js-css items-carousel jcarousel-container jcarousel-container-horizontal iscarousel" style="position: relative; display: block;">
                              <div class="content-carousel container">
                                <div class="jcarousel-clip jcarousel-clip-horizontal" style="position: relative;">
                                  <ul class="items items-catalog items-h-carousel items-product animateListItems jcarousel-list jcarousel-list-horizontal" style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 1904px;">
                                    
                                    {{ callPlugin('eshop.show', {'number' : 10, 'mode' : 'stocked', 'template': 'block_eshop'}) }}

                                  </ul>
                                </div>
                              </div>
                              <div class="group-button-carousel">
                                <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                                  <span class="icon_arrow_p">
                                  </span>
                                </button>
                                <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                                  <span class="icon_arrow_n">
                                  </span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                    
                    <div id="new_products" style="">
                      <div class="horizontal-carousel">
                        <section class="special-proposition">
                          <div class="big-container">
                            <div class="items-carousel carousel-js-css">
                              <div class="content-carousel container">
                                <ul class="items items-catalog items-h-carousel items-product animateListItems">
                                  
                                  {{ callPlugin('eshop.show', {'number' : 10, 'mode' : 'last', 'template': 'block_eshop'}) }}

                                </ul>
                              </div>
                              <div class="group-button-carousel">
                                <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                                  <span class="icon_arrow_p">
                                  </span>
                                </button>
                                <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                                  <span class="icon_arrow_n">
                                  </span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                    
                    <div id="action_products" style="">
                      <div class="horizontal-carousel">
                        <section class="special-proposition">
                          <div class="big-container">
                            <div class="items-carousel carousel-js-css">
                              <div class="content-carousel container">
                                <ul class="items items-catalog items-h-carousel items-product animateListItems">
                                  
                                  {{ callPlugin('eshop.show', {'number' : 10, 'mode' : 'featured', 'template': 'block_eshop'}) }}

                                </ul>
                              </div>
                              <div class="group-button-carousel">
                                <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                                  <span class="icon_arrow_p">
                                  </span>
                                </button>
                                <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                                  <span class="icon_arrow_n">
                                  </span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                  </div>
                  
                {{ callPlugin('xnews.show', { 'template' : 'xnews1', 'extractEmbeddedItems' : 1, 'count' : '3'}) }}

                </div>
              </div>
              {% else %}
              
              {{ breadcrumbs }}

              {{ mainblock }}
              {% endif %}
              
              {% if isHandler('news:main') %}
              <div class="container">
                <div id="ViewedProducts">
                </div>
              </div>
                <script>
                $(document).ready(function() {
                    
                    var page_stack = br.storage.get('page_stack');
                    
                    if(page_stack != null) {
                        page_stack_str = page_stack.join(",");

                        rpcEshopRequest('eshop_viewed', {'action': 'show', 'page_stack':page_stack_str }, function (resTX) {
                            $('#ViewedProducts').html(resTX['update']);
                            $('#viewed_list').height(100);
                            $('#viewed_list').width(20000);
                            $('.jcarousel-clip').css( "position", "relative" ); 
                        });
                        
                    }

                });    
                </script>
              {% endif %}
            </div>
            <div class="h-footer footer-main" data-mq-prop="height" data-mq-prop-pool="height" data-mq-elem-pool="footer" style="height: 456px;">
            </div>
          </div>
          <footer class="footer-main" data-mq-prop="margin-top" data-mq-prop-pref="-" data-mq-prop-pool="height" data-mq-elem-pool="footer" style="margin-top: -456px;">
            {% if isHandler('news:main') %}{% endif %}
            <div class="content-footer">
              <div class="container">
                <div class="frame-box23">
                  <div class="box-3">
                    <div class="inside-padd">
                      <div class="title-h1">
                        <a href="{{home}}" class="t-d_n f-s_0 s-all-d">
                          <span class="text-el">
                            Бренды
                          </span>
                          <span class="icon_arrow">
                          </span>
                        </a>
                      </div>
                      <div class="horizontal-carousel">
                        <div class="big-container frame-brands">
                          <div class="items-carousel">
                            <div class="frame-brands-carousel">
                              <div class="content-carousel">
                                <ul class="items items-brands">
                                  <li>
                                    <a href="{{home}}" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img src="{{tpl_url}}/img/brands/pioneer.png" title="Pioneer" alt="Pioneer">
                                      </span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="{{home}}" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img src="{{tpl_url}}/img/brands/sony.png" title="Sony" alt="Sony">
                                      </span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="{{home}}" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img src="{{tpl_url}}/img/brands/apple.png" title="Apple" alt="Apple">
                                      </span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="{{home}}" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img src="{{tpl_url}}/img/brands/samsung.png" title="Samsung" alt="Samsung">
                                      </span>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div class="group-button-carousel">
                              <button type="button" class="prev arrow">
                                <span class="icon_arrow_p">
                                </span>
                              </button>
                              <button type="button" class="next arrow">
                                <span class="icon_arrow_n">
                                </span>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--Start. Load menu in footer-->
                <div class="box-1">
                  <div class="inside-padd">
                    <div class="frame-seo-text">
                      <div class="text seo-text">
                        <h1>
                          Интернет-магазин электронной техники
                        </h1>
                        <p>
                          Интернет-магазин – это интерактивный сайт с каталогом, в котором представляются товары и услуги, а также корзиной для формирования заказа. В правильном интернет-магазине обязательно должны присутствовать : рекламируемые товары и услуги, контактная информация, предложения различных вариантов оплаты, предоставление счета. Работа интернет-магазина похожа на работу простого магазина. Клиент, просматривая каталог, помещает интересующую его позицию в корзину.
                        </p>
                        <p>
                          Интернет-магазин – это интерактивный сайт с каталогом, в котором представляются товары и услуги, а также корзиной для формирования заказа. В правильном интернет-магазине обязательно должны присутствовать : рекламируемые товары и услуги, контактная информация, предложения различных вариантов оплаты, предоставление счета. Работа интернет-магазина похожа на работу простого магазина. Клиент, просматривая каталог, помещает интересующую его позицию в корзину.
                        </p>
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="footer-footer">
              <div class="container">
                <div class="inside-padd t-a_j">
                
                <!--
                <div>
                    <div class="footer-social">
                        <div class="footer-title">Мы в соцсетях</div>
                        <div class="f-s_0">
                                                        <a rel="nofollow" href="#" class="icon-vk-link"></a>
                                                                                    <a rel="nofollow" href="#" class="icon-fb-link"></a>
                                                                                    <a rel="nofollow" href="#" class="icon-tw-link"></a>
                                                    </div>
                    </div>
                    <div class="footer-payment">
                        <div class="footer-title">Мы принимаем</div>
                        <div class="f-s_0">
                            <span class="icon-visa"></span>
                            <span class="icon-mastercard"></span>
                        </div>
                    </div>
                </div>
                -->
                
                  <div class="frame-type-company">
                    <div class="c_b f-w_b">
                      © YourStore 2015
                    </div>
                    <div class="c_9">
                      Все права защищены.
                    </div>
                    <div class="engine c_9">
                    </div>
                  </div>
                
                  
                  <div class="footer-menu-stat">
                    <ul class="nav nav-vertical">
                      
                      <li>
                        <a href="{{home}}" target="_self" title="О компании">
                          О компании
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}" target="_self" title="Доставка">
                          Доставка
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}" target="_self" title="Оплата">
                          Оплата
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}" target="_self" title="Новости">
                          Новости
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}" target="_self" title="Бренды">
                          Бренды
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}" target="_self" title="Контакты">
                          Контакты
                        </a>
                      </li>
                      
                    </ul>
                  </div>
                  <div class="footer-category-menu">
                    <ul class="nav nav-vertical">
                      <li>
                        <a href="{{home}}" title="Телефония, МР3-плееры, GPS" class="title">
                          Телефония, МР3-плееры, GPS
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}" title="Домашнее видео" class="title">
                          Домашнее видео
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}" title="Детские товары" class="title">
                          Детские товары
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}" title="Активный отдых и туризм" class="title">
                          Активный отдых и туризм
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}" title="Музыкальные инструменты" class="title">
                          Музыкальные инструменты
                        </a>
                      </li>
                      
                    </ul>
                  </div>
                  
                  <div class="items-contact-footer">
                    <ul class="items-contact">
                      <li>
                        <span class="f-s_0">
                          <span class="text-el">
                            0 (800) 567-43-21, 
                          </span>
                          <span class="text-el">
                            0 (800) 567-43-21
                          </span>
                        </span>
                      </li>
                      <li>
                        <span class="f-s_0">
                          <span class="text-el">
                            E-mail: partner@imagecms.net
                          </span>
                        </span>
                      </li>
                      <li>
                        <span class="f-s_0">
                          <span class="text-el">
                            Skype: imagecms
                          </span>
                        </span>
                      </li>
                    </ul>
                  </div>
                  <div class="work-footer">
                    <div>
                      Работаем: 
                      <span class="text-el">
                        Пн–Пт 09:00–20:00,
                        <br>
                        Сб 09:00–17:00, Вс выходной
                      </span>
                    </div>
                  </div>
                  <div class="footer-profile">
                    <ul class="nav nav-vertical">
                      <li>
                        <button type="button" onclick="location = '{{home}}/login/'" title="Вход">
                          Вход
                        </button>
                      </li>
                      <li>
                        <button onclick="location = '{{home}}/register/'" title="Регистрация">
                          Регистрация
                        </button>
                      </li>
                      <li>
                        <button type="button" title="Обратный звонок">
                          Обратный звонок
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              
              <!--
                <div class="frame-type-company">
                    <span class="f_r">
                        Магазин работает на 
                        <a href="http://www.imagecms.net/">ImageCMS<span class="icon-imagecms"></span></a>
                    </span>
                    <span>
                        © 2014-2015, Интернет-магазин Full Market. Все права защищены.
                    </span>
                </div>
              -->
              </div>
            </div>
          </footer>
 
          <script src="{{ tpl_url }}/js/jquery.equalhorizcell.js"></script>
          <script src="{{ tpl_url }}/js/compare.js"></script>
          <script src="{{ tpl_url }}/js/united_scripts.js"></script>
  
          <script type="text/javascript">
            init();
            //initDownloadScripts(['united_scripts'], 'init', 'scriptDefer');
          </script>
          
          <button type="button" id="showCartPopup" data-drop="#popupCart" style="display: none;" class="isDrop">
          </button>
          <div class="drop-bask drop drop-style" id="popupCart">
          </div>
          
          <span class="tooltip">
          </span>
          <div class="apply">
            <div class="content-apply">
              <a href="{{home}}/#">
                Фильтр
              </a>
              <div class="description">
                Найдено 
                <span class="f-s_0">
                  <span id="apply-count">
                    5
                  </span>
                  <span class="text-el">
                    &nbsp;
                  </span>
                  <span class="plurProd">
                  </span>
                </span>
              </div>
            </div>
            <button type="button" class="icon_times_drop icon_times_apply">
            </button>
          </div>
          <div class="drop drop-style" id="notification">
            <div class="drop-content-notification">
              <div class="inside-padd notification">
                
              </div>
            </div>
            <div class="drop-footer">
            </div>
          </div>
          <button style="display: none;" type="button" data-drop="#notification" data-modal="true" data-effect-on="fadeIn" data-effect-off="fadeOut" class="trigger isDrop">
          </button>
          
          <div class="drop drop-style" id="confirm">
            <div class="drop-header">
              <div class="title">
                Подтвердите
              </div>
            </div>
            <div class="drop-content-confirm">
              <div class="inside-padd cofirm w-s_n-w">
                <div class="btn-def">
                  <button type="button" data-button-confirm="" data-modal="true">
                    <span class="text-el">
                      Подтвердить
                    </span>
                  </button>
                </div>
                <div class="btn-cancel">
                  <button type="button" data-closed="closed-js">
                    <span class="text-el d_l_1">
                      Отменить
                    </span>
                  </button>
                </div>
              </div>
            </div>
            <div class="drop-footer">
            </div>
          </div>
          <button style="display: none;" type="button" data-drop="#confirm" data-confirm="true" data-effect-on="fadeIn" data-effect-off="fadeOut" class="isDrop">
          </button>
          
          <div class="drop drop-style" id="dropAuth">
            <button type="button" class="icon_times_drop" data-closed="closed-js">
            </button>
            <div class="drop-content t-a_c" style="width: 350px;min-height: 0;">
              <div class="inside-padd">
                Для того, что бы добавить товар в список желаний, Вам нужно 
                <button type="button" class="d_l_1 isDrop" data-drop=".drop-enter" data-source="{{home}}/auth">
                  авторизоваться
                </button>
              </div>
            </div>
          </div>
                    
          {{ eshop_ebasket_notify }}

        <div id="jq-dropdown-1" class="jq-dropdown jq-dropdown-tip">
            <ul class="jq-dropdown-menu">
                {% for cc in system_flags.eshop.currency %}
                    <li{% if (system_flags.eshop.current_currency.id == cc.id) %} class="active"{% endif %}><a href="{{ cc.currency_link }}">{{ cc.code }}</a></li>
                {% endfor %}
            </ul>
        </div>

        {% if (global.flags.isLogged) %}
        <div id="jq-dropdown-2" class="jq-dropdown jq-dropdown-tip">
            <ul class="jq-dropdown-menu">
                {% if (global.user.status == 1) %}<li><a href="{{admin_url}}">Админка</a></li>{% endif %}
                <li><a href="{{home}}/profile.html">Основные данные</a></li>
                <li><a href="{{home}}/logout/">Выход</a></li>
            </ul>
        </div>
        {% endif %}

<style>
.pull-right {
    float: right;
}

.btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}


.ratingtypeplus {
    color: #6c838e;
    padding: 0 0 0 5px;
}
    
.ratebox2 {
line-height:30px;
color:#6A920F;

background: #ddd;

border: 1px solid transparent;
}

.ratebox2 i {
color:#C1C1C1;
}

.rate {
font-size:18px;
}

.ratebox2:hover .btn {
background:#f5f5f5;
}

.ratebox2:hover i {
color:#E83564;
}

.rate i:hover {
color:#D9534F;
}

.rplus i {
font-size:18px;
color:#C1C1C1;
}

.rplus {
font-size:16px;
color:#6A920F;
}
</style>

<script>
$(document).ready(function() {

    $(".icon_times_drop, #basket_back").click(function(e){
        $(".forCenter").css("display", "none");
        $(".overlayDrop").css("display", "none");
        
        $(".forCenter_fastOrder").css("display", "none");
        $(".overlayDrop_fastOrder").css("display", "none");
        
        $(".forCenter_fastPrice").css("display", "none");
        $(".overlayDrop_fastPrice").css("display", "none");
        
        e.preventDefault();
    });
    
    $(".orderBut").click(function(e){
        var id = $(this).attr('data-id');
        var count = $("input[name='quantity']").attr('value');
        if( count == undefined) {
            count = 1;
        }

        rpcEshopRequest('eshop_ebasket_manage', {'action': 'add', 'ds':1,'id':id,'count':count }, function (resTX) {
            document.getElementById('tinyBask').innerHTML = resTX['update'];
            $(".forCenter").css("display", "block");
            $(".overlayDrop").css("display", "block");
            e.preventDefault();
        });

    });
    
    $(".btnCompare").click(function(e){
        var id = $(this).attr('data-id');
        var bl = $(this);
        
        if( bl.hasClass("active") ) {

            rpcEshopRequest('eshop_compare', {'action': 'remove', 'id':id }, function (resTX) {
                bl.removeClass("active");
                bl.find(".niceCheck").removeClass("active");
                bl.find("input:checkbox").prop('checked', false);
                $('.compare-button').html(resTX['update']);
            });
            
        }
        else {

            rpcEshopRequest('eshop_compare', {'action': 'add', 'id':id }, function (resTX) {
                bl.addClass("active");
                bl.find(".niceCheck").addClass("active");
                bl.find("input:checkbox").prop('checked', true);
                $('.compare-button').html(resTX['update']);
            });

        }

    });

    $(".deleteFromCompare").click(function(e){
        var id = $(this).attr('data-id');

        rpcEshopRequest('eshop_compare', {'action': 'remove', 'id':id }, function (resTX) {
            location.reload();
        });

    });
    
    $(".ratebox2").click(function() {
        
        var id = $(this).attr('data-id');

        rpcEshopRequest('eshop_likes_result', {'action': 'do_like', 'id' : id }, function (resTX) {
            $(".ratebox2").html(resTX['update']);
        });

    });
 
});    

</script>
          <div id="fancybox-loading">
            <div>
            </div>
          </div>
          <div id="fancybox-loading">
            <div>
            </div>
          </div>
  </body>
</html>
[debug]
{debug_queries}<br/>{debug_profiler}
[/debug]
[/TWIG]

   <div class="bt">
        <div class="blt">
        </div>
        <div class="brt">
        </div>
        <div id="ctl00_ctl21_divDetails" class="bct">{name-championat-title} <!--{start-championat} / {finish-championat}--></div>
        <div class="clr">
        </div>
    </div>
    <div class="bc">
        <div class="bcl">
            <div class="bcr">
                <div class="bcc">

                    <div id="ctl00_ctl21_mcteam1" class="mcteam">
                        <div class="mclp">

                            <div class="mline"><center> {williamhill}</center>
Автоматичне оновлення <select id="autoreload" name="reload_interval" onchange="setReloadTimeout(this.value);" style="margin: 10px;">
						<option value="60" >1 хв.</option>
						<option value="120" >2 хв.</option>
						<option value="180" >3 хв.</option>
						<option value="300" >5 хв.</option>
						<option value="600" >10 хв.</option>
						<option value="-1">Вимкнено</option>
					</select>
Переглядів: {views}
<div class="clr"></div>
 <table cellpadding="1" cellspacing="1" width="100%">
                                    <tr>
<td align="left"><img src="{photo-owner}" width="85" /></td>
<td width="160" class="scorel-green"> <a href="{link-team-owner}">{team-owner} {reds_owner}</a> </td>
<td width="130" class="scorel-green" {line_height}><center><h1>{goals-owner}:{goals-guest}</h1>{penalti}{status}<br></center></td>
<td width="160" class="scorel-green"> <a href="{link-team-guest}">{reds_guest} {team-guest}</a> </td>
<td align="right"><img src="{photo-guest}" width="85" /></td>
                                    </tr>
                                </table><center><b><a href="https://kim-affiliates.g2afse.com/click?pid=544&offer_id=42" target="_blank"><u>Перейти на сайт партнера</u></a></b></center>
                            </div>
                            <div class="clr"></div>
 <div align="center" class="bline3">
{date} , {channel}  [if-championat] {round} тур. [/if-championat] {name-championat}
</div>
<div class="clr"></div>
<table cellpadding="2" cellspacing="2" width="100%">
<tr >
<td style="padding: 2px 5px;" class="bline4" valign="top" align="left" rowspan="2">
{players-owner}
{coach-owner}
</td>
<td style="padding: 2px 5px; width: 175px;" class="bline4" valign="top" align="right">{players-goals-owner}</td>
<td style="padding: 2px 5px; width: 175px;" class="bline4" valign="top" align="left">{players-goals-guest}</td>
<td style="padding: 2px 5px;" class="bline4" valign="top" align="right" rowspan="2">
{players-guest}<br>
{coach-guest}
</td>
</tr>
<tr>
<td style="padding: 20px 5px; width: 175px;" class="bline4" valign="top" align="right">{players-penalti-owner}</td>
<td style="padding: 20px 5px; width: 175px;" class="bline4" valign="top" align="left">{players-penalti-guest}</td>
</tr>
</table>

<table width="100%" cellpadding="2" cellspacing="2">
    <tr>
        <td class="bline4" style="width: 50%;" align="left">{replasements-owner}</td>
        <td class="bline4" style="width: 50%;" align="right">{replasements-guest}</td>
    </tr>
</table>

<div class="clr"></div>
<div class="clr"></div>
<div class="clr"></div>
<div class="clr"></div>
                       </div>
                    </div>
              </div>
            </div>
        </div>
    </div>
    <div class="bb">
        <div class="blb">
        </div>
        <div class="brb">
        </div>
        <div class="bcb">

            {a_trans}

           <div id="ctl00_ctl21_sta" class="blockbuts">
                <div class="bsl">
                </div>
                <div class="bsr">
                </div>
                <div class="bsc">
                    {photo}</div>
                <div class="clr">
                </div>
            </div>

           <div id="ctl00_ctl21_ann" class="blockbuts">
                <div class="bsl">
                </div>
                <div class="bsr">
                </div>
                <div class="bsc">
                    {video}</div>
                <div class="clr">
                </div>
            </div>
            <div id="ctl00_ctl21_onl" class="blockbuts">
                <div class="bsl">
                </div>
                <div class="bsr">
                </div>
                <div class="bsc">
                    {stats}</div>
                <div class="clr">
                </div>
            </div>
       </div>
        <div class="clr">
        </div>
    </div>



<div class="toppad clr"></div>

	{% if (global.flags.isLogged and ((global.user['status'] == 1) or (global.user['status'] == 2))) %}
		{admin_link_match}
	{% endif %}

<br>
<center><script type="text/javascript"><!--
google_ad_client = "ca-pub-4577254435597104";
/* 600 */
google_ad_slot = "6648564412";
google_ad_width = 600;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="//pagead2.googlesyndication.com/pagead/show_ads.js">
</script></center>
<br>
{translation}
<br>{650}<br>


    <div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'httpsportanalyticcom'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>




<script type="text/javascript">
var windowOnReload;
var a = getCookie('windowSetReload');
if (Math.floor(a) >= 15) {
       windowOnReload = setTimeout('window.location.reload()', (a * 1000));
	   setSelect('autoreload', a);
}
function setSelect(id, value){
	var i,o;
	var sel = document.getElementById(id);
	var opt = sel.options;

	for(i=0; i<opt.length; i++){
		o = opt[i];
		if(o.value == value) {
			o.selected=true;
			break;
		}
	}
sel.style.display = 'none';
sel.style.display = '';
}
function setReloadTimeout(a) {
     if (a > 0) {
         setCookie('windowSetReload', a);
         var windowSetReload = a;
        if (Math.floor(windowSetReload) >= 15) {
             clearTimeout(windowOnReload);
             windowOnReload = setTimeout('window.location.reload()', (windowSetReload * 1000));
        }
     } else {
        deleteCookie('windowSetReload');
     }
}
</script>

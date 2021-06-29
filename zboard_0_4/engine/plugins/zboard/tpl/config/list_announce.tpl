<!-- List of news start here -->
<form action="/engine/admin.php?mod=extra-config&plugin=zboard&action=modify" method="post" name="zboard">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center" width="100%">
<tr align="left" class="contHead">
<td width="5%" nowrap>ID</td>
<td width="10%" nowrap>Р”Р°С‚Р°</td>
<td width="10%" nowrap>РљР°С‚РµРіРѕСЂРёСЏ</td>
<td>Р—Р°РіРѕР»РѕРІРѕРє</td>
<td width="10%">РђРІС‚РѕСЂ</td>
<td width="20%">РџРµСЂРёРѕРґ</td>
<td width="5%">РђРєС‚РёРІРµРЅ?</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" title="Р’С‹Р±СЂР°С‚СЊ РІСЃРµ" onclick="javascript:check_uncheck_all(zboard)" /></td>
</tr>
{entries}
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>

<tr align="center">
<td colspan="8" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">
Р”РµР№СЃС‚РІРёРµ: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
<option value="">-- Р”РµР№СЃС‚РІРёРµ --</option>
<option value="mass_approve">РђРєС‚РёРІРёСЂРѕРІР°С‚СЊ</option>
<option value="mass_forbidden">Р”РµР°РєС‚РёРІРёСЂРѕРІР°С‚СЊ</option>
<option value="" style="background-color: #E0E0E0;" disabled="disabled">===================</option><option value="mass_delete">РЈРґР°Р»РёС‚СЊ РѕР±СЉСЏРІР»РµРЅРёРµ</option>
</select>
<input type="submit" value="Р’С‹РїРѕР»РЅРёС‚СЊ.." class="button" />
<br/>
</div>
</td>
</tr>
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>
<tr>
<td align="center" colspan="8" class="contentHead">{pagesss}</td>
</tr>
</table>
</form>
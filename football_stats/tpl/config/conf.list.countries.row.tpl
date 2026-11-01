<tr align="center" class="contRow1">
<td><img src="/engine/plugins/football_stats/flags/{alias}.gif"> {name} <a href="/engine/admin.php?mod=football_stats&amp;action=championats&amp;cid={id}">{l_fs:country_championats}</a> <a href="/engine/admin.php?mod=football_stats&amp;action=teams&amp;cid={id}">{l_fs:country_teams}</a></td>
<td>{alias}</td>
<td>
<input type="image" src="{admin_url}/plugins/football_stats/tpl/images/edit.png" style="margin-right: 20px;" title="{l_fs:button_edit}"  onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=edit-country&amp;id={id}'" />&#160;
<input type="image" src="{admin_url}/plugins/football_stats/tpl/images/dell.png" title="{l_fs:button_dell}"  onmousedown="askDeleteCountry({id}, &#34;{name}&#34;)" />
</td>
</tr>
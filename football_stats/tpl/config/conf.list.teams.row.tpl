<tr align="center" class="contRow1">
<td>{name} <a href="/engine/admin.php?mod=football_stats&amp;action=edit_team_player&amp;tid={id}">Редактировать команду</a></td>
<td><img src="/engine/plugins/football_stats/flags/{alias}.gif"> {country_name}</td>
<td>
<input type="image" src="{admin_url}/plugins/football_stats/tpl/images/edit.png" title="{l_fs:button_edit}" style="margin-right: 20px;"  onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=edit-team&amp;id={id}'" />&#160;
<input type="image" src="{admin_url}/plugins/football_stats/tpl/images/dell.png" title="{l_fs:button_dell}"  onmousedown="askDeleteTeam({id}, &#39;{name}&#39;)" />
</td>
</tr>
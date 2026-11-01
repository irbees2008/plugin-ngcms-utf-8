{% if isHandler('news:main') %}
<tr>
<td class="contentEntry1"><center>{status} {date_mini}</center></td>
<td class="contentEntry1"><center>{channel}</center></td>
<td class="contentEntry1" style="text-align: right" >{reds_owner} <a href="{link-team-owner}">{team-owner}</a></td>		
<td class="contentEntry2"><a href="{link-game}"><center>{goals-owner}:{goals-guest}</a></center></td>
<td class="contentEntry1"><a href="{link-team-guest}">{team-guest}</a> {reds_guest}</td>	
</tr>
{% endif %}
{% if isHandler('news:by.category') %}
<tr>
<td class="contentEntry1"><center>{status} {date_mini}</center></td>
<td class="contentEntry1"><center>{channel}</center></td>
<td class="contentEntry1" style="text-align: right" >{reds_owner} <a href="{link-team-owner}">{team-owner}</a></td>		
<td class="contentEntry2"><a href="{link-game}"><center>{goals-owner}:{goals-guest}</a></center></td>
<td class="contentEntry1"><a href="{link-team-guest}">{team-guest}</a> {reds_guest}</td>	
</tr>
{% endif %}

{% if not isHandler('news:main|news:by.category|news:news') %}
<tr>
		<td class="cell3">{status} {date_mini}</td>
<td class="cell3">{% if game_started %}{reds_owner}{% endif %} <a href="{link-team-owner}">{team-owner}</a> - <a href="{link-team-guest}">{team-guest}</a> {% if game_started %}{reds_guest}{% endif %}</td>
	<td class="cell3"><center><a href="{link-game}">{goals-owner} : {goals-guest}</a></center></td>
</tr>
{% endif %}

{% if isHandler('news:news') %}
<tr>
<td class="contentEntry1"><center>{status} {date_mini}</center></td>
<td class="contentEntry1"><center>{channel}</center></td>
<td class="contentEntry1" style="text-align: right" >{reds_owner} <a href="{link-team-owner}">{team-owner}</a></td>		
<td class="contentEntry2"><a href="{link-game}"><center>{goals-owner}:{goals-guest}</a></center></td>
<td class="contentEntry1"><a href="{link-team-guest}">{team-guest}</a> {reds_guest}</td>	
</tr>
{% endif %}
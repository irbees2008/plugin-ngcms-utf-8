<style type="text/css">

table { 
border-bottom-width: 1px;
border-left-width: 1px;
border-right-width: 1px;
border-spacing: 1px;
border-top-width: 1px;
display: table;
text-indent: 0px;
}
 
.cells { 
background-color: #dddddd;
border-bottom-color: #dddddd;
border-bottom-style: solid;
border-bottom-width: 1px;
border-left-color: #dddddd;
border-left-style: solid;
border-left-width: 1px;
border-right-color: #dddddd;
border-right-style: solid;
border-right-width: 1px;
border-top-color: #dddddd;
border-top-style: solid;
border-top-width: 1px;
color: #000000);
font-family: "tahoma", sans-serif;
font-size: 11px;
font-style: normal;
font-variant: normal;
font-weight: 700;
line-height: normal;
padding-bottom: 4px;
padding-left: 4px;
padding-right: 4px;
padding-top: 4px;
text-align: left;
}

.cell { 
background-color: #f9f9f9;
border-color: #e9e9e9;
border-bottom-style: solid;
border-bottom-width: 1px;
border-left-style: solid;
border-left-width: 1px;
border-right-style: solid;
border-right-width: 1px;
border-top-style: solid;
border-top-width: 1px;
color: #000000;
font-family: "tahoma", sans-serif;

font-size: 11px;

font-style: normal;
font-variant: normal;
line-height: normal;
font-weight: 200;
padding-bottom: 1px;
padding-left: 3px;
padding-right: 1px;
padding-top: 1px;
text-align: left;
}

 </style>


<div class="bt">
        <div class="blt">
        </div>
        <div class="brt">
        </div>
        <div id="ctl00_ctl21_divDetails" class="bct">Чемпіонат</div>
        <div class="clr">
        </div>
    </div>



    <div class="bc">
        <div class="bcl">
            <div class="bcr">
                <div class="bcc">
 <div class="bline4">
<br> <center>{ads9}</center><br><br />
Інші сезони:<br />

<script>
function reload(){
	location = document.getElementById('other_championats').value;
}
</script>

<select size=1 onchange="reload()" name="other_championats" id="other_championats">
{other-championats}
</select>
<br />
<br />
<table class="score-table" style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
	<tr>
		<td class="cells">№</td>
		<td class="cells">Команда</td>
		<td class="cells">І</td>
		<td class="cells">В</td>
		<td class="cells">Н</td>
		<td class="cells">П</td>
		<td class="cells">М</td>
		<td class="cells">О</td>
	</tr>
	
	{entries_first}

</tbody>
</table>
<br />
<br />
<table class="score-table" style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
	<tr>
		<td class="cells">№</td>
		<td class="cells">Команда</td>
		<td class="cells">І</td>
		<td class="cells">В</td>
		<td class="cells">Н</td>
		<td class="cells">П</td>
		<td class="cells">М</td>
		<td class="cells">О</td>
	</tr>
	
	{entries_second}

</tbody>
</table>

<br>
{goal-scorers}
<br>
<center> {williamhill}</center>

{games}



<br><br>
    </div>        </div>       
  
</div></div>  </div>

    <div class="bb">
        <div class="blb">
        </div>
        <div class="brb">
        </div>
        <div class="bcb">
       </div>
        <div class="clr">
        </div>
    </div>



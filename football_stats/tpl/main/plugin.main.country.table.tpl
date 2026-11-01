<style type="text/css">

table { 
border-bottom-width: 0px;
border-left-width: 0px;
border-right-width: 0px;
border-spacing: 0px;
border-top-width: 0px;
display: table;
text-indent: 0px;
}
 
.score-table { 
background-attachment: scroll;
background-clip: border-box;
background-color: rgb(255, 255, 255);
background-image: none;
background-origin: padding-box;
background-position: 0% 0%;
background-repeat: repeat;
background-size: auto;
color: rgb(0, 0, 0);
font-family: "tahoma", sans-serif;
font-size: 11px;
font-style: normal;
font-variant: normal;
font-weight: 700;
line-height: 1.3em;
padding-bottom: 0px;
padding-left: 0px;
padding-right: 0px;
padding-top: 1px;
text-align: left;
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
padding-bottom: 5px;
padding-left: 5px;
padding-right: 5px;
padding-top: 5px;
text-align: left;
}

.cell_champo { 
border-color: #dddddd;
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
padding-bottom: 4px;
padding-left: 4px;
padding-right: 4px;
padding-top: 1px;
text-align: left;
}

.left { 
background-attachment: scroll;
background-clip: border-box;
background-color: rgb(255, 255, 255);
background-image: none;
background-origin: padding-box;
background-position: 0% 0%;
background-repeat: repeat;
background-size: auto;
border-right-color: rgb(238, 238, 239);
border-right-style: solid;
border-right-width: 1px;
width: 20%;
}
 
.pluginEntryUninstalled td { 
background-attachment: scroll;
background-clip: border-box;
background-color: rgb(89, 166, 236);
background-image: url("/engine/skins/default/images/1px.png");
background-origin: padding-box;
background-position: 0% 0%;
background-repeat: repeat-x;
background-size: auto;
border-bottom-color: rgb(238, 238, 238);
border-bottom-style: solid;
border-bottom-width: 1px;
color: rgb(255, 255, 255);
font-family: "tahoma", sans-serif;
font-size: 11px;
font-style: normal;
font-variant: normal;
font-weight: 400;
line-height: normal;
padding-bottom: 5px;
padding-left: 3px;
padding-right: 3px;
padding-top: 5px;
vertical-align: top;
 } 

.cell_champo a {text-decoration: underline;}
 </style>
 
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>

[country]<a href="{championat-link}">{championat-name}</a>[/country]
[not-country]<a href="{championat-link}">{championat-round}</a>[/not-country]<center> {williamhill}</center>

	<tr>
		<td class="cells" style="text-align: right;">№</td>
		<td class="cells" style="text-align: left;">Команда</td>
		<td class="cells" style="text-align: center;">І</td>
		<td class="cells" style="text-align: center;">О</td>
	</tr>
	
	{entries}

</tbody>
</table>
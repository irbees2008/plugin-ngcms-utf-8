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
 
.cells2 { 
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
font-size: 10px;
font-style: normal;
font-variant: normal;
font-weight: 700;
line-height: normal;
padding-bottom: 1px;
padding-left: 2px;
padding-right: 1px;
padding-top: 1px;
text-align: left;
}

.cell2 { 
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
font-size: 98%;
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

.cell3 { 
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
font-size: 90%;
font-style: normal;
font-variant: normal;
line-height: normal;
font-weight: 200;
padding-bottom: 1px;
padding-left: 1px;
padding-right: 1px;
padding-top: 1px;
text-align: left;
}

.contentEntry1 { 
color: #000000;
font-family: "tahoma", sans-serif;
font-size: 93%;
border-color: #eeeeee;
border-bottom-style: solid;
border-bottom-width: 1px;
border-left-style: solid;
border-left-width: 1px;
border-right-style: solid;
border-right-width: 1px;
border-top-style: solid;
border-top-width: 1px;
padding-bottom: 1px;
padding-left: 1px;
padding-right: 1px;
padding-top: 1px;
text-align: left;
}

.contentEntry2 { 
border-color: #eeeeee;
border-bottom-style: solid;
border-bottom-width: 1px;
border-left-style: solid;
border-left-width: 1px;
border-right-style: solid;
border-right-width: 1px;
border-top-style: solid;
border-top-width: 1px;
padding-bottom: 1px;
padding-left: 1px;
padding-right: 1px;
padding-top: 1px;
text-align: left;
}

.contentEntry2 a{ 
color: #000000;
font-family: "tahoma", sans-serif;
font-size: 93%;
text-decoration:underline;
}
.contentEntry1 a{
text-decoration:underline;
}
.contentEntry2 a:hover {text-decoration: none;}

 </style>

[country]<a href="{championat-link}">{championat-name}</a> , останній {championat-round} тур  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [/country]
[not-country]<a href="{championat-link}">{championat-round}</a>[/not-country]<center> {williamhill}</center>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
{% if isHandler('news:main') %}
<tr>
<td class="contentEntry1"style="text-align: center;" width="19%"><center><b>Дата</b></center></td>
<td class="contentEntry1"style="text-align: center;" width="10%"><center><b>Канал</b></center></td>
<td class="contentEntry1" style="text-align: right" width="29%"><b>Господарі</b></td>
<td class="contentEntry1" style="text-align: center;" width="8%"><b>Рах.</b></td>
<td class="contentEntry1" style="text-align: left;" width="29%"><b>Гості</b></td>
</tr>
{% endif %}
{% if isHandler('news:by.category') %}
<tr>
<td class="contentEntry1"style="text-align: center;" width="19%"><center><b>Дата</b></center></td>
<td class="contentEntry1"style="text-align: center;" width="10%"><center><b>Канал</b></center></td>
<td class="contentEntry1" style="text-align: right" width="29%"><b>Господарі</b></td>
<td class="contentEntry1" style="text-align: center;" width="8%"><b>Рах.</b></td>
<td class="contentEntry1" style="text-align: left;" width="29%"><b>Гості</b></td>
</tr>
{% endif %}

{% if not isHandler('news:main|news:by.category|news:news') %}
<tr>
	<td class="cells2" width="31%"><b>Дата</b></td>
	<td class="cells2" width="61%"><b>Господарі - Гості</b> </td>
	<td class="cells2" style="text-align: center;" width="8%">Рах.</td>
</tr>
{% endif %}


{% if isHandler('news:news') %}
<tr>
<td class="contentEntry1"style="text-align: center;" width="19%"><center><b>Дата</b></center></td>
<td class="contentEntry1"style="text-align: center;" width="10%"><center><b>Канал</b></center></td>
<td class="contentEntry1" style="text-align: right" width="29%"><b>Господарі</b></td>
<td class="contentEntry1" style="text-align: center;" width="8%"><b>Рах.</b></td>
<td class="contentEntry1" style="text-align: left;" width="29%"><b>Гості</b></td>
</tr>
{% endif %}

{entries}
</table>
<br/>
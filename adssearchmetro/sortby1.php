<?php
$s1 = $_GET['s1'];


if($s1 == "1") { ?>

	<label for="s2">РђСЂРµРЅРґР° РёР»Рё РїСЂРѕРґР°Р¶Р°:<br /></label>
	<select id="s2">
	<option value="1">РђСЂРµРЅРґР°</option>
	<option value="2">РџСЂРѕРґР°Р¶Р°</option>
	</select>


<script type="text/javascript" language="JavaScript">
    $("#s2").change(function () {
          $("#feld2").load("http://metroskop.ru/engine/plugins/adssearchmetro/sortby2.php?s2="+ $(this).val());
        })
        .change();

</script>
	
	<div id="feld2"> </div>


<?php
}else if($s1 == "2") { ?>

	<label for="s2">РђСЂРµРЅРґР° РёР»Рё РїСЂРѕРґР°Р¶Р°<br /></label>
	<select id="s2">
	<option value="11">РђСЂРµРЅРґР°</option>
	<option value="12">РџСЂРѕРґР°Р¶Р°</option>
	</select>
	

<script type="text/javascript" language="JavaScript">
    $("#s2").change(function () {
          $("#feld2").load("http://metroskop.ru/engine/plugins/adssearchmetro/sortby2.php?s2="+ $(this).val());
        })
        .change();
</script>
	
	<div id="feld2"> </div>


<?php
}else{
print '';
}

?>

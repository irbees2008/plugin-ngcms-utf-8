<script><input type="text" class="form-control"<input type="text" class="form-control"<input type="text" class="form-control"
$(document).ready(function(){

$("#name").change(function() {
  	if ($('#name').val().length < 3) {
	$("#name").css({
	  "border": "1px solid #b54d4b"
    });
	}
	else {
	$("#name").css({
	  "border": "1px solid #94c37a"
    });
	}
});

$("#phone").change(function() {
  	if ($('#phone').val().length < 1) {
	$("#phone").css({
	  "border": "1px solid #b54d4b"
    });
	}
	else {
	$("#phone").css({
	  "border": "1px solid #94c37a"
    });
	}
});

$("#message").change(function() {

  	if ($('#message').val().length < 3) {
	$("#message").css({
	  "border": "1px solid #b54d4b"
    });
	}
	else {
	$("#message").css({
	  "border": "1px solid #94c37a"
    });
	}
});

$("#mcode").keyup(function() {

		$.post('/engine/rpc.php', { json : 1, methodName : 'news_feedback_captcha', rndval: new Date().getTime(), params : json_encode({ 'mcode' : $('#mcode').val() }) }, function(data) {
		// Try to decode incoming data
		try {
			resTX = eval('('+data+')');
		//	alert(resTX['data']['feedback_text']);
		} catch (err) { alert('Error parsing JSON output. Result: '+linkTX.response); }
		if (!resTX['status']) {
			alert('Error ['+resTX['errorCode']+']: '+resTX['errorText']);
		} else {
			if ((resTX['data']['feedback_captcha']>0)&&(resTX['data']['feedback_captcha'] < 100)) {

				$("#mcode").css({
				  "border": "1px solid #b54d4b"
				});

			} else {

				$("#mcode").css({
				  "border": "1px solid #94c37a"
				});

			}
		}
	}).error(function() {
		alert('HTTP error during request', 'ERROR');
	});

});

 $("#send_feedback").click(function() {

//	alert($('textarea#message').val());

	$.post('/engine/rpc.php', { json : 1, methodName : 'news_feedback_add', rndval: new Date().getTime(), params : json_encode({ 'name' : $('#name').val(), 'phone' : $('#phone').val(), 'message' : $('#message').val(), 'mcode' : $('#mcode').val(), 'news_url' : '{{news_url}}', 'news_title' : '{{news_title}}' }) }, function(data) {
		// Try to decode incoming data
		try {
			resTX = eval('('+data+')');
		//	alert(resTX['data']['feedback_text']);
		} catch (err) { alert('Error parsing JSON output. Result: '+linkTX.response); }
		if (!resTX['status']) {
			alert('Error ['+resTX['errorCode']+']: '+resTX['errorText']);
		} else {
			if ((resTX['data']['feedback']>0)&&(resTX['data']['feedback'] < 100)) {
				$("div#feedback_status").html("<span style='color:#b54d4b;'>"+resTX['data']['feedback_text']+"</span>");
			} else {
				$("div#feedback_status").html("<span style='color:#94c37a;'>"+resTX['data']['feedback_text']+"</span>");
				$('#name').val('');
				$('#phone').val('');
				$('#message').val('');
				$('#mcode').val('');
				$("#name").css({"border": "1px solid #e2e2e2"});
				$("#phone").css({"border": "1px solid #e2e2e2"});
				$("#message").css({"border": "1px solid #e2e2e2"});
				$("#mcode").css({"border": "1px solid #e2e2e2"});
				reload_captcha();
			}
		}
	}).error(function() {
		alert('HTTP error during request', 'ERROR');
	});

  });
});

	function reload_captcha() {
		var captc = document.getElementById('img_captcha');
		if (captc != null) {
			captc.src = "{admin_url}/captcha.php?rand="+Math.random();
		}
	}

</script>
<style>
input[type="text"], input[type="password"] {
font-size: 100%;
padding: 0;
}
textarea {
font-size: 100%;
padding: 0;
font-family: arial;
}

.btn-s {
	-moz-box-shadow:inset 0px 1px 0px 0px #cae3fc;
	-webkit-box-shadow:inset 0px 1px 0px 0px #cae3fc;
	box-shadow:inset 0px 1px 0px 0px #cae3fc;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #79bbff), color-stop(1, #4197ee) );
	background:-moz-linear-gradient( center top, #79bbff 5%, #4197ee 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#79bbff', endColorstr='#4197ee');
	background-color:#79bbff;
	-webkit-border-top-left-radius:0px;
	-moz-border-radius-topleft:0px;
	border-top-left-radius:0px;
	-webkit-border-top-right-radius:0px;
	-moz-border-radius-topright:0px;
	border-top-right-radius:0px;
	-webkit-border-bottom-right-radius:0px;
	-moz-border-radius-bottomright:0px;
	border-bottom-right-radius:0px;
	-webkit-border-bottom-left-radius:0px;
	-moz-border-radius-bottomleft:0px;
	border-bottom-left-radius:0px;
	text-indent:0px;
	border:1px solid #469df5;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:12px;
	font-weight:bold;
	font-style:normal;
	height:20px;
	line-height:20px;
	width:120px;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #287ace;
}
.btn-s:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #4197ee), color-stop(1, #79bbff) );
	background:-moz-linear-gradient( center top, #4197ee 5%, #79bbff 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#4197ee', endColorstr='#79bbff');
	background-color:#4197ee;
}.btn-s:active {
	position:relative;
	top:1px;
}
</style>
<h2>Купить этот проект</h2>
<div class="projects-block">
<div class="blocks">

<table id="maincontent" class="info" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td colspan="3"><div id="feedback_status"></div></td>
</tr>

<tr>
<td>Имя</td>
<td style="padding-bottom: 5px; padding-left: 5px;"><input type="text" id="name" name="name" value="{{name}}" size="18" /></td>
</tr>
<tr>
<td>Телефон</td>
<td style="padding-bottom: 5px; padding-left: 5px;"><input type="text" id="phone" name="phone" value="{{phone}}" size="18" /></td>
</tr>
<tr>
<td class="entry">Комментарий</td>
<td style="padding-bottom: 5px; padding-left: 5px;"><textarea type="text" id="message" name="message" rows="7" cols="18">{{message}}</textarea></td>
</tr>
<tr>
<td><img src="{admin_url}/captcha.php" onclick="reload_captcha();" style="height:25px; cursor:pointer;" id="img_captcha" /></td>
<td style="padding-bottom: 5px; padding-left: 5px;"><input type="text" id="mcode" name="mcode" maxlength="5" size="18" /></td>
</tr>
<tr>
<td style="padding-top: 15px;" colspan="3"><input type="button" class="btn-s" style="cursor: pointer;" id="send_feedback" name="send_feedback" value="Отправить" /></td>
</tr>
</table>

</div>
</div>

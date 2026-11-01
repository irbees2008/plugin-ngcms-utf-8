<script src="/engine/plugins/football_stats/tpl/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/jquery-ui-timepicker-ru.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/jquery.dd.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/ajax.js" type="text/javascript"></script>
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery.dd.css" type="text/css" />
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery-ui-1.8.16.custom.css" type="text/css" />

<script src="/engine/plugins/football_stats/tpl/js/jquery.wysiwyg.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/wysiwyg.link.js" type="text/javascript"></script>
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery.wysiwyg.css" type="text/css" />

<script>
var currentInputAreaID = 'translation';
function ChangeOption(optn) {
document.getElementById('maincontent').style.display = (optn == 'maincontent')?"block":"none";
document.getElementById('additional').style.display = (optn == 'additional')?"block":"none";
document.getElementById('attaches').style.display = (optn == 'attaches')?"block":"none";
}
function approveMode(mode) {
document.getElementById('approve').value = mode;
return true;
}
function preview(){
var form = document.getElementById("postForm");
if (form.translation.value == '' || form.title.value == '') {
alert('Заполните заголовок и краткое содержание!');
return false;
}
form.mod.value = "preview";
form.target = "_blank";
form.submit();
form.mod.value = "news";
form.target = "_self";
return true;
}

/*
function insertext(open, close, field) {
    if (open == 'img') {
        var path = document.getElementById('src').value;
        var alt = document.getElementById('alt').value;
        var title = document.getElementById('title').value;
        var width = document.getElementById('width').value;
        if (path == "" || alt == "" || title == "" || width == "") {
            alert("Заповніть всі дані зображення!");
            return false;
        }
        else {
            open = "<img src='"+path+"' title='"+title+"' alt='"+alt+"' width='"+width+"' />";
        }
    }
    $('#' + field).wysiwyg('insertHtml', open + close);
    return true;
}


	$(function() {
		$('#translation').wysiwyg( { 
			controls: {      
				html : { visible : true },
				insertImage : { visible : false },
				h1 : { visible : false },
				h2 : { visible : false },
				h3 : { visible : false },
				insertTable : { visible : false }
			}
		});	
		
	});*/
   
$(document).ready(function(){
$('#edit_game_btn').click(function(eventObj){
  //alert('ура!');
  //eventObj.preventDefault();
 
   var owner = new Array();
       $('.owner_radio:checked').each(function()
                {  
                     owner.push( {
                        id_player:$(this).attr('name'),
                            val:$(this).val()
                        
                        } );

                }
                ); 
   $('#owners').val(JSON.stringify(owner)); 
   
    var guest = new Array();
       $('.guest_radio:checked').each(function()
                {  
                     guest.push( {
                        id_player:$(this).attr('name'),
                            val:$(this).val()
                        
                        } );

                }
                ); 
   $('#guests').val(JSON.stringify(guest)); 
  
   
 //$("form[name=game").submit();
   
});
});
</script>


<script>
	$(function() {
		// initial values array(id_player, minute, auto)
		players_goals_owner = {json.players_goals_owner};
		players_goals_guest = {json.players_goals_guest};
		
	        if (players_goals_owner['auto'] == undefined) { 
        		players_goals_owner['auto'] = [];
		}
	        if (players_goals_guest['auto'] == undefined) {
        		players_goals_guest['auto'] = [];
		}

		// players lists array(id_player, name)
		players_owner = new Array();
		players_guest = new Array();
		
		goals_owner = parseInt($( "#goals_owner :selected" ).val());
		goals_guest = parseInt($( "#goals_guest :selected" ).val());
		
		owner_value = owner_html = guest_value = guest_html = 0;
		
		// if edit game
		if({add} == 0){
			get_players('team_owner', players_owner, step = 1);
			get_players('team_guest', players_guest, step = 1);
			
			var tmp = goals_owner;
			goals_owner = 0;
			add_goals_owner(tmp);
			goals_owner = tmp;
			
			var i;
			var length = players_goals_owner['id_player'] != undefined ? players_goals_owner['id_player'].length : 0;
			for(i=1; i<=length; i++){
				var x = i - 1;
				setSelect('player_goal_owner[' + i + ']', players_goals_owner['id_player'][x]);
				document.getElementById('player_minute_owner[' + i + ']').setAttribute('value', players_goals_owner['minute'][x]);
                document.getElementById('player_minute_owner_add[' + i + ']').setAttribute('value', players_goals_owner['minute_add'][x]);
			}		
			
			tmp = goals_guest;
			goals_guest = 0;
			add_goals_guest(tmp);
			goals_guest = tmp;
			
			var length = players_goals_guest['id_player'] != undefined ? players_goals_guest['id_player'].length : 0;
			for(i=1; i<=length; i++){
				var x = i - 1;
				setSelect('player_goal_guest[' + i + ']', players_goals_guest['id_player'][x]);
				document.getElementById('player_minute_guest[' + i + ']').setAttribute('value', players_goals_guest['minute'][x]);
                document.getElementById('player_minute_guest_add[' + i + ']').setAttribute('value', players_goals_guest['minute_add'][x]);
			}
			
			owner_value	= $("#team_guest :selected").val();
			owner_html	= $("#team_guest :selected").html();
		
			guest_value	= $("#team_owner :selected").val();
			guest_html	= $("#team_owner :selected").html();
		
			if(guest_value != 0)
				$('#team_guest option[value=' + guest_value + ']').remove();
			
			if(owner_value != 0)
				$('#team_owner option[value=' + owner_value + ']').remove();
		} // enf if add == 0
		
		if(guest_value == 0)
			$("#goals_owner").attr("disabled","disabled");
		else
			$("#goals_owner").removeAttr("disabled");
				
		if(owner_value == 0)
			$("#goals_guest").attr("disabled","disabled");
		else
			$("#goals_guest").removeAttr("disabled");
				
		$( "#date" ).datetimepicker();
		$( "#channel" ).msDropDown();
		var oDD = $( "#status" ).msDropDown().data("dd");
		oDD.set('selectedIndex', parseInt($( "#status-id" ).html()));			
			
		$('#team_owner').attr('onchange', 'owner_onchange()');
		$('#team_guest').attr('onchange', 'guest_onchange()');
	});
	
	function setSelect(id, value){
		var i,o,
		sel = document.getElementById(id),
		opt = sel.options;

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

	// get team players from server
	function get_players(id_team, players, step){
		var ajaxGetPlayers = new sack(); 
		ajaxGetPlayers.onComplete = function (){		
			if (ajaxGetPlayers.responseStatus[0] != 200) {
				alert('AJAX error with code ' + ajaxGetPlayers.responseStatus[0]);
				return;
			}
			
			var data = $.parseJSON(ajaxGetPlayers.response); 

			// clear current players list
			players.length = 0; 
			
			// fill array
			for (var i in data){
				players.push({id_player:data[i]['id_player'], name:data[i]['name']});
			}

            {anotherPlayerOld}
					
			if(id_team == 'team_owner' && step != 1){
				delete_goals_owner($( "#goals_owner :selected" ).val());
				$("#goals_owner :nth-child(1)").attr("selected", "selected");
				goals_owner = 0;
			}
			else if(step != 1) {
				delete_goals_guest($( "#goals_guest :selected" ).val());
				$("#goals_guest :nth-child(1)").attr("selected", "selected");
				goals_guest = 0;
			}
		};		
				
		ajaxGetPlayers.setVar("id_team", $('#' + id_team + ' :selected').val());
		ajaxGetPlayers.setVar("ajax", true);
		ajaxGetPlayers.requestFile = "{url}";
		ajaxGetPlayers.method = "GET";
		ajaxGetPlayers.runAJAX();
	}
	
    // get list of players	
	function owner_onchange(){		
		get_players('team_owner', players_owner, step = 2);
		
		if(owner_value != 0 && owner_value != $("#team_guest :selected").val()){
			$("#team_owner").append( $('<option value="' + owner_value +'">' + owner_html +'</option>'));
			owner_value = 0;
			owner_html = '';
		}
		
		if(guest_value != 0){
			$("#team_guest").append( $('<option value="' + guest_value +'">' + guest_html +'</option>'));
			guest_value = 0;
			guest_html = '';
		}
		
		guest_value	= $("#team_owner :selected").val();
		guest_html	= $("#team_owner :selected").html();
		
		if(guest_value != 0)
			$('#team_guest option[value=' + guest_value + ']').remove();
			
		if(guest_value == 0)
			$("#goals_owner").attr("disabled","disabled");
		else
			$("#goals_owner").removeAttr("disabled");
	}
	
	function add_goals_owner(count, ifnew){
		if($( "#team_owner option:selected" ).val() == 0) return;
		if (typeof ifnew == "undefined") ifnew = false;
	
		var tbl = document.getElementById('game');
		
		var counter = goals_owner, i;
	
		for (i=1; i<=count; i++) { 
			counter += 1;
			var lastRow = 4 + i + goals_owner;
			var row = tbl.insertRow(lastRow);
			var cellRight = row.insertCell(0);
			cellRight.setAttribute('class', 'contentEntry1');
			cellRight.setAttribute('style', 'padding-left: 15%;');
			cellRight.innerHTML = 'Имя игрока забишего ' + counter + ' мяч';
			
			cellRight = row.insertCell(1);
			cellRight.setAttribute('class', 'contentEntry1');
			cellRight.setAttribute('style', 'text-align:right; padding-right: 27%;');
			
			var cb = document.createElement('input');
			cb.setAttribute('type', 'checkbox');
			cb.setAttribute('name', 'auto_owner['  + counter + ']');
			cb.setAttribute('id', 'auto_owner['  + counter + ']');
			cb.setAttribute('value', counter);
			cb.setAttribute('onchange', 'auto_goal_owner(this.value)');
			if(!ifnew && players_goals_owner['auto'][counter-1] == 1)
				cb.setAttribute('checked', 'checked');
			cellRight.appendChild(cb);
			
			var el = document.createElement('select');
			el.setAttribute('name', 'player_goal_owner['  + counter + ']');
			el.setAttribute('id', 'player_goal_owner['  + counter + ']');
			cellRight.appendChild(el);

            var name_player = document.createElement('input');
			name_player.setAttribute('type', 'text');
			name_player.setAttribute('name', 'player_name_owner['  + counter + ']');
			name_player.setAttribute('id', 'player_name_owner['  + counter + ']');
			name_player.setAttribute('size', '25');
            name_player.setAttribute('placeholder', 'Ім\'я нового футболіста');
			cellRight.appendChild(name_player);
			
			var els = document.createElement('input');
			els.setAttribute('type', 'text');
			els.setAttribute('name', 'player_minute_owner['  + counter + ']');
			els.setAttribute('id', 'player_minute_owner['  + counter + ']');
			els.setAttribute('size', '15');
			els.setAttribute('placeholder', 'Хвилина');
			cellRight.appendChild(els);

            var el_min_add = document.createElement('input');
			el_min_add.setAttribute('type', 'text');
			el_min_add.setAttribute('name', 'player_minute_owner_add['  + counter + ']');
			el_min_add.setAttribute('id', 'player_minute_owner_add['  + counter + ']');
			el_min_add.setAttribute('size', '17');
            el_min_add.setAttribute('placeholder', 'Додаткова хвилина');
			cellRight.appendChild(el_min_add);
			
			options = new Array();
			options.push(new Option('—', '-1', false, false));
			
			if(!ifnew && players_goals_owner['auto'][counter-1] == 1){
				for ( var j in players_guest )
					options.push(new Option(players_guest[j]['name'], players_guest[j]['id_player'], false, false));
			} else {
				for ( var j in players_owner )
					options.push(new Option(players_owner[j]['name'], players_owner[j]['id_player'], false, false));
			}
			
			 for ( var option in options ){
				el.appendChild(options[option]);
			}
		}
	}
	
	function auto_goal_owner(id){
		var options = new Array();
		options.push(new Option('—', '-1', false, false));
		var el = document.getElementById('player_goal_owner[' + id + ']');
		el.options.length = 0;
		if (document.getElementById('auto_owner[' + id + ']').checked == false){
			for ( var j in players_owner )
				options.push(new Option(players_owner[j]['name'], players_owner[j]['id_player'], false, false));
		}
		else {
			for ( var j in players_guest )
				options.push(new Option(players_guest[j]['name'], players_guest[j]['id_player'], false, false));
		}
				
		for ( var option in options ){
				el.appendChild(options[option]);
		}			
	}
	
	function delete_goals_owner(count){
		var tbl = document.getElementById('game');
		
		for (i = 1; i <= count; i += 1){
			var lastRow = 5 + goals_owner - i;
			tbl.deleteRow(lastRow);
		}
	}
	
	function modify_goals_owner(){
		var current = parseInt($( "#goals_owner :selected" ).val());

		if(goals_owner > current)
			delete_goals_owner(goals_owner - current);
		else
			add_goals_owner(current - goals_owner, true);
		
		goals_owner = parseInt($( "#goals_owner :selected" ).val()); 
	}
	
	function guest_onchange(){
		get_players('team_guest', players_guest, step = 2);
			
		if(guest_value != 0 && guest_value != $("#team_owner option:selected").val()){
			$("#team_guest").append( $('<option value="' + guest_value +'">' + guest_html +'</option>'));
			guest_value = 0;
			guest_html = '';
		}	
		
		if(owner_value != 0 && step != 1){
			$("#team_owner").append( $('<option value="' + owner_value +'">' + owner_html +'</option>'));
			owner_value = 0;
			owner_html = '';
		}
		
		owner_value	= $("#team_guest :selected").val();
		owner_html	= $("#team_guest :selected").html();
		
		if(owner_value != 0)
			$('#team_owner option[value=' + owner_value + ']').remove();
		
		if(owner_value == 0)
			$("#goals_guest").attr("disabled","disabled");
		else
			$("#goals_guest").removeAttr("disabled");
	}
	
	function add_goals_guest(count, ifnew){
		if($( "#team_guest option:selected" ).val() == 0) return;
		if (typeof ifnew == "undefined") ifnew = false;
		
		var tbl = document.getElementById('game');
		
		var counter = goals_guest;
	
		for (i = 1; i <= count; i += 1) {
			counter += 1;
			var lastRow = 5 + i + goals_guest + goals_owner;
			var row = tbl.insertRow(lastRow);
			var cellRight = row.insertCell(0);
			cellRight.setAttribute('class', 'contentEntry1');
			cellRight.setAttribute('style', 'padding-left: 15%;');
			cellRight.innerHTML = 'Имя игрока забишего ' + counter + ' мяч';
			
			cellRight = row.insertCell(1);
			cellRight.setAttribute('class', 'contentEntry1');
			cellRight.setAttribute('style', 'text-align:right; padding-right: 27%;');
			
			var cb = document.createElement('input');
			cb.setAttribute('type', 'checkbox');
			cb.setAttribute('name', 'auto_guest['  + counter + ']');
			cb.setAttribute('id', 'auto_guest['  + counter + ']');
			cb.setAttribute('value', counter);
			cb.setAttribute('onchange', 'auto_goal_guest(this.value)');
			if(!ifnew && players_goals_guest['auto'][counter-1] == 1)
				cb.setAttribute('checked', 'checked');
			cellRight.appendChild(cb);
			
			var el = document.createElement('select');
			el.setAttribute('name', 'player_goal_guest['  + counter + ']');
			el.setAttribute('id', 'player_goal_guest['  + counter + ']');
			cellRight.appendChild(el);

            var name_player = document.createElement('input');
			name_player.setAttribute('type', 'text');
			name_player.setAttribute('name', 'player_name_guest['  + counter + ']');
			name_player.setAttribute('id', 'player_name_guest['  + counter + ']');
			name_player.setAttribute('size', '25');
            name_player.setAttribute('placeholder', 'Ім\'я нового футболіста');
			cellRight.appendChild(name_player);
			
			var els = document.createElement('input');
			els.setAttribute('type', 'text');
			els.setAttribute('name', 'player_minute_guest['  + counter + ']');
			els.setAttribute('id', 'player_minute_guest['  + counter + ']');
			els.setAttribute('size', '15');
			els.setAttribute('placeholder', 'Хвилина');
			cellRight.appendChild(els);

            var el_min_add = document.createElement('input');
			el_min_add.setAttribute('type', 'text');
			el_min_add.setAttribute('name', 'player_minute_guest_add['  + counter + ']');
			el_min_add.setAttribute('id', 'player_minute_guest_add['  + counter + ']');
			el_min_add.setAttribute('size', '17');
            el_min_add.setAttribute('placeholder', 'Додаткова хвилина');
			cellRight.appendChild(el_min_add);
			
			options = new Array();
			options.push(new Option('—', '-1', false, false));
			
			
			if(!ifnew && players_goals_guest['auto'][counter-1] == 1){
				for ( var j in players_owner )
					options.push(new Option(players_owner[j]['name'], players_owner[j]['id_player'], false, false));
			} else {
				for ( var j in players_guest )
					options.push(new Option(players_guest[j]['name'], players_guest[j]['id_player'], false, false));
			}
			
			 for ( var option in options ){
				el.appendChild(options[option]);
			} 
		}
	}

	function delete_goals_guest(count){
		var tbl = document.getElementById('game');
		
		for (i = 1; i <= count; i += 1){
			var lastRow = 6 + goals_guest + goals_owner - i;
			tbl.deleteRow(lastRow);
		}
	}
	
	function auto_goal_guest(id){
		var options = new Array();
		options.push(new Option('—', '-1', false, false));
		var el = document.getElementById('player_goal_guest[' + id + ']');
		el.options.length = 0;
		if (document.getElementById('auto_guest[' + id + ']').checked == false){
			for ( var j in players_guest )
				options.push(new Option(players_guest[j]['name'], players_guest[j]['id_player'], false, false));
		}
		else {
			for ( var j in players_owner )
				options.push(new Option(players_owner[j]['name'], players_owner[j]['id_player'], false, false));
		}
				
		for ( var option in options ){
				el.appendChild(options[option]);
		}			
	}
	
	function modify_goals_guest(){
		var current = parseInt($( "#goals_guest :selected" ).val());

		if(goals_guest > current)
			delete_goals_guest(goals_guest - current);
		else
			add_goals_guest(current - goals_guest, true);
		
		goals_guest = parseInt($( "#goals_guest :selected" ).val()); 
	}
</script>





<!--AJAX-->
<script type="text/javascript">
    $(document).ready(function () {
        $('#id_selected_team_owner').val($('#team_owner option:selected').val());
        $('#id_selected_team_guest').val($('#team_guest option:selected').val());
        $('#table_players_owners caption b').html($('#team_owner option:selected').text());
        $('#table_players_guests caption b').html($('#team_guest option:selected').text());
        $('#coach_owner_id').val($('#team_owner option:selected').val());
        $('#coach_guest_id').val($('#team_guest option:selected').val());
        get_coaches_ajax($('#team_owner option:selected').val(), '#coach_name_team_owner', $('#id_game').val());
        get_coaches_ajax($('#team_guest option:selected').val(), '#coach_name_team_guest', $('#id_game').val());
        get_players_ajax($('#team_owner option:selected').val(), '#replacement_in_owners');
        get_players_ajax($('#team_guest option:selected').val(), '#replacement_in_guests');

        $('#caption_penalti').click(function(){
            $('#main_tr_penalti_own').toggle(100);
            $('#main_tr_penalti_gue').toggle(100);
            $('.tr_penalti_owner').remove();
            $('.tr_penalti_guest').remove();
        });

        $('#team_owner').change(function () {
            $('#team_owner option:selected').each(function () {
                $('#table_players_owners caption b').html($(this).text());
                $('#id_selected_team_owner').val($(this).val());
                $('#coach_owner_id').val($(this).val());
                get_players_ajax($(this).val(), '.list_players_owners');
                get_coaches_ajax($(this).val(), '#coach_name_team_owner', $('#id_game').val());
            });
        });
        $('#team_guest').change(function () {
            $('#team_guest option:selected').each(function () {
                $('#table_players_guests caption b').html($(this).text());
                $('#id_selected_team_guest').val($(this).val());
                $('#coach_guest_id').val($(this).val());
                get_players_ajax($(this).val(), '.list_players_guests');
                get_coaches_ajax($(this).val(), '#coach_name_team_guest', $('#id_game').val());
            });
        });

        function get_players_ajax(id_team, selector) {
            jQuery.ajax({
                type: "POST",
                url: "/engine/plugins/football_stats/tpl/config/get_players.php",
                dataType: "text",
                data: { id_team: encodeURIComponent(id_team) },
                success: function (response) {
                    $(selector).html(response);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        }

        function get_coaches_ajax(id_team, selector, id_game) {
            jQuery.ajax({
                type: "POST",
                url: "/engine/plugins/football_stats/tpl/config/get_coach.php",
                dataType: "text",
                data: { id_team: encodeURIComponent(id_team), id_game: encodeURIComponent(id_game) },
                success: function (response) {
                    $(selector).val(response);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        }
		
		$('#table_players_owners select').prop("disabled", true);
		$('#table_players_guests select').prop("disabled", true);
		
		$('#table_players_owners input').prop("disabled", true);
		$('#table_players_guests input').prop("disabled", true);
		
		$('#add_edit_storage').click(function(){
			if (!$("#add_edit_storage").is(":checked")) {
				$('#table_players_owners select').prop("disabled", true);
				$('#table_players_guests select').prop("disabled", true);
				
				$('#table_players_owners input').prop("disabled", true);
				$('#table_players_guests input').prop("disabled", true);
			}
			else {
				$('#table_players_owners select').prop("disabled", false);
				$('#table_players_guests select').prop("disabled", false);
				
				$('#table_players_owners input').prop("disabled", false);
				$('#table_players_guests input').prop("disabled", false);
			}
		});
    });

    function correctData() {
        if ($('#correctDataCheck').attr("checked") == 'checked') {
            var tur = document.getElementById('round');
            var unique_number_owner;
            var unique_number_guest;
            for (var index = 1; index < 12; index++) {
                $('#owner_player_name_' + index + ', #guest_player_name_' + index + ', #owner_player_number_' + index + ', #guest_player_number_' + index).css({
                    'border': '1px solid #dbe4ed'
                });
            }
            if (tur.value.trim() == '') {
                alert("Введіть номер туру!!");
                tur.focus();
                return false;
            }
            for (var i = 1; i < 12; i++) {
                unique_number_owner = document.getElementById('owner_player_number_' + i).value;
                unique_number_guest = document.getElementById('guest_player_number_' + i).value;
                unique_name_guest = document.getElementById('guest_player_name_' + i).value;
                unique_name_owner = document.getElementById('owner_player_name_' + i).value;
                for (var j = 1; j < 12; j++) {
                    if (i != j && unique_name_owner == document.getElementById('owner_player_name_' + j).value) {
                        $('#owner_player_name_' + i + ', #owner_player_name_' + j).css({
                            'border': '1px solid red'
                        });
                        alert("Виявлено однакові ім'я футболістів серед господарів!");
                        return false;
                    }
                    if (i != j && unique_name_guest == document.getElementById('guest_player_name_' + j).value) {
                        $('#guest_player_name_' + i + ', #guest_player_name_' + j).css({
                            'border': '1px solid red'
                        });
                        alert("Виявлено однакові ім'я футболістів серед гостей!");
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function createPenaltiOwner() {
        //var pen_own = document.getElementById('penalti_owner');
        $('.tr_penalti_owner').remove();
        /*for (var i = 1; i <= $('#penalti_owner_count option:selected').val() - {count_of_penalti_owner}; i++) {
            var tr = document.createElement('TR');
            tr.setAttribute("class", "tr_penalti_owner");
            tr.innerHTML = "<td class='contentEntry1' style='padding-left: 15%;'>Гравець, який забив пенальті</td>" +
            "<td class='contentEntry1' style='text-align:right; padding-right: 27%;'>" +
            "<input type='text' name='penalti_owner_player_name" + i + "' placeholder='Ім&#039;я нового футболіста' /> <select name='penalti_owner_player" + i + "'>{players_penalti_owner}</select></td>";

            $('#penalti_guest').before(tr);
        }*/
        for (var i = 1; i <= $('#penalti_owner_count option:selected').val() - {count_of_penalti_owner}; i++) {
            var tr = document.createElement('TR');
            tr.setAttribute("class", "tr_penalti_owner");
            tr.innerHTML = "<td style='text-align: right;'><input type='text' name='penalti_owner_player_name" + i + "' placeholder='Ім&#039;я нового футболіста' /> <select name='penalti_owner_player" + i + "'>{players_penalti_owner}</select></td>";
            $('#main_tr_penalti_own').after(tr);
        }
    }
    function createPenaltiGuest() {
        $('.tr_penalti_guest').remove();
        /*for (var i = 1; i <= $('#penalti_guest_count option:selected').val() - {count_of_penalti_guest}; i++) {
            var tr = document.createElement('TR');
            tr.setAttribute("class", "tr_penalti_guest");
            tr.innerHTML = "<td class='contentEntry1' style='padding-left: 15%;'>Гравець, який забив пенальті</td>" +
            "<td class='contentEntry1' style='text-align:right; padding-right: 27%;'>" +
            "<input type='text' name='penalti_guest_player_name" + i + "' placeholder='Ім&#039;я нового футболіста' /> <select name='penalti_guest_player" + i + "'>{players_penalti_guest}</select></td>";

            $('#cardsOwner').before(tr);
        }*/
        for (var i = 1; i <= $('#penalti_guest_count option:selected').val() - {count_of_penalti_guest}; i++) {
            var tr = document.createElement('TR');
            tr.setAttribute("class", "tr_penalti_guest");
            tr.innerHTML = "<td><input type='text' name='penalti_guest_player_name" + i + "' placeholder='Ім&#039;я нового футболіста' /> <select name='penalti_guest_player" + i + "'>{players_penalti_guest}</select></td><td></td><td></td>";
            $('#main_tr_penalti_gue').after(tr);
        }
    }

    function createCardsOwner(count, img, before, player, name_player, minute, count_ready) {
        $('.tr_cards_owner').remove();
        for (var i = 1; i <= count - count_ready; i++) {
            var tr = document.createElement('TR');
            tr.setAttribute("class", "tr_cards_owner");
            tr.innerHTML = "<td class='contentEntry1' style='padding-left:15%;'>Гравець, який получив карточку</td>" +
            "<td class='contentEntry1' style='text-align:right; padding-right:27%;'>" +
            "<img src='http://sportanalytic.com/engine/skins/default/tags/online/"+img+".gif' width='16' />" +
            "<select name='"+player+i+"'>{players_penalti_owner}</select> " +
            "<input type='text' name='"+name_player+i+"' placeholder='Ім&#039;я нового футболіста' /> " +
            "<input type='text' name='"+minute+i+"' placeholder='хвилина' style='width: 45px;' /> ";
            $(before).before(tr);
        }
    }

    function createCardsGuest(count, img, before, player, name_player, minute, count_ready) {
        $('.tr_cards_guest').remove();
        for (var i = 1; i <= count - count_ready; i++) {
            var tr = document.createElement('TR');
            tr.setAttribute("class", "tr_cards_guest");
            tr.innerHTML = "<td class='contentEntry1' style='padding-left:15%;'>Гравець, який получив карточку</td>" +
            "<td class='contentEntry1' style='text-align:right; padding-right:27%;'>" +
            "<img src='http://sportanalytic.com/engine/skins/default/tags/online/"+img+".gif' width='16' />" +
            "<select name='"+player+i+"'>{players_penalti_guest}</select> " +
            "<input type='text' name='"+name_player+i+"' placeholder='Ім&#039;я нового футболіста' /> " +
            "<input type='text' name='"+minute+i+"' placeholder='хвилина' style='width: 45px;' /> ";
            $(before).before(tr);
        }
    }

    function select_img(src){
        var select_img_mico = document.getElementById('select_img_mico');
        var select_img_input = document.getElementById('select_img_input');
        select_img_mico.setAttribute("src", src);
        select_img_input.setAttribute("value", "<img src='"+src+"' width='16'/>");
    }

    function paste_img(file, id){
        var value_file = file;
        if(value_file != '' && value_file != null){
            var src_file = document.getElementById(id);
            var img = "<img width='500' src='http://sportanalytic.com/uploads/football_stats/translation/";
            //value_file.substring(value_file.lastIndexOf('\\')+1,value_file.length)+"'  />";
            src_file.setAttribute("value", img);
        }
    }

    function make_bold(str1, str2){
        var obj = document.getElementById("text_translation");
        obj.focus();

        if (document.selection) {
            var s = document.selection.createRange();
            s.text = str1 + s.text + str2;
        } else {
            var len = obj.value.length;
            var start = obj.selectionStart;
            var end = obj.selectionEnd;

            var scrollTop = obj.scrollTop;
            var scrollLeft = obj.scrollLeft;

            var sel = obj.value.substring(start, end);
            var rep = str1 + sel + str2;
            obj.value = obj.value.substring(0, start) + rep + obj.value.substring(end, len);

            obj.scrollTop = scrollTop;
            obj.scrollLeft = scrollLeft;
        }
    }

    $(function() {
		$( ".micos" ).msDropDown();	
	});
</script>

<form action="?mod=football_stats&amp;action=modify-game-save" method="post" name="game" enctype="multipart/form-data" onsubmit="return correctData();">
{link_plugin_match}
<input type="hidden" name="id_selected_team_owner" id="id_selected_team_owner" value="0" />
    <input type="hidden" name="id_selected_team_guest" id="id_selected_team_guest" value="0" />
<span id="status-id" style="display:none">{status-id}</span>
		<input type="hidden" id="id_game" name="id" value="{id}" />
		<input type="hidden" name="cid" value="{cid}" />
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="game">
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry2" style="padding-right: 40%;">
				<select style="width:200px;" name="status" id="status" value="" >
					<option value="0" title="/engine/plugins/football_stats/tpl/images/gray-status.gif">матч не начался</option>
					<option value="1" title="/engine/plugins/football_stats/tpl/images/green-status.gif">идет игра</option>	
					<option value="2" title="/engine/plugins/football_stats/tpl/images/red-status.gif">матч закончился</option>
					<option value="3" title="/engine/plugins/football_stats/tpl/images/black-status.gif">матч остановлен</option>
					<option value="4" title="/engine/plugins/football_stats/tpl/images/black-status.gif">матч не состоялся</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Номер тура
				<input type="text" id="round" name="round" value="{round}" />
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Команда-господар
				<select name="team_owner" id="team_owner" onclick="owner_onchange()">
					{team-owner}
				</select>
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Команда-гість
				<select name="team_guest" id="team_guest" onclick="guest_onchange()">
					{team-guest}
				</select>
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Голи господарів
					<b>Голи господарів</b> {goals-owner}
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Голи гостей
				<b>Голи гостей</b> {goals-guest}
			</td>
		</tr>

		<!--here-->

        <tr id="cardsOwner">
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">
                <b>Карточки господарів</b>
                <img src="http://sportanalytic.com/engine/skins/default/tags/online/mico4.gif" width="16" alt="Жовта картка" />
                <select name="count_yellow_card_owner" id="count_yellow_card_owner"
                onchange="createCardsOwner($('#count_yellow_card_owner option:selected').val(), 'mico4', '#cardsGuest', 'players_cards_yellow_owner', 'cards_name_yellow_owner', 'cards_minute_yellow_owner', {card_yellow_owner})">{count_yellow_card_owner}</select>
                <img src="http://sportanalytic.com/engine/skins/default/tags/online/mico5.gif" width="16" alt="Червона картка" />
                <select name="count_red_card_owner" id="count_red_card_owner"
                onchange="createCardsOwner($('#count_red_card_owner option:selected').val(), 'mico5', '#cardsGuest', 'players_cards_red_owner', 'cards_name_red_owner', 'cards_minute_red_owner', {card_red_owner})">{count_red_card_owner}</select>
			</td>
		</tr>
        {cards_players_owner}
        <tr id="cardsGuest">
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">
                <b>Карточки гостей</b>
                <img src="http://sportanalytic.com/engine/skins/default/tags/online/mico4.gif" width="16" alt="Жовта картка" />
                <select name="count_yellow_card_guest" id="count_yellow_card_guest"
                onchange="createCardsGuest($('#count_yellow_card_guest option:selected').val(), 'mico4', '#DateOfMatch', 'players_cards_yellow_guest', 'cards_name_yellow_guest', 'cards_minute_yellow_guest', {card_yellow_guest})">{count_yellow_card_guest}</select>
                <img src="http://sportanalytic.com/engine/skins/default/tags/online/mico5.gif" width="16" alt="Червона картка" />
                <select name="count_red_card_guest" id="count_red_card_guest"
                onchange="createCardsGuest($('#count_red_card_guest option:selected').val(), 'mico5', '#DateOfMatch', 'players_cards_red_guest', 'cards_name_red_guest', 'cards_minute_red_guest', {card_red_guest})">{count_red_card_guest}</select>
			</td>
		</tr>
        {cards_players_guest}
		<tr id="DateOfMatch">
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Червоні карточки господарів
                <img src='http://sportanalytic.com/engine/skins/default/tags/online/mico5.gif' width='16' />
					{reds-owner}
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Червоні карточки гостей
                <img src='http://sportanalytic.com/engine/skins/default/tags/online/mico5.gif' width='16' />
					{reds-guest}
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Дата
				<input type="text" name="date" id="date" value="{date}" />
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Фото
				<input type="text" name="photo" value="{photo}" />
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Видео
				<input type="text" name="video" value="{video}" />
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Статистика
				<input type="text" name="stats" value="{stats}" />
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry1" style="text-align:right; padding-right: 40%;">Трансляція
				<input type="text" name="a_trans" value="{a_trans}" />
			</td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry2" style="padding-right: 40%;">Канал трансляции<br/><select style="width:200px;" name="channel" id="channel" value="" />{channel}</select></td>
		</tr>
		<tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry2" style="padding-right: 40%;">Посилання на онлайн рахунок матчу(myscore)
				<input type="text" name="source_link" value="{source_link}" />
			</td>
		</tr>
                <tr>
			<td class="contentEntry1" style="padding-left: 20%;"></td>
			<td class="contentEntry2" style="padding-right: 40%;">Виводить матч
                <input type="checkbox" name="show_match" value="1" {show_match} />
            </td>
		</tr>
            <tr>
                <td class="contentEntry1" style="padding-left: 20%;"></td>
			    <td class="contentEntry2" style="padding-right: 40%;">Перевіряти чи коректно заповнені дані
                <input type="checkbox" id="correctDataCheck"/>
            </td>
            </tr>
            <tr>
                <td class="contentEntry1" style="padding-left: 20%;"></td>
			    <td class="contentEntry2" style="padding-right: 40%;">Автоматично починати і завершувати матч
                    <input type="checkbox" name="auto_in_out" value="1" {checked}/>
                </td>
            </tr>
			<tr>
                <td class="contentEntry1" style="padding-left: 20%;"></td>
			    <td class="contentEntry2" style="padding-right: 40%;">Додати або редагувати склади
                    <input type="checkbox" name="add_edit_storage" id="add_edit_storage" />
                </td>
            </tr>
		</table>
		<br/>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50%" class="contentEntry1" align="left" valign="top" id="players_owner"><!--{players_owner}-->
           {players-owners}
</td>
		<td width="50%" class="contentEntry1" align="left" valign="top" id="players_guest">
{players-guests}
</td>
	</tr>
</table>
    <br/><hr/>
    <table class="penalti_table" style="width: 30%; float: left;">
        <caption style="text-align: left;">&nbsp;<br/><br/></caption>
        <tr id="main_tr_penalti_own" {penalties_display}>
            <td style="text-align: right; padding-right: 30px;">
                Пенальті господарів <select name="penalti_owner" id="penalti_owner_count" onchange="createPenaltiOwner()">{penalti_owner}</select>
            </td>
        </tr>
        {penalties_players_own}
    </table>
    <table class="penalti_table" style="width: 70%; float: left;">
        <caption style="text-align: left; padding-left: 140px;"><b id="caption_penalti" style="cursor: pointer; font-size: 15px;">Післяматчеві пенальті</b><br/><br/></caption>
        <tr id="main_tr_penalti_gue" {penalties_display}>
            <td style="width: 55%;">
                <select name="penalti_guest" id="penalti_guest_count" onchange="createPenaltiGuest()">{penalti_guest}</select>Пенальті гостей
            </td>
            <td style="text-align: right;">
                Пенальті господарів <select name="penalti_owner_mini">{penalti_owner_mini}</select>
            </td>
            <td>
                <select name="penalti_guest_mini">{penalti_guest_mini}</select>Пенальті гостей
            </td>
        </tr>
        {penalties_players_gue}
    </table>
    <br/>
<h1 style='text-align: center; margin-top: 20px; clear: both;'>Онлайн трансляція</h1><hr/>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<!--<tr>
		<td width="100%" colspan="5" class="contentEntry1" align="center">{smile}</td>
	</tr>-->
<!--<tr>
        <td width="100%" class="contentEntry1" align="center">
            src = <input type="text" id="src" placeholder="Введіть шлях" />
            alt = <input type="text" id="alt" placeholder="Заголовок" />
            title = <input type="text" id="title" placeholder="Заголовок" />
            width = <input type="text" id="width" placeholder="Введіть ширину" />
        </td>
    </tr>-->
    <tr>
        <td class="contentEntry1" align="center"><b>Фото (подія)</b></td>
        <td class="contentEntry1" align="center"><b>Хвилина</b></td>
        <td class="contentEntry1" align="center"><b>Додана хв</b></td>
        <td class="contentEntry1" align="center"><b>Текст</b>
            <input type="button" style="float: right;" value="Жирним" onclick="make_bold('<b>', '</b>')" />
        </td>
        <td class="contentEntry1" align="center"><b>Зображення (матчу) (.jpg)</b></td>
        <td class="contentEntry1" align="center"><b>Відео</b></td>
    </tr>
    <tr>
        <td class="contentEntry1" align="center">
            <!--<img id="select_img_mico" src="" width="16"/>
            <input type="hidden" id="select_img_input" name="src_img" value="" />-->
            <select style="width:50px;" name="src_img" class="micos" value="" />{micos_img}</select>
        </td>
        <td class="contentEntry1" align="center">
            <input type="text" name="minute_translation" placeholder="хвилина" style="width: 42px; border-color: #ff6a00;" />
        </td>
        <td class="contentEntry1" align="center">
            <input type="text" name="minute_translation_add" placeholder="хвилина" style="width: 42px;" />
        </td>
        <td class="contentEntry1" align="center">
            <textarea name="text_translation" id="text_translation" placeholder="Впишіть подію..." style="width: 350px; height: 50px; border-color: #ff6a00;"></textarea>
        </td>
        <td class="contentEntry1" align="center">
            <input type="file" name="file" onchange="paste_img(this.value, 'src_file')" />
            <input type="hidden" name="src_file" id="src_file" value="" />
        </td>
        <td class="contentEntry1" align="center">
            <textarea name="video_for_translation" placeholder="Приклад: {l_fs:iframe}" style="width: 350px; height: 50px;"></textarea>
        </td>
    </tr>
    <!--
	<tr>
		<td class="contentEntry1" align="center" style="width: 100%;"><textarea style="width: 80%; font-size: 15px;" name="translation" id="translation" rows="20"></textarea></td>
	</tr>-->
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="hidden" name="owners" id="owners" value="{owners}" />
<input type="hidden" name="guests" id="guests" value="{guests}" />
<input type="submit" value="{button-action}" class="button" name="save_and_stay_here" />
{save_and_exit}
</td>
</tr>
</table>
</form>

{edit_translation}

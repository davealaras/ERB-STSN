$(document).ready(function(){
	var yes=false, no=true;
	enableboxes(no);
	$('#user_classes').change(function(e){
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
	});
	$('#user_classes').attr('disabled','disabled');
	$('.rights').click(function(e){
		var type = $(this).val();
		if(type==1){
			$('#user_classes').attr('disabled','');
		}else if(type==2){
			$('#user_classes').attr('disabled','disabled');
			$('#user_class').html('<div class="medium head">CUSTOMIZE</div>');
			enableboxes(yes);
			$('.checkbox').attr({checked:''});
		}
	});
	$('#user_classes').change(function(e){
		var type = $(this).find('option:selected').text();
		var role = $(this).find('option:selected').val();
		$('#user_class').html('<div class="medium head">'+type+'</div>');
		displayaccess(role, 1);
	});
	function displayaccess(role, acs_type){
		var roles ={};
		if (acs_type==1){
			roles=$.parseJSON($('#user_classes').parent().attr('data'));
		}else if(acs_type==2){
			var uid = $('#username').attr('fid');
			$.ajax({
				type: 'GET',
				url: 'ajax2.php?func=getcustom_acl&uid='+uid,
				success: function(data) {
					roles =  $.parseJSON(data);
					console.log(data);
				}
			});
		}
		var access = [];
		$.each(roles, function(i,r){
			if(r.role==role){
				var obj ={levelid :r.level, itemid : r.id};
				access.push(obj);
			}
		});
		if(access!=null){
			$('.checkbox').attr({checked:'',disabled:'disabled'});
			var chkbx = $('.checkbox');
			$.each(access, function(i,a){
				$.each(chkbx,function(j, cb){
					var levelid = $(cb).attr('levelid');
					var itemid = $(cb).attr('itemid');
					if(a.levelid==levelid && a.itemid==itemid){
						$(cb).attr('checked','checked');
					}
				});
			});
		}
	}
	$('#edit_access').button().click(function(e){
		if($('#username').val()==''){
			alert('Enter username first');
		}
		else if($('#username').attr('fid')==undefined){
			alert('Username not found');
		}else{
			$('#access_1, #access_2').attr('disabled','');
			if($('#access_1:checked')){
				 $('#user_classes').attr('disabled','');
			}
			$('#cancel_access').button( "option", "disabled", false );
			$('#edit_access').button( "option", "disabled", true );
		}
	});
	$('#cancel_access').button().click(function(e){
		$('.checkbox').attr({checked:''});
		$('#cancel_access').button( "option", "disabled", true );
		$('#edit_access').button( "option", "disabled", false );
		$('#access_1, #access_2, #user_classes').attr({disabled:'disabled','checked':''});
		$('#username').val('');
	});
	$('#cancel_access').button( "option", "disabled", true );
	$('#save_acces').button().click(function(e){
		var acl_data = $('#acl').serialize();
		var uid = $('#username').attr('fid');
		$.ajax({
				type: 'GET',
				url: 'ajax2.php?func=saveacl&uid='+uid,
				beforeSend : function(){ enableboxes(no);},
				data: acl_data,
				success: function(data) {
					enableboxes(no);
					alert('Access Updates');
					 $('#username').val('').focus();
					 $('#user_classes').attr('disabled','disabled');
					 $('#access_1').attr('disabled','disabled');
					 $('.checkbox').attr({checked:''});
				}
			});
	});
	function enableboxes(no){
		$('input[type="checkbox"]').attr('disabled', (no?'disabled':''));
	}
	$('#username').autocomplete({
			source:$.parseJSON($('#teachers').attr('data')),
			focus: function (event, ui) {
				  $(event.target).val(ui.item.label);
				  return false;
			},
			select: function(event, ui) {
					$(event.target).val(ui.item.label);
					$(event.target).attr('fid',ui.item.value);
					$.ajax({
						type: 'GET',
						url: 'ajax2.php?func=getacskey&uid='+ui.item.value,
						success: function(data) {
							var json_data = $.parseJSON(data);
							$('#edit_access').button( "option", "disabled", false );
							$('#access_'+json_data.access).attr('checked', 'checked');
							$('#role_'+json_data.role).attr('selected', 'selected');
							displayaccess(json_data.role, json_data.access);							
						}
					});
					return false;
			}						   
	});
});
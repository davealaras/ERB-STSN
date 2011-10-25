// JavaScript Document
$(document).ready(function(){
	var allowEdit=true;
	var allowAuto=false;
	enableFields(false);
	$('#student_personal_info').validationEngine(); // Fire validation engine
	$('#dob').datepicker({'changeYear':true, 'changeMonth': true, 'yearRange': 'c-50:c', 'dateFormat': 'yy-mm-dd'}); // Initialize dob
	$('#dob').bind('keypress', function(e) {     e.preventDefault(); }); // Disable input 
	if(allowAuto){
		$('.brgy').autocomplete(); // Initialize autocomplete
	}
	$('#student_personal_info input').attr('disabled','disabled');
	$('#student_personal_info select').attr('disabled','disabled');
	$('#sno').attr('disabled','');
	$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$( "#dialog-modal" ).dialog({
			autoOpen:false,
			height: 200,
			modal: true,
			draggable:false,
			buttons: {
				"Log in" :function(){
					var ovrrd_name=$('#ovrrd_name').val();
					var ovrrd_password=$('#ovrrd_password').val();
						$.ajax({
							type: 'POST',
							url: 'ajax.php',
							data: {user_name: ovrrd_name, password:ovrrd_password,  func:'login'},
							success:function(data){
								var json_data = $.parseJSON(data);
								console.log(json_data);
								if(json_data.status==0){
									$('#ovrrd_by').val(json_data.id);
									$( "#dialog-modal" ).dialog( "close" );
									enableFields(true);
								}else{
									$('#log_msg').html('<div class="warning">'+json_data.msg+"</div>");
									$('#ovrrd_password').val('');
									enableFields(true);
								}
							}
						});
					
				},
				"Cancel": function() {
					$( this ).dialog( "close" );
					enableFields(false);
				}
			},open: function(event, ui) { 
				var default_msg = $('#log_msg').attr('msg');
				$('#log_msg').html(default_msg);
				$('#ovrrd_name').val('');
				$('#ovrrd_password').val('');				
			}
		});
	if(allowAuto){
		 $('.link_list').autocomplete({
				focus: function (event, ui) {
					  $(event.target).val(ui.item.label);
					  return false;
				},
				select:function(event, ui) {
						$(event.target).val(ui.item.label);
						$(event.target).attr('data',ui.item.value);
						var link_to = $(event.target).attr('link_to');
						if(link_to!=null){
							$('#'+link_to).attr('fk', ui.item.value);
							console.log('#'+link_to);
						}
						return false;
				}
		 });
	
		$('.link_list').livequery("focus",function(){
			var id  = $(this).attr('fk');
			var data = $.parseJSON($(this).parent().attr('data'));
			var src =[];		
			$.each(data,function(j,s){
				var obj={};
				obj.label =s.c;
				obj.value=s.id;
				if(s.fk==id||id==undefined){
					src.push(obj);
				}
			});
			$(this).autocomplete("option", "source",src);
		});
	}
	//Update Educ Level
	
	//Update Gr/Yr Level	
	$('#gryrlvl').change(function(){
		var lvl = $('#gryrlvl').find('option:selected').val();
		var dep = $('#educlvl').find('option:selected').val();
		var data = $.parseJSON($('#section').parent().attr('data'));
		var opt_str='';
		$.each(data,function(i,e){
			if(e.level==lvl && e.deptcode==dep){
				opt_str+='<option value="'+e.seccode+'">'+e.section+'</option>';
			}
		});
		$('#section').html(opt_str);
	});	
	$('.smart_list').livequery("change",function(){
		var id  = $(this).find('option:selected').val();
		var link_to = $(this).attr('link_to');
		render_list(id, link_to,this);
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
	});
	$('.refer').livequery("change",function(){
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
	});
	$('.derived').livequery("keypress",function(e){
		e.preventDefault(); 
	});
	//Render appropriate listings
	function render_list(id, link_to, src){
		var target = '#'+link_to;
		var opt_str='<option value="#" class="default" >Select a level</option>';
		var munis= $.parseJSON($(target).parent().attr('data'));
		$.each(munis,function(i,e){
			if(e.fk==id){
				opt_str+='<option value="'+e.id+'">'+e.c+'</option>';
			}
		});
		$(target).html(opt_str);
	}
	
	//Copy Home to Mailing addressd
	$('.home_copy').keyup(function(){
		var affected = $('.same:checked');
		$.each(affected, function(i,obj){
			copyHomeAdd(obj);
		});
	});
	$('.same').click(function(){
		copyHomeAdd(this);
	});
	function copyHomeAdd(obj){
		var ch = $(obj).attr('checked');
		var affct = $(obj).attr('affect');
		var fldname=$(obj).attr('fieldname');
		if(ch){
			var td_coun = $( affct+' .td_coun').html();
			$(affct+' .td_coun').attr('bkup',td_coun);
			var country = $('#home_coun').val();
			$(affct+' .td_coun').html('<input type="text" class="large derived" value="'+country+'" name="'+fldname+'_coun" />');
			var td_prov =  $(affct+' .td_prov').html();
			$(affct+' .td_prov').attr('bkup',td_prov);
			var province =  $('#home_prov').val();
			$(affct+' .td_prov').html('<input type="text" class="large derived" value="'+province+'" name="'+fldname+'_prov"/>');
			var td_muni =  $(affct+' .td_muni').html();
			$(affct+' .td_muni').attr('bkup',td_muni);
			var municipality = $('#home_muni').val();
			$(affct+' .td_muni').html('<input type="text" class="xlarge derived" value="'+municipality+'" name="'+fldname+'_muni" />');
			var td_brgy = $(affct+' .td_brgy').html();
			$(affct+' .td_brgy').attr('bkup',td_brgy);
			var barangay = $('#home_brgy').val();
			$(affct+' .td_brgy').html('<input type="text" class="large derived"  value="'+barangay+'" name="'+fldname+'_brgy"/>');
			var td_subd = $(affct+' .td_subd').html();
			$(affct+' .td_subd').attr('bkup',td_subd);
			var subd = $('#home_subd').val();
			$(affct+' .td_subd').html('<input type="text" class="xlarge derived"  value="'+subd+'" name="'+fldname+'_subd"/>');
			var td_streeno = $(affct+' .td_streetno').html();
			$(affct+' .td_streetno').attr('bkup',td_streeno);
			var streetno = $('#home_streetno').val();
			$(affct+' .td_streetno').html('<input type="text" class="xlarge derived"  value="'+streetno+'" name="'+fldname+'_streetno"/>');
			var td_zip = $(affct+' .td_zip').html();
			$(affct+' .td_zip').attr('bkup',td_zip);
			var zip = $('#home_zip').val();
			$(affct+' .td_zip').html('<input type="text" class="small derived"  value="'+zip+'" name="'+fldname+'_zip"/>');
		}else{
			var td_coun = $(affct+' .td_coun').attr('bkup');
			$(affct+' .td_coun').attr('bkup','');
			$(affct+' .td_coun').html(td_coun);			
			var td_prov =  $(affct+' .td_prov').attr('bkup');
			$(affct+' .td_prov').attr('bkup', '');
			$(affct+' .td_prov').html(td_prov);
			var td_muni =  $(affct+' .td_muni').attr('bkup');
			$(affct+' .td_muni').attr('bkup', '');
			$(affct+' .td_muni').html(td_muni);
			var td_brgy = $(affct+' .td_brgy').attr('bkup');
			$(affct+' .td_brgy').html(td_brgy);
			var td_subd = $(affct+' .td_subd').attr('bkup');
			$(affct+' .td_subd').html(td_subd);
			var td_streetno = $(affct+' .td_streetno').attr('bkup');
			$(affct+' .td_streetno').html(td_streetno);
			var td_zip = $(affct+' .td_zip').attr('bkup');
			$(affct+' .td_zip').html(td_zip);
		}
	}
	$('#cancel_201').button({ disabled: true }).click(function(e){
		enableFields(false);
		
	});
	$('#go_201').button().click(function(e){
			var studentnumber = $('#sno').val();
			
			if(studentnumber==0){
				alert('Student Number required!');
				return;
			}			
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				beforeSend :function(){
					$('#sno').attr('disabled','disabled');
					$('#go_201').button( "option", "disabled", true );
					$('#progress-bar').html('<img src=img/ajax-loader.gif>Searching...');
					//Clean derived input fields for addresses
					$('.derived').parent().attr('bkup');
					$('.derived').removeClass('derived');
					$('.same').attr('checked','');
				},
				data: {sno: studentnumber,  func:'getstudent201'},
				success: function(data) {
					$('#progress-bar').html('');
					
					var stud201 = $.parseJSON(data);					
					if(stud201.sno!=null){
						if(allowEdit){
							//$('#action').html('<a class="edit_201" href="javascript:void()"> Edit</a> ? <a class="cancel_201" href="javascript:void()"> Cancel</a>');
							$('#lastname').val(stud201.lname);
							$('#firstname').val(stud201.fname);
							$('#middlename').val(stud201.mname);
							$.each($('#educlvl option'), function(i,r){
								if($(r).val()==stud201.alias[0].dept){
									$(r).attr('selected','selected');
									$('#educlvl').change();
								}
							});
							$.each($('#gryrlvl option'), function(i,r){
								if($(r).val()==stud201.alias[0].level){
									$(r).attr('selected','selected');
									$('#gryrlvl').change();
								}
							});
							$.each($('#section option'), function(i,r){
								if($(r).text()==stud201.alias[0].section){
									$(r).attr('selected','selected');
									$('#section').change();
								}
							});
							$('#dob').val(stud201.bday);
							$('#pob').val(stud201.pob);
							$('#gender').val(stud201.gender);
							$('#religion').val(stud201.reli);
							$('#citizen').val(stud201.citizen);
							$('#home_landno').val(stud201.land);
							$('#home_mobno').val(stud201.mob);
							$('#home_coun').val(stud201.h_c);
							$('#home_prov').val(stud201.h_p);
							$('#home_muni').val(stud201.h_m);
							$('#home_brgy').val(stud201.h_b);
							$('#home_subd').val(stud201.h_sb);
							$('#home_streetno').val(stud201.h_sn);
							$('#home_zip').val(stud201.h_z);
							$('#mail_coun').val(stud201.m_c);
							$('#mail_prov').val(stud201.m_p);
							$('#mail_muni').val(stud201.m_m);
							$('#mail_brgy').val(stud201.m_b);
							$('#mail_subd').val(stud201.m_sb);
							$('#mail_streetno').val(stud201.m_sn);
							$('#mail_zip').val(stud201.m_z);
							$('#parent_name').val(stud201.p_n);
							$.each($('#parent_rel option'),function(i,r){
								if($(r).val()==stud201.p_r){
									$(r).attr('selected','selected');
								}
							});
							$('#parent_occupation').val(stud201.p_o);
							$('#parent_streetno').val(stud201.p_sn);
							$('#parent_muni').val(stud201.p_m);
							$('#parent2_name').val(stud201.s_n);
							$('#parent2_occupation').val(stud201.s_o);
							$.each($('#parent2_rel option'),function(i,r){
								if($(r).val()==stud201.s_r){
									$(r).attr('selected','selected');
								}
							});
							$('#parent2_streetno').val(stud201.s_sn);
							$('#parent2_muni').val(stud201.s_m);
							enableFields(true);
						}else{
							$( "#dialog-modal" ).dialog( "open" );
						}
					}else{
						//$('#action').html('<a class="add_201" href="javascript:void()">Add</a> ? <a class="cancel_201" href="javascript:void()"> Cancel</a>');
						enableFields(true);
						
					}
				}
			});
		
	});
	//Edit mode
	$('.over_201').livequery("click",function(){
		$('#student_personal_info input').attr('disabled','');
		$('#student_personal_info select').attr('disabled','');
		$('#sno').attr('disabled','disabled');
		$('#action').html('');
	});
	//Add mode
	$('.add_201').livequery("click",function(){
		enableFields();
	});
	//Save 201
	$('#save_201').livequery("click",function(){
		$('#sno').attr('disabled', '');
		var details = $('#student_personal_info').serialize();
		$('#student_personal_info input').attr('disabled','');
		$('#student_personal_info select').attr('disabled','');
		var studentnumber = $('#sno').val();
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				beforeSend: function(){
					$( ".tab .tab-content" ).slideUp();
					$('#progress-bar').html('<img src=img/ajax-loader.gif>Saving...');
					$('#save_201').css({'display':'none'});
					enableFields(false);
				},
				data: {sno: studentnumber,  func:'check201'},
				success: function(data) {
						var json_data = parseInt(data);
						var ovrrd_by = $('#ovrrd_by').val();
						if(json_data!=0&&ovrrd_by=='null'){
							$( "#dialog-modal" ).dialog( "open" );
						}else{
							$.ajax({
								type: 'POST',
								url: 'ajax.php?func=savestudent201',
								data: details,
								success: function(data) {
										$('#progress-bar').html('');
										var json_data= $.parseJSON(data);
										console.log(data);
										alert('Data has been saved');
										$('#educlvl').prepend('<option value="#" class="default" selected>Select department</option>');
										$('#gryrlvl').html('');
										$('#section').html('');
										$('#ovrrd_by').val('null');	
										$( ".tab .tab-content" ).slideDown();
								}
							});						
						}
				}
				});
	});
	function enableFields(enable){
		if(enable){
			$('#student_personal_info input').attr('disabled','');
			$('#student_personal_info select').attr('disabled','');
			$('#sno').attr('disabled','disabled');
			$('#action').html('');
			$('#go_201').button( "option", "disabled", true );
			$('#cancel_201').button( "option", "disabled", false );
			$('#save_201').css({'display':''});
		}else {
			$('#student_personal_info input:not(#sno)').val('');
			$('#student_personal_info input').attr('disabled','disabled');
			$('#student_personal_info select').attr('disabled','disabled');
			$('#sno').attr('disabled','');
			$('#sno').val('');
			$('#action').html('');
			$('#go_201').button( "option", "disabled", false );
			$('#cancel_201').button( "option", "disabled", true );
			$('#save_201').css({'display':'none'});
		}
	}
});;
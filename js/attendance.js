$(document).ready(function() {
var HEADER_ABSENT = "ABST";
	$('input').livequery('keypress', function(e){
		if($(this).length==0&& e.which ==8){
			e.preventDefault();			
		}
	});
	var attendancehtml  = $('#div-tbAttendance').html();
	var conducthtml  = $('#div-tbConduct').html();
	$('#RIGHT-CONTENT-HOME').animate({opacity:'1'},1000);
	$('#LEFT-CONTENT').animate({opacity:'1'},1000);
	$.ajax({
			type: 'GET',
			url: 'ajax.php',
			data: {func:'getSession'},
			success: function(data) {
				var json_data = $.parseJSON(data);
				if(json_data!=null){
					$('#LEFT-CONTENT').remove();
					load_sysdef(json_data);
					$('#logout').show();
				}
				else{
					$('#logout').hide();
					$('#login').show();
				}
			}
	});
	var templates;
	var components;
	var measurables;
	var periods;
	var conduct;
	var attendance;
	var ztb_w;
	var ztb_h;
	var sys_timeout;
	var LOADED_PERIOD;
	$('#menu li').hover(function() {
		$(this)
		.find('ul')
		.stop(true, true)
		.slideDown('fast');
	}, function() {
		$(this)
		.find('ul')
		.stop(true,true)
		.slideUp('fast');
	});
	//load button
	$('.load_btn').click(function(){
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		var level = $('#load').find('option:selected').attr('level');
		var dept = $('#load').find('option:selected').attr('dept');
		if(dept=='PS' && level == 1){
			$('#conductlabel').text('Affective and Psychomotor Development');
			$('#postlabel').text("POST Affective and Psych Dev't");
		}else if(dept=='PS' && level > 1){
			$('#conductlabel').text('Skills and Conduct');
			$('#postlabel').text('POST Skills and Conduct');
		}else{
			$('#conductlabel').text('Traits');
			$('#postlabel').text('POST Traits');
		}
		$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'func':'checkposting'},
				timeout: sys_timeout,
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading components...');
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying');
					load_routine_components(classcode,sy, period);
				},
				success: function(data) {
					var json_data = $.parseJSON(data);
					load_routine(classcode, sy, period, level, dept);
				}
			});
		
	});
	function load_routine(classcode,sy, period,level, dept){
			$('#debug').html('Loading...');
			$('.tab-content').animate({opacity:'0.1'},1000);
			$('#div-tbAttendance').html('').html(attendancehtml);
			$('#div-tbConduct').html('').html(conducthtml);
			$('#tbAttendance').find('.students').remove();
			$('#tbConduct').find('.students').remove();
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'level' : level, 'deptcode': dept, 'func':'getcomponents'},
				timeout: sys_timeout,
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading components...');
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying');
					load_routine(classcode,sy, period);
				},
				success: function(data) {
					var json_data = $.parseJSON(data);
					load_routine_studnrol(classcode,sy, period,level, dept);
				}
			});
			
	}
	function load_routine_components(classcode,sy, period, level, dept){
		//HIDE when trying to load a new recordset
		$('#post-grades').hide();
		$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'level' : level, 'deptcode': dept, 'func':'getcomponents'},
				timeout: sys_timeout,
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading components...');
					$('#postAttendance, #postConduct').hide();
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying');
					load_routine_components(classcode,sy, period);
				},
				success: function(data) {
					$('#debug').html('Done');
					//$('#debug').html(data);
					var json_data = $.parseJSON(data);				  
					var rwctr = json_data.rownum;
					components = json_data.components;
					measurables = json_data.measurables;
					conduct = json_data.conduct;
					isposted = json_data.isposted;
					attendance = json_data.attendance;
					$('#postAttendance, #postConduct').show();
					
					if(json_data.postAtt){
						$('#postAttendance').hide();
					}
					if(json_data.postCond){
						$('#postConduct').hide();
					}
					//Grand total
					//Correct display when post state is known
					//SHOW If posted HIDE if not posted
					if(isposted){
						$('#post-grades').hide();
					}
					else{
						$('#post-grades').show();
					}
					var gtotal=0;
				
					//Populate attendance template
					var colctr=0;
					$.each(attendance, function(i,result){
						var hdrs = result.hdrs;
						var mo= result.month;
						var dys = result.days;
						var months = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
						//var maxG =result.maxG;
						//var minG =result.minG;
						var cspan =0;
						$.each(hdrs, function(j, r){
							var hdr = r.hdr;
							var col ='<td class="micro  attendance"><div style="width:35px;overflow:none;"><span style="width:15px;overflow:hidden;height:15px;text-align:left;">'+hdr+'</span></div></td>';
							$(col).insertBefore('.eoa');
							if(j==0||j==2){
								$('<td style="text-align:right"><span class="micro smartbox" hdr="'+hdr+'"colnum="'+colctr+'" mo="'+mo+'" maxitems="'+dys+'" >'+dys+'</span></td>').insertBefore('#tbAttendance .end');
							}else{
								if(json_data.postAtt){
									$('<td><div  isposted="true" class="micro smartbox" saveas="tbAttendance" hdr="'+hdr+'"colnum="'+colctr+'" maxitems="'+dys+'" mo="'+mo+'" ><div></td>').insertBefore('#tbAttendance .end');
								}else{
									$('<td><input isposted="false"  type="text" class="micro smartbox" saveas="tbAttendance" hdr="'+hdr+'"colnum="'+colctr+'" maxitems="'+dys+'" mo="'+mo+'" /></td>').insertBefore('#tbAttendance .end');
								}
							}
							cspan+=1;
							colctr+=1;
						});
						var monhdr = '<td colspan="'+cspan+'" class="head">'+months[mo]+'</td>';
						$(monhdr).insertBefore('.eomh');
					});
					//Populate conduct template
					if(conduct!=null){
						$.each(conduct, function(i,result){
							var hdr = result.hdr;
							var maxG =result.maxG;
							var minG =result.minG;
							var col ='<td class="small head conduct"><div style="width:35px;overflow:none;"><span style="height:15px;text-align:center;">'+hdr+'</span></div></td>';
							$(col).insertBefore('.eoc');
							if(json_data.postCond){
								$('<td style="text-align:center"><div  isposted="true" class="micro smartbox" maxlength="1" saveas="tbConduct" hdr="'+hdr+'" colnum="'+(i+1)+'" maxitems="'+maxG+'" minG="'+minG+'"></div></td>').insertBefore('#tbConduct .end');
							}else{
								$('<td style="text-align:center"><input isposted="false" type="text" class="micro smartbox" maxlength="1" saveas="tbConduct" hdr="'+hdr+'" colnum="'+(i+1)+'" maxitems="'+maxG+'" minG="'+minG+'"/></td>').insertBefore('#tbConduct .end');
							}
						});
					}
					//Populate smart box
					populate_smartbox();
					
					//RawScore Drop
					var row_record = $('#rawscores .students');
					$.each(row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							$(res).find('input').addClass('disabled');
							$(res).find('input').val('D');
							$(res).find('td .smartbox').text('D');
						}
					});
					//Equivalent Drop
					var e_row_record = $('#equivalent .students');
					$.each(e_row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							//$(res).find('.dumbbox').addClass('disabled');
							$(res).find('td .dumbbox').text('D');
						}
					});
					//Equivalent Drop
					var s_row_record = $('#summary .students');
					$.each(s_row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							//$(res).find('.dumbbox').addClass('disabled');
							$(res).find('td .dumbbox').text('D');
							$(res).find('td .totalbox').text('D');
						}
					});
					//Overall Drop
					var o_row_record = $('#overall .students');
					$.each(o_row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							//$(res).find('.dumbbox').addClass('disabled');
							$(res).find('td .totalbox').text('D');
						}
					});
				}
			});
	}
	function load_routine_studnrol(cs,s, p, l,d){
		var classcode = cs;
		var sy = s;
		var period =p;
		var level = l;
		var dept =d;
		$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period,'func':'getstudnrol'},
				timeout: sys_timeout,
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading students...');
				},
				error: function (){ 
					$('#progress-bar').html('Time out. Retrying');
					load_routine_studnrol(classcode,sy, period, level, dept);
				},
				success: function(data) {
				var json_data = $.parseJSON(data);				  
				var students = json_data.students;
				var curr_g_flg='', prev_g_flg='';
				$.each(students,function(i,res){
						var sno = res.sno;
						var fullname = res.fullname;
						var status = res.status;
						var gender = res.gender;
						curr_g_flg = gender;
						var row ='';
						if(curr_g_flg!=prev_g_flg){
							gender_alias = gender=='M'? 'Boys': 'Girls';
							row = '<tr class="students gender">';
							row +='<td class="jumbo head" colspan="2">'+gender_alias+'</td>';
							row +='</tr>';
							$(row).insertBefore('.list');
						}
							prev_g_flg = curr_g_flg;
						
							row = '<tr class="students" rownum="'+i+'" status="'+status+'" >';
							row +='<td class="studno">'+sno+'</td>';
							row +='<td class="jumbo fullname">'+fullname+'</td>';
							row +='<td class="end"></td>';
							row +='</tr>';
						$(row).insertBefore('.list');
					});
					
					load_routine_components(classcode,sy, period,level, dept);
				}
					
				
			});
	}
	function populate_smartbox(){
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'func':'getatt+conduct'},
				timeout: sys_timeout*4,
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading attendance...');
				},
				error: function (){ 
					$('#progress-bar').html('Time out. Retrying...').delay(1000);
					populate_smartbox(); 
				},
				success: function(data) {
					$('.tab').slideDown();
					$('#progress-bar').html('').delay(2000);
					var json_data = $.parseJSON(data);
					var smartboxes = $('#tbAttendance').find('.smartbox');
					
					//Populate smartboxes
					$.each(smartboxes, function(i,res){
						var box =$(res);
						var header = $(res).attr('hdr');
						var row = $(res).parent().parent();
						var studentnumber = $(row).find('.studno').text();
						var month = $(res).attr('mo');
						 if( json_data.attendance!=null){
							 $.each( json_data.attendance, function(i,record){				   
								var sno = record.sno;
								var mo =record.month;
								var score = record.absent;
								if(studentnumber==sno && month==mo){
									if(header==HEADER_ABSENT){
										if($(box).attr('isposted')=="true"){
											$(box).text(score);
										}else{
											$(box).val(score);
										}
									}
								}
							});
						}
						
					});
					
					//Smartboxes for conduct
					smartboxes = $('#tbConduct').find('.smartbox');
					//Populate smartbox for conduct
					$.each(smartboxes, function(i,res){
						var header = $(res).attr('hdr');
						var row = $(res).parent().parent();
						 var studentnumber = $(row).find('.studno').text();
						 if(json_data.conduct!=null){
							 $.each(json_data.conduct, function(i,record){				   
								var sno = record.sno;
								var hdr =record.hdr;
								var score =record.score;
								if(studentnumber==sno && header==hdr){
									var mxitms = $(res).attr('maxitems');
									if($(res).attr('isposted')=="true"){
										$(res).text(score);
									}else{
										$(res).val(score);
										//score>mxitms?$(res).addClass('invaliddata'):$(res).removeClass('invaliddata');
									}

									if(score=='IGN'){
										$(res).attr('IGN', true);
										$(res).removeClass('invaliddata');
									}
								}
							});
						}
					});
					$('#tbConduct').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#tbAttendance').ztable({tableWidth: ztb_w, tableHeight: ztb_h, colHeight: 55});
					$('.tab-content').animate({opacity:'1'},500);
				}
			});
	}

	$('.smartbox').livequery('keypress',function(e){
		var saveas = $(this).attr("saveas");
		var perfectscore = parseFloat($(this).attr('maxitems'));
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		var hdr =$(this).attr('hdr');
		var sno, ccode;
		var rawscore;
		var items = $('.ztable-data-container #'+saveas).find('.smartbox');
		if(saveas=='tbConduct'){
			var isValid = (e.which>=97 && e.which<=100)|| (e.which>=65 && e.which<=68);
			var isIgnore = e.which==105; // KEYCODE i
			var ctr=1;
			var trace="";
			if(($(this).hasClass('disabled')||isIgnore)||!isValid){
				e.preventDefault();
				if(isIgnore){
					$(this).val('');
				}
			}
			if(e.which==13){
				var colnum = parseInt($(this).attr('colnum'));
				var row = $(this).parent().parent();
				var rownum = $(row).attr('rownum');	
				var sno =$(row).find('.studno').text();
				var mo=parseInt($(this).attr('mo'));
				var hdr =  $(this).attr('hdr');
				var maxitems = parseInt($(this).attr('maxitems'));
				var val = parseInt($(this).val());
				if($(this).hasClass('disabled')){
					e.preventDefault();
				}
				else{
					rawscore =  $(this).val().toUpperCase();
					$(this).val(rawscore);
				}
				$.ajax({
					type: 'GET',
					url: 'ajax.php',
					data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'rawscore': rawscore, 'func':'saveconduct'},
					success: function(data) {
					}
				});
			}
		}else if (saveas=='tbAttendance'){
			var isValid = (e.which>=46 && e.which<=59);
			var isIgnore = e.which==105; // KEYCODE i
			if(($(this).hasClass('disabled')||isIgnore)||!isValid){
				e.preventDefault();
				if(isIgnore){
					$(this).val('');
				}
			}
			if(e.which==13){
				var colnum = parseInt($(this).attr('colnum'));
				var row = $(this).parent().parent();
				var rownum = $(row).attr('rownum');	
				var sno =$(row).find('.studno').text();
				var mo=parseInt($(this).attr('mo'));
				var hdr =  $(this).attr('hdr');
				var maxitems = parseInt($(this).attr('maxitems'));
				var val = parseInt($(this).val());
				if($(this).hasClass('disabled')){
					e.preventDefault();
				}
				else{
					rawscore =  parseInt($(this).val());
					if(!isNaN(rawscore)){					
						$(this).attr('IGN',false);
					}
					$(this).attr('style','');
					if(rawscore!=-3){
						if((rawscore<0||rawscore>maxitems)){
								alert("Invalid data entry! Score should be between 0 and "+ maxitems);
								$(this).addClass('invaliddata');
								return;
						}else{
								$(this).removeClass('invaliddata');
								$.ajax({
									type: 'GET',
									url: 'ajax.php',
									data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'val': val, 'mo': mo, 'func':'save_attendance'},
									success: function(data) {
									}
								});
						}
					}
				}
			}
		}
		//JUMP 
		for(var index=items.length; index>=0;index--){
			var res =items[index];
			var cn  = $(res).attr('colnum');
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			if(cn==colnum){
				if(rn==parseInt(rownum)+1){
					$(res).focus();
					return;
				}
				
			}
		}
		
	});

	
	//.tab Animation
	$('.tab-header').click(function(){
		var src = $(this);
		var dis = $(src).find('.indicator').text();
		$(src).find('.indicator').text(dis=='+'?'-':'+');
		$(this).parent().find('.tab-content').slideToggle(500);		
		}
	 );
	//Login 
	$('#login-btn').click(function(){
			var user_name= 	$('#user_name').val();
			var password=	$('#password').val();
			//console.log(user_name);
			//console.log(password);
			$('#login-form form').animate({opacity:'0.4'},200);
			$('#response').animate({opacity:'0'},200);
			$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: {'user_name' :user_name, 'password':password, 'func':'login'},
				success: function(data) {
					var o= $.parseJSON( data);
					if(o.status==1){
						$('#response').animate({opacity:'1'},200);
						$('#response').html(o.msg);
						$('#login-form form').animate({opacity:'1'},200);
					}
					else{
						$('#login-form form').animate({opacity:'0'},200);
						$('#login-form').hide().html(o.msg).fadeIn('slow');
						$('#logout').show();
						//Load System Default
						load_sysdef(o);
					}
				}
			});
	});
	function load_sysdef(o){	
				$.getJSON('ajax.php?token='+o.token+'&func=getInfo&id='+o.id, function(data){
							$('#faculty_id').val(data.id);
							$('#full_name').val(data.full_name);
				});
				$.getJSON('ajax.php?func=getSysDefa&token='+o.token, function(data){
							var sy = data.active_sy;
							var base =data.base;
							ztb_w = data.ztb_w;
							ztb_h = data.ztb_h;
							sys_timeout = data.timeout;
							periods = data.period;
							var str = sy + ' - '+ (sy+1);
							var period_str="";
							//templates = data.templates;
							$.each(periods, function(i,res){
								period_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							
							var component_str="";
							$.each(data.component, function(i,res){
								component_str +='<option value="'+res.code+'">'+res.desc+'</option>';
							});
							var load_str="";
							$.each(data.advisory, function(i,res){
								load_str +='<option sy="'+res.sy+'"value="'+res.sec_code+'-'+res.comp_code+'" dept="'+res.dept+'" level="'+res.level+'">'+res.dept+' '+res.level+'-'+res.sec+'</option>';								
							});
							/*var temp_str="";
							$.each(data.templates, function(i,res){
								temp_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							*/
							$('#load').html(load_str);
							var subjects = $('#load').find('option');
							correct_list(subjects, sy);
							$('#description').html('<option value="###" class="default">Select Description</option>'+component_str);
							$('#period').html(period_str);
							//$('#templates').html('<option value="###" class="default">Select Template</option>'+temp_str);
							$('#sy').html('<option value="'+sy+'">'+str+'</option> <option value="'+(sy+1)+'">2012-2013</option>');
							$('#base').attr('def',base);
							$('#base').val(base);
						});
						$('#content').animate({opacity:'1'},1000);
						$('#LEFT-CONTENT').remove();
						$('#RIGHT-CONTENT-HOME .overview-table-inner').animate({opacity:'0'},1000).remove();
						$('#RIGHT-CONTENT-HOME').html('<div style="height:500px;"></div>');
						$('.tab-header').prepend('<span class="indicator">+</span>');
						$('.tab-header').next().prepend('<div class="nodatafound"></div>');
	}
	//EXPORT MODULE
	$('#export-rawscore').click(function(){
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		window.location.href = 'ajax.php?classcode='+classcode+'&sy='+sy+'&period='+period+'&func=exportraw';
	});
	$('#sy').change(function(){
		var sy =$(this).val();
		var subjects = $('#load').find('option');
		correct_list(subjects, sy);
	});
	function correct_list(subjects, sy){
		$.each(subjects, function (i,res){
				if($(res).attr('sy')!=sy){
					$(res).css('display','none');
					$(res).attr('selected', false);
				}
				else{
					$(res).css('display','');
					$(res).attr('selected', true);
				}
			});
	}

	$('.post_btn').click(function(){
		var datasource =  $(this).attr('datasource');
		var rwscr = $('.ztable-data-container #'+datasource).find('input');
		var allowPost = true;
		for(var i=0; i<rwscr.length; i+=1){
			var res = rwscr[i];
			console.log(res);
			if($(res).val()==''){
				alert('Incomplete raw scores');
				allowPost =false;
				break;
			}
			if($(res).hasClass('invaliddata')){
				alert('Could not post grade. There are invalid data.');
				allowPost =false;
				break;
			}
		}
		
		if(allowPost){
			$('.tab-header').click();
			var classcode = $('#load').find('option:selected').val();
			var sy = $('#sy').find('option:selected').val();
			var selected_period = $('#period').find('option:selected').val();
			var level = $('#load').find('option:selected').attr('level');
			var dept = $('#load').find('option:selected').attr('dept');
			var field = datasource;
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Posting data...');
				},
				error: function (){ 
					
				},
				data: {'func':'postAttCond','seccode':classcode,'sy':sy, 'period':selected_period, 'field' :field},
				success: function(data) {
					$('#progress-bar').html('');
					alert('Record posted');
					$('.tab-header').click();
					load_routine(classcode, sy, selected_period, level, dept);
				}
			});
		}
	});
});

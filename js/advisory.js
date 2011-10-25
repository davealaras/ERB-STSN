$(document).ready(function() {
var SUMMARY_ROUND =2;
var EQUIVALENT_ROUND=0;
var OVERALL_ROUND=0;
var FINALAVE_ROUND =2;
var FINALRATE_ROUND = 2;
var TCGS = 'Temporary Consolidated Grading Sheet';
	$('input').livequery('keypress', function(e){
		if($(this).length==0&& e.which ==8){
			e.preventDefault();			
		}
	});
	
	var rawscorehtml = $('#div-rawscore').html();
	var equivalenthtml = $('#div-equivalent').html();
	var summaryhtml  = $('#div-summary').html();
	var overallhtml  = $('#div-overall').html();
	var conducthtml  = $('#div-tbConduct').html();
	$('#RIGHT-CONTENT-HOME').animate({opacity:'1'},1000);
	$('#LEFT-CONTENT').animate({opacity:'1'},1000);
	
	
	$.ajax({
			type: 'GET',
			url: 'ajax.php',
			data: {func:'getSession'},
			success: function(data) {
				var json_data = $.parseJSON(data);
				var arrObj = eval('(' +data+ ')');
				if(arrObj.length!=0){
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
	var subject_details;
	var templates;
	var components;
	var measurables;
	var periods;
	var conduct;
	var ztb_w;
	var ztb_h;
	var sys_timeout=1000;
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
		$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'func':'checkposting'},
				
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading components...');
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying');
					load_routine(classcode, sy, period, level, dept);
				},
				success: function(data) {
					var json_data = $.parseJSON(data);
					load_routine(classcode, sy, period, level, dept);				
				}
			});
		
	});
	function load_routine(classcode,sy, period,level, dept){
			$('.tab-content').animate({opacity:'0.1'},1000);
			$('#div-rawscore').html('').html(rawscorehtml);
			load_routine_studnrol(classcode,sy, period,level, dept);			
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
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'level':level, 'dept':dept,'func':'getclass'},

				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading students...');
					$('.post_btn, .export_btn, .print_btn').hide();
				},
				error: function (){ 
					$('#progress-bar').html('Time out. Retrying');
					load_routine_studnrol(classcode,sy, period, level, dept);
				},
				success: function(data) {
				$('#progress-bar').html('');
				var json_data = $.parseJSON(data);				  
				var students = json_data.students;
				var subjects = json_data.subjects;
				var attendance =  json_data.attendance;
				var is_sec_posted = json_data.is_section_posted;
					$('.post_btn, .export_btn, .print_btn').show();
					if(is_sec_posted){
						$('.post_btn').hide();
					}else{
						$('.post_btn').show();
					}
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
							row = '<tr class="students">';
							row +='<td class="jumbo head" colspan="2">'+gender_alias+'</td>';
							row +='</tr>';
							$(row).insertBefore('.list');
						}
							prev_g_flg = curr_g_flg;
						
							row = '<tr class="students '+gender+' boygirl" rownum="'+i+'" status="'+status+'" >';
							row +='<td class="studno">'+sno+'</td>';
							row +='<td class="jumbo fullname">'+fullname+'</td>';
							row +='<td class="end"></td>';
							row +='</tr>';
						$(row).insertBefore('.list');
					});
					
					//Special row CHANGE
					s_row = '<tr class="students special" >';
							s_row +='<td></td><td></td>';
							s_row +='<td class="change"></td>'
							s_row +='</tr>';
						$(s_row).insertAfter('.list'); 
					$.each(subjects,function(i, res){
						var alias = res.alias;
						var nomen  = res.nomen;
						var sub_code = res.comp_code;
						var weight = res.weight;
						var under = res.under;
						var col ='<td class="small head measurables head_fix" compcode="'+sub_code+'"><div class="hdrsmall hdrsmall_fix"><a class="tip" title="'+nomen+'">'+alias+'</a></div> </td>';
						$(col).insertBefore('.eof');
						var change_markup="";
						var statusclass = res.status ==3?"posted":"";
						if(res.status ==1 && res.period == period){
							change_markup = '<a class="art-button change_btn" comp_code="'+sub_code+'" href="javascript:void(0)">Change</a>';
						}
						$('<td>'+ change_markup+'</td>').insertBefore('#gradesheet .students .change');
						if(under=='A'){
							$('<td style="text-align:center;"><span class="micro finalgradebox makabox fingrdbox" hdr="'+sub_code+'" weight="'+weight+'" maka="0" >NG</span></td>').insertBefore('#gradesheet .students .end');
						}else if (under=='F'){
							$('<td style="text-align:center;"><span class="micro finalgradebox gradebox fingrdbox '+statusclass+'" under="'+under+'" hdr="'+sub_code+'" weight="'+weight+'">NG</span></td>').insertBefore('#gradesheet .students .end');
						}else{
							$('<td style="text-align:center;"><span class="micro finalgradebox gradebox '+statusclass+'" under="'+under+'" hdr="'+sub_code+'" weight="'+weight+'">NG</span></td>').insertBefore('#gradesheet .students .end');
						}
					});
					//DEPORTMENT
					
					var col ='<td class="small head measurables " ><div class="hdrsmall hdrsmall_fix"><a class="tip" title="Deporment">D</a></div></td>';
						col +='<td></td>';
						$(col).insertBefore('.eof');
						$('<td style="text-align:center;"><span class="micro depobox " hdr="D" >NG</span></td><td></td>').insertBefore('#gradesheet .students .end');
					
					//FINAL
					var col ='<td class="small head measurables " ><div class="hdrsmall hdrsmall_fix"><a class="tip" title="Final Average">F Ave</a></div></td>';
						col+='<td class="small head measurables" ><div class="hdrsmall hdrsmall_fix"><a class="tip" title="Final Rating">F Rate</a></div></td>';
						$(col).insertBefore('.eof');
						$('<td style="text-align:center;"><span class="micro finavebox " hdr="F Ave" finave="0" divisor="0" >NG</span></td>').insertBefore('#gradesheet .students .end');
						$('<td style="text-align:center;"><span class="micro finrtebox " hdr="F Rate" finrate="0" >NG</span></td>').insertBefore('#gradesheet .students .end');
			
					
					//Attendance
					var months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
					$.each(attendance, function(i,result){
						var mon = result.month;
						var day = result.days;
						var col ='<td class="small head measurables head_mon" ><div class="hdrsmall hdrsmall_fix"><a class="tip" title="Days Present">'+months[mon]+'</a></div></td>';
							$(col).insertBefore('.eof');
							$('<td style="text-align:center;"><span class="micro attbox " month="'+mon+'" days="'+day+'" >xx</span></td>').insertBefore('#gradesheet .students .end');
					});
					$('#gradesheet').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					populate_gradebox();
				}
					
				
			});
	}
	function populate_gradebox(){
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'func':'get_cgs_scores'},
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading grades...');
				},
				error: function (){ 
					$('#progress-bar').html('Time out. Retrying...').delay(1000);
					populate_gradebox(); 
				},
				success: function(data) {
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Recalculating...');
					var json_data = $.parseJSON(data);
					var gradeboxes = $('.ztable-data-container #gradesheet').find('.gradebox');
					var makaboxes = $('.ztable-data-container #gradesheet').find('.makabox');
					var depoboxes =  $('.ztable-data-container #gradesheet').find('.depobox');
					var attboxes = $('.ztable-data-container #gradesheet').find('.attbox');
					var fingrdboxes = $('.ztable-data-container #gradesheet').find('.fingrdbox');
					var finaveboxes = $('.ztable-data-container #gradesheet').find('.finavebox');
					var finrteboxes =  $('.ztable-data-container #gradesheet').find('.finrtebox');
					//Populate gradeboxes
					$.each(gradeboxes, function(i,res){
						var header = $(res).attr('hdr');
						var row = $(res).parent().parent();
						 var studentnumber = $(row).find('.studno').text();
						 if(json_data.cgs_grades!=null){
							 $.each(json_data.cgs_grades, function(i,record){				   
								var sno = record.sno;
								var hdr =record.comp_code;
								var grade =record.grade;
								var display = record.display;
								if(studentnumber==sno && header==hdr){
									var g =parseFloat(grade);
									var d = parseFloat(display);
									var w = parseFloat($(res).attr('weight'));
									if(!isNaN(w)){
										$(res).attr('maka', g*w);
									}
									if(isNaN(d)){
										d = display; 
									}else{
										d = roundNumber(d,OVERALL_ROUND);
									}
									$(res).text(d);
									$(res).attr('grade', grade);
									failGrade(res);
								}
							});
						}
					});
					//Populate gradeboxes
					$.each(depoboxes, function(i,res){
						var row = $(res).parent().parent();
						 var studentnumber = $(row).find('.studno').text();
						 if(json_data.deportment!=null){
							 $.each(json_data.deportment, function(i,record){				   
								var sno = record.sno;
								var score =record.rawscore;
								if(studentnumber==sno){
									$(res).text(score);
									failGrade(res);
								}
							});
						}
					});
					//Populate attboxes
					$.each(attboxes, function(i,res){
						var row = $(res).parent().parent();
						 var studentnumber = $(row).find('.studno').text();
						 var school_days =$(res).attr('days');
						 var curr_month = $(res).attr('month');
						 if(json_data.attendance!=null){
							 $.each(json_data.attendance, function(i,record){				   
								var sno = record.sno;
								var absent =record.absent;
								var mon = record.month;
								if(studentnumber==sno&&mon==curr_month){
									$(res).text(school_days-absent);
								}
							});
						}
						
					});
					//Compute MAKABAYAN
					$.each(makaboxes, function(i,res){
						var row = $(res).parent().parent();
						var studentnumber = $(row).find('.studno').text();
						var hdr = $(res).attr('hdr');
						$.each(gradeboxes, function(j,record){
						//console.log(record);
							var r = $(record).parent().parent();
							var sno = $(r).find('.studno').text();
							var under =$(record).attr("under");
							if(sno==studentnumber && under==hdr){
								var value = parseFloat($(record).attr("maka"));
								var total = parseFloat($(res).attr("maka"));
								if(value){
									total+=value;
									$(res).attr("maka", total);
								}
							}
						});
						var makavalue = parseFloat($(res).attr("maka"));
						$(res).attr("maka", roundNumber(makavalue,SUMMARY_ROUND));
						$(res).attr("grade",roundNumber(makavalue,FINALAVE_ROUND));
						$(res).text(roundNumber(makavalue,OVERALL_ROUND));
					});
					
					//Populate finaveboxes
					$.each(finaveboxes, function(i,res){
						var row = $(res).parent().parent();
						var studentnumber = $(row).find('.studno').text();
						$.each(fingrdboxes, function(j,record){
							var r = $(record).parent().parent();
							var sno = $(r).find('.studno').text();
							if(sno==studentnumber){
								var value = parseFloat($(record).attr("grade"));
								var total = parseFloat($(res).attr("finave"));
								var divisor = parseFloat($(res).attr("divisor"));
								if(value){
									total+=value;
									divisor+=1;
									$(res).attr("finave", total);
									$(res).attr("divisor", divisor);
								}
							}
						});
						var finavevalue = parseFloat($(res).attr("finave"));
						$(res).attr("finave", roundNumber(finavevalue,FINALAVE_ROUND));
						$(res).text(roundNumber(finavevalue,FINALAVE_ROUND));
					});
					
					//Populate finaveboxes
					$.each(finaveboxes, function(i,res){
						var row = $(res).parent().parent();
						var studentnumber = $(row).find('.studno').text();
						var total = parseFloat($(res).attr("finave"));
						var divisor = parseFloat($(res).attr("divisor"));
						$.each(finrteboxes, function(j,record){
							var r = $(record).parent().parent();
							var sno = $(r).find('.studno').text();
							if(sno==studentnumber){
								if(divisor){
									var ave = total/divisor;
									$(record).text(roundNumber(ave,FINALRATE_ROUND));
								}
							}
						});
					});
					$('#progress-bar').html('');
					$('.tab-content').animate({opacity:'1'},1000);
					$('.tab').slideDown();
				}
		});
	}
	function roundNumber(number,decimal_points) {
		if(!decimal_points) return Math.round(number);
		if(number == 0) {
			var decimals = "";
			for(var i=0;i<decimal_points;i++) decimals += "0";
			return "0."+decimals;
		}

		var exponent = Math.pow(10,decimal_points);
		var num = Math.round((number * exponent)).toString();
		return num.slice(0,-1*decimal_points) + "." + num.slice(-1*decimal_points);
	}
	// Make bad grades red
	function failGrade(res){
	var value = $(res).text();
	
		if(value<75){
			$(res).addClass('failgrade');
		}else{
			$(res).removeClass('failgrade');
		}
	
	}
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
								load_str +='<option sy="'+res.sy+'"value="'+res.sec_code+'-" dept="'+res.dept+'" level="'+res.level+'">'+res.dept+' - '+res.level+' ' +res.sec+'</option>';
							});
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
	$('.change_btn').livequery('click',function(e){
		var btn =  $(this);
		var comp_code = $(this).attr('comp_code');
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var selected_period = $('#period').find('option:selected').val();
		$.ajax({
				type: 'POST',
				url: 'ajax.php',
				beforeSend: function(){
						//$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Posting grades...');
						//$('#post-grades').fadeOut();
				},
				error: function (){ 
						//$('#progress-bar').delay(1000).html('<div class="warning"><strong>Warning:</strong> Posting failed. Try again.</div>');
						//$('#post-grades').fadeIn();
				},
				data: {'classcode': classcode, 'sy': sy, 'period':selected_period,'compcode':comp_code, 'func':'unpostgrade'},
				success: function(data) {
					var json_data = $.parseJSON(data);
					$(btn).fadeOut();
					alert('Allow change');
				}
			});
	
	});
	
	$('.post_btn').livequery('click',function(e){
		$('.tab-header').click();
		$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Preparing grades...');
		var sy = $('#sy').find('option:selected').val();
		var selected_period= $('#period').find('option:selected').val();
		var section_code =$('#load').find('option:selected').val();
		var classcode = $('#load').find('option:selected').val();
		var level = $('#load').find('option:selected').attr('level');
		var dept = $('#load').find('option:selected').attr('dept');
		var change_btns = $('.ztable-data-container #gradesheet .change_btn');
		var gradeboxes = $('.ztable-data-container #gradesheet .gradebox');
		var headers =$('.ztable-data-container #gradesheet .head_fix');
		var stud_recs = $('.ztable-data-container #gradesheet .boygirl');
		var students_records =[];
		$.each(stud_recs, function(i,e){
			var studno = $(e).find('.studno').text();
			var student = {};
			var grades =[];
			student.sno= studno;
			$.each($(e).find('.finalgradebox'), function(j,k){
				var comp_code = $(k).attr('hdr');
				var grade = $(k).attr('grade');
				var equivalent = $(k).text();
				var subject={};
				subject.comp_code = comp_code;
				subject.grade=grade;
				subject.equivalent = equivalent;
				grades.push(subject);
			});
			student.grades = grades;
			students_records.push(student);
		});
		var compcodes = [];
		$.each(headers,function(i,e){
			compcodes.push($(e).attr('compcode'));
		});
		$.each(change_btns, function(i,e){
			$(e).fadeOut();
		});
		$.each(gradeboxes, function(i,e){
			var that_compcode = $(e).attr('hdr');
				$(e).addClass('posted');
		});
	

		$.ajax({
				type: 'POST',
				url: 'ajax.php',
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Posting grades...');
				},
				error: function (){ 
					
				},
				data: {'sy': sy, 'period':selected_period,'compcodes':compcodes, 'seccode': section_code, 'student_records':students_records,'func':'postgrade'},
				success: function(data) {
					$('#progress-bar').html('');
					alert('Grade posted');
					$('.tab-header').click();
					load_routine(classcode, sy, selected_period, level, dept);
				}
			});	

	});	

	
	$('.print_btn').click(function(e){
		var datasource = $(this).attr('datasource');
		var rows = $('.ztable-data-container #'+datasource+' .boygirl');
		var hdrs = $('.ztable-data-container #'+datasource+' .head_fix');
		var mos = $('.ztable-data-container #'+datasource+' .head_mon');
		var sy = $('#sy').find('option:selected').text();
		var adviser = $('#full_name').val();
		var period = $('#period').find('option:selected').text();
		var  section = $('#load').find('option:selected').val();
		var info = [sy, period, section, adviser, TCGS];
		var dataset=[];
		var alias =[];
		var months = [];
		$.each(rows, function(i,r){
			
			if(!$(r).hasClass('special')){
				arr_row = [];
				arr_row.push($(r).hasClass('M')?'M':'F');				
				var cols = $(r).find('td');
				$.each(cols, function(j,s){
					arr_row.push($(s).text());
				});
				dataset.push(arr_row);
				
			}
		});
		$.each(hdrs, function(i,r){
			alias.push($(r).text());
		});
		$.each(mos, function(i,r){
			months.push($(r).text());
		});
		var frm = $(this).attr('form');
		var ds = $('#'+frm).find('.dataset');
		var inf = $('#'+frm).find('.info');
		var als = $('#'+frm).find('.alias');
		var mon = $('#'+frm).find('.months');
		ds.val( $.toJSON(dataset));
		inf.val( $.toJSON(info));
		als.val( $.toJSON(alias));
		mon.val( $.toJSON(months));
		console.log(alias);
		$('#'+frm).submit();
	});
	
	$('.export_btn').click(function(e){
		var datasource = $(this).attr('datasource');
		var rows = $('.ztable-data-container #'+datasource+' tr:not(.special)');
		dataset=[];
		$.each(rows, function(i,r){
			arr_row = [];
			
			var cols = $(r).find('td');
			$.each(cols, function(j,s){
				var cell = !$(s).text()?$(s).find('input').val():$(s).text();
				arr_row.push(cell);
			});
			dataset.push(arr_row);
		});
		var frm = $(this).attr('form');
		var ds = $('#'+frm).find('.dataset');
		ds.val( $.toJSON(dataset));
		$('#'+frm).submit();
	});
});
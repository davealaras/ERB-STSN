$(document).ready(function() {
var SUMMARY_ROUND =2;
var EQUIVALENT_ROUND=0;
var OVERALL_ROUND=0;
	$('input').livequery('keypress', function(e){
		if($(this).length==0&& e.which ==8){
			e.preventDefault();			
		}
	});
	
	var rawscorehtml = $('#div-rawscore').html();
	var equivalenthtml = $('#div-equivalent').html();
	var summaryhtml  = $('#div-summary').html();
	var overallhtml  = $('#div-overall').html();
	$('#RIGHT-CONTENT-HOME').animate({opacity:'1'},1000);
	$('#LEFT-CONTENT').animate({opacity:'1'},1000);
	
	$.ajax({
			type: 'GET',
			url: 'ajax.php',
			data: {func:'getSession'},
			success: function(data) {
			console.log(data);
				var json_data = $.parseJSON(data);
				if(json_data != null){
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
			$('#div-rawscore').html('').html(rawscorehtml);
			$('#div-equivalent').html('').html(equivalenthtml);
			$('#div-summary').html('').html(summaryhtml);
			$('#div-overall').html('').html(overallhtml);
			$('#rawscores').find('td.measurables').remove();
			$('#equivalent').find('td.measurables').remove();
			$('#summary').find('td.components').remove();
			$('#rawscores').find('.students').remove();
			$('#equivalent').find('.students').remove();
			$('#summary').find('.students').remove();
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'level' : level, 'deptcode': dept, 'func':'getcomponents'},
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading components...');
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying. ');
					load_routine(classcode,sy, period);
				},
				success: function(data) {
					var json_data = $.parseJSON(data);
					components = json_data.components;
					measurables = json_data.measurables;
					if(components==null||components.length==0){
						$('#progress-bar').html('<div class="warning"><strong>Warning:</strong> No Components found!<div></div>');
						return;
					}
					else if(measurables==null||measurables.length==0){
						$('#progress-bar').html('<div class="warning"><strong>Warning:</strong> No Measurable items found!<div></div>');
						return;
					}else{
						load_routine_studnrol(classcode,sy, period,level, dept);
					}
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
				
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading components...');
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying');
					load_routine_components(classcode,sy, period);
				},
				success: function(data) {
					$('#debug').html('Done');
					//$('#debug').html(data);
					//$('#progress-bar').html(data);
					var json_data = $.parseJSON(data);				  
					console.log(json_data.components);
					var rwctr = json_data.rownum;
					components = json_data.components;
					measurables = json_data.measurables;
					isposted = json_data.isposted;
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
					//Build components
					$.each(components,function(i,result){
						var rwctr = result.rownum;
						var ccode =result.ccode;
						var descp = result.desc;
						var prcnt = result.perc;
						var count = result.count;
						var base =50;
						//Grand total for components 
						gtotal+=(base*(prcnt/100));
						//Load existing components
						var col ='<td class="small head components"><div class="hdrsmall"><a class="tip" title="'+descp+'<br><strong>'+prcnt+'%</strong>">'+ccode+'</a></div></td>';
						$(col).insertBefore('.eos');
						$('<td><div class="small dumbbox right" colnum="'+(i+1)+'" percent="'+prcnt+'" classcode="'+ccode+'" count="'+count+'">'+(base*(prcnt/100))+'</div></td>').insertBefore('#summary .end');	
					});
					$('#summary .students .end').html('<div class="small totalbox right">'+gtotal+' </div>');
					$('#summary .students .end').parent().append('<td><div class="small finalbox right">'+roundNumber(gtotal,0)+' </div></td>');
					$.each(periods, function(i,result){
						var col ='<td class="small head periods" pcode="'+result.id+'"><div style="height:20px;width:60px;overflow:hidden;"><div style="width:150px;overflow:hidden;height:15px;text-align:left;">'+result.alias+'</div></div></td>';
						$(col).insertBefore('.eoo');
						$('<td><div class="small finalbox right" pcode="'+result.id+'"></div></td>').insertBefore('#overall .end');
					});
						var col ='<td class="small head periods" pcode="FG"><div style="height:20px;width:60px;overflow:hidden;"><div style="width:150px;overflow:hidden;height:15px;text-align:left;">Final Grade</div></div></td>';
						$(col).insertBefore('.eoo');
						$('<td><div class="small finalbox right" pcode="FG"></div></td>').insertBefore('#overall .end');
					
					//$('<td></td>').insertAfter('#overall .students .end');
					$.each(json_data.measurables,function(i,result){
						var clctr = result.colnum;
						var ccode =result.ccode;
						var hdr = result.hdr;
						var dsc = result.dsc;
						var itm =result.itm;
						var bse =result.base;
						var count = result.count;
						//Load measurables
						var col ='<td class="small head measurables"><div class="hdrsmall"><a class="tip" title="'+dsc+'<br>Max items:<strong>'+itm+'</strong>">'+hdr+'</a></div> </td>';
						$(col).insertBefore('.eof');	
						//IF NOT POSTED: display as textbox ELSE: display as label
						if(!isposted){
							$('<td style="text-align:center;"><input value=" " isposted="'+isposted+'"type="text" class="micro smartbox"  saveas="rawscores" hdr="'+hdr+'" ccode="'+ccode+'"colnum="'+(i+1)+'"maxitems="'+itm+'" base="'+bse+'"  /></td>').insertBefore('#rawscores .end');
							
						}else{
							$('<td style="text-align:center;"><span isposted="'+isposted+'" type="text" class="micro smartbox" saveas="rawscores" hdr="'+hdr+'" ccode="'+ccode+'"colnum="'+(i+1)+'"maxitems="'+itm+'" base="'+bse+'"></span></td>').insertBefore('#rawscores .end');
						}
						$('<td><div class="micro dumbbox right failgrade" style="text-align:right" colnum="'+(i+1)+'"maxitems="'+itm+'"  ccode="'+ccode+'" base="'+bse+'" count="'+count+'" headername="'+hdr+'" >'+bse+'</div></td>').insertBefore('#equivalent .end');	
					});
					
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
							$(res).find('td div').removeClass('failgrade').addClass('dropped');
						}
					});
					//Summary Drop
					var s_row_record = $('#summary .students');
					$.each(s_row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							//$(res).find('.dumbbox').addClass('disabled');
							$(res).find('td .dumbbox').text('D').addClass('dropped');
							$(res).find('td .totalbox').text('D').addClass('dropped');
							$(res).find('td .finalbox').text('D').addClass('dropped');
						}
					});
					//Overall Drop
					var o_row_record = $('#overall .students');
					$.each(o_row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							//$(res).find('.dumbbox').addClass('disabled');
							$(res).find('td .totalbox').text('D').addClass('dropped');
							$(res).find('td .finalbox').text('D').addClass('dropped');
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
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'func':'getraw+equivalent'},
				beforeSend: function(){
					$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Loading scores...');
				},
				error: function (){ 
					$('#progress-bar').html('Time out. Retrying...').delay(1000);
					populate_smartbox(); 
				},
				success: function(data) {
					$('.tab').slideDown();
					
					$('#progress-bar').html('').delay(2000);
					var json_data = $.parseJSON(data);
					 console.log('RAW');
						 console.log(json_data.raw);
					var smartboxes = $('#rawscores').find('.smartbox');
					var e_dumbboxes = $('#equivalent').find('.dumbbox');
					var s_dumbboxes = $('#summary').find('.dumbbox');
					var o_totalboxes= $('#overall').find('.finalbox');
					//Populate smartboxes for rawscore
					$.each(smartboxes, function(i,res){
						var colnumber =  $(res).attr('colnum');
						var header = $(res).attr('hdr');
						var row = $(res).parent().parent();
						 var studentnumber = $(row).find('.studno').text();
						 if(json_data.raw!=null){
							 $.each(json_data.raw, function(i,record){				   
								var colnum = record.col;
								var sno = record.sno;
								var hdr =record.hdr;
								var score =record.score;
								if(studentnumber==sno && header==hdr){
									var mxitms = $(res).attr('maxitems');
									var isposted_bool = $(res).attr('isposted');
									$(res).attr('value',score);
									if(isposted_bool){
										$(res).text(score);
									}else{
										score>mxitms?$(res).addClass('invaliddata'):$(res).removeClass('invaliddata');
									}
									if(score=='IGN'){
										$(res).attr('IGN', true);
										$(res).removeClass('invaliddata');
									}
								}
							});
						}
					});
					
					var items =$('#equivalent').find('.dumbbox');
					var sum =$('#summary').find('.dumbbox');
					var oall =$('#overall').find('.finalbox');
					//Populate equivalent table
					$.each(e_dumbboxes, function(i,res){
						var header = $(res).attr('headername');
						var row = $(res).parent().parent();
						var rownum = $(row).attr('rownum');
						var ccode = $(res).attr('ccode');
						var colnum =$(res).attr('colnum');
						var studentnumber = $(row).find('.studno').text();
						if(json_data.equivalent!=null){
							$.each(json_data.equivalent, function(i,record){
								var sno = record.sno;
								var hdr = record.hdr;
								var value = record.equivalent;
								if(studentnumber==sno && header==hdr){
									if(value!='IGN'){
										value = parseFloat(value);
										$(res).text(value);
										failGrade(res);
									}
									else{
										$(res).text(value);
										$(res).removeClass('failgrade');
									}
									
								}						
								
							});
						}
					});
					
					
					//Populate summary table
					$.each(s_dumbboxes, function(i,res){
						var header = $(res).attr('classcode');
						var row = $(res).parent().parent();
						var rownum = $(row).attr('rownum');
						var ccode = $(res).attr('ccode');
						var colnum =$(res).attr('colnum');
						var studentnumber = $(row).find('.studno').text();
						if(json_data.summary!=null){
							$.each(json_data.summary, function(i,record){
								var sno = record.sno;
								var hdr = record.hdr;
								var value = record.summary;
								if(studentnumber==sno && header==hdr){
									value = roundNumber(parseFloat(value), SUMMARY_ROUND);
									$(res).text(parseFloat(value));
									var total=0;
									$.each(s_dumbboxes,function(i, res){
										var r = $(res).parent().parent();
										var rn = $(r).attr('rownum');
										var cc = $(res).attr('classcode');
										var v;
										if(rn==rownum){
											var v = parseFloat($(res).text());
											v = isNaN(v)?0:v;
											total+=v;
											$(r).find('.totalbox').text(roundNumber(total,2));
											$(r).find('.finalbox').text(roundNumber(total,0));
											$(r).find('.finalbox').attr('finalgrade',roundNumber(total,2));
										}
									});
								}						
							});
						}
					});
					var totalscores = new Array();
					
					//Get all totalboxes
					$.each(s_dumbboxes, function(i,res){
						var r = $(res).parent().parent();
						var rownum = $(r).attr('rownum');
						var studentnumber = $(r).find('.studno').text();
						var tb  = $(r).find('.totalbox');
						failGrade(tb);
						var total =$(tb).text();
						
						var totalbox={};
						totalbox.rownum=rownum;
						totalbox.sno=studentnumber;
						totalbox.total=total;
						totalscores.push(totalbox);
					});
					var selected_period = $('#period').find('option:selected').val();
					//Populate total table
					$.each(o_totalboxes,function(i,res){
						var row = $(res).parent().parent();
						var rownum = $(row).attr('rownum');
						var pc = $(res).attr('pcode');
						var studentnumber = $(row).find('.studno').text();
						if(json_data.overall!=null){
							$.each(json_data.overall, function(i,record){
								var sno = record.sno;
								var fr = record.fr;
								var se = record.se;
								var th = record.th;
								var fo = record.fo;
								var fg =record.fg;
								if(studentnumber==sno){
									switch(pc){
										case '1':
											if(fr){
												$(res).attr('overall',roundNumber(fr,SUMMARY_ROUND));
												$(res).text(roundNumber(fr,OVERALL_ROUND));	
											}
											break;
										case '2':
											if(se){
												$(res).attr('overall',roundNumber(se,SUMMARY_ROUND));
												$(res).text(roundNumber(se,OVERALL_ROUND));
											}
											break;
										case '3':
											if(th){
												$(res).attr('overall',roundNumber(th,SUMMARY_ROUND));
												$(res).text(roundNumber(th,OVERALL_ROUND));
											}
											break;
										case '4':
											if(fo){
												$(res).attr('overall',roundNumber(fo,SUMMARY_ROUND));
												$(res).text(roundNumber(fo,OVERALL_ROUND));
											}
											break;
										case  'FG':
											$(res).text(fg);
											break;
									}
									failGrade(res);
								}
							});
						}
					});
					//Populate total table
					$.each(o_totalboxes,function(i,res){
						var row = $(res).parent().parent();
						var rownum = $(row).attr('rownum');
						var pc = $(res).attr('pcode');
						var studentnumber = $(row).find('.studno').text();
						if(totalscores!=null){
							$.each(totalscores, function(i,record){
								var sno = record.sno;
								var value = record.total;
								if(studentnumber==sno&&pc==selected_period){
									$(res).attr('overall',roundNumber(value,SUMMARY_ROUND));
									$(res).text(roundNumber(value,OVERALL_ROUND));
									failGrade(res);
								}
							});
						}
					});
					$( "#top_tabs" ).tabs();
					$('#rawscores').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#equivalent').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#summary').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#overall').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('.tab-content').animate({opacity:'1'},500);
					$("a[title]").tooltip({offset: [-10,0], effect: 'fade'});
					$('#close_btn').css({opacity:1}).fadeIn();
				}
			});
	}
	//Smart Box Keypress
	$('.smartbox').livequery('keypress',function(e){
		var isNum = (e.which>47 && e.which<=59)||e.which==46;
		var sno, ccode;
		var score;
		var saveas = $(this).attr("saveas");
		var isIgnore = e.which==105; // KEYCODE i
		var perfectscore = parseFloat($(this).attr('maxitems'));
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();

		var hdr =$(this).attr('hdr');
		var items = $('.ztable-data-container #'+saveas).find('.smartbox');
		var ctr=1;
		var trace="";
		if($(this).hasClass('disabled')){
			$(this).val('D');
			if($(this).attr('IGN')){
				$(this).val('IGN');
			}
		}
		
		if(($(this).hasClass('disabled')||isIgnore)||!isNum){
			e.preventDefault();
			if(isIgnore){
				$(this).val('');
			}
		}
		if($(this).attr('IGN')==undefined&&!$(this).hasClass('disabled')){
			if(isIgnore){
				$(this).attr('IGN',true);
			}else{
				$(this).attr('IGN',false);
			}
		}else if(isIgnore){
				$(this).attr('IGN',true);
		}
		if(e.which==13|| e.which==105){
			var colnum =  $(this).attr('colnum');
			var row = $(this).parent().parent();
			var rownum = $(row).attr('rownum');	
			var sno =$(row).find('.studno').text();
			if($(this).hasClass('disabled')){
				e.preventDefault();
			}
			else{
				score =  parseFloat($(this).val());
				if(!isNaN(score)){					
					$(this).attr('IGN',false);
				}
				$(this).attr('IGN',$(this).attr('IGN'));
				score = ($(this).attr('IGN')==true)? -3 :score;	
				var isIGN = $(this).attr('IGN');
				if(isIGN=="true"){
					score=-3;
					$(this).val('IGN');
				}
				
				if(score!=-3){
					if((score<0||score>perfectscore)){
							alert("Invalid data entry! Score should be between 0 and "+ perfectscore);
							$(this).addClass('invaliddata');
							return;

					}else{
						if(saveas=="rawscores"){
							compute_smartbox(this,false);
						}
						$(this).removeClass('invaliddata');
					}
				}
				if(score==-3){
						if(saveas=="rawscores"){
							compute_smartbox(this,false);
						}
					
				}
				if(saveas=='rawscores'){
					$.ajax({
						type: 'GET',
						url: 'ajax.php',
						data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'rawscore': score, 'func':'saveraw'},
						success: function(data) {
						}
					});
				}
			}
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
		}
		
	});
	// Make bad grades red
	function failGrade(res){
	var value = $(res).text();
	
		if(value<75){
			$(res).addClass('failgrade');
		}else{
			$(res).removeClass('failgrade');
		}
		value >100?$(res).addClass('invaliddata'):$(res).removeClass('invaliddata');
	}
	//Compute routine
	function compute_smartbox(obj, isinit){
		var rawscore = ($(obj).attr('IGN')=="true")? -3 : parseFloat($(obj).val());
		var perfectscore = parseFloat($(obj).attr('maxitems'));
		var base  = parseFloat($(obj).attr('base'));
		var colnum =  $(obj).attr('colnum');
		var row = $(obj).parent().parent();
		var rownum = $(row).attr('rownum');
		var ccode = $(obj).attr('ccode');
		var itemscore = isinit? $('#rawscores').find('.smartbox') : $('.ztable-data-container #rawscores').find('.smartbox');
		var items = isinit? $('#equivalent').find('.dumbbox') : $('.ztable-data-container #equivalent').find('.dumbbox');
		var sum =isinit?  $('#summary').find('.dumbbox') :  $('.ztable-data-container #summary').find('.dumbbox');
		var oall =isinit?  $('#overall').find('.finalbox') :  $('.ztable-data-container #overall').find('.finalbox');
		if(isNaN(rawscore)){
			rawscore=0;		
		}
		$.each(items,function(i,res){
			var cn  = $(res).attr('colnum');
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var sno = $(r).find('.studno').text();
			var hdr = $(res).attr('headername');
			var classcode = $(res).attr('ccode');
			//var classcode = $('#load').find('option:selected').val();
			//var sy = $('#sy').find('option:selected').val();
			//var period = $('#period').find('option:selected').val();
			if(rn==rownum && cn==colnum){
				//FORMULA
				console.log('r'+rawscore+' '+ classcode);					
				useformula(rawscore, perfectscore,classcode,base, res,EQUIVALENT_ROUND);
			}
		});
		smart_compute(itemscore,sum,oall, rownum, colnum,ccode);
	}
	function useformula(rawscore, perfectscore,classcode,base, res,roundvalue){
				var value=0;
				if(rawscore==-3){
					//IGNORE
					value=rawscore;
					$(res).text('IGN');
					$(res).removeClass('failgrade');
				}else if(classcode !='QZ' && classcode !='QE'){
					// Regular formula
					value = ((rawscore / perfectscore) * (100 - base)) + base;
					value = roundNumber(value,roundvalue);
					$(res).text(value);
					failGrade(res);
				}else{
					//Special formula
					var factor = perfectscore/(100/base);
					if(rawscore>=factor){
						value = (rawscore-factor) * (24/factor)+75;
						value = roundNumber(value,roundvalue);
						$(res).text(value);
						failGrade(res);
						
					}else  if(rawscore< factor && rawscore>=0){
						value = rawscore * (4/factor)+70;
						value = roundNumber(value,roundvalue);
						$(res).text(value);
						failGrade(res);
					}
				}	
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
	function smart_compute(itemscore,sum,oall, rownum, colnum, ccode){
		var cell;
		var count;
		var S=sum;
		var p;
		$.each(sum,function(i, res){
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var cc = $(res).attr('classcode');
			if(rn==rownum && ccode==cc){
				cell=$(res);
				p = $(res).attr('percent');
			}
		});
		var total =0;
		var maxscore=0;
		var divisor=0;
		var colctr=0;
		var curr_base=0;
		$(cell).text('0');
		$.ajax
		$.each(itemscore,function(i, res){
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var cc = $(res).attr('ccode');
			var pf =$(res).attr('maxitems');
			var txt = $(res).val();
			var value = parseFloat($(res).val());
			if(rn==rownum){
				//colctr+=1;
				if(cc==ccode){
					if(txt!='IGN'){
						curr_base=parseInt($(res).attr('base'));
						total+=value; // Collect rawscore
						maxscore+=parseInt(pf); // Collect maxscore to get grand total
						divisor+=1;
					}
				//value = isNaN(value)? 0 : value;
				//total +=value;
				}
			}
		});
		console.log(colctr);
		//Common Variables for summary and overall
		var cr = $(cell).parent().parent();
		var crn =$(cr).attr('rownum');
		var sno = $(cr).find('.studno').text();
		var hdr = $(cell).attr('classcode');
		var classcode = $('#load').find('option:selected').val();
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		//Save Summary
		var ave =0;
		
		useformula(total, maxscore, hdr,curr_base, cell,SUMMARY_ROUND);
		var vv=$(cell).text();
		ave = parseFloat($(cell).text())*(p/100);
		$(cell).text(roundNumber(ave,SUMMARY_ROUND));
		total =0;			//RESET total to get correct sum
		//Traverse on summary dumbboxes
		$.each(S,function(i, res){
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var cc = $(res).attr('classcode');
			var value;
			if(rn==rownum){
					var value = parseFloat($(res).text());
					value = isNaN(value)?0:value;
					total+=value;
					var tb = $(r).find('.totalbox');
					var fb = $(r).find('.finalbox');
					tb.text(roundNumber(total,SUMMARY_ROUND)); // totalbox
					fb.text(roundNumber(total,OVERALL_ROUND)); // finalbox
					failGrade(tb);
			}
		});
		var selected_period = $('#period').find('option:selected').val();
		//Save total
		$.each(oall,function(i,res){
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var pc = $(res).attr('pcode');
			//pc==1 : First Period
			if(rn==rownum&&pc==selected_period){
				$(res).attr('overall',roundNumber(total,SUMMARY_ROUND));
				$(res).text(roundNumber(total,OVERALL_ROUND));
				failGrade(res);
				/*
				$.ajax({
						type: 'GET',
						url: 'ajax.php',
						data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': pc, 'overall': total, 'func':'saveoverall'},
						success: function(data) {
					}
					});
				*/
			}
		});
		
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
			var remember = $('#remember:checked').val();
			//console.log(user_name);
			//console.log(password);
			$('#login-form form').animate({opacity:'0.4'},200);
			$('#response').animate({opacity:'0'},200);
			$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: {'user_name' :user_name, 'password':password, 'remember':remember,'func':'login'},
				success: function(data) {
					var o= $.parseJSON(data);
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
						top.location.href="index.php";
					}
				}
			});
	});
	function load_sysdef(o){	
			
				$.getJSON('ajax.php?token='+o.token+'&func=getInfo&id='+o.id, function(data){
							$('#faculty_id').val(data.id);
							$('#full_name').val(data.full_name);
							console.log(data);
				});
				$.getJSON('ajax.php?func=getSysDefa&token='+o.token, function(data){
							var sy = data.active_sy;
							var base =data.base;
							ztb_w = data.ztb_w;
							ztb_h = data.ztb_h;
							//sys_timeout = data.timeout;
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
							if(data.faculty_load){
								$.each(data.faculty_load, function(i,res){
									load_str +='<option sy="'+res.sy+'"value="'+res.sec_code+'-'+res.comp_code+'" dept="'+res.dept+'" level="'+res.level+'">'+res.subject+' / '+res.section+'</option>';
								});
							}
							/*
							var temp_str="";
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
							$('#sy').html('<option value="'+sy+'">'+str+'</option>');
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
	$('#post-grades').click(function(){
		var rwscr = $('.ztable-data-container #rawscores').find('.smartbox');
		var allowPost = true;
		for(var i=0; i<rwscr.length; i+=1){
			var res = rwscr[i];
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
			var oall = $('.ztable-data-container #overall').find('.finalbox');
			var classcode = $('#load').find('option:selected').val();
			var sy = $('#sy').find('option:selected').val();
			var selected_period = $('#period').find('option:selected').val();
			var snos = new Array();
			var values = new Array();
			$.each(oall,function(i,res){
				var r = $(res).parent().parent();
				var rn = $(r).attr('rownum');
				var pc = $(res).attr('pcode');
				if(pc==selected_period){
					var value = $(res).attr('overall');
					var sno = $(r).find('.studno').text();
					snos.push(sno);
					values.push(value);				
				}
			});
			$.ajax({
				type: 'POST',
				url: 'ajax.php',
				beforeSend: function(){
						$('#progress-bar').html('<img src="img/ajax-loader.gif"/>Posting grades...');
						$('#post-grades').fadeOut();
				},
				error: function (){ 
						$('#progress-bar').delay(1000).html('<div class="warning"><strong>Warning:</strong> Posting failed. Try again.</div>');
						$('#post-grades').fadeIn();
				},
				data: {'sno' :snos, 'grade':values, 'classcode': classcode, 'sy': sy, 'period':selected_period, 'func':'sendtocgs'},
				success: function(data) {
					$('#post-grades').hide();
					
						var classcode = $('#load').find('option:selected').val();
						var sy = $('#sy').find('option:selected').val();
						var period = $('#period').find('option:selected').val();
						var level = $('#load').find('option:selected').attr('level');
						var dept = $('#load').find('option:selected').attr('dept');	
						alert('Record book has been sent to CGS');
						load_routine(classcode, sy, period, level, dept);
					
				}
			});
			
		}
	});
	$('.export_btn').click(function(e){
		var datasource = $(this).attr('datasource');
		var rows = $('.ztable-data-container #'+datasource+' tr');
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
	$('#close_btn').click(function(){
		$('.tab').slideUp();
		$('#close_btn').fadeOut();
		$('#post-grades').fadeOut();
	});
});
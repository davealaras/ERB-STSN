$(document).ready(function() {
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
	/*
	$.ajax({
			type: 'GET',
			url: 'ajax.php',
			data: {func:'getSession'},
			success: function(data) {
				var json_data = $.parseJSON(data);
				if(json_data!=-1){
					$('#LEFT-CONTENT').remove();
					load_sysdef(json_data);
					$('#logout').show();
				}
				else{
					$('#login').show();
					$('#logout').hide();
				}
			}
	});
	*/
	var templates;
	var components;
	var measurables;
	var periods;
	var conduct;
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
					/*if(json_data.isposted){
						
						LOADED_PERIOD = period;
					}else{
						$('#progress-bar').html('<div class="warning"><strong>Warning:</strong> Post grades before trying to go to the next grading period.</div>');
						var options  = $('#period').find('option');
						$.each(options, function(i,res){
							if($(res).val()==LOADED_PERIOD){
								$(res).attr('selected', true);
							}
						});
					}*/
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
			$('#div-tbConduct').html('').html(conducthtml);
			$('#rawscores').find('td.measurables').remove();
			$('#equivalent').find('td.measurables').remove();
			$('#summary').find('td.components').remove();
			$('#tbConduct').find('td.conduct').remove();
			$('#rawscores').find('.students').remove();
			$('#equivalent').find('.students').remove();
			$('#summary').find('.students').remove();
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
					components = json_data.components;
					measurables = json_data.measurables;
					if(components==null){
						$('#progress-bar').html('<div class="warning"><strong>Warning:</strong> No Components found!<div></div>');
						return;
					}
					else if(measurables==null){
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
				timeout: sys_timeout,
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
					var json_data = $.parseJSON(data);				  
					var rwctr = json_data.rownum;
					components = json_data.components;
					measurables = json_data.measurables;
					conduct = json_data.conduct;
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
						var col ='<td class="micro head components"><div style="width:40px">'+ccode+'</div></td>';
							$(col).insertBefore('.eos');
							$('<td><div class="micro dumbbox right" colnum="'+(i+1)+'" percent="'+prcnt+'" classcode="'+ccode+'" count="'+count+'">'+(base*(prcnt/100))+'</div></td>').insertBefore('#summary .end');	
					});
					$('#summary .students .end').html('<div class="micro totalbox right">'+gtotal+' </div>');
					$.each(periods, function(i,result){
						var col ='<td class="micro head periods" pcode="'+result.id+'"><div style="height:20px;width:60px;overflow:hidden;"><div style="width:150px;overflow:hidden;height:15px;text-align:left;">'+result.alias+'</div></div></td>';
						$(col).insertBefore('.eoo');
						$('<td><div class="micro totalbox right" pcode="'+result.id+'"></div></td>').insertBefore('#overall .end');
					});
						var col ='<td class="micro head periods" pcode="FG"><div style="height:20px;width:60px;overflow:hidden;"><div style="width:150px;overflow:hidden;height:15px;text-align:left;">Final Grade</div></div></td>';
						$(col).insertBefore('.eoo');
						$('<td><div class="micro totalbox right" pcode="FG"></div></td>').insertBefore('#overall .end');
					
					//$('<td></td>').insertAfter('#overall .students .end');
					$.each(measurables,function(i,result){
						var clctr = result.colnum;
						var ccode =result.ccode;
						var hdr = result.hdr;
						var dsc = result.dsc;
						var itm =result.itm;
						var bse =result.bse;
						var count = result.count;
						//Load measurables
						var col ='<td class="small head measurables">'+hdr+'</td>';
						$(col).insertBefore('.eof');	
						//IF NOT POSTED: display as textbox ELSE: display as label
						if(!isposted){
							$('<td style="text-align:center;"><input isposted="'+isposted+'"type="text" class="micro smartbox" hdr="'+hdr+'" ccode="'+ccode+'"colnum="'+(i+1)+'"maxitems="'+itm+'" base="'+bse+'" /></td>').insertBefore('#rawscores .end');
							
						}else{
							$('<td style="text-align:center;"><span isposted="'+isposted+'" type="text" class="micro smartbox" hdr="'+hdr+'" ccode="'+ccode+'"colnum="'+(i+1)+'"maxitems="'+itm+'" base="'+bse+'"></span></td>').insertBefore('#rawscores .end');
						}
						$('<td><div class="micro dumbbox right" style="text-align:right" colnum="'+(i+1)+'"maxitems="'+itm+'"  ccode="'+ccode+'" base="'+bse+'" count="'+count+'" headername="'+hdr+'">'+bse+'</div></td>').insertBefore('#equivalent .end');	
					});
					$.each(conduct, function(i,result){
						var hdr = result.hdr;
						var maxG =result.maxG;
						var minG =result.minG;
						var col ='<td class="small head conduct"><div style="width:45px;overflow:hidden;"><span style="width:150px;overflow:hidden;height:15px;text-align:left;">'+hdr+'</span></div></td>';
						$(col).insertBefore('.eoc');
						$('<td><input type="text" class="micro" hdr="'+hdr+'"colnum="'+(i+1)+'"maxG="'+maxG+'" minG="'+minG+'"/></td>').insertBefore('#tbConduct .end');
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
				$('#debug').html('Done');
				var json_data = $.parseJSON(data);				  
				var students = json_data.students;
				//$('#debug').html(data);
				$.each(students,function(i,res){
						var sno = res.sno;
						var fullname = res.fullname;
						var status = res.status;
						var row = '<tr class="students" rownum="'+i+'" status="'+status+'" >';
							row +='<td class="studno">'+sno+'</td>';
							row +='<td class="jumbo fullname">'+fullname+'</td>';
							row +='<td class="end"></td>'
							row +='</tr>';
						$(row).insertAfter('.label');
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
				timeout: sys_timeout*4,
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
					var smartboxes = $('#rawscores').find('.smartbox');
					var e_dumbboxes = $('#equivalent').find('.dumbbox');
					var s_dumbboxes = $('#summary').find('.dumbbox');
					var o_totalboxes= $('#overall').find('.totalbox');
					//Populate smartboxes
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
								if(colnumber == colnum && studentnumber==sno && header==hdr){
									if($(res).attr('isposted')=="true"){
										$(res).text(score);
									}else{
										$(res).val(score);
									}
									if(score=='IGN'){
										$(res).attr('IGN', true);
									}
								}
							});
						}
					});
					var items =$('#equivalent').find('.dumbbox');
					var sum =$('#summary').find('.dumbbox');
					var oall =$('#overall').find('.totalbox');
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
									$(res).text(value);
									failGrade(res);
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
									$(res).text(value);
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
											$(r).find('.totalbox').text(total);
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
											$(res).text(fr);	
											break;
										case '2':
											$(res).text(se);
											break;
										case '3':
											$(res).text(th);
											break;
										case '4':
											$(res).text(fo);
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
									$(res).text(value);
									failGrade(res);
								}
							});
						}
					});
					
					$('#rawscores').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#equivalent').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#summary').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#overall').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('#tbConduct').ztable({tableWidth: ztb_w, tableHeight: ztb_h, columnCount:2});
					$('.tab-content').animate({opacity:'1'},500);
				}
			});
	}
	function correct_counter(sno, ccode,e_dumbboxes, s_dumbboxes, value, correction){
	console.log('correcting');
		$.each(e_dumbboxes, function(i,res){
			var classcode = $(res).attr('ccode')
			var row = $(res).parent().parent();
			var studentnumber = $(row).find('.studno').text();
			if(studentnumber==sno && classcode==ccode){
				console.log('correcting');
				$(res).attr(value, correction==-1);
				var count = parseInt($(res).attr('count'))+correction;
				$(res).attr('count', count);
			}
		});
		$.each(s_dumbboxes, function(i,res){
			var classcode = $(res).attr('classcode')
			var row = $(res).parent().parent();
			var studentnumber = $(row).find('.studno').text();
			if(studentnumber==sno && classcode==ccode){
				$(res).attr(value, correction==-1);
				var count = parseInt($(res).attr('count'))+correction;
				$(res).attr('count', count);
			}
		});
	}
	//Smart Box Keypress
	$('.smartbox').livequery('keypress',function(e){
		var isNum = (e.which>47 && e.which<=59);
		var sno, ccode;
		var rawscore
		var isIgnore = e.which==105; // KEYCODE i
		var perfectscore = parseFloat($(this).attr('maxitems'));
			var classcode = $('#load').find('option:selected').val();
				var sy = $('#sy').find('option:selected').val();
				var period = $('#period').find('option:selected').val();
				
				var hdr =$(this).attr('hdr');
				var items = $('.ztable-data-container #rawscores').find('.smartbox');
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
		if(e.which==13){
			var colnum =  $(this).attr('colnum');
			var row = $(this).parent().parent();
			var rownum = $(row).attr('rownum');	
			var sno =$(row).find('.studno').text();
			if($(this).hasClass('disabled')){
				e.preventDefault();
			}
			else{
				rawscore =  parseFloat($(this).val());
				if(!isNaN(rawscore)){					
					$(this).attr('IGN',false);
				}
				$(this).attr('IGN',$(this).attr('IGN'));
				rawscore = ($(this).attr('IGN')==true)? -3 :rawscore;	
				var isIGN = $(this).attr('IGN');
				if(isIGN=="true"){
					rawscore=-3;
					$(this).val('IGN');
				}
				
				if(rawscore!=-3){
					if((rawscore<0||rawscore>perfectscore)){
							alert("Invalid data entry! Score should be between 0 and "+ perfectscore);
							return;

					}else{
						compute_smartbox(this,false);
					}
				}
			
				for(var index=0; index<items.length;index++){
						var res =items[index];
						var cn  = $(res).attr('colnum');
						var r = $(res).parent().parent();
						var rn = $(r).attr('rownum');
						if(cn==colnum){
							//console.log('col found');
							//trace += 'cn '+cn+'rn '+rn+ 'row'+ row+'<br>';
							if(rn==rownum-ctr){
								//console.log('cell foun');
								$(res).focus();
								$('#debug').html(trace);
								break;
							}
							
						}
					}
				$.ajax({
					type: 'GET',
					url: 'ajax.php',
					data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'rawscore': rawscore, 'func':'saveraw'},
					success: function(data) {
	//					alert (data);
					}
				});
			}
			
			
			
			
		}
		
	});
	// Make bad grades red
	function failGrade(res){
	var value = $(res).text();
	/*
		if(value<75){
			$(res).addClass('failgrade');
		}else{
			$(res).removeClass('failgrade');
		}
	*/
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
		var items = isinit? $('#equivalent').find('.dumbbox') : $('.ztable-data-container #equivalent').find('.dumbbox');
		var sum =isinit?  $('#summary').find('.dumbbox') :  $('.ztable-data-container #summary').find('.dumbbox');
		var oall =isinit?  $('#overall').find('.totalbox') :  $('.ztable-data-container #overall').find('.totalbox');
		var value=0;
		if(isNaN(rawscore)){
			rawscore=0;		
		}
		$.each(items,function(i,res){
			var cn  = $(res).attr('colnum');
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var sno = $(r).find('.studno').text();
			var hdr = $(res).attr('headername');
			var classcode = $('#load').find('option:selected').val();
			var sy = $('#sy').find('option:selected').val();
			var period = $('#period').find('option:selected').val();
			if(rn==rownum && cn==colnum){
				//FORMULA
				console.log('r'+rawscore);
				if(rawscore>=0){
					value = ((rawscore / perfectscore) * (100 - base)) + base;
					$(res).text(value);
				}else if(rawscore==-3){
					//IGNORE
					value=rawscore;
					$(res).text('IGN');
				}
				/*
				//Ajax
				$.ajax({
					type: 'POST',
					url: 'ajax.php',
					data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'equivalent': value, 'func':'saveequivalent'},
					success: function(data) {
					}
				});
				*/
			}
		});
		smart_compute(items,sum,oall, rownum, colnum,ccode);
	}
	function smart_compute(items,sum,oall, rownum, colnum, ccode){
		var cell;
		var count;
		var S=sum;
		var p;
		$.each(sum,function(i, res){
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var cc = $(res).attr('classcode');
			if(rn==rownum && ccode==cc){
				cell=res;
				count = parseFloat($(res).attr('count'));
				p = $(res).attr('percent');
			}
		});
		var total =0;
		var divisor=0;
		var colctr=0;
		$(cell).text('0');
		$.ajax
		$.each(items,function(i, res){
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			var cc = $(res).attr('ccode');
			var txt = $(res).text();
			var value = parseFloat($(res).text());
			if(rn==rownum){
				//colctr+=1;
				if(cc==ccode){
					if(txt!='IGN'){
						total+=value;
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
		//var ave = (total/count)*(p/100);
		var ave = (total/divisor)*(p/100);
		console.log(total);
		console.log('divisor'+divisor);
		console.log(p/100);
		console.log(ave);
		/*
		$.ajax({
					type: 'POST',
					url: 'ajax.php',
					data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'summary': ave, 'func':'savesummary'},
					success: function(data) {
					}
				});
		*/
		$(cell).text(ave);  // Displays the summary on the CORRECT cell
		failGrade(cell);
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
					tb.text(total);
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
				$(res).text(total);
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
	$('.tab').click(function(){
		$(this).find('.tab-header').toggle(
			function(){
				$(this).next().slideDown();
				$(this).find('.indicator').html('-');
			},
			function(){
				$(this).next().slideUp();
				$(this).find('.indicator').html('+');
			}
			);
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
							templates = data.templates;
							$.each(periods, function(i,res){
								period_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							var component_str="";
							$.each(data.component, function(i,res){
								component_str +='<option value="'+res.code+'">'+res.desc+'</option>';
							});
							var load_str="";
							$.each(data.faculty_load, function(i,res){
								load_str +='<option sy="'+res.sy+'"value="'+res.sec_code+'-'+res.subject+'" dept="'+res.dept+'" level="'+res.level+'">'+res.subject+' / '+res.section+'</option>';								
							});
							var temp_str="";
							$.each(data.templates, function(i,res){
								temp_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							$('#load').html(load_str);
							var subjects = $('#load').find('option');
							correct_list(subjects, sy);
							$('#description').html('<option value="###" class="default">Select Description</option>'+component_str);
							$('#period').html(period_str);
							$('#templates').html('<option value="###" class="default">Select Template</option>'+temp_str);
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
	$('#post-grades').click(function(){
		var oall = $('.ztable-data-container #overall').find('.totalbox');
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
				var value = $(res).text();
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
			data: {'sno' :snos, 'grade':values, 'classcode': classcode, 'sy': sy, 'period':selected_period, 'func':'postgrade'},
			success: function(data) {
				$('#post-grades').hide();
					$('#progress-bar').html('<div class="info"><strong>Info:</strong> Grades posted!</div>').delay(5000, function(){
						var classcode = $('#load').find('option:selected').val();
						var sy = $('#sy').find('option:selected').val();
						var period = $('#period').find('option:selected').val();
						var level = $('#load').find('option:selected').attr('level');
						var dept = $('#load').find('option:selected').attr('dept');	
						load_routine(classcode, sy, period, level, dept);
					});
			}
		});
	});
});
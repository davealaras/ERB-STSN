<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/livequery.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
	var templates;
	var components;
	var measurables;
	var conduct;
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
		load_routine(classcode, sy, period, level, dept);
	});
	function load_routine(classcode,sy, period,level, dept){
			$('#debug').html('Loading...');
			$('#rawscores').find('td.measurables').remove();
			$('#equivalent').find('td.measurables').remove();
			$('#rawscores').find('.students').remove();
			$('#equivalent').find('.students').remove();
			load_routine_studnrol(classcode,sy, period,level, dept);
	}
	function load_routine_components(classcode,sy, period, level, dept){
		$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'level' : level, 'deptcode': dept, 'func':'getcomponents'},
				timeout: 500,
				beforeSend: function(){
					$('#progress-bar').html('Loading components...');
				},
				error: function (){
					$('#progress-bar').html('Time out. Retrying');
					load_routine_components(classcode,sy, period);
				},
				success: function(data) {
					$('#debug').html('Done');
					//$('#debug').html(data);
					populate_smartbox();
					var json_data = $.parseJSON(data);				  
					var rwctr = json_data.rownum;
					components = json_data.components;
					measurables = json_data.measurables;
					conduct = json_data.conduct;
					$.each(components,function(i,result){
						var rwctr = result.rownum;
						var ccode =result.ccode;
						var descp = result.desc;
						var prcnt = result.perc;
						//Load existing components
						var col ='<td class="small head components">'+ccode+'</td>';
							$(col).insertBefore('.eos');	
							$('<td><div class="micro dumbbox right" colnum="'+(i+1)+'" percent="'+prcnt+'"></div></td>').insertBefore('#summary .end');	
						
					});
					$.each(measurables,function(i,result){
						var clctr = result.colnum;
						var ccode =result.ccode;
						var hdr = result.hdr;
						var dsc = result.dsc;
						var itm =result.itm;
						var bse =result.bse;
						//Load measurables
						var col ='<td class="small head measurables">'+hdr+'</td>';
						$(col).insertBefore('.eof');	
						$('<td><input type="text" class="micro smartbox" hdr="'+hdr+'"colnum="'+(i+1)+'"maxitems="'+itm+'" base="'+bse+'"/></td>').insertBefore('#rawscores .end');	
						
						$('<td><div class="micro dumbbox right" colnum="'+(i+1)+'"maxitems="'+itm+'" base="'+bse+'"></div></td>').insertBefore('#equivalent .end');	
					});
					$.each(conduct, function(i,result){
						var hdr = result.hdr;
						var maxG =result.maxG;
						var minG =result.minG;
						var col ='<td class="head conduct"><span>'+hdr+'<span></td>';
						$(col).insertBefore('.eoc');
						$('<td><input type="text" class="micro smartbox" hdr="'+hdr+'"colnum="'+(i+1)+'"maxG="'+maxG+'" minG="'+minG+'"/></td>').insertBefore('#tbConduct .end');
					});
					var row_record = $('#rawscores .students');
					$.each(row_record, function(i, res){
						var status = $(res).attr('status');
						if(status<0){
							$(res).find('input').attr('disabled','disabled');
							$(res).find('input').val('D');
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
				type: 'POST',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period,'func':'getstudnrol'},
				timeout: 500,
				beforeSend: function(){
					$('#progress-bar').html('Loading students...');
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
							row +='<td class="medium studno"><div>'+sno+'</div></td>';
							row +='<td class="jumbo fullname"><div>'+fullname+'</div></td>';
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
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'func':'getraw'},
				timeout: 500,
				beforeSend: function(){
					$('#progress-bar').html('Loading scores...');
				},
				error: function (){ 
				$('progress-bar').html('Time out. Retrying...');
				populate_smartbox(); 
				},
				success: function(data) {
					var json_data = $.parseJSON(data);
					var smartboxes = $('#rawscores').find('.smartbox');
					$.each(smartboxes, function(i,res){
						var colnumber =  $(res).attr('colnum');
						var header = $(res).attr('hdr');
						var row = $(res).parent().parent();
						 var studentnumber = $(row).find('.studno').text();
						 $.each(json_data.result, function(i,record){				   
							var colnum = record.col;
							var sno = record.sno;
							var hdr =record.hdr;
							var score =record.score;
							if(colnumber == colnum && studentnumber==sno && header==hdr){
								$(res).val(score);
								compute_smartbox(res);
							}
						});
					});
					$('.tab').slideDown();
					$('#progress-bar').html();
				}
			});
	}
	//Smart Box Keypress
	$('.smartbox').livequery('keypress',function(e){
		var rawscore = parseFloat($(this).val());
		var perfectscore = parseFloat($(this).attr('maxitems'));
		if(rawscore<0||rawscore>perfectscore){
			alert("Invalid data entry! Score should be between 0 and "+ perfectscore);
			return;
		}else{
			compute_smartbox(this);
		}
		if(e.which==13){
			var colnum =  $(this).attr('colnum');
			var row = $(this).parent().parent();
			var rownum = $(row).attr('rownum');	
			
			var classcode = $('#load').find('option:selected').val();
			var sy = $('#sy').find('option:selected').val();
			var period = $('#period').find('option:selected').val();
			var sno =$(row).find('.studno').text();
			var hdr =$(this).attr('hdr');
			
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period': period, 'sno': sno, 'hdr': hdr, 'rawscore': rawscore, 'func':'saveraw'},
				success: function(data) {
//					alert (data);
				}
			});
			var items = $('#rawscores').find('.smartbox');
			var ctr=1;
			var trace="";
			for(var index=0; index<items.length;index++){
				var res =items[index];
				var cn  = $(res).attr('colnum');
				var r = $(res).parent().parent();
				var rn = $(r).attr('rownum');
				var value = $(res).val();
				var isDisabled = $(res).attr('disabled');
				if(cn==colnum){
					trace += 'cn '+cn+'rn '+rn+ 'row'+ row+'<br>';
					if(rownum<=9&&isDisabled){
						ctr=0;
					}else if(rownum==1){
						ctr=1;
					}
					if(isDisabled&&rn<=rownum){
						ctr+=1;	
					}
					if(rn==rownum-ctr && !isDisabled){
						$(res).focus();
						$('#debug').html(trace);
						return;
					}
					
				}
			}
			/*$.each(items,function(i,res){
				var cn  = $(res).attr('colnum');
				var r = $(res).parent().parent();
				var rn = $(r).attr('rownum');
				var value = $(res).val();
				var isDisabled = $(res).attr('disabled');
				if(cn==colnum&&rn<rownum){
					trace += 'cn '+cn+'rn '+rn+ 'row'+ row+'<br>';
					if(rn==rownum-1){
						$(res).focus();
						return;
					}
				}
			});
			*/
		}
		
	});
	//Smart Box keyup
	function compute_smartbox(obj){
		var rawscore = parseFloat($(obj).val());
		var perfectscore = parseFloat($(obj).attr('maxitems'));
		var base  = parseFloat($(obj).attr('base'));
		var colnum =  $(obj).attr('colnum');
		var row = $(obj).parent().parent();
		var rownum = $(row).attr('rownum');
		var items = $('#equivalent').find('.dumbbox');
		$.each(items,function(i,res){
			var cn  = $(res).attr('colnum');
			var r = $(res).parent().parent();
			var rn = $(r).attr('rownum');
			if(rn==rownum && cn==colnum){
				//FORMULA				
				var value = ((rawscore / perfectscore) * (100 - base)) + base;
				$(res).text(value);
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
			var user_name= 	'MENARN '; //$('#user_name').val();
			var password=	'12345'; //$('#password').val();
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
						$.getJSON(this.url+'?token='+o.token+'&func=getInfo&id='+o.id, function(data){
							$('#faculty_id').val(data.id);
							$('#full_name').val(data.full_name);
						});
						//Load System Default
						$.getJSON('ajax.php?func=getSysDefa&token='+o.token, function(data){
							var sy = data.active_sy;
							var base =data.base;
							var str = sy + ' - '+ (sy+1);
							var period_str="";
							templates = data.templates;
							$.each(data.period, function(i,res){
								period_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							var component_str="";
							$.each(data.component, function(i,res){
								component_str +='<option value="'+res.code+'">'+res.desc+'</option>';
							});
							var load_str="";
							$.each(data.faculty_load, function(i,res){
								load_str +='<option value="'+res.sec_code+'-'+res.subject+'" dept="'+res.dept+'" level="'+res.level+'">'+res.subject+' / '+res.section+'</option>';
							});
							var temp_str="";
							$.each(data.templates, function(i,res){
								temp_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							$('#load').html(load_str);
							$('#description').html('<option value="###" class="default">Select Description</option>'+component_str);
							$('#period').html(period_str);
							$('#templates').html('<option value="###" class="default">Select Template</option>'+temp_str);
							$('#sy').html('<option value="'+sy+'">'+str+'</option>');
							$('#base').attr('def',base);
							$('#base').val(base);
						});
						$('#content').animate({opacity:'1'},1000);
						$('.tab-header').prepend('<span class="indicator">+</span>');
					}
				}
			});
	});	
});
</script>
<title>Electronic Grade Book</title>
</head>

<body>
<div class="container page">
		<div class="header">
        	<div id="branding" class="column left">
                	<div id="logo" class="column left">
						<img src="img/HTA Logo.png" class="right"/>
                    </div>
                    <div id="name" class="column right">
                    	<div class="middle">
                    		<div id="title">Integrated School Management System</div>
                        	<div id="subtitle">Electronic Grade Book</div>
                        </div>
                    </div>
            </div>
            <div id="login" class="column right">
            	<div id="message" class="column right">
                	<div id="login-form"> 
                    	<div id="response"> </div>
                        <form class="right" action="javascript:void();">
                        	<div >
                        		Username:<input type="text" id="user_name" />
                            	Password: <input type="password" id="password" />
                            </div>
                        	<div class="right">
                    			<button id="login-btn">Log-in</button> or
                    			<a>Register</a>
                        	</div>
                        </form>
                    </div>
                </div>
            </div>
		</div>
		<div class="main outline">
        		<div id="debug">
                </div>
        		<div class="container">
				<center>
              		<ul id="menu">
                	<li>
                    	<img src="img/home.png" width="20px"/>
                    	<a> Home</a>
                    </li>
                    <li>
                         <img src="img/all friends.png" width="20px"/> 
						<a>Student Services</a>
                        <ul>
							<li><a href="#">Admission</a>
                            </li>
						</ul>
                    </li>
                    <li>
                    	 <img src="img/genericfriendicon.png" width="20px"/><a> Academics</a>
                        <ul>
							<li><a href="#">Head</a></li>
							<li><a href="#">Faculty</a></li>
							<li><a href="#">Registrar</a></li>
						</ul>
                    </li>
                    <li>
                    	<img src="img/privacy.png" width="20px"/>
                        <a>Adminitrative</a>
                         <ul>
							<li><a href="#">Human Resources</a></li>
							<li><a href="#">Info Tech</a></li>
							<li><a href="#">Registrar</a></li>
						</ul>
                    </li>
                    <li>
                    	<img src="img/list.png" width="20px"/>
                        <a>Revenue Center</a>
                    </li>
                    <li>
                    	<img src="img/mobileicon.png" width="20px"/> 
                        <a>Misc</a>
                         <ul>
							<li><a href="#">Common Services</a></li>
						</ul>
                    </li>
                    
                	</ul>
                </center>
                </div>
                <div id="content" style="opacity:0">
                <h2><img src="img/editprofile.png" width="30px" /> Grade Book</h2>
                <hr>
                            <table cellpadding="0" cellspacing="0" class="container">
                            	<tr>
                            		<td>
                                   	<div align="right">Faculty ID: </div></td>
                                    <td>
                                   		<input class="large disable-text" id="faculty_id" type="text" disabled="disabled"/ >
                                	</td>
                                     <td>
                                    <div class="spacer"></div>
                                    </td>
                                    <td>
                                    <div align="right">Faculty Name: </div>
                                    </td>
                                    <td>
                                   		<input class="xlarge disable-text" id="full_name" type="text" disabled="disabled"/>
                                	</td>
                                    <td>
                                	</td>
                                    <td>
                                	</td>
                                    <td>
                                	</td>
                                </tr>
                            	<tr>
                                	<td>
                                   	<div align="right">SY: </div></td>
                                     <td>
                                   		<select id="sy" class="large">
                                        </select>
                                	</td>
                                      <td>
                                    <div class="spacer"></div>
                                    </td>
                                    <td>
                                    <div align="right">Subject/Section: </div>
                                    </td>
                                    <td>
                                   		<select type="text"  id="load" class="xlarge">
                                        </select>
                                	</td>
                                    <td>
                                    <div class="spacer"></div>
                                    </td>
                                    <td>
                                    <div align="right">Period: </div>
                                    </td>
                                    <td>
                                   		<select id="period" class="large">
                                        </select>
                                	</td>
                                </tr>
                                <tr>
                            	  <td>&nbsp;</td>
                            	  <td>&nbsp;</td>
                            	  <td>&nbsp;</td>
                            	  <td>&nbsp;</td>
                            	  <td>&nbsp;</td>
                            	  <td>&nbsp;</td>
                            	  <td>&nbsp;</td>
                            	  <td><div align="right"><button class="load_btn">Load</button></div></td>
                          	  </tr>
                            </table>
                    		<div id="entry-form">
                            	<div id="progress-bar"></div>
                            	<?php include('grade-entry.php');?>
                            </div>                    
                </div>
		</div>
        <div class="footer">
			<div class="left">Integrated School Management System  © 2011</div>
            <div class="right">Privacy · Terms · Help</div>
		</div>

</div>
</body>

</html>
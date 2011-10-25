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
		load_routine(classcode, sy, period);
	});
	function load_routine(classcode, sy, period){
		//Clean Up
		$('#tbGenComp').find('.component').fadeOut('slow').remove();
		$('#tbMeasItem').find('.measurable').fadeOut('slow').remove();
		$('#row-counter').html('1');
		$('#col-counter').html('1');
		$('#classcodes').html('');
		$('#total').html('0');
		$('.tab').slideUp();
		
		$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period':period,'func':'getcomponents'},
				success: function(data) {
					$('#debug').html(data);
					var json_data = $.parseJSON(data);				  
					var rwctr = json_data.rownum;
					components = json_data.components;
					measurables = json_data.measurables;
					if(components!=null){
					$.each(components,function(i,result){
						var rwctr = result.rownum;
						var ccode =result.ccode;
						var descp = result.desc;
						var prcnt = result.perc;
						//Load existing components
						var row = '<tr row-ctr='+rwctr+' class="component" >';
							row += '<td class="mini counter">'+rwctr+'</td>';
				            row += '<td class="small classcode">'+ccode+'</td>';							
							row	+= '<td class="large  type">'+descp+'</td>';
				            row	+= '<td class="mini percentage" value="'+prcnt+'" ><div>'+prcnt+'</div></td>';
				 			row += '<td class="mini noborder"><a href="javascript:void();" class="edit-btn" inside="tbGenComp" ><img src="img/edit.jpg"/></a></td>';
							row += '<td class="mini noborder"><a href="javascript:void();" class="delete-btn" inside="tbGenComp" ><img src="img/delete.png"/></a></td>';
							row +='</tr>';
						$(row).hide().insertBefore('#input-source').show('slow');
						$('#classcodes').html($('#classcodes').html()+'<option values="'+ccode+'">'+ccode+'</option>');	
						rwctr = parseFloat(rwctr)+1;
						$('#row-counter').text(rwctr);
						$('#percentage').val('');
						computeSum();
					});
					}
					if(components==null){
						$('#tbMeasItem').hide();
						$('#nodata').html('No data yet!');
					}
					else{
					$('#tbMeasItem').show();
					$('#nodata').html('');
					$.each(measurables,function(i,result){
						var clctr = result.colnum;
						var ccode =result.ccode;
						var hdr = result.hdr;
						var dsc = result.dsc;
						var itm =result.itm;
						var bse =result.bse;
						var col = '<tr col-ctr='+clctr+' class="measurable">';
							col += '<td class="mini counter">'+clctr+'</td>';
            				col += '<td class="small classcode">'+ccode+'</td>';
			            	col	+= '<td class="mini item-header"><div>'+hdr+'</div></td>';
							col	+= '<td class="mini type"><div>'+dsc+'</div></td>';
							col	+= '<td class="mini num-items"><div>'+itm+'</div></td>';
							col	+= '<td class="mini const-base"><div>'+bse+'</div></td>';
 							col += '<td class="micro noborder"><a href="javascript:void();" class="edit-btn" inside="tbMeasItem"><img src="img/edit.jpg"/></a></td>';
							col += '<td class="micro noborder"><a href="javascript:void();" class="delete-btn" inside="tbMeasItem"><img src="img/delete.png"/></a></td>';
							col +='</tr>';
						$(col).hide().insertBefore('#data-source').show('slow');
						clctr = parseFloat(clctr)+1;
						$('#col-counter').text(clctr);				
					});
					}
				}
		});
		$('.tab').slideDown();
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
	//Description
	$('#description').change(function(){
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
		var value = $(this).val();
		$('#class-code').html(value);
		$('#percentage').focus();
		if(!parseInt($('#total').text())){
			$('#total').text(0);
		}
		$(this).parent().find('.add-btn').removeClass('disable-text');
	});
	//Template
	$('#templates').change(function(){
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
		var id = $(this).find('option:selected').val();
		var row="";
		var rwctr=0;
		$('#save-record').attr('disabled', '');
		$.each(templates, function(i,t){
			if(id==t.id){
				var tcomponents = t.components;
				$.each(tcomponents, function(i,result){
					rwctr = result.rownum;
					var ccode =result.ccode;
					var descp = result.desc;
					var prcnt = result.perc;					   
					row += '<tr row-ctr='+rwctr+' class="component">';
					row += '<td class="mini counter">'+rwctr+'</td>';
            		row += '<td class="small classcode">'+ccode+'</td>';
					row	+= '<td class="large  type">'+descp+'</td>';
            		row	+= '<td class="mini percentage"><div>'+prcnt+'</div></td>';
 					row += '<td class="mini noborder"><a href="javascript:void();" class="edit-btn" inside="tbGenComp" ><img src="img/edit.jpg"/></a></td>';
					row += '<td class="mini noborder"><a href="javascript:void();" class="delete-btn" inside="tbGenComp" ><img src="img/delete.png"/></a></td>';
					row +='</tr>';
				});
			}
		});
		$('#tbGenComp').find('.component').fadeOut('slow').remove();
			$(row).hide().insertBefore('#input-source').show('slow');
			rwctr = parseFloat(rwctr)+1;
			$('#row-counter').text(rwctr);
			$('#percentage').val('');
			computeSum();
	});
	//Add new component
	$('.add-btn').click(function(){
		var source = $(this).attr('inside');
		//tgGenComp Routine
		if(source=='tbGenComp'){
			var total = parseFloat($('#total').text());
			var data = $('#description').find('option:selected').text();
			var duplicate =false;
			$('#'+source).find('td.type').each(function(){
				var type=$(this).html();
				if(type==data){
					duplicate = true;
				}
			});
			if(duplicate==true){
				alert ('Duplication of Component Class not allowed. Please select another class.');
			}
			else if(total<100){
			var rwctr = $('#row-counter').text();
			var ccode = $('#class-code').text();
			var descp = $('#description').find('option:selected').text();
			var prcnt =	$('#percentage').val();
			var row = '<tr row-ctr='+rwctr+' class="component">';
				row += '<td class="mini counter">'+rwctr+'</td>';
            	row += '<td class="small classcode">'+ccode+'</td>';
				row	+= '<td class="large  type">'+descp+'</td>';
            	row	+= '<td class="mini percentage"><div>'+prcnt+'</div></td>';
 				row += '<td class="mini noborder"><a href="javascript:void();" class="edit-btn" inside="'+source+'"><img src="img/edit.jpg"/></a></td>';
				row += '<td class="mini noborder"><a href="javascript:void();" class="delete-btn" inside="'+source+'"><img src="img/delete.png"/></a></td>';
				row +='</tr>';
			$(row).hide().insertBefore('#input-source').show('slow');
			rwctr = parseFloat(rwctr)+1;
			$('#row-counter').text(rwctr);
			$('#percentage').val('');
			computeSum();
			}else if(total==100){
				alert('Total already 100');
			}
		}
		else if(source=='tbMeasItem'){
			var clctr = $('#col-counter').text();
			var ccode =  $('#classcodes').find('option:selected').val();
			var header = $('#item_header').val();
			var descp = $('#item-desc').val();
			var items =$('#items').val();
			var base =$('#base').val();
			var col = '<tr col-ctr='+clctr+' class="measurable">';
				col += '<td class="mini counter">'+clctr+'</td>';
            	col += '<td class="small classcode">'+ccode+'</td>';
            	col	+= '<td class="mini item-header"><div>'+header+'</div></td>';
				col	+= '<td class="mini type"><div>'+descp+'</div></td>';
				col	+= '<td class="mini num-items"><div>'+items+'</div></td>';
				col	+= '<td class="mini const-base"><div>'+base+'</div></td>';
 				col += '<td class="micro noborder"><a href="javascript:void();" class="edit-btn" inside="'+source+'"><img src="img/edit.jpg"/></a></td>';
				col += '<td class="micro noborder"><a href="javascript:void();" class="delete-btn" inside="'+source+'"><img src="img/delete.png"/></a></td>';
				col +='</tr>';
			$(col).hide().insertBefore('#data-source').show('slow');
			clctr = parseFloat(clctr)+1;
			$('#col-counter').text(clctr);
			$('#item_header').val('').focus();
			$('#item-desc').val('');
			$('#items').val('');
			$('#base')	.val($('#base').attr('def'));		
		}
	});
	//Edit selected component
	$('.edit-btn').livequery('click',function(){
		var source = $(this).attr('inside');
		var parent = $(this).parent().parent();
		parent.find('td .edit-btn').removeClass('edit-btn').addClass('save-btn').html('<img src="img/update.jpg" />');
		parent.find('td .delete-btn').removeClass('delete-btn').addClass('cancel-btn').html('<img src="img/cancel.jpg" />');
		//tbGenComp routine
		if(source=='tbGenComp'){
			var v  = parent.find('.percentage div').text();
			parent.find('.percentage').html('<input type="text" class="mini" value="'+v+'" prev="'+v+'"/>');
			parent.find('.percentage input').focus();
		}
		else if(source=='tbMeasItem'){
			var itm_hdr = parent.find('.item-header div').text();
			parent.find('.item-header div').html('<input type="text" class="small" value="'+itm_hdr+'" prev="'+itm_hdr+'"/>');
			var itm_dsc = parent.find('.type div').text();
			parent.find('.type div').html('<input type="text" class="large" value="'+itm_dsc+'" prev="'+itm_dsc+'"/>');
			var itm_num = parent.find('.num-items div').text();
			parent.find('.num-items div').html('<input type="text" class="small" value="'+itm_num+'" prev="'+itm_num+'"/>');
			var itm_bse = parent.find('.const-base div').text();
			parent.find('.const-base div').html('<input type="text" class="small" value="'+itm_bse+'" prev="'+itm_bse+'"/>');
			parent.find('.item-header input').focus();
		}
	});
	//Delete selected component
	$('.delete-btn').livequery('click',function(){
		var source = $(this).attr('inside');
		var parent = $(this).parent().parent();
		var index=0;
		parent.fadeOut('slow', function(){
			parent.remove();
			$('#'+source).find('td.counter').each(function(){
				index+=1;
				$(this).html(index);
			});
			//tbGenComp routine
			if(source=='tbGenComp'){
				$('#row-counter').text(index+=1);
				computeSum();
			}
			else if(source=='tbMeasItem'){
				$('#col-counter').text(index+=1);
			}
		});
		
	});
	//Save changes to selected component
	$('.save-btn').livequery('click',function(){
		var source = $(this).attr('inside');
		var parent = $(this).parent().parent();
		parent.find('td .save-btn').addClass('edit-btn').removeClass('save-btn').html('<img src="img/edit.jpg" />');
		parent.find('td .cancel-btn').addClass('delete-btn').removeClass('cancel-btn').html('<img src="img/delete.png" />');
		//tbGenComp routine
		if(source=='tbGenComp'){
			var v = parent.find('.percentage input').val();
			parent.find('.percentage').html('<div>'+v+'</div>');
			computeSum();
		}
		else if(source=='tbMeasItem'){
			var itm_hdr = parent.find('.item-header input').val();
			parent.find('.item-header').html('<div>'+itm_hdr+'</div>');
			var itm_dsc = parent.find('.type input').val();
			parent.find('.type').html('<div>'+itm_dsc+'</div>');
			var itm_num = parent.find('.num-items input').val();
			parent.find('.num-items').html('<div>'+itm_num+'</div>');
			var itm_bse = parent.find('.const-base input').val();
			parent.find('.const-base').html('<div>'+itm_bse+'</div>');
		}
	});
	//Cancel action and retrieve previous value of the selected component
	$('.cancel-btn').livequery('click',function(){
		var source = $(this).attr('inside');
		var parent = $(this).parent().parent();
		parent.find('td .save-btn').addClass('edit-btn').removeClass('save-btn').html('<img src="img/edit.jpg" />');
		parent.find('td .cancel-btn').addClass('delete-btn').removeClass('cancel-btn').html('<img src="img/delete.png" />');
		//tbGenComp routine
		if(source=='tbGenComp'){
			var v = parent.find('.percentage input').attr('prev');
			parent.find('.percentage').html('<div>'+v+'</div>');										
		}
		else if(source=='tbMeasItem'){
			var itm_hdr = parent.find('.item-header input').attr('prev');
			parent.find('.item-header').html('<div>'+itm_hdr+'</div>');
			var itm_dsc = parent.find('.type input').attr('prev');
			parent.find('.type').html('<div>'+itm_dsc+'</div>');
			var itm_num = parent.find('.num-items input').attr('prev');
			parent.find('.num-items').html('<div>'+itm_num+'</div>');
			var itm_bse = parent.find('.const-base input').attr('prev');
			parent.find('.const-base').html('<div>'+itm_bse+'</div>');
		}
	});
	//Calculates the total of all components
	function computeSum(){
		var sum = 0;
		for(var ctr=0; ctr<$('.percentage').length;ctr+=1){
			var value = parseFloat($('.percentage')[ctr].firstChild.innerHTML);
			sum = sum + value;
		}
		$('#total').text(sum);
		if(sum>100){
			$('#total').parent().addClass('error').removeClass('noborder').remove;
			$('#error-terminal').hide().html('<strong>Error:</strong> Total is greater than 100!').show('slow');
			$('#add-btn').addClass('disable-text');
		}
		else{
			$('#total').parent().removeClass('error').addClass('noborder');
			$('#error-terminal').hide('slow');
			$('#add-btn').removeClass('disable-text');
		}
		if(sum==100){
			$('#add-btn').addClass('disable-text');
		}
	}
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
								load_str +='<option value="'+res.sec_code+'-'+res.subject+'">'+res.subject+' / '+res.section+'</option>';
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
	//Save Record
	$('.save-record').click(function(){
		var source = $(this).attr('inside');
		if(source=='tbGenComp'){
			var total = parseFloat($('#total').text());
			if(total!=100){
				alert("Total not equal to 100%.");
			}else{
				//Save All
				var section_code = $('#load').find('option:selected').val();
				var sy = $('#sy').find('option:selected').val();
				var period = $('#period').find('option:selected').val();
				var classcode=[];
				var rownumber=[];
				var percentage=[];
				//Collect class code
				$('#classcodes').html('');
				var index=0;
				$.each($('#tbGenComp tr').find('.classcode'),function(i,result){
					var ccode  = result.innerHTML;
					classcode[index] = ccode ;
					$('#classcodes').html($('#classcodes').html()+'<option values="'+ccode+'">'+ccode+'</option>');	
					index+=1;
				});
				//Collect row number
				index=0;
				$.each($('#tbGenComp tr').find('.counter'),function(i,result){
					rownumber[index] = result.innerHTML;
					index+=1;
				});
				//Collect percentage
				index=0;
				$.each($('#tbGenComp tr').find('.percentage div'),function(i,result){
					percentage[index] = result.innerHTML;
					index+=1;
				});
				//Submit record
				$.ajax({
					type: 'POST',
					url: 'ajax.php',
					data: {'section_code' : section_code,'sy':sy, 'classcode': classcode, 'period': period, 'rownumber': rownumber, 
							'percentage':percentage, 'func': 'save_record_gencomp'},
					success: function(data) {
						alert('Record saved');
						$('#save-record').attr('disabled', 'disabled');
						$('#tbMeasItem').show();
						$('#nodata').html('');
						var classcode = $('#load').find('option:selected').val();
						var sy = $('#sy').find('option:selected').val();
						var period = $('#period').find('option:selected').val();
						load_routine(classcode, sy, period);
					}
				});
			}
		}
		else if(source =='tbMeasItem'){
			var section_code = $('#load').find('option:selected').val();
			var sy = $('#sy').find('option:selected').val();
			var classcodes = $('#tbMeasItem').find('td.classcode');
			var period = $('#period').find('option:selected').val();
			var isComplete;
			var trace ="";
			for(var i=0; i<components.length;i++){
				isComplete=false;
				trace += 'code - '+ components[i].ccode;
				for(var j=0;j<classcodes.length;j++){
					if(classcodes[j].innerHTML==components[i].ccode){
						isComplete=true;
						break;
					}
				}
				if(!isComplete){
					break;
				}
				trace += ' - ' + isComplete +'<br>';
			}
			$('#debug').html(trace);
			if(!isComplete){
				alert('Could not save. Incomplete components');
			}else{
				var classcode=[];
				var colnumber=[];
				var header=[];
				var description=[];
				var noofitem=[];
				var base=[];
				//Collect class code
				var index=0;
				$.each($('#tbMeasItem tr').find('.classcode'),function(i,result){
					classcode[index] = result.innerHTML;
					index+=1;
				});
				//Collect column number
				index=0;
				$.each($('#tbMeasItem tr').find('.counter'),function(i,result){
					colnumber[index] = result.innerHTML;
					index+=1;
				});
				//Collect header
				var index=0;
				$.each($('#tbMeasItem tr').find('.item-header div'),function(i,result){
					header[index] = result.innerHTML;
					index+=1;
				});
				//Collect description
				index=0;
				$.each($('#tbMeasItem tr').find('.type div'),function(i,result){
					description[index] = result.innerHTML;
					index+=1;
				});
				//Collect number of items
				index=0;
				$.each($('#tbMeasItem tr').find('.num-items div'),function(i,result){
					noofitem[index] = result.innerHTML;
					index+=1;
				});
				//Collect base
				index=0;
				$.each($('#tbMeasItem tr').find('.const-base div'),function(i,result){
					base[index] = result.innerHTML;
					index+=1;
				});
				//Submit record
				$.ajax({
					type: 'POST',
					url: 'ajax.php',
					data: {'section_code' : section_code,'sy':sy, 'period': period, 'classcode': classcode, 'colnumber': colnumber, 
							'header':header, 'description': description, 'noofitem':noofitem, 'base':base, 'func': 'save_record_measitem'},
					success: function(data) {
						alert('Record saved');
					}
				});
			}
		}
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
                            	<?php include('component-entry.php'); ?>
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
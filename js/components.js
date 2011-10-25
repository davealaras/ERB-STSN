$(document).ready(function() {
	var QRT_EXAM = 'QE';
	var QUIZ = 'QZ';
	var DESC_OPTIONAL = true;
	var ALLOW_DRAG = false;
	$('input[type="text"]').livequery('keypress', function(e){
		if($(this).length==0&& e.which ==8){
			e.preventDefault();			
		}
	});
	
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
	//Close button
	$('#close_btn').click(function(){
		$('.tab').slideUp();
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
		var level = $('#load').find('option:selected').attr('level');
		var deptcode = $('#load').find('option:selected').attr('dept');
		$('#templates').removeAttr('disabled');
		$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: {'classcode' :classcode, 'sy':sy, 'period':period,'level': level, 'deptcode':deptcode,'func':'getcomponents'},
				success: function(data) {
					$('#debug').html(data);
					var json_data = $.parseJSON(data);				  
					var rwctr = json_data.rownum;
					components = json_data.components;
					measurables = json_data.measurables;
					templates = json_data.templates;	
					//Build template drop down
					var temp_str="";
					if(templates.length>0){
						$.each(templates, function(i,res){
							temp_str +='<option value="'+res.id+'" ccode="'+res.compcode+'">'+res.desc+'</option>';
						});
						$('#templates').removeAttr('disabled').html('<option value="###" class="default">Select Template</option>'+temp_str);
					}
					else{
						$('#templates').attr('disabled','disabled').html('<option value="###" class="default">No Template</option>');
					}
					if(components.length>0){
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
							$('#tbGenComp tbody').append(row).hide().fadeIn('slow');
							$('#classcodes').html($('#classcodes').html()+'<option values="'+ccode+'">'+ccode+'</option>');	
							rwctr = parseFloat(rwctr)+1;
							$('#row-counter').text(rwctr);
							$('#percentage').val('');
							computeSum();
						});
						$('#tbGenComp .edit-btn').hide();
						$('#tbGenComp .delete-btn').hide();
						$('#input-source').css('opacity', 0);
						
						$.each($('.save-record'),function(i,res){
							if($(res).attr('inside')=='tbGenComp'){
								$(res).hide();
								$(res).parent().parent().find('.cancel-action').hide();
								$(res).parent().parent().find('.modify-record').show();
							}
						});
						
					}
					if (components.length==0){
						$('#templates').attr('disabled','');
						$('#tbGenComp .edit-btn').show();
						$('#tbGenComp .delete-btn').show();
						$('#input-source').css('opacity', 1);
						$.each($('.save-record'),function(i,res){
							if($(res).attr('inside')=='tbGenComp'){
								$(res).show();
								$(res).parent().parent().find('.cancel-action').hide();
								$(res).parent().parent().find('.modify-record').hide();
							}
							if($(res).attr('inside')=='tbMeasItem'){
								$(res).hide();
								$(res).parent().parent().find('.cancel-action').hide();
								$(res).parent().parent().find('.modify-record').hide();
							}
						});
						$('#tbMeasItem').hide();
						$('#nodata').html('<div class="warning"><strong>Warning:</strong> No Components yet.<div></div>');
					}
					if(measurables.length>0){
						$('#templates').attr('disabled', 'disabled');
						build_meas(measurables);
					}
				}
		});
		$('.tab').slideDown();
	}
	var fixHelper = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
	};
	function handle_resort(event, ui){
	  var i = 1;
	  $("#tbGenComp tbody tr").each(function(i){
	  i = i + 1;
		$(this).attr('row-ctr',i);
	   $(this).find('.counter').text(i);
		
	  });
	  i=1;
	  $("#tbMeasItem tbody tr").each(function(i){
	  i = i + 1;
		$(this).attr('row-ctr',i);
	   $(this).find('.counter').text(i);
		
	  });
	} 
	$('.component, .measurable').livequery('mousedown',function(){
		var mode =$(this).parent().parent().attr('edit');
		$(this).css('cursor','none');
		if(Boolean(mode)==true){
			$(this).css('cursor','move');
		}
			
		
	});
	$('.component, .measurable').livequery('mouseup',function(){
		$(this).css('cursor','none');
	});
	//.tab Animation
		$('.tab-header').click(function(){
		var src = $(this);
		var dis = $(src).find('.indicator').text();
		$(src).find('.indicator').text(dis=='+'?'-':'+');
		$(this).parent().find('.tab-content').slideToggle(1000);		
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
		//Load components
		$('#error-terminal').html('');
		$('#tbGenComp tbody').html('');
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
		var ccode = $(this).find('option:selected').attr('ccode');
		var id = $(this).find('option:selected').val();
		var row="";
		var rwctr=0;
		$('#save-record').attr('disabled', '');
		$.each(templates, function(i,t){
			if(ccode==t.compcode && id ==t.id){
				var tcomponents = components = t.components;
				var tmeasurables = t.measurables;
				//Load measurables
				$('#classcodes').html('');				
				build_meas(tmeasurables);
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
 					//row += '<td class="mini noborder"><a href="javascript:void();" class="edit-btn" inside="tbGenComp" ><img src="img/edit.jpg"/></a></td>';
					//row += '<td class="mini noborder"><a href="javascript:void();" class="delete-btn" inside="tbGenComp" >&nbsp;</a></td>';
					row +='</tr>';
					var ls_ccodes = $('#classcodes').html();
					$('#classcodes').html(ls_ccodes +'<option value="'+ccode+'">'+ccode+'</option>');
				});
			}
		});
		$('#tbGenComp').find('.component').fadeOut('slow').remove();
		$('#tbGenComp tbody').append(row);
		rwctr = parseFloat(rwctr)+1;
		$('#row-counter').text(rwctr);
		$('#percentage').val('');
		computeSum();
		$('#tbMeasItem').fadeIn();
		
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
			var v = $('#description').find('option:selected').val();
			if(v =="###"){
					$('#error-terminal').fadeIn().html('<div class="warning"><strong>Warning:</strong> Please select component first.');
			}else if(parseInt(v)<0){
				alert('Invalid entry');
			}else{
				if(duplicate==true){
					$('#error-terminal').fadeIn().html('<div class="warning"><strong>Warning:</strong> Duplication of Component Class not allowed. Please select another class.');
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
				$('#tbGenComp tbody').append(row).fadeOut().fadeIn('slow');
				rwctr = parseFloat(rwctr)+1;
				$('#row-counter').text(rwctr);
				$('#percentage').val('');
				computeSum();
				$('#error-terminal').hide().html('');
				}else if(total==100){
					$('#error-terminal').fadeIn().html('<div class="warning"><strong>Warning:</strong> Total already 100.');
				}
			}			
		}
		else if(source=='tbMeasItem'){
			var clctr = $('#col-counter').text();
			var ccode =  $('#classcodes').find('option:selected').val();
			var header = $('#item_header').val();
			var descp = $('#item-desc').val();
			var items =$('#items').val();
			var base =$('#base').val();
			var isValid =true;
			var err_arr= new Array ();
			console.log($('#item_header').val().length);
			if($('#item_header').val().length==0){
				isValid =false;
				err_arr.push('header');
			}
			if(!DESC_OPTIONAL){
				if($('#item-desc').val().length==0){
					isValid =false;
					err_arr.push('description');
				}
			}
			if(parseInt($('#items').val())<=0||$('#items').val().length==0){
				isValid =false;
				err_arr.push('number of items');
			}
			if(parseInt($('#base').val())<0||$('#base').val().length==0){
				isValid =false;
				err_arr.push('base');
			}
			if(parseInt($('#base').val())>100){
				isValid =false;
				err_arr.push('base');
			}
			
			if(isValid){
				$('#nodata').html('');
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
				$('#tbMeasItem tbody').append(col).fadeOut().fadeIn('slow');
				clctr = parseFloat(clctr)+1;
				$('#col-counter').text(clctr);
				$('#item_header').val('').focus();
				$('#item-desc').val('');
				$('#items').val('');
				$('#base').val($('#base').attr('def'));	
			}else{
				$('#nodata').html('<div class="warning"><strong>Warning:</strong> Invalid '+err_arr.toString());
			}
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
			parent.find('.percentage').html('<input type="text" class="mini prct" value="'+v+'" prev="'+v+'"/>');
			parent.find('.percentage input').focus();
		}
		else if(source=='tbMeasItem'){
			$('.save-record').css('opacity', '0.1');
			var itm_hdr = parent.find('.item-header div').text();
			parent.find('.item-header div').html('<input type="text" class="small" value="'+itm_hdr+'" prev="'+itm_hdr+'"/>');
			var itm_dsc = parent.find('.type div').text();
			parent.find('.type div').html('<input type="text" class="large" value="'+itm_dsc+'" prev="'+itm_dsc+'"/>');
			var itm_num = parent.find('.num-items div').text();
			parent.find('.num-items div').html('<input type="text" class="small prct" value="'+itm_num+'" prev="'+itm_num+'"/>');
			var itm_bse = parent.find('.const-base div').text();
			parent.find('.const-base div').html('<input type="text" class="small prct" value="'+itm_bse+'" prev="'+itm_bse+'"/>');
			parent.find('.item-header input').focus();
		}
	});
	//prct logic
	$('#percentage, #items, #base').keypress(function(e){
		var isNum = (e.which>47 && e.which<=59);
		if(!isNum){
			e.preventDefault();
		}
	});
	$('.prct').livequery('keypress',function(e){
		var isNum = (e.which>47 && e.which<=59);
		if(!isNum){
			e.preventDefault();
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
		$('#error-terminal').hide().html('');
		//tbGenComp routine
		if(source=='tbGenComp'){
			var v = parent.find('.percentage input').val();
			if(parseInt(v)<0|| parseInt(v)>100 || v==''){
				$('#error-terminal').fadeIn().html('<div class="warning"><strong>Error:</strong> Invalid percentage</div>');
			}
			else{
				parent.find('.percentage').html('<div>'+v+'</div>');
				parent.find('td .save-btn').addClass('edit-btn').removeClass('save-btn').html('<img src="img/edit.jpg" />');
				parent.find('td .cancel-btn').addClass('delete-btn').removeClass('cancel-btn').html('<img src="img/delete.png" />');
				computeSum();
			}
			
		}
		else if(source=='tbMeasItem'){
			var itm_hdr = parent.find('.item-header input').val();
			var itm_dsc = parent.find('.type input').val();
			var itm_num = parent.find('.num-items input').val();
			var itm_bse = parent.find('.const-base input').val();
			if(parseInt(itm_bse)>100||parseInt(itm_bse)<0){
				var msg ="";
				if(parseInt(itm_bse)>100){
					msg = 'Must not be greater than 100';
				}
				if(parseInt(itm_bse)<0){
					msg = 'Must not be less than zero';
				}
				$('#nodata').fadeOut().fadeIn().html('<div class="warning"><strong>Warning:</strong> Invalid percentage. '+msg+'</div>');
				parent.find('.const-base input').focus();
			}else if(itm_num=='' || isNaN(parseInt(itm_num)) || parseInt(itm_num)<=0){
				var msg ="";
				if(itm_num==''){
					msg = 'Must not be empty.';
				}
				if(isNaN(parseInt(itm_num))){
					msg = 'Must be numeric.';
				}
				if(parseInt(itm_num)<=0){
					msg = 'Must be greater than zero.';
				}
				$('#nodata').fadeOut().fadeIn().html('<div class="warning"><strong>Warning:</strong> Invalid number of items.'+msg+'</div>');
				parent.find('.num-items input').focus();
			}else{
				$('.save-record').css('opacity', '1');
				parent.find('.const-base').html('<div>'+itm_bse+'</div>');
				parent.find('.num-items').html('<div>'+itm_num+'</div>');
				parent.find('.type').html('<div>'+itm_dsc+'</div>');
				parent.find('.item-header').html('<div>'+itm_hdr+'</div>');
				parent.find('td .save-btn').addClass('edit-btn').removeClass('save-btn').html('<img src="img/edit.jpg" />');
				parent.find('td .cancel-btn').addClass('delete-btn').removeClass('cancel-btn').html('<img src="img/delete.png" />');
				$('#nodata').html('');
			}
		}
	});
	//Cancel action and retrieve previous value of the selected component
	$('.cancel-btn').livequery('click',function(){
		var source = $(this).attr('inside');
		var parent = $(this).parent().parent();
		parent.find('td .save-btn').addClass('edit-btn').removeClass('save-btn').html('<img src="img/edit.jpg" />');
		parent.find('td .cancel-btn').addClass('delete-btn').removeClass('cancel-btn').html('');
		//tbGenComp routine
		if(source=='tbGenComp'){
			$('#error-terminal').hide().html('');
			var v = parent.find('.percentage input').attr('prev');
			parent.find('.percentage').html('<div>'+v+'</div>');										
		}
		else if(source=='tbMeasItem'){
			$('#nodata').html('');
			$('.save-record').css('opacity', '1');
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
			$('#error-terminal').show().html('<div class="warning"><strong>Error:</strong> Total is greater than 100!</div>');
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
			var user_name= 	$('#user_name').val();
			var password=	$('#password').val();
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
						$('#login-form').hide().html(o.msg).fadeIn('slow').parent().remove();
						$('#logout').show();
						//Load System Default
						load_sysdef(o);
					}
				}
			});
	});	
	//Modify Record
	$('.modify-record').click(function(){
		var tbID = $(this).attr('inside');
		if(ALLOW_DRAG){
			$('#'+tbID+' tbody').sortable({helper: fixHelper, update: handle_resort}).disableSelection();
			$('#'+tbID+' tbody').sortable({disabled: false});
		}
		var backup = $('#'+tbID+' tbody').html();
		$('#'+tbID+' tbody').attr('backup',backup);
		$(this).parent().parent().find('.save-record').fadeIn();
		$(this).parent().parent().find('.cancel-action').fadeIn();
		$(this).hide();
		$('#'+tbID).attr('edit',true);
		$('#'+tbID+' .edit-btn').fadeIn();
		$('#'+tbID+' .delete-btn').fadeIn();
		if(tbID=='tbGenComp'){
			$('#input-source').css('opacity', 1);
		}
		if(tbID=='tbMeasItem'){
			$('#data-source').css('opacity', 1);
		}
	});
	//Cancel Action
	$('.cancel-action').click(function(){
		var tbID = $(this).attr('inside');
		if(ALLOW_DRAG){
			$('#'+tbID+' tbody').sortable({disabled: true});
		}
		var backup = $('#'+tbID+' tbody').attr('backup');
		$('#'+tbID+' tbody').fadeOut().html(backup).fadeIn();
		$('#'+tbID+' tbody').attr('backup','');
		$('#'+tbID).attr('edit',false);
		$(this).parent().parent().find('.save-record').hide();
		$(this).parent().parent().find('.modify-record').fadeIn();
		$(this).hide();
		$('#'+tbID+' .edit-btn').fadeOut();
		$('#'+tbID+' .delete-btn').fadeOut();
		if(tbID=='tbGenComp'){
			$('#error-terminal').html('');
			$('#input-source').css('opacity', 0);
			computeSum();
		}
		if(tbID=='tbMeasItem'){
			$('#nodata').html('');
			$('#data-source').css('opacity', 0);
		}
	});
	//Save Record 
	$('.save-record').click(function(){
		$('#error-terminal').show().html('');
		var source = $(this).attr('inside');
		var allowSave;
		allowSave = $(this).css('opacity')==1? true: false;
		if(!allowSave){
			console.log('neglect save');
			return;
		}
		//Collect number of items
		index=0;
		$.each($('#tbMeasItem tr').find('.num-items div'),function(i,result){
			if(isNaN(parseInt(result.innerHTML))){
				$(result).addClass('invaliddata');
				allowSave =false;
			}
			if(parseInt(result.innerHTML)==0){
				$(result).addClass('invaliddata');
				allowSave =false;
			}
			index+=1;
		});
		if(!allowSave){
			$('#nodata').fadeOut().fadeIn().html('<div class="warning"><strong>Warning:</strong> Invalid number of items.');
			return;
		}
		//Save General components
		
			var total = parseFloat($('#total').text());
			if(total!=100){
				$('#error-terminal').show().html('<div class="warning"><strong>Warning:</strong> Total not equal to 100%.');
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
			
				var today = new Date();
				var utcDate = today.toUTCString();
				//Save  record
					$.ajax({
						type: 'GET',
						url: 'ajax.php',
						data: {'section_code' : section_code,'sy':sy, 'classcode': classcode, 'period': period, 'rownumber': rownumber, 
								'percentage':percentage, 'func': 'save_record_gencomp', 'cache': utcDate},
						success: function(data) {
							$('#save-record').attr('disabled', 'disabled');
							$('#tbMeasItem').show();
							$('#nodata').html('');
							//var classcode = $('#load').find('option:selected').val();
							//var sy = $('#sy').find('option:selected').val();
							//var period = $('#period').find('option:selected').val();
							//Save Measurable items
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
							}
							$('#debug').html(trace);
							if(!isComplete){
								$('#nodata').show().html('<div class="warning"><strong>Warning:</strong> Could not save. Incomplete components.');
							}else if(isComplete) {
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
								$('#templates').attr('disabled', 'disabled');
								//Submit record
								
								$.ajax({
									type: 'POST',
									url: 'ajax.php',
									data: {'section_code' : section_code,'sy':sy, 'period': period, 'classcode': classcode, 'colnumber': colnumber, 
											'header':header, 'description': description, 'noofitem':noofitem, 'base':base, 'func': 'save_record_measitem'},
									success: function(data) {
										alert('Record saved');
										var classcode = $('#load').find('option:selected').val();
										var sy = $('#sy').find('option:selected').val();
										var period = $('#period').find('option:selected').val();
										load_routine(classcode, sy, period);
									}
								});
								
							}
						}
					});
				
			}
		
		
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
							var str = sy + ' - '+ (sy+1);
							var period_str="";
							$.each(data.period, function(i,res){
								period_str +='<option value="'+res.id+'">'+res.desc+'</option>';
							});
							var component_str="";
							$.each(data.component, function(i,res){
								component_str +='<option value="'+res.code+'">'+res.desc+'</option>';
							});
							var load_str="";
							
							$.each(data.faculty_load, function(i,res){
									load_str +='<option sy="'+res.sy+'"value="'+res.sec_code+'-'+res.comp_code+'" dept="'+res.dept+'" level="'+res.level+'">'+res.subject+' / '+res.section+'</option>';								
							});
							$('#load').html(load_str);
							var subjects = $('#load').find('option');
							correct_list(subjects, sy);
							$('#description').html('<option value="###" class="default">Select Description</option>'+component_str);
							$('#period').html(period_str);
							$('#sy').html('<option value="'+sy+'">'+str+'</option> <option value="'+(sy+1)+'">2012-2013</option>');
							$('#base').attr('def',base);
							$('#base').val(base);
						});
						$('#content').animate({opacity:'1'},1000);
						$('#LEFT-CONTENT').remove();
						$('.tab-header').prepend('<span class="indicator">+</span>');
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
	function build_meas(meas_obj){
		$('#tbMeasItem').fadeIn();
		$('#nodata').html('');
		$('#tbMeasItem tbody').html('');
		if(meas_obj!=null){
			$.each(meas_obj,function(i,result){
				var clctr = result.colnum;
				var ccode =result.ccode;
				var hdr = result.hdr;
				var dsc = result.dsc==undefined?'Place description here.':result.dsc;
				var itm =result.itm==undefined?'100':result.itm;
				var bse =result.base;
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
				$('#tbMeasItem tbody').append(col);
				clctr = parseFloat(clctr)+1;
				$('#col-counter').text(clctr);				
			})
			$('#tbMeasItem .edit-btn').fadeOut();
			$('#tbMeasItem .delete-btn').fadeOut();
			$('#data-source').css('opacity', 0);
			$.each($('.save-record'),function(i,res){
				if($(res).attr('inside')=='tbMeasItem'){
					$(res).css('display', 'none');
					$(res).parent().parent().find('.cancel-action').css('display', 'none');
					$(res).parent().parent().find('.modify-record').css('display', '');
				}
			});
			$('.edit-btn').css('display', 'none');
			$('.delete-btn').css('display', 'none');
		}else{
		
			$('#tbMeasItem .edit-btn').fadeIn();
			$('#tbMeasItem .delete-btn').fadeIn();
			$('#data-source').css('opacity', 1);
			$.each($('.save-record'),function(i,res){
				if($(res).attr('inside')=='tbMeasItem'){
					$(res).fadeIn();
					$(res).parent().parent().find('.cancel-action').fadeIn();
					$(res).parent().parent().find('.modify-record').fadeOut();
				}
			});
			
		}
	}
	$('#classcodes').change(function(){
	
		var ccode =  $(this).find('option:selected').val();
		if(ccode==QRT_EXAM || ccode == QUIZ){
			$('#base').attr('def', 50).val(50).attr('disabled','disabled');
		}else{
			$('#base').attr('def', 50).val(50).attr('disabled','');
		}
	});
});
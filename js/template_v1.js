$(document).ready(function() {
	$('input').livequery('keypress', function(e){
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
					$('.tab').slideDown();
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
							$('#tbGenComp tbody').append(row).hide().show('slow');
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
					
					if(components==null){
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
					else{
						$('#tbMeasItem').show();
						$('#nodata').html('');
						if(measurables!=null){
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
								$('#tbMeasItem tbody').append(col).hide().show('slow');
								clctr = parseFloat(clctr)+1;
								$('#col-counter').text(clctr);				
							})
							$('#tbMeasItem .edit-btn').hide();
							$('#tbMeasItem .delete-btn').hide();
							$('#data-source').css('opacity', 0);
							$.each($('.save-record'),function(i,res){
								if($(res).attr('inside')=='tbMeasItem'){
									$(res).hide();
									$(res).parent().parent().find('.cancel-action').hide();
									$(res).parent().parent().find('.modify-record').show();
								}
							});
							
						}else{
						
							$('#tbMeasItem .edit-btn').show();
							$('#tbMeasItem .delete-btn').show();
							$('#data-source').css('opacity', 1);
							$.each($('.save-record'),function(i,res){
								if($(res).attr('inside')=='tbMeasItem'){
									$(res).show();
									$(res).parent().parent().find('.cancel-action').show();
									$(res).parent().parent().find('.modify-record').hide();
								}
							});
							
						}
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
		$('#tbGenComp tbody').append(row).hide().show('slow');
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
			var v = $('#description').find('option:selected').val();
			if(v =="###"){
					$('#error-terminal').show().html('<div class="warning"><strong>Warning:</strong> Please select component first.');
			}else if(parseInt(v)<0){
				alert('Invalid entry');
			}else{
				if(duplicate==true){
					$('#error-terminal').show().html('<div class="warning"><strong>Warning:</strong> Duplication of Component Class not allowed. Please select another class.');
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
				$('#tbGenComp tbody').append(row).hide().fadeIn('slow');
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
			if(ccode==undefined){
				isValid =false;
				err_arr.push('class code');
			}
			if($('#item_header').val().length==0){
				isValid =false;
				err_arr.push('header');
			}
			/*
			if($('#item-desc').val().length==0){
				//isValid =false;
				err_arr.push('description');
			}
			
			if(parseInt($('#items').val())<0||$('#items').val().length==0){
				//isValid =false;
				err_arr.push('number of items');
			}
			*/
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
					//col	+= '<td class="mini type"><div>'+descp+'</div></td>';
					//col	+= '<td class="mini num-items"><div>'+items+'</div></td>';
					col	+= '<td class="mini const-base"><div>'+base+'</div></td>';
					col += '<td class="micro noborder"><a href="javascript:void();" class="edit-btn" inside="'+source+'"><img src="img/edit.jpg"/></a></td>';
					col += '<td class="micro noborder"><a href="javascript:void();" class="delete-btn" inside="'+source+'"><img src="img/delete.png"/></a></td>';
					col +='</tr>';
				$('#tbMeasItem tbody').append(col).hide().fadeIn('slow');
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
			if(parseInt(v)<=0|| parseInt(v)>100 || v==''){
				$('#error-terminal').show().html('<div class="warning"><strong>Error:</strong> Invalid percentage</div>');
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
			if(parseInt(itm_bse)>100){
				$('#nodata').show().html('<div class="warning"><strong>Error:</strong> Invalid percentage</div>');
			}else{
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
		parent.find('td .cancel-btn').addClass('delete-btn').removeClass('cancel-btn').html('<img src="img/delete.png" />');
		//tbGenComp routine
		if(source=='tbGenComp'){
			$('#error-terminal').hide().html('');
			var v = parent.find('.percentage input').attr('prev');
			parent.find('.percentage').html('<div>'+v+'</div>');										
		}
		else if(source=='tbMeasItem'){
			$('#nodata').html('');
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
		var ls_ccodes =" ";
		var ccs = $('#tbGenComp .classcode');
		$.each(ccs, function(i,r){
			var val = $(r).text();
			ls_ccodes+='<option value="'+val+'">'+val+'</option>';
		}); 
		$('#classcodes').html(ls_ccodes);
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

	$('#tbMeasItem tbody, #tbGenComp tbody').sortable({helper: fixHelper, update: handle_resort}).disableSelection();
	$('#tbMeasItem tbody, #tbGenComp tbody').sortable({disabled: false});
	//Save Record 
	$('.deptcode:radio').change(function(){
		var str_sub = $(this).attr('subj');
		var arr_sub = $.parseJSON(str_sub);
		var ls_sub='<option value="#" class="default">Select a subject</option>';
		/*
		$.each(arr_lvl,function(i, res){
			ls_lvl+='<input class="grd_yr" type="checkbox" name="grd_yr" value="'+res+'"/><span>'+res+'</span>';
		});
		*/
		//$('#grd_yr').html(ls_lvl);
		$('#grd_yr').html('Select a subject');
		if(arr_sub==null){
			$('#tmplt_subject').html('<option value="novalue" yrs="noyears">No subjects found</option>');
			$('#tmplt_subject').attr('disabled', 'disabled');
			$('#sy').attr('disabled', 'disabled');
			$('#tmplt_name').attr('disabled', 'disabled');
		}
		else{
			$.each(arr_sub,function(i, res){
				var years =[];
				var compcodes=[];
				$.each (res.subj_dtl, function(j, o){
						years.push(o.year);
						compcodes.push(o.compcode);
				});
				ls_sub+='<option value="'+res.nomen+'" yrs="'+years+'" ccodes="' + compcodes + '"  >'+res.nomen+'</option>';
			});
			$('#tmplt_subject').html(ls_sub);
			$('#tmplt_subject').attr('disabled', '');
			$('#sy').attr('disabled', '');
		}		
	});
	$('#tmplt_subject').change(function(){
		if($('.add_tmpl').hasClass('cancel_tmpl')){
			toggleButton($('.add_tmpl'),'cancel_tmpl');
		}
		$(this).find('.default').remove();
		var arr_lvl = $(this).find(':selected').attr('yrs').split(',');
		var arr_ccd = $(this).find(':selected').attr('ccodes').split(',');
		var subj_dtls = [];
		for(var index=0; index < arr_lvl.length;index+=1){
			var obj ={}
			obj.lvl = arr_lvl[index];
			obj.code = arr_ccd[index];
			subj_dtls[index] = obj;
		}
		
		subj_dtls.sort(function(a, b) {
				var compA = a.lvl;
				var compB = b.lvl;
				return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		});		
		var ls_lvl="";
		var dept = $('.deptcode:checked').val();
		$.each(subj_dtls,function(i, res){
			ls_lvl+='<input class="grd_yr" type="checkbox" name="grd_yr" value="'+res.lvl+'" ccode="'+res.code+'"/><span>'+dept+'-'+res.lvl+'</span>';
		});
		$('#grd_yr').html(ls_lvl);
		
	});
	$('.save-record').click(function(){
		if(check_tname($('#tmplt_name'))==false){
			return;
		}
		var template_id=0;
		$('#error-terminal').show().html('');
		//General Components routine
			var total = parseFloat($('#total').text());
			if(total!=100){
				$('#error-terminal').show().html('<div class="warning"><strong>Warning:</strong> Total not equal to 100%.');
			}else{
				//Save All
				var tmpl_name =$('#tmplt_name').val();
				var sy = $('#sy').find('option:selected').val();
				var deptcode=$('.deptcode:checked').val();
				var yr_lvl=[];
				var comp_code =[];
				var classcode=[];
				var rownumber=[];
				var percentage=[];
				//Collect class code
				//$('#classcodes').html('');
				var index=0;
				$.each($('#tbGenComp tr').find('.classcode'),function(i,result){
					var ccode  = result.innerHTML;
					classcode[index] = ccode ;
					//$('#classcodes').html($('#classcodes').html()+'<option values="'+ccode+'">'+ccode+'</option>');	
					index+=1;
				});
				//Collect row number
				index=0;
				$.each($('#tbGenComp tr').find('.counter'),function(i,result){
					rownumber[index] = parseInt(result.innerHTML);
					index+=1;
				});
				//Collect percentage
				index=0;
				$.each($('#tbGenComp tr').find('.percentage div'),function(i,result){
					percentage[index] = result.innerHTML;
					index+=1;
				});
				//Collect levels and compcode
				index=0;
				$('.grd_yr:checked').each(function(i,res){
					yr_lvl[index]= $(res).val();
					comp_code[index]=$(res).attr('ccode');
					index+=1;
				});
				if(yr_lvl.length==0){
					alert('Select grade/year level first');
				}
				//Collect comp code
				var today = new Date();
				var utcDate = today.toUTCString();
				//Save  record
					$.ajax({
						type: 'GET',
						url: 'ajax.php',
						data: {'yr_level': yr_lvl, 'deptcode':deptcode, 'comp_code' : comp_code, 'sy':sy, 'classcode': classcode, 'rownumber': rownumber, 'template_name':tmpl_name,
								'percentage':percentage, 'func': 'save_gc_tmplt', 'cache': utcDate},
						success: function(data) {
							var json_data = $.parseJSON(data);
							template_id = json_data.tmp_id;
							$('#save-record').attr('disabled', 'disabled');
							$('#tbMeasItem').show();
							$('#nodata').html('');
							
							var classcode = $('#load').find('option:selected').val();
							var sy = $('#sy').find('option:selected').val();
							var period = $('#period').find('option:selected').val();
							//load_routine(classcode, sy, period);
							
							//Measurable items routine
							
								var classcodes = $('#tbMeasItem').find('td.classcode');
								var isComplete;
								var trace ="";
								var comp = $('#classcodes').find('option');
								for(var i=0; i<comp.length;i++){
									isComplete=false;
									for(var j=0;j<classcodes.length;j++){
										if(classcodes[j].innerHTML==$(comp[i]).val()){
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
									$('#nodata').show().html('<div class="warning"><strong>Warning:</strong> Could not save. Incomplete components.');
									alert('Incomplete Measurable');
									var tname =$('#tmplt_name').val();
									//Clean template heading
									$.ajax({
										type: 'POST',
										url: 'ajax3.php?func=cleantmphdg',
										data:{'template_name':tname},
										success: function(data) {
										}
									});
								}else if(isComplete) {
									var sy = $('#sy').find('option:selected').val();
									var tmpl_name =$('#tmplt_name').val();
									var comp_code = $('#tmplt_subject').find('option:selected').val();
									var deptcode=$('.deptcode:radio').val();
									var yr_lvl =[];
									var classcode=[];
									var colnumber=[];
									var header=[];
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
										colnumber[index] = parseInt(result.innerHTML);
										index+=1;
									});
									//Collect header
									var index=0;
									$.each($('#tbMeasItem tr').find('.item-header div'),function(i,result){
										header[index] = result.innerHTML;
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
										data: {'tmplt_id': template_id, 'comp_code' : comp_code,'sy':sy, 'colnumber': colnumber, 'classcode':classcode,
												'header':header, 'base':base, 'func':'save_ms_tmplt'},
										success: function(data) {
											alert('Record saved');
											top.location.href ="template.php"
										}
									});

								}
						}
					});	
			}
	});
	function load_sysdef(o){
		$.ajax({
			type: 'GET',
			url: 'ajax.php',
			data: {func:'getSysDefa', token:o.token},
			success: function(data) {
				var json_data = $.parseJSON(data);
				var sy = json_data.active_sy;
				var base =json_data.base;
				ztb_w = json_data.ztb_w;
				ztb_h = json_data.ztb_h;
				sys_timeout = json_data.timeout;
				var str = sy + ' - '+ (sy+1);
				var component_str="";
				$.each(json_data.component, function(i,res){
					component_str +='<option value="'+res.code+'">'+res.desc+'</option>';
				});
				$('#sy').html('<option value="'+sy+'">'+str+'</option> <option value="'+(sy+1)+'">2012 - 2013</option>');
				$('#description').html('<option value="###" class="default">Select Description</option>'+component_str);
				$('#base').attr('def',base);
				$('#base').val(base);
			}
		});
		$('#content').animate({opacity:'1'},1000);
		$('#LEFT-CONTENT').remove();
		$('.tab-header').prepend('<span class="indicator">+</span>');
	}
	$('#sy').change(function(){
		var sy =$(this).val();
		var subjects = $('#load').find('option');
		//	correct_list(subjects, sy);
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
	
	function check_tname (obj){
		var tname  = $(obj).val();
		if(tname==''){
			alert('Template name required!');
			return false;
		}
		$.ajax({
			type: 'GET',
			url: 'ajax3.php',
			data: {'func':'checktname', 'tname':tname},
			success: function(data) {
				var json_data  = $.parseJSON(data);
				if(json_data.count>0){
					alert('Oops template name already taken!');
					return false;
				}else{
					return true;
				}
			}
			
		});
	}
	$('.update-record').click(function(){
		var tid = $(this).attr('tid');
		$('#error-terminal').show().html('');
		//General Components routine
			var total = parseFloat($('#total').text());
			if(total!=100){
				$('#error-terminal').show().html('<div class="warning"><strong>Warning:</strong> Total not equal to 100%.');
			}else{
				//Save All
				var tmpl_name =$('#tmplt_name').val();
				var sy = $('#sy').find('option:selected').val();
				var deptcode=$('.deptcode:checked').val();
				var yr_lvl=[];
				var comp_code =[];
				var classcode=[];
				var rownumber=[];
				var percentage=[];
				//Collect class code
				//$('#classcodes').html('');
				var index=0;
				$.each($('#tbGenComp tr').find('.classcode'),function(i,result){
					var ccode  = result.innerHTML;
					classcode[index] = ccode ;
					//$('#classcodes').html($('#classcodes').html()+'<option values="'+ccode+'">'+ccode+'</option>');	
					index+=1;
				});
				//Collect row number
				index=0;
				$.each($('#tbGenComp tr').find('.counter'),function(i,result){
					rownumber[index] = parseInt(result.innerHTML);
					index+=1;
				});
				//Collect percentage
				index=0;
				$.each($('#tbGenComp tr').find('.percentage div'),function(i,result){
					percentage[index] = result.innerHTML;
					index+=1;
				});
				//Collect levels and compcode
				index=0;
				$('.grd_yr').each(function(i,res){
					yr_lvl[index]= $(res).val();
					comp_code[index]=$(res).attr('ccode');
					index+=1;
				});
				//Collect comp code
				var today = new Date();
				var utcDate = today.toUTCString();
				//Save  record
					$.ajax({
						type: 'GET',
						url: 'ajax3.php',
						data: {'template_id':tid, 'yr_level': yr_lvl, 'deptcode':deptcode, 'comp_code' : comp_code, 'sy':sy, 'classcode': classcode, 'rownumber': rownumber, 'template_name':tmpl_name,
								'percentage':percentage, 'func': 'update_gc_tmplt', 'cache': utcDate},
						success: function(data) {
							var json_data = $.parseJSON(data);
							template_id = json_data.tmp_id;
							$('#save-record').attr('disabled', 'disabled');
							$('#tbMeasItem').show();
							$('#nodata').html('');
							
							var classcode = $('#load').find('option:selected').val();
							var sy = $('#sy').find('option:selected').val();
							var period = $('#period').find('option:selected').val();
							//load_routine(classcode, sy, period);
							
							//Measurable items routine
							
								var classcodes = $('#tbMeasItem').find('td.classcode');
								var isComplete;
								var trace ="";
								var comp = $('#classcodes').find('option');
								for(var i=0; i<comp.length;i++){
									isComplete=false;
									for(var j=0;j<classcodes.length;j++){
										if(classcodes[j].innerHTML==$(comp[i]).val()){
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
									$('#nodata').show().html('<div class="warning"><strong>Warning:</strong> Could not save. Incomplete components.');
									alert('Incomplete Measurable');
								}else if(isComplete) {
									var sy = $('#sy').find('option:selected').val();
									var tmpl_name =$('#tmplt_name').val();
									var comp_code = $('#tmplt_subject').find('option:selected').val();
									var deptcode=$('.deptcode:radio').val();
									var yr_lvl =[];
									var classcode=[];
									var colnumber=[];
									var header=[];
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
										colnumber[index] = parseInt(result.innerHTML);
										index+=1;
									});
									//Collect header
									var index=0;
									$.each($('#tbMeasItem tr').find('.item-header div'),function(i,result){
										header[index] = result.innerHTML;
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
										url: 'ajax3.php',
										data: {'tmplt_id': template_id, 'comp_code' : comp_code,'sy':sy, 'colnumber': colnumber, 'classcode':classcode,
												'header':header, 'base':base, 'func':'update_ms_tmplt'},
										success: function(data) {
											alert('Record saved');
											top.location.href ="template.php"
										}
									});

								}
						}
					});	
			}
	});
	
	var TEMPLATES;
	$('.add_tmpl').livequery(function(e){
		var source = this;
		$(this).button().click(function(e){
			toggleButton(source,"cancel_tmpl");
			$('#tab_tmpl8_vw .tab-content').slideUp();
		})
	});	
	function toggleButton(source,tclass){
		$(source).toggleClass(tclass);
		if($(source).hasClass('cancel_tmpl')){
				$('#tmplt_name').val('').attr('disabled', '').focus();
				$('.save-record').parent().css({'display':'block'});
				$('.update-record').parent().css({'display':'none'});
				$('#tbGenComp tbody, #tbMeasItem tbody, #classcodes').html('');
				$('#col-counter, #row-counter').text('1');
				$('#input-source, #data-source').css({'opacity':'1'});
				$('#tbTemplates tbody').html('');
				computeSum();
				$('#tab_tmpl8_entry .tab-content').slideDown();
				$(source).attr('title', 'Cancel action');
				$(source).html('<img src="icons/cancel.png" />');
			}else{
				$('#tmplt_name').val('').attr('disabled', 'disabled');
				$('.save-record').parent().css({'display':'none'});
				$('.update-record').parent().css({'display':'none'});
				$('#tbGenComp tbody, #tbMeasItem tbody, #classcodes').html('');
				$('#col-counter, #row-counter').text('1');
				$('#input-source, #data-source').css({'opacity':'0'});
				$('#tbTemplates tbody').html('');
				computeSum();
				$('#tab_tmpl8_entry .tab-content').slideUp();
				$(source).attr('title', 'Add new template');
				$(source).html('<img src="icons/add.png" />');
			}
	}
	$('#view_tmpl').button().click(function(){
		$('#tmplt_name').val('').attr('disabled','disabled');
		var grd_yrs = $('.grd_yr:checked');
		if(grd_yrs.length==0){
			alert('Select a grade/year first to view.');
		}else{
			reloadTemplates();
		}
	});
	function reloadTemplates(){
		var deptcode = $('.deptcode:checked').val();
		var sy =  $('#sy').find('option:selected').val();
		var arr_gy = [];
		var arr_cc =[];
		$('#tbGenComp tbody, #tbMeasItem tbody, #classcodes').html('');
			$('#col-counter, #row-counter').text('1');
			$('#input-source, #data-source').css({'opacity':'0'});
			computeSum();
			$('.save-record').parent().css({'display':'none'});
			arr_cc =$('#tmplt_subject option:selected').attr('ccodes').split(',');
			arr_gy = $('#tmplt_subject option:selected').attr('yrs').split(',');
			var row=''
			$('#tbTemplates tbody').html(row);
			TEMPLATES=[]; // Reset templates array
			$.ajax({
				type: 'POST',
				url: 'ajax3.php?func=checktmpl',
				data:{'comp_code':arr_cc, 'grd_yrs':arr_gy, 'dept_code':deptcode, 'sy':sy},
				success: function(data) {
					var tmpl8s = $.parseJSON(data);
					if(tmpl8s.length==0){
						alert('No templates found!');
						return;
					}
				
					$.each(tmpl8s, function(i,r){
						var level_str ='';
						var components  = r.components;
						var measurables = r.measurables;
						var template = {id:r.id, comp:components, meas:measurables};
						TEMPLATES.push(template);
						$.each(r.levels, function(j,s){
							level_str+=r.deptcode+'-'+s+'/';
						});
							row += '<tr tid="'+r.id+'" >';
							row+='<td class="name">'+r.name+'</td>';
							row+='<td class="author">'+r.author+'</td>';
							row+='<td>'+level_str+'</td>';
							row+='<td>'+r.status+'</td>';
							if(r.status=='A'){
								row+='<td>'
								row+='<a class="view_rec" title="View this template"><img src="icons/eye.png"></a>';
								if(r.allow){
									row+='<a class="delt_rec" title="Delete this template"><img src="icons/cut_red.png"></a>';
								}
								row+='</td>';
							}else{
								row+='<td>';
								if(r.allow){
								row+='<a class="edit_rec" title="Edit this template"><img src="icons/pencil.png"></a>';
								row+='<a class="post_rec" title="Post this template"><img src="icons/disk.png"></a>';
								row+='<a class="delt_rec" title="Delete this template"><img src="icons/cut_red.png"></a>';	
								}else{
									row+='<a class="view_rec" title="View this template"><img src="icons/eye.png"></a>';
								}
								row+='</td>';
							}
					});
					$('#tbTemplates tbody').html(row);	
					$('#tab_tmpl8_entry .tab-content').slideUp();
					$('#tab_tmpl8_vw .tab-content').slideDown();
				}
			});
	}
	$('.view_rec, .edit_rec').livequery(function(e){
		var source = this;
		$(source).button().click(function(e){
			
			var tmp_name = $(source).parent().parent().find('.name');
			var tid = $(tmp_name).parent().attr('tid');
			$('.update-record').attr('tid',tid);
			$('#tmplt_name').val($(tmp_name).text());
			$('#error-terminal').html('');
			$('#tbGenComp tbody').html('');
			if($(this).find('.default')){
				$(this).find('.default').remove();
			}
			var ccode = $(this).find('option:selected').attr('ccode');
			var id = $(this).find('option:selected').val();
			var row="";
			var rwctr=0;
			var tid = $(this).parent().parent().attr('tid');
			$('#classcodes').html('');
			$('#tab_tmpl8_entry .tab-content').slideDown();
			$.each(TEMPLATES, function (i,r){
				var id = r.id;
				if(id==tid){
					var tcomponents = components = r.comp;
					var tmeasurables = r.meas;
					$('#row-counter').text('1');
					computeSum();
					build_meas(tmeasurables, source);
					build_comp(tcomponents, source);
					if($(source).hasClass('view_rec')){
						$('#input-source, #data-source').css({'opacity':'0'});
						$('.update-record').parent().css({'display':'none'});
						}
					else if($(source).hasClass('edit_rec')){
						$('#input-source, #data-source').css({'opacity':'1'});
						$('.update-record').parent().css({'display':'block'});
						}
					}
				});
				
			});
		}
	);
	
	
	$('.delt_rec').livequery(function(e){
		var source = this;
		$(this).button().click(function(e){
			var tid = $(source).parent().parent().attr('tid');
			var tname = $(source).parent().parent().find('.name').text();
			$.ajax({
				type: 'POST',
				url: 'ajax3.php?func=deltmpl8',
				data:{'template_id':tid, 'template_name':tname},
				success: function(data) {
					alert('Template has been deleted');
					reloadTemplates();
				}
			});
		});
	});
	
	$('.post_rec').livequery(function(e){
		var source = this;
		$(this).button().click(function(e){
			var tid = $(source).parent().parent().attr('tid');
			$.ajax({
				type: 'POST',
				url: 'ajax3.php?func=posttmpl8',
				data:{'tid':tid},
				success: function(data) {
					alert('Template has been posted');
					reloadTemplates();
				}
			});
		});
	});
	
	function build_comp(comp_obj, source){
		$('#row-counter').text('1');
		var row="";
		$.each(comp_obj, function(i,result){
			rwctr = result.rownum;
			var ccode =result.ccode;
			var descp = result.desc;
			var prcnt = result.perc;					   
			row += '<tr row-ctr='+rwctr+' class="component">';
			row += '<td class="mini counter">'+rwctr+'</td>';
			row += '<td class="small classcode">'+ccode+'</td>';
			row	+= '<td class="large  type">'+descp+'</td>';
			row	+= '<td class="mini percentage"><div>'+prcnt+'</div></td>';
			if($(source).hasClass('edit_rec')){
				row += '<td class="mini noborder"><a href="javascript:void();" class="edit-btn" inside="tbGenComp"><img src="img/edit.jpg"/></a></td>';
				row += '<td class="mini noborder"><a href="javascript:void();" class="delete-btn" inside="tbGenComp"><img src="img/delete.png"/></a></td>';
			}
			row +='</tr>';
			var ls_ccodes = $('#classcodes').html();
			$('#classcodes').html(ls_ccodes +'<option value="'+ccode+'">'+ccode+'</option>');
		});
		
		$('#tbGenComp').find('.component').fadeOut('slow').remove();
		$('#tbGenComp tbody').append(row);
		rwctr = parseFloat(rwctr)+1;
		$('#row-counter').text(rwctr);
		$('#percentage').val('');
		computeSum();
		$('#tbMeasItem').fadeIn();
	}
	function build_meas(meas_obj, source){
		$('#col-counter').text('1');
		$('#tbMeasItem').fadeIn();
		$('#nodata').html('');
		$('#tbMeasItem tbody').html('');
		if(meas_obj!=null){
			$.each(meas_obj,function(i,result){
				var clctr = result.colnum;
				var ccode =result.ccode;
				var hdr = result.hdr;
				//var dsc = result.dsc==undefined?'Place description here.':result.dsc;
				//var itm =result.itm==undefined?'100':result.itm;
				var bse =result.base;
				var col = '<tr col-ctr='+clctr+' class="measurable">';
					col += '<td class="mini counter">'+clctr+'</td>';
					col += '<td class="small classcode">'+ccode+'</td>';
					col	+= '<td class="mini item-header"><div>'+hdr+'</div></td>';
					//col	+= '<td class="mini type"><div>'+dsc+'</div></td>';
					//col	+= '<td class="mini num-items"><div>'+itm+'</div></td>';
					col	+= '<td class="mini const-base"><div>'+bse+'</div></td>';
					if($(source).hasClass('edit_rec')){
						col += '<td class="micro noborder"><a href="javascript:void();" class="edit-btn" inside="tbMeasItem"><img src="img/edit.jpg"/></a></td>';
						col += '<td class="micro noborder"><a href="javascript:void();" class="delete-btn" inside="tbMeasItem"><img src="img/delete.png"/></a></td>';
					}
					col +='</tr>';
				$('#tbMeasItem tbody').append(col);
				clctr = parseFloat(clctr)+1;
				$('#col-counter').text(clctr);				
			})
		}
	}
});
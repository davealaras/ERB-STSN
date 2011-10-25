// JavaScript Document
$(document).ready(function(){
						   
	//Update Educ Level
	$('#educlvl').change(function(){
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
	});
	//Update Gr/Yr Level	
	$('#gryrlvl').change(function(){
		var lvl = $('#gryrlvl').find('option:selected').val();
		var dep = $('#educlvl').find('option:selected').val();
		var data = $.parseJSON($('#section').parent().attr('data'));
		var opt_str=" ";
		$.each(data,function(i,e){
			if(e.level==lvl && e.deptcode==dep){
				opt_str+='<option value="'+e.seccode+'">'+e.section+'</option>';
			}
		});
		$('#section').html(opt_str);
		if($(this).find('.default')){
			$(this).find('.default').remove();
		}
	});	
	$('.link_list').livequery("change",function(){
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
		var opt_str='<option value="#" class="default">Select a level</option>';
		var munis= $.parseJSON($(target).parent().attr('data'));
		$.each(munis,function(i,e){
			if(e.fk==id){
				opt_str+='<option value="'+e.id+'">'+e.c+'</option>';
			}
		});
		$(target).html(opt_str);
	}
	//Autocomplete adviser
	$('#adviser').autocomplete({
			source:$.parseJSON($('#teachers').attr('data')),
			focus: function (event, ui) {
				  $(event.target).val(ui.item.label);
				  return false;
			},
			select: function(event, ui) {
					$(event.target).val(ui.item.label);
					$(event.target).attr('fid',ui.item.value);
					console.log($(event.target));
					return false;
			}						   
	});
	$('#add_fload').button().click(function (e){		
		var educlvl =$('#educlvl').find('option:selected').val();
		var gryrlvl =$('#gryrlvl').find('option:selected').val();
		var section = $('#section').find('option:selected').text();
		var seccode = $('#section').find('option:selected').val();
		var fid = $('#faculty_id').val();
		var sy = $('#sy').find('option:selected').val();
		var allow;
		allow=true;
		$.ajax({
				type: 'POST',
				url: 'ajax3.php',
				data: {'seccode':seccode, 'faculty_id':fid, 'sy':sy, 'func': 'checkadvisory'},
				success: function(data) {
					var json_data = $.parseJSON(data);
					if(json_data.available.count==0){
						var exist_loads = $('.existloads');	
						$(exist_loads).removeClass('warning');
						$.each(exist_loads, function(i,r){
							var s = $(r).attr('seccode');
							if(s==seccode){
								allow =false;
								$(r).addClass('warning');
							}
						});
						var new_loads = $('.newloads');
						$(new_loads).removeClass('warning');
						$.each(new_loads, function(i,r){
							var s = $(r).attr('seccode');
							if(s==seccode){
								allow =false;
								$(r).addClass('warning');
							}
						});
						if(allow){
							var remove ='<div class="remove_fload"><img src="img/delete.png"/></div>';
							var load =  '<tr class="newloads" seccode="'+seccode+'"  >';
								load += '<td>'+gryrlvl+'-'+educlvl+'</td>';
								load += '<td>'+section+'</td>';
								load += '<td>'+remove+'</td>';
							$('#new_fload tbody').append(load);
							$('#subject').val('');
						}else{
							alert('Duplicate section found!');
						}
					}else{
						alert('Section already loaded!');
					}
				}
			});
	});
	$('#cancel_fload').button().click(function (e){
	});
	$('#save_fload').button().click(function (e){
		var new_loads = $('.newloads');
		var seccodes =[];
		var fid = $('#faculty_id').val();
		var sy = $('#sy').find('option:selected').val();
		$.each(new_loads, function(i,r){
			var seccode = $(r).attr('seccode');
			seccodes.push(seccode);
		});
		if(seccodes.length>0){
		$.ajax({
				type: 'POST',
				url: 'ajax3.php',
				data: {'seccodes':seccodes, 'faculty_id':fid, 'sy':sy, 'func': 'addadvisory'},
				success: function(data) {
						var json_data = $.parseJSON(data);
						if(json_data.err_ctr!=0){
							alert(json_data.err_ctr +' subject(s) not saved. Duplicate load found during saving. Try again.');
						}else{
							alert('Subject loads saved');
							top.location.href="assignsection.php";
						}
				}
			});
		}else{
			alert('Oops! You do not have loads to be saved.')
		}
	});
	
	$('.remove_fload').livequery('click', function(e){
		var src = $(this);
		var row = $(src).parent().parent();
		$(row).fadeOut().remove();
		});
	 $('.close_fload').click(function(){
        $(this).parent().parent().slideUp();
    });
	$("#floads").tablesorter(); 
});

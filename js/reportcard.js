// JavaScript Document
$(document).ready(function(){
	var SUMMARY_ROUND =2;
var EQUIVALENT_ROUND=0;
var OVERALL_ROUND=0;
var FINALAVE_ROUND =2;
var FINALRATE_ROUND = 2;
var MCGS ='Master Consolidated Grading Sheet';
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
		var all_sections = '';
		$.each(data,function(i,e){
			if(e.level==lvl && e.deptcode==dep){
				opt_str+='<option value="'+e.seccode+'-">'+e.section+'</option>';
				all_sections+='-'+e.seccode;
			}
		});
		opt_str+='<option value="'+all_sections+'-">ALL</option>';
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
	$('.load_btn').click(function(){
		var sy = $('#sy').find('option:selected').val();
		var period = $('#period').find('option:selected').val();
		var mode =$(this).attr('mode');
		var frm = $(this).attr('frm');
		var classcode;
		var sno;
		$('#prnt_sno').attr('value',0); 
		$('#prnt_classcode').attr('value',0); 
		if(mode=='individual'){
			sno = $('#studentname').attr('fid');
		}else if(mode=='batch'){
			classcode= $('#section').find('option:selected').val();
		}
		$('#prnt_sy').attr('value',sy); 
		$('#prnt_period').attr('value',period); 
		$('#prnt_sno').attr('value',sno); 
		$('#prnt_classcode').attr('value',classcode); 
		$('#prnt_mode').attr('value', mode);
		$('#'+frm).submit();
		$('#studentname').val('');
	});
	
	$('.mode').click(function(){
		var mode = $(this).val();
		$('.individual, .batch').hide();
		$('.'+mode).show();
		$('.load_btn').attr('mode', mode);
	});
		//.tab Animation
	$('.tab-header').click(function(){
		var src = $(this);
		var dis = $(src).find('.indicator').text();
		$(src).find('.indicator').text(dis=='+'?'-':'+');
		$(this).parent().find('.tab-content').slideToggle(500);		
		}
	 );
	  $('#studentname').autocomplete({
			focus: function (event, ui) {
				  $(event.target).val(ui.item.label);
				  return false;
			},
			select: function(event, ui) {
					$(event.target).val(ui.item.label);
					$(event.target).attr('fid',ui.item.value);
					return false;
			}						   
	}).livequery("keyup",function(){
		var key = $(this).val();
			$.ajax({
				type: 'GET',
				url: 'ajax.php',
				data: {'search_string':key, 'func': 'searchstud'},
				success: function(data) {
						var json_data = $.parseJSON(data);
						var search_results = [];
						console.log(json_data.result);
						$.each(json_data.result, function(i,result){
							var obj = { 'label' : result.full_name, 'value' :result.sno};
							search_results.push(obj);
						});
						console.log(search_results);
						 $('#studentname').autocomplete("option", "source", search_results);
				}
			});
	 });
	
	
});

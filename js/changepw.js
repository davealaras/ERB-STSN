$(document).ready(function(){		
	
				// Custom validator (checks if password == confirm password)
				function doesPasswordFieldsMatch(values){
					if(values.password == values.verification)
						return {valid:true}
					else
						return {valid:false, message:'Password does not match'}
				}
				$('#inputPassword').valid8('Required');
					$('#inputConfirm').valid8({
						'jsFunctions': [
							{ function: doesPasswordFieldsMatch, values: function(){
									return { password: $('#inputPassword').val(), verification: $('#inputConfirm').val() }
								}
							}
						]
					});
				$('#inputCurrPassword').valid8();
				$('.change_pw').click(function(){
			
	
						var valid= true;
				var user_id=$('#faculty_id').val();
				var curr_pass=$('#inputCurrPassword').val();
				var new_pass = $('#inputConfirm').val();
				$.ajax({
						type: 'GET',
						url: 'ajax4.php',
						data: {func:'checkpass', uid: user_id, password: curr_pass, newpassword: new_pass},
						success: function(data) {
							var json_data =  $.parseJSON(data);
							alert(json_data.msg);
							if(json_data.ok){
								top.location.href="logout.php";
							}else{
							
							}
						}
				});
				
				
				});
});
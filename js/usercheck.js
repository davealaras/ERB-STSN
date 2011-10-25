$(document).ready(function(){				
				
				// Set focus to first input
				$('#inputUsername').focus();
				
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
			
				$('#inputUsername').valid8();
				$( '#inputLastName').valid8({
					'regularExpressions': [
					{ expression: /^.+$/, errormessage: 'Required'}
					]
				});
				$('#inputFirstName').valid8({
					'regularExpressions': [
					{ expression: /^.+$/, errormessage: 'Required'}
					]
				});
				$('#inputUsername').valid8({
					'regularExpressions': [
						{expression: /^.+$/, errormessage: 'Required'}
					],
					'ajaxRequests': [
						{ url: 'check.php', loadingmessage: 'Checking availability...', errormessage:'Username is unavailable'}
					]
				});
				// Check if all input fields are valid
				$('#buttonRegister').click(function(){
				
					var allow =false;
				var radios = $('.req');
				$.each(radios, function(i,r){
					console.log(r);
					if($(r).attr('checked')){
						allow =true;
					}
				});
				if($('#inputUsername, #inputFirstName, #inputLastName, #inputConfirm, #inputPassword').isValid() && allow){
						var username = $('#inputUsername').val();
						var firstname = $('#inputFirstName').val();
						var middlename = $('#inputMiddleName').val();
						var lastname = $('#inputLastName').val();
						var password =$('#inputPassword').val();
						var iscoor = $('#isCoor:checked').val();
						$.ajax({
							type: 'GET',
							url: 'ajax.php',
							data: {'username':username, 'password':password, 'lastname':lastname, 'firstname':firstname, 'middlename':middlename, 'iscoor':iscoor, 'func':'register'},
							success: function(data) {
								alert ('Thank you! You are now registered.');
								console.log(data);
								//top.location.href='index.php';								
							}
						});
					}else{
						alert('Pleas fill-up all required fields!');
					}
					
				});
				//Reset form
				$('#buttonReset').click(function(){
					//$('#reg-form').reset();
					//RESET CODE HERE
				});
			});	
		
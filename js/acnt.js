$(document).ready(function(){
	$('.btn_acnt').button();
	
	$('.reset').click(function(){
		var id = $(this).attr('fid');
		$.ajax({
				type: 'POST',
				url: 'ajax2.php?func=resetpw',
				data: {'id':id},
				success: function(data) {
					alert("Password has been reset!");
				}
			});
	});
});
<?php
			if(!isset($_SESSION['faculty_id'])){
		?>
<div id="login" class="column right" style="display:none;">
	<div id="message" class="column right">
		<div id="login-form"> 
		
			<form class="right" action="javascript:void();">
				<div style="margin-botom:4px;height:28px">
				<div id="response" > </div>
				</div>
				<div >
					Username:<input type="text" id="user_name" />
					Password: <input type="password" id="password" />
					<input type="checkbox" name="remember" id="remember" value="1" /> Remember me
				</div>
				<div class="right">
					 <span class="art-button-wrapper">
					<span class="l"> </span>
					<span class="r"> </span>
					<a class="art-button" href="javascript:void(0)" id="login-btn">Log-in</a>
				  </span>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
	}
?>
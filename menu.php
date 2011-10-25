<?php	
	include('header.php');
	include('access_controller.php');
	$EGB->db_connect();				//Open database connection for EGB
	$ACL->db_connect();				//Open database connection for ACL
	if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
			$_SESSION['username']=isset($_COOKIE['username'])?$_COOKIE['username']:' ';
			$_SESSION['password']=isset($_COOKIE['password'])?$_COOKIE['password']:' ';
			$_SESSION['faculty_id'] = $EGB->login($_SESSION['username'],$_SESSION['password']);
			$_SESSION['session_token'] = md5($_SESSION['faculty_id']+time());
	}
	$fid= $_SESSION['faculty_id'];
	$access = $ACL->get_access($fid);
	$level0 = $level1 = $level2 = $level3 =array();
	foreach($access as $a){
		switch($a['level']){
			case 0 :
				
				array_push($level0, $a);
				break;
			case 1 :
				array_push($level1, $a);
				break;
			case 2 :
				array_push($level2, $a);
				break;
			case 3 :
				array_push($level3, $a);
				break;
		}
	}
?>
<?php  $allowed_url = array(); ?>
<ul class="art-menu">
	<?php	foreach($ACL->get_level(0) as $toplevel){ ?>
		<?php 
			foreach ($level0 as $allowed0){ 
				if($allowed0['id']==$toplevel['id']){
						array_push($allowed_url,  $toplevel['url']);
		?>
		<li>
		<a href="<?php echo $toplevel['url']; ?>"><span class="l"></span><span class="r"></span><span class="t"><?php echo $toplevel['title']; ?></span></a>
		<?php
			$sublevels = $ACL->get_level(1,$toplevel['id']);
			if($sublevels!=null){ ?>
				<ul>
					<?php foreach($sublevels as $sublevel){ ?>
						<?php 
							foreach ($level1 as $allowed1){ 
								if($allowed1['id']==$sublevel['id']){
									array_push($allowed_url,  $sublevel['url']);
						?>
						<li>
						<a href="<?php echo $sublevel['url']; ?>"><?php echo $sublevel['title']; ?></a>
						<?php	
						$lowlevels = $ACL->get_level(2,$sublevel['id']);
						if($lowlevels!=null){ ?>
						<ul>
							<?php foreach($lowlevels as $lowlevel){ ?>
								<?php 
									foreach ($level2 as $allowed2){ 
										if($allowed2['id']==$lowlevel['id']){
										array_push($allowed_url,  $lowlevel['url']);
								?>
							<li>
							<a href="<?php echo $lowlevel['url']; ?>"><?php echo $lowlevel['title']; ?></a>
								<?php	
								$lowestlevels = $ACL->get_level(3,$lowlevel['id']); ?>
								<?php if($lowestlevels!=null){	?>
								<ul>
									<?php foreach($lowestlevels as $lowestlevel){ ?>
										<?php 
											foreach ($level3 as $allowed3){ 
												if($allowed3['id']==$lowestlevel['id']){
												array_push($allowed_url,  $lowestlevel['url']);
										?>
									<li>
									<a href="<?php echo $lowestlevel['url']; ?>" ><?php echo $lowestlevel['title']; ?></a>
									</li>
									<?php
													}
											}
									?>
								<?php }?>
								</ul>
								<?php } ?>
							</li>
								<?php
											}
										}
								?>
							<?php 	} ?>
						</ul>
						<?php } ?>
						</li>
						<?php
								} 
							}
						?>
					<?php } ?>
				</ul>
			<?php } ?>
		</li>
		<?php 	}
			} ?>
	<?php } ?>
		<?php
			if(isset($_SESSION['faculty_id'])){
		?>
		<li>
			<a href="logout.php"><span class="l"></span><span class="r"></span><span class="t">Logout</span></a>
		</li>
		<?php
			}
		?>
</ul>
<?php 
$page_name = substr($_SERVER['PHP_SELF'],10);
$redirect_url = "index.php";
$logout_url = "logout.php";
$register_url = "register.php";
if(!in_array($page_name,$allowed_url)){
	
 echo("<script> top.location.href='" . $redirect_url . "'</script>");
}

?>
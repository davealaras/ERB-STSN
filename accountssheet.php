 <table border="0">
  <tr valign="top">
	   <td><img src="img/RegisterIcon.jpg" /></td>
	<td><h1>Accounts Panel</h1></td>
  </tr>
</table>
<table  class="datagrid">
<tr>
	<td class="head small">Username</td>
	<td class="head xlarge">Name</td>
	<td class="head large"colspan="2">Password</td>
</tr>
<?php
	foreach($ACL->get_all_users() as $user){
		if($user['id']!=0 && $user['id']!=3){
		?>
		<tr>
<?php
		$faculty = $EGB->get_users($user['id']);
		$first_name = $faculty['first_name'];
		$last_name = $faculty['last_name'];
		$middle_name = $faculty['middle_name'];
		$full_name  = $last_name.', '.$first_name. ' '. $middle_name;
?>
		<td><?php echo $user['username'] ?></td>
		<td><?php echo $full_name; ?></td>
		<td><a class="btn_acnt reset" fid="<?php echo $faculty['faculty_id']; ?>">RESET</a></td>
		</tr>
		
<?php
		}
	}
?>
</table>
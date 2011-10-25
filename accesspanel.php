 <table border="0">
  <tr valign="top">
	   <td><img src="img/RegisterIcon.jpg" /></td>
	<td><h1>Access Panel</h1></td>
  </tr>
</table>
	<br><br>
	<form id="acl">
	<table>
		<tr>
			<td>User:</td>
			<td><input type="hidden" class="xlarge" id="teachers" data='<?php echo json_encode($EGB->list_get('faculties'));?>' />
					<input  type="text" class="large" id="username"/></td>
			<td><div id="edit_access">Edit</div></td>
			<td><div id="cancel_access">Cancel</div></td>
		</tr>
		<tr>
			<td colspan="2"><input type="radio" name="rights" value="1" class="rights" disabled="disabled" id="access_1"/>User Class</td>
			<td colspan="2" style="display:none"><input type="radio" name="rights" value="2" class="rights" disabled="disabled" id="access_2"/> Customize</td>
		</tr>
		<tr>
			<td colspan="4" data='<?php echo json_encode($ACL->get_acl());?>' >
				<select id="user_classes" class="large" name="role">
					<option value="#" class="default">Select User class</option>
					<?php foreach($ACL->get_roles() as $role): ?>	
						<option value='<?php  echo $role['role'];?>' id="role_<?php echo $role['role'];?>"><?php  echo $role['name'];?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
	
	
	<table class="datagrid">
	<tr> 
		<td class="head">Pages</td>
		<td><div id="user_class">{USER CLASS}</div></td>
	</tr>
	<?php	foreach($ACL->get_level(0) as $toplevel): ?>
	<tr toplevel="<?php echo $toplevel['id'];?>" >
		<td><strong><?php echo $toplevel['title']; ?></strong></td>
		<td><input type="checkbox" class="checkbox" name="level_0[]" value="<?php echo $toplevel['id'];?>"  levelid="0" itemid="<?php echo $toplevel['id'];?>"/></td>
	</tr>
	
	<?php	
		$sublevels = $ACL->get_level(1,$toplevel['id']);
		if($sublevels!=null){
			foreach($sublevels as $sublevel): 
	?>
		<tr toplevel="<?php echo $toplevel['id'];?>"  sublevel="<?php echo $sublevel['id'];?>">
		<td>&nbsp;&nbsp;&nbsp;<?php echo $sublevel['title']; ?></td>
		<td><input type="checkbox" class="checkbox" name="level_1[]" value="<?php echo $sublevel['id'];?>"  levelid ="1"  itemid="<?php echo $sublevel['id'];?>"/></td>
		</tr>
				<?php	
					$lowlevels = $ACL->get_level(2,$sublevel['id']);
					if($lowlevels!=null){
						foreach($lowlevels as $lowlevel): 
				?>
					<tr toplevel="<?php echo $toplevel['id'];?>"  sublevel="<?php echo $sublevel['id'];?>" lowlevel="<?php echo $lowlevel['id'];?>">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lowlevel['title']; ?></td>
					<td><input type="checkbox" class="checkbox" name="level_2[]" value="<?php echo $lowlevel['id'];?>" levelid ="2"  itemid="<?php echo $lowlevel['id'];?>"/></td>
					</tr>
					<?php	
						$lowestlevels = $ACL->get_level(3,$lowlevel['id']);
						if($lowestlevels!=null){
							foreach($lowestlevels as $lowestlevel): 
					?>
						<tr toplevel="<?php echo $toplevel['id'];?>"  sublevel="<?php echo $sublevel['id'];?>" lowlevel="<?php echo $lowlevel['id'];?>" lowestlevel="<?php echo $lowestlevel['id'];?>">
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lowestlevel['title']; ?></td>
						<td><input type="checkbox" class="checkbox" name="level_3[]" value="<?php echo $lowestlevel['id']; ?>" levelid="3" itemid="<?php echo $lowestlevel['id']; ?>" /></td>
						</tr>
						<?php 	endforeach; 
						
						}?>
				<?php 	endforeach; 
				
				}?>
	<?php 	endforeach; 
	
	}?>
	
	<?php endforeach; ?>
	</table>
	</form>
	<div id="save_acces">
	Save
	</div>
<?php 
	$EGB->db_close();
	$ACL->db_close();	 
?>
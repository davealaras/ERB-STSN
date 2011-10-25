
<?php 
$EGB->db_connect();
$gryrlv = array(
				  array('dept'=> 'PS', 'level'=>'Nursery,Kinder 1,Kinder 2'),
				  array('dept'=> 'GS', 'level'=>'Grade 1, Grade 2, Grade 3, Grade 4, Grade 5, Grade 6, Grade 7'),
				  array('dept'=> 'HS', 'level'=>'First Year, Second Year, Third Year,Fourth Year'),
				);
$index=0;
$data = array();
foreach($gryrlv as $g){
	$gryrlv[$index]['level']= explode(',', $gryrlv[$index]['level']);
	$id=1;
	foreach($gryrlv[$index]['level'] as $level){
		array_push($data, array('fk'=>$g['dept'], 'c'=>$level , 'id'=> $id++));
		}
	$index+=1;
}
?>
<input type="hidden" class="xlarge" id="teachers" data='<?php echo json_encode($EGB->list_get('faculties'));?>' />
<table >
<tr>
	<td>User ID:</td>
	<td>
		<input class="large disable-text" id="faculty_id" type="text" disabled="disabled"/ >
	</td>
	
	<td>
	Faculty Name:
	</td>
	<td>
		<input class="xlarge disable-text" id="full_name" type="text" disabled="disabled"/>
	</td>
</tr>
</table>
<table border="0">

  <tr>
    <td>SY</td>
    <td><select name="select" class="medium" id="sy" >
    </select></td>
    <td>Educ Level</td>
    <td><select name="educlvl" class="medium validate[required] link_list" id="educlvl" link_to="gryrlvl">
		<option value="#" class="default">Select department</option>
		<option value="PS">Pre-school</option>
		<option value="GS"> Grade School</option>
        <option value="HS">High School</option>
    </select></td>
    <td>Gr/Yr Level</td>
    <td data='<?php echo json_encode($data);?>' ><select name="gryrlvl" class="medium validate[required] refer" id="gryrlvl"  >
    </select></td>
    <td>Section</td>
   <td data='<?php echo json_encode($EGB->list_get('sections'));?>' ><select class="medium validate[required]" id="section" >
    </select></td>
	<td>
		Subject
	</td>
	<td data="" >
		<input type="text" class="large" id="subject" />
	</td>
  </tr>
  <tr>
	<td colspan="9">
	</td>
	<td>
	<div id="add_fload">Add</div>
	<div id="cancel_fload">Cancel</div>
	</td>
  </tr>
</table>

<div class="tab" style="display:block" >
	<div class="tab-header">
		View existing load(s)
	</div>
	<div class="tab-content">
	<table class="datagrid tablesorter"" id="floads">
	<thead> 
	<tr>
		<th class="head xlarge">Subject</td>
		<th class="head large">Grade/Year</td>
		<th class="head large">Section</td>
	</tr>
	</thead> 
	<tbody>
	<?php
		$fac_load = $EGB->get_fac_load($_SESSION['faculty_id']);
		if($fac_load!=null){
		foreach($fac_load as $load){
			$str="";
			$section= $EGB->get_sec_alias($load['sec_code']);
				foreach($section as $sec){
					if($sec['dept']=='GS'){
						$str="Gr".'-'.$sec['level'];
					}
					else if($sec['dept']=="HS"){
						$str= $sec['level'].'-'."Yr";
						}
					else if($sec['dept']=="PS"){
						if($sec['level']==1){
							$str=" PREP";
						}
						if($sec['level']==2){
							$str=" KINDER";
						}
						if($sec['level']==3){
							$str=" NURSERY";
						}
					}
				}
		?>
		<tr class="existloads" seccode="<?php echo $load['sec_code'] ?>" compcode="<?php echo $load['comp_code'] ?>">
			<td><?php echo $load['nomen']; ?></td>
			<td><?php echo $str;?></td>
			<td><?php echo $section[0]['section'];?></td>
		</tr>
		<?php
		}
		}else{
	?>
		Load empty.
	<?php } ?>
	</tbody>
	</table>
	<span class="art-button-wrapper">
			<span class="l"> </span>
			<span class="r"> </span>
			<a class="art-button close_fload"  inside="tbMeasItem" href="javascript:void(0);" >Close</a>
		 </span>
	</div>
</div>
<div class="tab" style="display:block" id="tab_newload">
	<div class="tab-header">
		New load(s)
	</div>
	<div class="tab-content">
	<table class="datagrid" id="new_fload">
	<thead>
		<tr>
			<td class="head xlarge">Subject</td>
			<td class="head large">Grade/Year</td>
			<td class="head large">Section</td>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot >
	<tr>
		<td style="border:none"></td>
		<td style="border:none" ></td>
		<td style="border:none"><div id="save_fload" class="right">
	Save
	</div></td>
	</tr>
	</tfoot>
	</table>
	<span class="art-button-wrapper">
			<span class="l"> </span>
			<span class="r"> </span>
			<a class="art-button close_fload"  inside="tbMeasItem" href="javascript:void(0);" >Close</a>
		 </span>
	</div>
	
</div>

<?php
	$EGB->db_close();
?>

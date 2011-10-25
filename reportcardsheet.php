
<?php 
$EGB->db_connect();
$gryrlv = array(
				  array('dept'=> 'PS', 'level'=>'Nursery,Kinder 1,Kinder 2'),
				  array('dept'=> 'GS', 'level'=>'Grade 1, Grade 2, Grade 3, Grade 4, Grade 5, Grade 6'),
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
	Name:
	</td>
	<td>
		<input class="xlarge disable-text" id="full_name" type="text" disabled="disabled"/>
	</td>
	<td>
	<input type="radio" class="mode" name="mode" value="individual" />Individual
	</td>
	<td>
	<input type="radio"class="mode"  name="mode" value="batch" /> Batch
	</td>
</tr>
</table>
<table border="0">

  <tr>
    <td align="right">SY:</td>
    <td><select name="select" class="medium" id="sy" >
    </select></td>
    <td>
	<div align="right">Period: </div>
	</td>
	<td>
		<select id="period" class="large">
		</select>
	</td>
		<td align="right" class="individual">Student Name:</td>
		<td class="individual" ><input type="text" id="studentname"  class="xlarge"/></td>
	<td class="batch">Educ Level</td>
    <td class="batch"><select name="educlvl" class="medium validate[required] link_list" id="educlvl" link_to="gryrlvl">
		<option value="#" class="default">Select department</option>
		<option value="PS">Pre-school</option>
		<option value="GS"> Grade School</option>
        <option value="HS">High School</option>
    </select></td>
    <td class="batch">Gr/Yr Level</td>
    <td class="batch" data='<?php echo json_encode($data);?>' ><select name="gryrlvl" class="medium validate[required] refer" id="gryrlvl"  >
    </select></td>
    <td class="batch">Section</td>
   <td class="batch" data='<?php echo json_encode($EGB->list_get('sections'));?>' ><select class="medium validate[required]" id="section" >
    </select></td>
  </tr>
</table>
<form id="printForm" action="fpdf17/createcard.php" method="GET" target="_blank">
	<input type="hidden" name="prnt_sy" id="prnt_sy" value="0"/>
	<input type="hidden" name="prnt_period" id="prnt_period" value="0"/>
	<input type="hidden" name="prnt_sno"  id="prnt_sno" value="0"/>
	<input type="hidden" name="prnt_classcode"  id="prnt_classcode"  value="0"/>
	<input type="hidden" name="prnt_mode"  id="prnt_mode" />
</form>
<div align="right">
				<span class="art-button-wrapper">
					<span class="l"> </span>
					<span class="r"> </span>
					<a class="art-button load_btn" href="javascript:void(0)" frm="printForm" >Load</a>
				</span>
			</div>
	<div id="progress-bar"></div>
<?php
	$EGB->db_close();
?>

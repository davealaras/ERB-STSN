
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
</tr>
</table>
<table border="0">

  <tr>
    <td>SY</td>
    <td><select name="select" class="medium" id="sy" >
    </select></td>
    <td>
	<div align="right">Period: </div>
	</td>
	<td>
		<select id="period" class="large">
		</select>
	</td>
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
  </tr>
    <tr>
		  
		  <td colspan="10">
			<div align="right">
				<span class="art-button-wrapper">
					<span class="l"> </span>
					<span class="r"> </span>
					<a class="art-button load_btn" href="javascript:void(0)" >Load</a>
				</span>
			</div>
		  </td>
	  </tr>
</table>
	<div id="progress-bar"></div>
	
<div class="tab" >
	<div class="tab-header">
	  Consolidated Grading Sheet
	</div>
	<div class="tab-content">
	<form id="ex-cgs" action="excel/export.php" method="POST">
					<input type="hidden" class="dataset" name="dataset"/>
				</form>
				<form id="pr-cgs" action="fpdf17/createpdf.php" method="POST"  target="_blank">
					<input type="hidden" class="info" name="info"/>
					<input type="hidden" class="dataset" name="dataset"/>
					<input type="hidden" class="alias" name="alias"/>
					<input type="hidden" class="months" name="months"/>
				</form>
					<div class="clear-div"></div>
						<span class="art-button-wrapper left" >
								<span class="l"> </span>
								<span class="r"> </span>
								<span class="art-button export_btn" datasource="gradesheet" form="ex-cgs" ><img src="img/export-excel.png" /> Export to Excel</span>
						</span>
						<span class="art-button-wrapper right">
								<span class="l"> </span>
								<span class="r"> </span>
								<span class="art-button print_btn" datasource="gradesheet" form="pr-cgs" ><img src="icons/printer.png" /> Print</span>
						</span>					
				<div class="clear-div"></div>
	<div id="div-rawscore">
						<table id="gradesheet"  class="datagrid">
							<tr class="label">
								<td class="mini head">
								ID No.
								</td>
							   <td class="head" id="name_student">
								<div class="jumbo">Name of Student</div>
								</td>
								<td class="eof head">                               
								</td>
							</tr>
							<tr class="list">
							</tr>
						</table>
					
				</div>
<hr>
</div>
</div>

<?php
	$EGB->db_close();
?>

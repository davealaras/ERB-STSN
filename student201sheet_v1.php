<?php 
$EGB->db_connect();
$gryrlv = array(
				  array('dept'=> 'PS', 'level'=>'Prep,Kinder,Nursery'),
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
    <form id="student_personal_info" method="post">
	<input type="hidden" id="ovrrd_by" name="ovrrd_by"value="null"/>
<div class="tab"  style="display:block;">
	<div class="tab-header" style="display:block;">
		Student Personal Information
	</div>
	<div class="tab-content" style="display:block;">
	<div id="query"></div>
<table>
  <tr>
    <td>Student No.</td>
    <td colspan="2"><input type="text" class="medium validate[required]" id="sno" name="sno"/>
	<div id="go_201">Go</div><div id="cancel_201">Cancel</div>
<span id="action"></span>
    <div class="micro"></div></td>
    <td>School Year</td>
    <td><select name="sy" class="medium validate[required]" id="sy">
    </select></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>Last Name</td>
    <td><input name="lastname" type="text" class="xlarge2 validate[required]" id="lastname" /></td>
    <td><div class="micro"></div></td>
    <td>First Name</td>
    <td colspan="2"><input name="firstname" type="text" class="xlarge2 validate[required]" id="firstname" /></td>
    <td>Middle Name</td>
    <td><input name="middlename" type="text" class="xlarge2 validate[required]" id="middlename" /></td>
    </tr>
</table>
<table>
  <tr>
    <td>Educ Level</td>
    <td><select name="educlvl" class="medium validate[required] smart_list" id="educlvl" link_to="gryrlvl">
		<option value="#" class="default">Select department</option>
		<option value="PS">Pre-school</option>
		<option value="GS"> Grade School</option>
        <option value="HS">High School</option>
    </select></td>
    <td><div class="micro"></div></td>
    <td>Gr/Yr Level</td>
    <td data='<?php echo json_encode($data);?>' ><select name="gryrlvl" class="medium validate[required] refer" id="gryrlvl"  >
    </select></td>
    <td><div class="micro"></div></td>
    <td>Section</td>
    <td colspan="3" data='<?php echo json_encode($EGB->list_get('sections'));?>' ><select class="medium validate[required]" id="section" name="section" >
    </select></td>
  </tr>
</table>
<table>
  <tr>
    <td>Date of Birth</td>
    <td><input type="text" class="large" id="dob" name="dob"/></td>
    <td>Place of Birth</td>
    <td><input type="text" class="xlarge" id="pob" name="pob"/>      <div class="micro"></div></td>
    <td>Gender</td>
    <td>
    <select name="gender" class="mini" id="gender" >
      <option value="M"> Male</option>
      <option value="F"> Female </option>
    </select>
    </td>
    <td>Citizenship</td>
    <td><input name="citizen" type="text" class="large" id="citizen" /></td>
    </tr>
</table>
<table>
  <tr>
    <td>Religion</td>
    <td colspan="3"><input type="text"class="large" id="religion"  name="religion" />
    </td>
    <td>&nbsp;</td>
    <td>Land Line No.</td>
    <td><input name="home_landno" type="text" class="medium" id="home_landno" /></td>
    <td><div class="micro"></div></td>
    <td>Mobile No</td>
    <td colspan="2"><input name="home_mobno" type="text" class="medium" id="home_mobno" /></td>
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
<br/>
<strong>Home Address</strong>

<table border="0">
  <tr>
    <td>Country</td>
    <td data='<?php echo json_encode($EGB->list_get('country'));?>'><input type="text" class="large validate[required] link_list" id="home_coun" name="home_coun" link_to="home_prov">
	</td>
    <td><div class="micro"></div></td>
    <td>Province</td>
    <td data='<?php echo json_encode($EGB->list_get('province'));?>'><input type="text" class="large validate[required] link_list" id="home_prov" name="home_prov" link_to="home_muni" />
	</td>
    <td>&nbsp;</td>
    <td>Municipality</td>
    <td data='<?php echo json_encode($EGB->list_get('municipality'));?>'><input type="text"  class="large validate[required] link_list" id="home_muni" name="home_muni"  link_to="home_brgy" />
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Barangay</td> 
    <td  data='<?php echo json_encode($EGB->list_get('barangays'));?>'>
	<input name="home_brgy" type="text" class="large validate[required] link_list" id="home_brgy" /></td>
  </tr>
</table>
<table border="0">
  <tr>
    <td>Subd</td>
    <td><input name="home_subd" type="text" class="xlarge validate[required]" id="home_subd" /></td>
    <td>&nbsp;</td>
    <td colspan="2">No./ Street</td>
    <td colspan="5"><input name="home_streetno" type="text" class="xlarge validate[required]" id="home_streetno" /></td>
    <td>&nbsp;</td>
    <td>Zip Code</td>
    <td><input name="home_zip" type="text" class="small validate[required]" id="home_zip" /></td>
  </tr>
</table>
<br/>
    <strong>Mailing Address</strong><input type="checkbox"  id="same"/> Same

<table border="0">
  <tr>

    <td>Country</td>
    <td id="td_coun" data='<?php echo json_encode($EGB->list_get('country'));?>'>
		<input type="text" class="large validate[required] link_list" id="mail_coun" name="mail_coun" link_to="mail_prov"></td>
    <td><div class="micro"></div></td>
    <td>Province</td>
    <td id="td_prov" data='<?php echo json_encode($EGB->list_get('province'));?>' >
	<input type="text" class="large validate[required] link_list" id="mail_prov" name="mail_prov" link_to="mail_muni" />
	</td>
    <td>&nbsp;</td>
    <td>Municipality</td>
    <td id="td_muni" data='<?php echo json_encode($EGB->list_get('municipality'));?>' >
	<input type="text"  class="large validate[required] link_list" id="mail_muni" name="mail_muni"  link_to="mail_brgy" />
	</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Barangay</td>
    <td id="td_brgy"  data='<?php echo json_encode($EGB->list_get('barangays'));?>'>
	<input type="text"  class="large validate[required] link_list" id="mail_brgy" name="mail_brgy"/>
  </tr>
</table>
<table border="0">
  <tr>
    <td>Subd</td>
    <td  id="td_subd"><input name="mail_subd" type="text" class="xlarge validate[required]" id="mail_subd" /></td>
    <td>&nbsp;</td>
    <td colspan="2" >No./ Street</td>
    <td colspan="5" id="td_streetno"><input name="mail_streetno" type="text" class="xlarge validate[required]" id="mail_streetno" /></td>
    <td>&nbsp;</td>
    <td >Zip Code</td>
    <td id="td_zip"><input name="mail_zip" type="text" class="small validate[required]" id="mail_zip" /></td>
  </tr>
  <tr>
  </tr>
</table>
   
  </div>
</div>

<div class="tab"  style="display:block;">
	<div class="tab-header" style="display:block;">
		Pupil/Student Educational Background
	</div>
	<div class="tab-content" style="display:block;">
	  <table border="0">
	    <tr>
	      <td>Previous School Attended</td>
	      <td><input type="text" class="xlarge" id="prev_school" name="prev_school"/>
          </td>
	      <td>&nbsp;</td>
	      <td>SY</td>
	      <td><select name="sy" class="large" id="sy">
          </select></td>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
        </tr>
	    <tr>
	      <td>Educ Level</td>
	      <td>
		  <select name="prev_educlvl" class="medium validate[required] smart_list" id="prev_educlvl" link_to="prev_gryrlvl">
			<option value="PS">Pre-School</option>
			<option value="GS">Grade School</option>
			<option value="HS">High School</option>
          </select>
		  </td>
	      <td>&nbsp;</td>
	      <td>Gr/Yr Level</td>
		  <td data='<?php echo json_encode($data);?>' ><select name="prev_gryrlvl" class="medium validate[required] refer" id="prev_gryrlvl"  >
    </select></td>
<!--
	      <td>Latest Deportmant Grade</td>
	      <td><input type="text" class="small" /></td>
		  -->
        </tr>
		<!--
	    <tr>
	      <td>Lack Unit(s) / Credit(s) in</td>
	      <td><input type="text" class="xlarge" /></td>
	      <td>&nbsp;</td>
	      <td>Gr/Yr Level</td>
	      <td><select name="select17" class="large">
          </select></td>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
        </tr>
		
		-->
      </table>
    </div>
</div>

<div class="tab"  style="display:block;">
	<div class="tab-header" style="display:block;">
		Primary Guardian
	</div>
	<div class="tab-content" style="display:block;">
	
	  <table border="0">
	    <tr>
	      <td>Name</td>
	      <td><input name="parent_name" type="text" class="xlarge2" id="parent_name" /></td>
	      <td>Relationship</td>
	      <td><select name="parent_rel" class="large" id="parent_rel">
	        <option value="Parent">Parent</option>
	        <option value="Guardian">Guardian</option>
          </select></td>
	      <td>Present Occupation</td>
	      <td><input name="parent_occupation" type="text" class="xlarge2" id="parent_occupation" /></td>
	      <td>&nbsp;</td>
        </tr>
      </table>
	  <br/>
	    <strong>Mailing Address</strong>
        <input type="checkbox" /> 
	  <table border="0">
	    <tr>
	  <td>Country</td>
    <td data='<?php echo json_encode($EGB->list_get('country'));?>'><input type="text" class="large validate[required] link_list" id="parent_coun" name="parent_coun" link_to="parent_prov">
	</td>
    <td><div class="micro"></div></td>
    <td>Province</td>
    <td data='<?php echo json_encode($EGB->list_get('province'));?>'><input type="text" class="large validate[required] link_list" id="parent_prov" name="parent_prov" link_to="parent_muni" />
	</td>
    <td>&nbsp;</td>
    <td>Municipality</td>
    <td data='<?php echo json_encode($EGB->list_get('municipality'));?>'><input type="text"  class="large validate[required] link_list" id="parent_muni" name="parent_muni"  link_to="parent_brgy" />
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Barangay</td> 
    <td  data='<?php echo json_encode($EGB->list_get('barangays'));?>'>
	<input name="parent_brgy" type="text" class="large validate[required] link_list" id="parent_brgy" /></td>
        </tr>
      </table>
	  <table border="0">
	    <tr>
	      <td><div class="small"></div></td>
	      <td>Subd</td>
	      <td><input name="parent_subd" type="text" class="xlarge" id="parent_subd" /></td>
	      <td>&nbsp;</td>
	      <td colspan="2">No./ Street</td>
	      <td colspan="5"><input name="parent_streetno" type="text" class="xlarge" id="parent_streetno" /></td>
	      <td>&nbsp;</td>
	      <td>Zip Code</td>
	      <td><input name="parent_zip" type="text" class="small" id="parent_zip" /></td>
        </tr>
      </table>
    </div>
</div>

<div class="tab"  style="display:block;">
	<div class="tab-header" style="display:block;">
		Secondary Guardian
	</div>
	<div class="tab-content" style="display:block;">
	<table border="0">
	    <tr>
	      <td>Name</td>
	      <td><input name="parent2_name" type="text" class="xlarge2" id="parent2_name" /></td>
	      <td>Relationship</td>
	      <td><select name="parent2_rel" class="large" id="parent2_rel">
	        <option value="Parent">Parent</option>
	        <option value="Guardian">Guardian</option>
          </select></td>
	      <td>Present Occupation</td>
	      <td><input name="parent2_occupation" type="text" class="xlarge2" id="parent2_occupation" /></td>
	      <td>&nbsp;</td>
        </tr>
      </table>
	  <br/>
	    <strong>Mailing Address</strong><input type="checkbox" />
      <table border="0">
	    <tr>
	  <td>Country</td>
    <td data='<?php echo json_encode($EGB->list_get('country'));?>'><input type="text" class="large validate[required] link_list" id="parent2_coun" name="parent2_coun" link_to="parent2_prov">
	</td>
    <td><div class="micro"></div></td>
    <td>Province</td>
    <td data='<?php echo json_encode($EGB->list_get('province'));?>'><input type="text" class="large validate[required] link_list" id="parent2_prov" name="parent2_prov" link_to="parent2_muni" />
	</td>
    <td>&nbsp;</td>
    <td>Municipality</td>
    <td data='<?php echo json_encode($EGB->list_get('municipality'));?>'><input type="text"  class="large validate[required] link_list" id="parent2_muni" name="parent2_muni"  link_to="parent2_brgy" />
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Barangay</td> 
    <td  data='<?php echo json_encode($EGB->list_get('barangays'));?>'>
	<input name="parent2_brgy" type="text" class="large validate[required] link_list" id="parent2_brgy" /></td>
        </tr>
      </table>
	  <table border="0">
	    <tr>
	      <td><div class="small"></div></td>
	      <td>Subd</td>
	      <td><input name="parent2_subd" type="text" class="xlarge" id="parent2_subd" /></td>
	      <td>&nbsp;</td>
	      <td colspan="2">No./ Street</td>
	      <td colspan="5"><input name="parent2_streetno" type="text" class="xlarge" id="parent2_streetno" /></td>
	      <td>&nbsp;</td>
	      <td>Zip Code</td>
	      <td><input name="parent2_zip" type="text" class="small" id="parent2_zip" /></td>
        </tr>
      </table>
    </div>
</div>
 <span class="art-button-wrapper ">
		<span class="l"> </span>
		<span class="r"> </span>
		<span class="art-button " id="save_201" >Save</span>
	</span>

</form>
<div id="dialog-modal" title="Information">
	<div id="log_msg" class="validateTips" msg="Student number unavailable. Log-in to override."></div>
	<form>
	<fieldset>
		<label for="name">Username</label>
		<input type="text" name="ovrrd_name" id="ovrrd_name" class="text ui-widget-content ui-corner-all" />
		<br/>
		<label for="password">Password</label>
		<input type="password" name="ovrrd_password" id="ovrrd_password" value="" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	</form>
</div>
<?php
$EGB->db_close();
?>
<?php
include_once ('database_login.php');
class EGB{
	private $db_server = "localhost:3306"; 
	private $db_username; 
	private $db_password;
	private $db_name;
	private $db_connection;

	
	public function __construct($db_username, $db_password, $db_server, $db_name ) {
		//Database login information
		$this->db_server = $db_server;
		$this->db_username = $db_username;
		$this->db_password = $db_password;
		$this->db_name = $db_name;
		
	}
	//Database open connection method
	//@param	null
	//@return	$this->db_connection	mysqli object
	public function db_connect() {
		$this->db_connection = new mysqli($this->db_server, $this->db_username, $this->db_password, $this->db_name);
		if(mysqli_connect_errno()) self::error();
		else return $this->db_connection;
	}
	//
	public function ms_connect(){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345")or die("Could not connect to the server!");
		mssql_select_db('egb') or die('Could not select a database.');
	}
	//Database close connection method
	//@param	$connection	boolean variable
	//@return	null
	public function db_close($connection=false) {
		if($connection != false) $connection->close(); 
		$this->db_connection->close();
	}
	
	//Login
	public function login($user_name, $password){
		$query = "SELECT FacultyID FROM tb_useraccount WHERE Username=? AND Password=? LIMIT 1";
			if ($stmt = $this->db_connection->prepare($query)) {
				$stmt->bind_param('ss',$user_name,$password);
				$stmt->execute();
				$stmt->bind_result($faculty_id);
				$stmt->fetch();
				$stmt->close();	
			}
			return $faculty_id;
	}
	//Check username availability
	public function check_user($username){
		$count =0;
		$query = "SELECT COUNT(*) FROM tb_useraccount WHERE Username = '$username'";
		if ($stmt = $this->db_connection->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result($count);
				$stmt->fetch();
				$stmt->close();	
			}
			return $count;
	}
	//Log-in MS SQL
	public function ms_login($user_name, $password){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345")or die("Could not connect to the server!");
		mssql_select_db('egb') or die('Could not select a database.');
		$query = "SELECT TOP(1) FacultyID FROM tb_useraccount WHERE (Username='$user_name') AND (Password LIKE '$password')";
		$result = mssql_fetch_assoc(mssql_query($query));
		return  $result['FacultyID'];

	}
	//Gets all users
	//@param	null	
	//@resutl 	$results	Array of user details
	public function get_users($faculty_id){
		$query ="SELECT * FROM tb_faculty201 WHERE FacultyID =? ";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->bind_param("i",$faculty_id);
			$stmt->execute();
			$stmt->bind_result($faculty_id, $last_name, $first_name, $middle_name);
			$stmt->fetch();
			$results['faculty_id']=$faculty_id;
			$results['last_name']=$last_name;
			$results['first_name']=$first_name;
			$results['middle_name']=$middle_name;
			$stmt->close();		
		}
		return $results;
	}
	//MS Get all users
	public function ms_get_users($faculty_id){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345")or die("Could not connect to the server!");
		mssql_select_db('egb') or die('Could not select a database.');
		$query ="SELECT  FacultyID, LastName, FirstName, MiddleName FROM tb_faculty201 WHERE(FacultyID = $faculty_id)";
		$result = mssql_fetch_assoc(mssql_query($query));
		$results['faculty_id']=$result['FacultyID'];
		$results['last_name']=$result['LastName'];
		$results['first_name']=$result['FirstName'];
		$results['middle_name']=$result['MiddleName'];
		return $results;
	}
	//Insert new account details
	public function creat_account($faculty_id, $user_name, $password, $role){
		$query = "INSERT INTO tb_useraccount (FacultyID, Username, Password, Role)  VALUES('$faculty_id', '$user_name', '$password', '$role')";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
		return $query;
	}
	//Insert faculty details
	public function create_fac201($username, $password, $last_name, $first_name, $middle_name, $iscoor){
		$queries = array();
		$faculty_id=0;
		$type = isset($iscoor) ? 3:1;
		$sql1="SELECT COUNT( DISTINCT FacultyId)+1 FROM tb_faculty201";
			if ($stmt1 = $this->db_connection->prepare($sql1)) {
				$stmt1->execute();
				$stmt1->bind_result($faculty_id);
				$stmt1->fetch();
				$stmt1->close();
			}
		$query = "INSERT INTO tb_faculty201 (FacultyId, LastName, FirstName, MiddleName)  VALUES('$faculty_id', '$last_name', '$first_name', '$middle_name')";
		array_push($queries, $query);
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
		$query2 = $this->creat_account($faculty_id, $username, $password, $type);
		array_push($queries, $query2);
		return $queries;
	}
	//Load subject
	public function load_subject($faculty_id, $comp_code, $section, $sy){
		$query = "INSERT INTO nrol_facultyload (FacultyID, CompCode, SectionCode, SY)  VALUES('$faculty_id', '$comp_code', '$section', '$sy')";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	//Load Section
	//Insert new account details
	public function load_section($section_code, $section, $level, $dept_code){
		$query = "INSERT INTO tb_mastersection (SectionCode, Section, Level, DeptCode)  VALUES('$section_code', '$section', '$level', '$dept_code')";		
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	//Get Menu
	public function getmenu(){
		$results1=array();
		$results2 =array();
		$sql="SELECT TopLevelId ,TopLevel, Link FROM tb_toplevel ORDER BY SeqIndex ASC";
		if ($stmt1 = $this->db_connection->prepare($sql)) {			
			$stmt1->execute();
			$stmt1->bind_result($id, $desc,$link);
			$index=0;
			while($stmt1->fetch()){
				$results1[$index]['id']=$id;
				$results1[$index]['desc']=$desc;
				$results1[$index]['link']=$link;
				$index+=1;
			}
			$stmt1->close();		
		}
		$sql2="SELECT SubLevelId, ParentId, SubLevel, Link FROM tb_sublevel ORDER BY SeqIndex ASC";
		if ($stmt2 = $this->db_connection->prepare($sql2)) {			
			$stmt2->execute();
			$stmt2->bind_result($s_id, $s_pid, $s_desc, $s_link);
			$index2=0;
			while($stmt2->fetch()){
				$results2[$index2]['id']=$s_id;
				$results2[$index2]['pid']=$s_pid;
				$results2[$index2]['desc']=$s_desc;
				$results2[$index2]['link']=$s_link;
				$index2+=1;
			}
			$stmt2->close();		
		}
		for($i=0; $i<count($results1);$i+=1){
			for($j=0; $j<count($results2);$j+=1){
				if($results1[$i]['id']==$results2[$j]['pid']){
					$results1[$i]['child']=$results2[$j];
				}
			}
		}
	
		return $results1;
	}
	//Get Faculty advisory
	public function get_fac_advisory($sy, $deptcode, $level, $seccode){
		$sql  = "SELECT   nrol_currdtl.Under, nrol_currdtl.CompCode    , tb_subject.Nomenclature,  nrol_currdtl.Weight, nrol_facultyload.FacultyId    , tb_faculty201.LastName    , tb_faculty201.FirstName    , tb_faculty201.MiddleName    , nrol_posted.StatusCode, nrol_posted.Period  ";
		$sql .= "FROM    erb_stsn.nrol_currhdr    INNER JOIN erb_stsn.nrol_currdtl         ON (nrol_currhdr.CurriculumId = nrol_currdtl.CurriculumId)    INNER JOIN erb_stsn.tb_mastersection         ON (tb_mastersection.DeptCode = nrol_currhdr.ProgramId) ";
		$sql .="AND (tb_mastersection.Level = nrol_currdtl.CurrYear)    INNER JOIN erb_stsn.tb_subject         ON (tb_subject.CompCode = nrol_currdtl.CompCode)    LEFT JOIN erb_stsn.nrol_facultyload         ON (nrol_facultyload.SY = nrol_currhdr.SY) ";
		$sql .="AND (nrol_facultyload.CompCode = nrol_currdtl.CompCode)    LEFT JOIN erb_stsn.tb_faculty201         ON (tb_faculty201.FacultyID = nrol_facultyload.FacultyId)    LEFT JOIN erb_stsn.nrol_posted  ON (nrol_posted.SY = nrol_facultyload.SY) ";
		$sql .="AND (nrol_posted.CompCode = nrol_facultyload.CompCode) AND (nrol_posted.SectionCode = nrol_facultyload.SectionCode)";
		$sql .=" WHERE (nrol_currhdr.SY ='$sy'    AND nrol_currhdr.ProgramId ='$deptcode'  ";
		$sql .="    AND tb_mastersection.SectionCode ='$seccode'    AND nrol_currdtl.CurrYear ='$level') ORDER BY nrol_currdtl.Under DESC";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($under, $comp_code, $nomen, $weight, $fac_id, $last_name, $first_name, $middle_name, $status, $period);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['comp_code']=$comp_code;
				$results[$index]['under']=$under;
				$results[$index]['nomen']=$nomen;
				$results[$index]['weight']=$weight;
				$results[$index]['fac_id']=$fac_id;
				$results[$index]['last_name']=$last_name;
				$results[$index]['first_name']=$first_name;
				$results[$index]['middle_name']=$middle_name;
				$results[$index]['status']=$status;
				$results[$index]['period']=$period;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	//Get Faculty Load
	public function get_fac_load($faculty_id){
		$results=array();
		$query = "SELECT    nrol_facultyload.CompCode, nrol_facultyload.SectionCode    , nrol_facultyload.SY    , tb_subject.Nomenclature ";
		$query .="FROM    nrol_facultyload    INNER JOIN tb_subject  ON (nrol_facultyload.CompCode = tb_subject.CompCode) ";
		$query .="WHERE (nrol_facultyload.FacultyId ='$faculty_id')";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($comp_code, $sec_code, $sy, $nomen);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['comp_code']=$comp_code;
				$results[$index]['nomen']=$nomen;
				$results[$index]['sec_code']=$sec_code;
				$results[$index]['sy']=$sy;
				$index+=1;
			}
			$stmt->close();
		}
		return $results;
	}
	//MS Get Faculty Load
	public function ms_get_fac_load($faculty_id){
		$results = array();
		$sql ="SELECT CompCode, SectionCode, SY FROM  nrol_facultyload WHERE FacultyID = $faculty_id";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['comp_code']=$row[0];
				$results[$index]['sec_code']=$row[1];
				$results[$index]['sy']=$row[2];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}
	//Get Section ALias
	public function get_sec_alias($sec_code){
		$results =array();
		$query ="SELECT Section, Level, DeptCode FROM tb_mastersection WHERE SectionCode ='$sec_code'";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($section,$level, $dept);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['section']=$section;
				$results[$index]['level']=$level;
				$results[$index]['dept']=$dept;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	//MS Get Section Alias
	public function ms_get_sec_alias($sec_code){
		$results =array();
		$sql ="SELECT Section, Level, DeptCode FROM tb_mastersection WHERE SectionCode ='$sec_code'";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['section']=$row[0];
				$results[$index]['level']=$row[1];
				$results[$index]['dept']=$row[2];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}	
	//Get Components
	public function get_components_rec($compcode,$seccode,$sy, $period){
		$results =array();
		$query ="SELECT  RowNumber,  ClassCode, Percentage FROM nrol_gradecomp WHERE  CompCode ='$compcode' AND SectionCode ='$seccode'AND SY='$sy' AND Period ='$period' ORDER BY RowNumber ASC";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($rownum,$ccode, $perc);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['rownum']=$rownum;
				$results[$index]['ccode']=$ccode;
				$results[$index]['perc']=$perc;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;		
	}
	//MS Get Components
	public function ms_get_components_rec($compcode,$seccode,$sy, $period){
		$results =array();
		$sql  ="SELECT  RowNumber,  ClassCode, Percentage FROM nrol_gradecomp ";
		$sql .="WHERE  CompCode ='$compcode' AND SectionCode ='$seccode'AND SY='$sy' AND Period='$period' ORDER BY RowNumber ASC";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['rownum']=$row[0];
				$results[$index]['ccode']=$row[1];
				$results[$index]['perc']=$row[2];
				$s = "SELECT COUNT(*)  FROM [egb].[dbo].[nrol_measitem]  WHERE  CompCode ='$compcode' AND SectionCode ='$seccode'AND SY='$sy' AND Period='$period' AND ClassCode='$row[1]'";
				$q = mssql_fetch_row(mssql_query($s,$con));
				$results[$index]['count']=$q[0];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}
	//Prepare Record General Components
	public function prepare_reccord_gencomp($comp_code, $section_code, $sy, $period){
		$query ="DELETE FROM nrol_gradecomp WHERE CompCode='$comp_code' AND SectionCode='$section_code' AND SY='$sy' AND Period=' $period'";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	//MS Prepare Record General Components
	public function ms_prepare_reccord_gencomp($comp_code, $section_code, $sy,  $period){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query ="DELETE FROM nrol_gradecomp WHERE CompCode='$comp_code' AND SectionCode='$section_code' AND SY='$sy' AND Period=' $period'";
		mssql_query($query);
	}	
	//Save Record General Components
	public function save_record_gencomp($rownumber,$classcode,$percentage,$sy,  $period, $section_code,$comp_code){
		$query = "INSERT INTO nrol_gradecomp (RowNumber, ClassCode, Percentage, SY, Period, SectionCode, CompCode) ";
		$query .=" VALUES('$rownumber','$classcode','$percentage','$sy', ' $period', '$section_code', '$comp_code')";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	//MS Save Record General Components
	public function ms_save_record_gencomp($rownumber,$classcode,$percentage,$sy,  $period, $section_code,$comp_code){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = "INSERT INTO nrol_gradecomp (RowNumber, ClassCode, Percentage, SY, Period, SectionCode, CompCode) ";
		$query .=" VALUES('$rownumber','$classcode','$percentage','$sy', ' $period', '$section_code', '$comp_code')";
		mssql_query($query);
	}
	public function prepare_meas($comp_code, $section_code, $sy, $period){
		$results =array();
		$sql  = "SELECT    nrol_measitem.SectionCode,  nrol_gradecomp.ClassCode,  nrol_measitem.HeaderName";
		$sql .= "FROM    erb_stsn.nrol_measitem    LEFT OUTER JOIN erb_stsn.nrol_gradecomp ";
		$sql .="ON (nrol_measitem.CompCode = nrol_gradecomp.CompCode) AND (nrol_measitem.SectionCode = nrol_gradecomp.SectionCode) AND (nrol_measitem.ClassCode = nrol_gradecomp.ClassCode)";
		$sql .="WHERE (nrol_measitem.CompCode='$comp_code' AND nrol_measitem.SY =$sy AND nrol_measitem.Period =$period    AND nrol_measitem.SectionCode ='$section_code')";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($seccode,$classcode, $hdr);
			while($stmt->fetch()){
				if($classcode==''){
					//Delete all affected measurable items
					$sql1 = "DELETE FROM nrol_measitem  WHERE (CompCode = '$comp_code') AND (SY = $sy) AND (Period = $period) AND (SectionCode = $section_code) AND (HeaderName ='$header')";
					if ($stmt1= $this->db_connection->prepare($sql1)) {			
						$stmt1->execute();
						$stmt1->fetch();
						$stmt1->close();		
					}
					//Delete all affected raw score
					$sql2 = "DELETE FROM nrol_rawscore  WHERE (CompCode = '$comp_code') AND (SY = $sy) AND (Period = $period) AND (SectionCode = $section_code) AND (HeaderName ='$header')";
					if ($stmt2= $this->db_connection->prepare($sql2)) {			
						$stmt2->execute();
						$stmt2->fetch();
						$stmt2->close();		
					}
				}
			}
			$stmt->close();		
		}
		return $results;
	}
	//MS Modify Measurable Items
	public function ms_prepare_meas($comp_code, $section_code, $sy, $period){
		$sql  = "SELECT     nrol_gradecomp.ClassCode, nrol_measitem_1.HeaderName, nrol_measitem_1.ClassCode AS Expr1 ";
		$sql .="FROM         nrol_measitem AS nrol_measitem_1 LEFT OUTER JOIN  ";
        $sql .="              nrol_gradecomp ON nrol_measitem_1.CompCode = nrol_gradecomp.CompCode AND nrol_measitem_1.SectionCode = nrol_gradecomp.SectionCode AND ";
        $sql .="              nrol_measitem_1.SY = nrol_gradecomp.SY AND nrol_measitem_1.ClassCode = nrol_gradecomp.ClassCode ";
		$sql .="WHERE     (nrol_measitem_1.CompCode = '$comp_code') AND (nrol_measitem_1.SY = $sy) AND (nrol_measitem_1.SectionCode = $section_code)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		
		$sql="";
		
		do{
			while($row=mssql_fetch_row($query)){
				$header = $row[1];
				$classcode = $row[0];
				$expr1 =$row[2];	//ClassCode from measurable items
				if($classcode==''){
					$sql = "DELETE FROM nrol_measitem  WHERE (CompCode = '$comp_code') AND (SY = $sy) AND (SectionCode = $section_code) AND (HeaderName ='$header')";
					mssql_query($sql,$con);
					$sql = "DELETE FROM nrol_rawscore  WHERE (CompCode = '$comp_code') AND (SY = $sy) AND (SectionCode = $section_code) AND (HeaderName ='$header')";
					mssql_query($sql,$con);
					$sql = "DELETE FROM nrol_equivalent  WHERE (CompCode = '$comp_code') AND (SY = $sy) AND (SectionCode = $section_code) AND (HeaderName ='$header')";
					mssql_query($sql,$con);
					$sql = "DELETE FROM nrol_summary  WHERE (CompCode = '$comp_code') AND (SY = $sy) AND (SectionCode = $section_code) AND (HeaderName ='$expr1')";
					mssql_query($sql,$con);
				}
			}
		}while(mssql_next_result($query));
	
	}
	//Get Measurables
	public function get_measurables_rec($compcode,$seccode,$sy, $period){
		$results =array();
		$query ="SELECT  ColNumber, ClassCode, HeaderName, Description,  Items, Base FROM nrol_measitem WHERE  CompCode ='$compcode' AND SectionCode ='$seccode'AND SY='$sy' AND Period='$period' ORDER BY ColNumber ASC";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($colnum,$ccode, $hdr, $dsc, $itm, $base);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['colnum']=$colnum;
				$results[$index]['ccode']=$ccode;
				$results[$index]['hdr']=$hdr;
				$results[$index]['dsc']=$dsc;
				$results[$index]['itm']=$itm;
				$results[$index]['base']=$base;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;		
	}
	//MS Get Measurables
	public function ms_get_measurables_rec($compcode,$seccode,$sy, $period){
		$results =array();
		$sql ="SELECT  ColNumber, ClassCode, HeaderName, Description,  Items, Base ";
		$sql .="FROM nrol_measitem WHERE  CompCode ='$compcode' AND SectionCode ='$seccode'AND SY='$sy' AND Period='$period' ORDER BY ColNumber ASC";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['colnum']=$row[0];
				$results[$index]['ccode']=$row[1];
				$results[$index]['hdr']=$row[2];
				$results[$index]['dsc']=$row[3];
				$results[$index]['itm']=$row[4];
				$results[$index]['bse']=$row[5];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}
	//Prepare Record Measurable Items
	public function prepare_reccord_measitem($comp_code, $section_code, $sy, $period){
		$query ="DELETE FROM nrol_measitem WHERE CompCode='$comp_code' AND SectionCode='$section_code' AND SY=$sy AND Period = $period";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	//MS Prepare Record Measurable Items
	public function ms_prepare_reccord_measitem($comp_code, $section_code, $sy,  $period){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query ="DELETE FROM nrol_measitem WHERE CompCode='$comp_code' AND SectionCode='$section_code' AND SY='$sy' AND Period='$period'";
		mssql_query($query);
	}
	//Save Record Measurable Items
	public function save_record_measitem($colnumber,$classcode,$header,$description,$noofitem, $base,$sy,  $period,$section_code,$comp_code){
		$query = "INSERT INTO nrol_measitem (ColNumber, ClassCode, HeaderName, Description,  Items, Base, SY, Period, SectionCode, CompCode) ";
		$query .=" VALUES('$colnumber','$classcode', '$header', '$description', '$noofitem', '$base', '$sy', ' $period', '$section_code', '$comp_code')";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	//MS Save Record Measurable Items
	public function ms_save_record_measitem($colnumber,$classcode,$header,$description,$noofitem, $base,$sy,  $period,$section_code,$comp_code){
		$query = "INSERT INTO nrol_measitem (ColNumber, ClassCode, HeaderName, Description,  Items, Base, SY, Period, SectionCode, CompCode) ";
		$query .=" VALUES('$colnumber','$classcode', '$header', '$description', '$noofitem', '$base', '$sy', ' $period', '$section_code', '$comp_code')";
		mssql_query($query);
	}
	//Get System Default
	public function get_sys_defa(){
		$results =array();
		$query = "SELECT active_sy, base , ztable_width, ztable_height, timeout FROM  tb_sysdefa";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($active_sy, $base, $ztable_width, $ztable_height, $timeout);
			$stmt->fetch();
			$results['active_sy']=$active_sy;
			$results['base']=$base;
			$results['ztable_height']=$ztable_height;
			$results['ztable_width']=$ztable_width;
			$results['timeout']=$timeout;
			$results['sql']=$query;
			$stmt->close();		
		}
		return $results;
	}
	//MS Get System Default
	public function ms_get_sys_defa(){
		$results =array();
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = "SELECT  active_sy, base , ztable_width, ztable_height, timeout FROM  tb_sysdefa";
		$results = mssql_fetch_assoc(mssql_query($query));
		return  $results;
	}
	//Get Periods
	public function get_periods(){
		$results =array();
		$query ="SELECT period_id, period_description, period_alias  FROM tb_period";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($id, $desc, $alias);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['id']=$id;
				$results[$index]['desc']=$desc;
				$results[$index]['alias']=$alias;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	//MS Get Periods
	public function ms_get_periods(){
		$results =array();
		$sql="SELECT period_id, period_description, period_alias FROM tb_period";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['id']=$row[0];
				$results[$index]['desc']=$row[1];
				$results[$index]['alias']=$row[2];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;		
	}
	//Get Components
	public function get_components(){
		$results =array();
		$query ="SELECT ClassCode, Description FROM  tb_components";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($code, $desc);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['code']=$code;
				$results[$index]['desc']=$desc;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	//MS Get Components
	public function ms_get_components(){
		$results =array();
		$sql ="SELECT ClassCode, Description FROM  tb_components";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['code']=$row[0];
				$results[$index]['desc']=$row[1];
				$index+=1;
			}
		}while(mssql_next_result($query));
		mssql_free_result($query);
		mssql_close();
		return $results;
	}
	//Get Template
	public function get_template($comp_code, $level, $dept_code, $sy){
		$results =array();
		$query = "SELECT TemplateId, Description, CompCode FROM  tb_templatehdg  WHERE CompCode ='$comp_code' AND YrLevel='$level' AND DeptCode ='$dept_code'  AND SY = '$sy' AND StatusCode ='A'";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($tempid,$desc, $compcode);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['id']=$tempid;
				$results[$index]['desc']=$desc;
				$results[$index]['compcode']=$compcode;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	public function get_templates_all($comp_codes, $sy){
		$results =array();
		$filter = '';
		for($ctr=0; $ctr<count($comp_codes); $ctr++){
			$filter .= 'CompCode = \''.$comp_codes[$ctr].'\' ';
			if($ctr<count($comp_codes)-1){
				$filter .= "OR ";
			}
		}
		$query = 'SELECT TemplateId, Description, CompCode, StatusCode, CreatedBy, FacultyId, YrLevel FROM  tb_templatehdg  WHERE ( '. $filter. ' )AND SY = '.$sy.' ORDER BY Description';
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($tempid,$desc, $compcode, $status, $author, $fid, $level);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['id']=$tempid;
				$results[$index]['desc']=$desc;
				$results[$index]['compcode']=$compcode;
				$results[$index]['status']=$status;
				$results[$index]['level']=$level;
				$results[$index]['author']=$author;
				$results[$index]['fid']=$fid;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	//MS Get Template
	public function ms_get_template(){
		$results =array();
		$sql = "SELECT TemplateId, Description FROM  tb_templatehdg ";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['id']=$row[0];
				$results[$index]['desc']=$row[1];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}
	//Get Template Details
	public function get_template_details($tempid){
		$results = array();
		$query = "SELECT RowNumber, ClassCode, Percentage FROM tb_templatedtl WHERE TemplateId ='$tempid' ORDER BY RowNumber ASC";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($rownum, $ccode, $perc);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['rownum']=$rownum;
				$results[$index]['ccode']=$ccode;
				$results[$index]['perc']=$perc;
				$index+=1;	
			}
			$stmt->close();		
		}
		$r['components']=$results;
		$query=	"SELECT ColNumber, HeaderName, ClassCode, Base FROM tb_templatemes WHERE (TemplateId ='$tempid') ORDER BY ColNumber ASC";
		$results2 = array();
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($colnum, $hdr, $ccode, $base);
			$index=0;
			while($stmt->fetch()){
				$results2[$index]['colnum']=$colnum;
				$results2[$index]['hdr']=$hdr;
				$results2[$index]['ccode']=$ccode;
				$results2[$index]['base']=$base;
				$index+=1;	
			}
			$stmt->close();		
		}
		$r['measurables']=$results2;
		return $r;		
	}
	public function check_tname($tname){
		$count =0 ;
		$sql = "SELECT COUNT(*) FROM  tb_templatehdg WHERE Description = '$tname' ";
		if ($stmt = $this->db_connection->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($count);
				$stmt->fetch();
				$stmt->close();
		}
		return $count;		
	}
	//MS Get Template Details
	public function ms_get_template_details($tempid){
		$results =array();
		$sql = "SELECT RowNumber, ClassCode, Percentage FROM tb_templatedtl WHERE TemplateId ='$tempid'";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['rownum']=$row[0];
				$results[$index]['ccode']=$row[1];
				$results[$index]['perc']=$row[2];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}
	public function post_tmplt($tid){
		$sql = "UPDATE tb_templatehdg SET StatusCode = 'A' WHERE TemplateId='$tid'";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
		
	}
	//Get Students Enrolled
	public function get_stud_nrol($compcode, $seccode, $sy, $isreverse = -1){
		$compcode = trim($compcode);
		$results =array();
		
		$sql = "SELECT	nrol_masterstud.Sno    , nrol_masterstud.LastName    , nrol_masterstud.FirstName    , nrol_masterstud.MiddleName    , nrol_masterstud.Gender  , nrol_enrolsec.Status  ";
		$sql.="FROM    erb_stsn.nrol_enrolsec    INNER JOIN erb_stsn.nrol_masterstud         ON (nrol_enrolsec.Sno = nrol_masterstud.Sno) ";
		$sql .="WHERE (nrol_enrolsec.SectionCode ='$seccode'    AND nrol_enrolsec.SY ='$sy') ORDER BY nrol_masterstud.Gender DESC, nrol_masterstud.LastName ";
		$sql .= $isreverse == -1 ? "ASC":"ASC";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $lastname, $firstname, $middle, $gender, $status);
			$index=0;
			while($stmt->fetch()){
				if($middle==null){
					$middle =" ";
				}
				if($lastname==null){
					$lastname = '{empty}';
				}
				if($firstname==null){
					$firstname = '{empty}';
				}
				$results[$index]['sno']=$sno;
				$results[$index]['fullname']= strtoupper(utf8_encode($lastname)).', '.strtoupper(utf8_encode($firstname)).' '.strtoupper(utf8_encode($middle[0]));
				$results[$index]['status']=$status;
				$results[$index]['gender']=$gender;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	public function get_class_list($sy, $deptcode, $seccode){
		$results =array();
		$sql ="SELECT    DISTINCT( nrol_enrollsubj.Sno)    , nrol_masterstud.LastName    , nrol_masterstud.FirstName    , nrol_masterstud.MiddleName, nrol_masterstud.Gender, nrol_enrollsubj.Status  ";
		$sql .="FROM   erb_stsn.nrol_currhdr    INNER JOIN erb_stsn.nrol_currdtl         ON (nrol_currhdr.CurriculumId = nrol_currdtl.CurriculumId)    INNER JOIN erb_stsn.tb_mastersection ";
        $sql .="ON (tb_mastersection.DeptCode = nrol_currhdr.ProgramId) AND (tb_mastersection.Level = nrol_currdtl.CurrYear)    LEFT JOIN erb_stsn.nrol_enrollsubj ";
		$sql .="ON (nrol_enrollsubj.SY = nrol_currhdr.SY) AND (nrol_enrollsubj.CompCode = nrol_currdtl.CompCode) AND (nrol_enrollsubj.SectionCode = tb_mastersection.SectionCode) ";
		$sql .="INNER JOIN erb_stsn.nrol_masterstud         ON (nrol_masterstud.Sno = nrol_enrollsubj.Sno) ";
		$sql .="WHERE (nrol_currhdr.SY =$sy    AND nrol_currhdr.ProgramId ='$deptcode'    AND tb_mastersection.SectionCode ='$seccode') ORDER BY nrol_masterstud.LastName DESC";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $lastname, $firstname, $middle, $gender, $status);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$results[$index]['fullname']= utf8_encode($lastname).', '.$firstname.' '.$middle[0];
				$results[$index]['status']=$status;
				//$results[$index]['desc']=$desc;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	public function ms_get_stud_nrol($compcode, $seccode, $sy, $isreverse = -1){
		$results =array();
		/*$sql = "SELECT TOP 50 dbo.nrol_enrollsubj.sno, dbo.nrol_enrollsubj.CompCode, dbo.nrol_enrollsubj.SY, dbo.nrol_masterstud.LastName, ";
		$sql .="dbo.nrol_masterstud.FirstName, dbo.nrol_masterstud.MiddleName, dbo.nrol_masterstud.Gender, dbo.nrol_enrollsubj.Status ";
		$sql .="FROM  dbo.nrol_enrollsubj INNER JOIN dbo.nrol_masterstud ON dbo.nrol_enrollsubj.sno = dbo.nrol_masterstud.Sno ";
		//$sql .="WHERE (dbo.nrol_enrollsubj.CompCode = '$compcode') AND (dbo.nrol_enrollsubj.SectionCode = '$seccode') AND (dbo.nrol_enrollsubj.SY = '$sy')";
		$sql .="WHERE (dbo.nrol_enrollsubj.SectionCode = '$seccode') AND (dbo.nrol_enrollsubj.SY = '$sy')";
		$sql .="ORDER BY dbo.nrol_masterstud.LastName DESC";
		*/
		$sql ="SELECT dbo.nrol_enrollsubj.sno, dbo.nrol_enrollsubj.SectionCode, dbo.nrol_enrollsubj.SY, dbo.nrol_masterstud.LastName, dbo.nrol_masterstud.FirstName, ";
        $sql.= "dbo.nrol_masterstud.MiddleName, dbo.nrol_masterstud.Gender, dbo.nrol_enrollsubj.Status ";
		$sql.="FROM dbo.nrol_enrollsubj INNER JOIN";
        $sql.=" dbo.nrol_masterstud ON dbo.nrol_enrollsubj.sno = dbo.nrol_masterstud.Sno ";
		$sql .="WHERE     (dbo.nrol_enrollsubj.SectionCode = '$seccode') AND (dbo.nrol_enrollsubj.SY = '$sy') ORDER BY dbo.nrol_masterstud.LastName  ";
		$sql .=$isreverse==-1? "DESC" : "ASC";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['sno']=$row[0];
				//$results[$index]['compcode']=$row[1];
				//$results[$index]['sy']=$row[2];
				//$results[$index]['lastname']=$row[3];
				//$results[$index]['firstname']=$row[4];
				//$results[$index]['middlename']=$row[5];
				$lastname = utf8_encode($row[3]).'';
				$firstname = $row[4].'';
				$middle = $row[5][0];
				$results[$index]['fullname']=$lastname.', '.$firstname.' '.$middle;
				$results[$index]['status']=$row[7];
				//$results[$index]['gender']=$row[6];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}
	public function save_rawscore($sno, $compcode, $seccode, $hdr, $period, $sy, $rawscore){
		$sql =  "SELECT COUNT(Sno) FROM nrol_rawscore";
		$sql .= " WHERE     (Sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND ";
		$sql .= "(HeaderName = '$hdr') AND (Period = $period) AND (SY = $sy)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($results);
			$stmt->fetch();
			$stmt->close();		
		}
		if((bool)$results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO nrol_rawscore(Sno, CompCode, SectionCode, HeaderName, Period, SY, RawScore)";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode', '$hdr', $period, $sy, $rawscore)";
			if ($stmt1 = $this->db_connection->prepare($sql)) {
				$stmt1->execute();
				$stmt1->fetch();
				$stmt1->close();
				$err='No error';
			}else{
				$err='Could not INSERT new data';
			}
		}else{
			$sql =  "UPDATE  nrol_rawscore ";
			$sql .= "SET   RawScore = $rawscore ";
			$sql .= "WHERE      Sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode' AND HeaderName ='$hdr' AND Period =$period AND SY =$sy";
			if ($stmt2 = $this->db_connection->prepare($sql)) {
				$stmt2->execute();
				$stmt2->fetch();
				$stmt2->close();		
			}
		}
		$r['results'] = $results;
		return  $r;
	}
	public function save_conduct($sno, $compcode, $seccode, $hdr, $period, $sy, $rawscore){
		$sql =  "SELECT COUNT(Sno) FROM nrol_conduct";
		$sql .= " WHERE     (Sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND ";
		$sql .= "(HeaderName = '$hdr') AND (Period = $period) AND (SY = $sy)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($results);
			$stmt->fetch();
			$stmt->close();		
		}
		if((bool)$results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO nrol_conduct(Sno, CompCode, SectionCode, HeaderName, Period, SY, RawScore)";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode', '$hdr', $period, $sy, $rawscore)";
			if ($stmt1 = $this->db_connection->prepare($sql)) {
				$stmt1->execute();
				$stmt1->fetch();
				$stmt1->close();
				$err='No error';
			}else{
				$err='Could not INSERT new data';
			}
		}else{
			$sql =  "UPDATE  nrol_conduct ";
			$sql .= "SET   RawScore = $rawscore ";
			$sql .= "WHERE      Sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode' AND HeaderName ='$hdr' AND Period =$period AND SY =$sy";
			if ($stmt2 = $this->db_connection->prepare($sql)) {
				$stmt2->execute();
				$stmt2->fetch();
				$stmt2->close();		
			}
		}
		$r['results'] = $results;
		return  $r;
	}
	public function save_equivalent($sno, $compcode, $seccode, $hdr, $period, $sy, $equivalent){
		$sql =  "SELECT sno, CompCode, SectionCode, HeaderName, Period, SY, Equivalent FROM nrol_equivalent";
		$sql .= " WHERE     (sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND ";
		$sql .= "(HeaderName = '$hdr') AND (Period = $period) AND (SY = $sy)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$results = mssql_fetch_assoc(mssql_query($sql));
		if($results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO nrol_equivalent(sno, CompCode, SectionCode, HeaderName, Period, SY, Equivalent)";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode', '$hdr', $period, $sy, $equivalent)";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}else{
			$sql =  "UPDATE  nrol_equivalent ";
			$sql .= "SET   Equivalent = $equivalent ";
			$sql .= "WHERE      sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode'AND HeaderName ='$hdr' AND Period =$period AND SY =$sy";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}
		return  $results;
	}
	public function save_summary($sno, $compcode, $seccode, $hdr, $period, $sy, $summary){
		$sql =  "SELECT sno, CompCode, SectionCode, HeaderName, Period, SY, Summary FROM nrol_summary";
		$sql .= " WHERE     (sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND ";
		$sql .= "(HeaderName = '$hdr') AND (Period = $period) AND (SY = $sy)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$results = mssql_fetch_assoc(mssql_query($sql));
		if($results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO nrol_summary (sno, CompCode, SectionCode, HeaderName, Period, SY, Summary)";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode', '$hdr', $period, $sy, $summary)";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}else{
			$sql =  "UPDATE  nrol_summary ";
			$sql .= "SET   Summary = $summary ";
			$sql .= "WHERE      sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode'AND HeaderName ='$hdr' AND Period =$period AND SY =$sy";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}
		return  $results;
	}
	public function save_overall($sno, $compcode, $seccode, $hdr, $period, $sy, $overall){
		$sql =  "SELECT sno, CompCode, SectionCode, HeaderName, Period, SY, Overall FROM nrol_overall";
		$sql .= " WHERE     (sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND ";
		$sql .= "(HeaderName = '$hdr') AND (Period = $period) AND (SY = $sy)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$results = mssql_fetch_assoc(mssql_query($sql));
		if($results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO nrol_overall (sno, CompCode, SectionCode, HeaderName, Period, SY, Overall)";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode', '$hdr', $period, $sy, $overall)";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}else{
			$sql =  "UPDATE  nrol_overall ";
			$sql .= "SET   Overall = $overall ";
			$sql .= "WHERE      sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode'AND HeaderName ='$hdr' AND Period =$period AND SY =$sy";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}
		return  $results;
	}
	public function save_attendance($sno, $seccode, $period, $sy, $hdr, $val, $mo){
		$sql =  "SELECT COUNT(Sno) FROM nrol_attendance";
		$sql .= " WHERE     (Sno = '$sno') AND (SectionCode = '$seccode') AND ";
		$sql .= "(Period = $period) AND (SY = $sy) AND (Month = $mo)";
		if($hdr=="PRES"){
			$field="Present";
		}
		else if($hdr=="LATE"){
			$field="Tardy";
		}
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($results);
			$stmt->fetch();
			$stmt->close();		
		}
		if((bool)$results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO nrol_attendance (Sno,  SectionCode, Period, SY, $field, Month) ";
			$sql .=	"VALUES ('$sno', '$seccode', $period, $sy, $val, $mo)";
			if ($stmt1 = $this->db_connection->prepare($sql)) {
				$stmt1->execute();
				$stmt1->fetch();
				$stmt1->close();
				$err='No error';
			}else{
				$err='Could not INSERT new data';
			}
		}else{
			$sql =  "UPDATE  nrol_attendance ";
			$sql .= "SET   $field = $val ";
			$sql .= "WHERE Sno ='$sno' AND SectionCode ='$seccode' AND Period =$period AND SY =$sy AND Month = $mo";
			if ($stmt2 = $this->db_connection->prepare($sql)) {
				$stmt2->execute();
				$stmt2->fetch();
				$stmt2->close();		
			}
		}
		$r['results'] = $results;
		return  $r;
	}
	public function get_rawscore($compcode, $seccode, $period, $sy){
		$sql =  "SELECT sno, CompCode, SectionCode, HeaderName, RawScore FROM nrol_rawscore ";
		$sql .= " WHERE   (CompCode = '$compcode') AND (SectionCode = '$seccode') AND ";
		$sql .= "(Period = $period) AND (SY = $sy)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$query = mssql_query($sql,$con);
		$index=0;
		do{
			while($row=mssql_fetch_row($query)){
				$results[$index]['sno']=$row[0];
				$results[$index]['compcode']=$row[1];
				$results[$index]['seccode']=$row[2];
				$results[$index]['hdr']=$row[3];
				$results[$index]['rawscore']=$row[4];
				$index+=1;
			}
		}while(mssql_next_result($query));
		return $results;
	}

	public function get_equivalent($compcode, $seccode, $period, $sy){
		$results =array();
		$sql="CALL erb_compute_equivalent('$compcode', '$seccode', $sy, $period)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $hdr, $ccode, $equivalent);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$results[$index]['hdr']=$hdr;
				$results[$index]['equivalent']= $equivalent==null? 'ING':$equivalent;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	
	}
	public function ms_get_equivalent($compcode, $seccode, $period, $sy){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$stmt=mssql_init("sp_Compute", $con);
		mssql_bind($stmt,"@CompCode",$compcode,SQLVARCHAR);
		mssql_bind($stmt,"@SectionCode",$seccode,SQLVARCHAR);
		mssql_bind($stmt,"@SY",$sy,SQLINT4);
		mssql_bind($stmt,"@Period",$period,SQLINT4);
		$r = mssql_execute($stmt);
		$equivalent = array();
		$result = array();
		while($row=mssql_fetch_array($r)){
			$result['sno']= $row['sno'];
			$result['hdr']= $row['hdr'];
			$result['equivalent']= $row['equivalent']==null? 'ING':$row['equivalent'];
			array_push($equivalent, $result);			
		}
		return $equivalent;
	}
	public function get_summary($compcode, $seccode, $period, $sy){
		$results =array();
		$sql="CALL erb_compute_summary('$compcode', '$seccode', $sy, $period)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $hdr, $summary, $average);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$results[$index]['hdr']=$hdr;
				$results[$index]['summary']= $summary;
				$index+=1;	
			}
			$stmt->close();		
		}else{
		$results ='Err EXEC: '. $sql;
		}
		return $results;
	}
	public function ms_get_summary($compcode, $seccode, $period, $sy){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$stmt=mssql_init("sp_Summary", $con);
		mssql_bind($stmt,"@CompCode",$compcode,SQLVARCHAR);
		mssql_bind($stmt,"@SectionCode",$seccode,SQLVARCHAR);
		mssql_bind($stmt,"@SY",$sy,SQLINT4);
		mssql_bind($stmt,"@Period",$period,SQLINT4);
		$r = mssql_execute($stmt);
		$summary = array();
		$result = array();
		while($row=mssql_fetch_array($r)){
			$result['sno']= $row['sno'];
			$result['hdr']= $row['hdr'];
			$result['summary']= $row['summary'];
			array_push($summary, $result);			
		}
		return $summary;
	}
	public function get_overall($compcode, $seccode, $sy){
		$results = array();
		$sql  = "SELECT     sno, FirstGrd as fr, SecondGrd as se, ThirdGrd as th, FourthGrd as fo, FinalGrd as fg ";
		$sql .="FROM  tb_FinalGrade ";
		$sql .="WHERE    (SY = $sy) AND (CompCode ='$compcode') AND (SectionCode = '$seccode')";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $fr, $se, $th, $fo, $fg);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']= $sno;
				$results[$index]['fr']= $fr;
				$results[$index]['se']= $se;
				$results[$index]['th']= $th;
				$results[$index]['fr']= $fr;
				$results[$index]['fg']= $fg;
				$index+=1;	
			}
			$stmt->close();		
		}else{
		$results ='Err EXEC: '. $sql;
		}
		return $results;
	}
	public function ms_get_overall($compcode, $seccode, $sy){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$stmt=mssql_init("sp_FinalGrade", $con);
		mssql_bind($stmt,"@CompCode",$compcode,SQLVARCHAR);
		mssql_bind($stmt,"@SectionCode",$seccode,SQLVARCHAR);
		mssql_bind($stmt,"@SY",$sy,SQLINT4);
		$r = mssql_execute($stmt);
		$overall = array();
		$result = array();
		while($row=mssql_fetch_array($r)){
			$result['sno']= $row['sno'];
			$result['fr']= $row['fr'];
			$result['se']= $row['se'];
			$result['th']= $row['th'];
			$result['fr']= $row['fr'];
			$result['fg']= $row['fg'];
			array_push($overall, $result);			
		}
		return $overall;
	}
	public function getraw_details($period, $sy, $seccode, $compcode){
		$results =array();
		$sql = "SELECT     nrol_rawscore.RawScore, nrol_rawscore.HeaderName, nrol_rawscore.Sno, nrol_measitem.ColNumber ";
		$sql.= "FROM  nrol_measitem INNER JOIN ";
        $sql.="nrol_rawscore ON nrol_measitem.HeaderName = nrol_rawscore.HeaderName AND ";
        $sql.="nrol_measitem.CompCode = nrol_rawscore.CompCode AND ";
		$sql.="nrol_measitem.SectionCode = nrol_rawscore.SectionCode AND nrol_measitem.SY = nrol_rawscore.SY ";
		$sql.="WHERE (nrol_rawscore.SY = $sy) AND (nrol_rawscore.SectionCode = '$seccode') AND (nrol_rawscore.CompCode = '$compcode')  AND(nrol_rawscore.Period='$period') ";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($score, $hdr, $sno, $col);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$results[$index]['hdr']=$hdr;
				$results[$index]['col']=$col;
				switch($score){
					case -3:
						$results[$index]['score']='IGN';
						break;
					default:
						$results[$index]['score']=$score;
						break;
				}
				$index+=1;	
			}
			$stmt->close();	
			$err='No error'	;
		}else{
			$err='Could not SELECT data';
		}
		return $results;
	}
	public function getconduct_details($period, $sy, $seccode, $compcode){
		$results =array();
		$sql = "SELECT    nrol_conduct.Sno , nrol_conduct.RawScore    , nrol_conduct.HeaderName ";
		$sql .="FROM    erb_stsn.tb_conducttemplate    INNER JOIN erb_stsn.nrol_conduct         ON (tb_conducttemplate.HeaderName = nrol_conduct.HeaderName) ";
		$sql .="WHERE (nrol_conduct.SY ='$sy'    AND nrol_conduct.Period ='$period'    AND nrol_conduct.CompCode ='$compcode'    AND nrol_conduct.SectionCode ='$seccode')";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $score, $hdr);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$results[$index]['hdr']=$hdr;
				switch($score){
					case -3:
						$results[$index]['score']='IGN';
						break;
					default:
						$results[$index]['score']=$score;
						break;
				}
				$index+=1;	
			}
			$stmt->close();	
			$err='No error'	;
		}else{
			$err='Could not SELECT data';
		}
		return $results;
	}
	//Get Conduct template
	public function get_conduct_tmplt($deptcode, $level, $version, $sy){
		$results = array();
		$sql  ="SELECT  HeaderName, HeaderDescription, MaxGrade, MinGrade ";
		$sql .=" FROM tb_conducttemplate ";
		$sql .=" WHERE (DeptCode = '$deptcode') AND (YrLevel LIKE '%$level%') AND (Version = $version) AND (SY = $sy) ";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($hdr, $desc, $max, $min);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['hdr']=$hdr;
				$results[$index]['desc']=$desc;
				$results[$index]['maxG']=$max;
				$results[$index]['minG']=$min;
				$index+=1;	
			}
			$stmt->close();	
		}
		return $results;
	}
	//Get Attendace template
	public function get_attendance_tmplt($sy, $period){
		$results2 = array();
		$sql1 = "SELECT HeaderName, HeaderDescription FROM tb_attendance_hdr";
		if ($stmt1 = $this->db_connection->prepare($sql1)) {
			$stmt1->execute();
			$stmt1->bind_result($hdr, $desc);
			$index=0;
			while($stmt1->fetch()){
				$results1[$index]['hdr']=$hdr;
				$results1[$index]['desc']=$desc;
				$index+=1;	
			}
			$stmt1->close();		
		}
		$sql2="SELECT Month, Acad_Days FROM nrol_academic_days WHERE SY=$sy AND Period=$period";
		if ($stmt2 = $this->db_connection->prepare($sql2)) {
			$stmt2->execute();
			$stmt2->bind_result($month, $days);
			$index=0;
			while($stmt2->fetch()){
				$results2[$index]['month']=$month;
				$results2[$index]['days']=$days;
				$index+=1;	
			}
			$stmt2->close();		
		}
		if(count($results2)){
			for($i = 0 ;$i<count($results2); $i++){
				$results2[$i]['hdrs']= $results1;
			}
		}
		return $results2;
	}
	public function get_attendance($seccode, $sy, $period){
		$results = array();
		$sql = "SELECT Sno, Present, Tardy, Month FROM nrol_attendance WHERE (SectionCode ='$seccode' AND SY =$sy AND Period =$period) ";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $present, $tardy, $month);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$results[$index]['present']=$present;
				$results[$index]['tardy']=$tardy;
				$results[$index]['month']=$month;
				$index+=1;	
			}
			$stmt->close();		
			return $results;
		}
	}
	public function unpost_grade($period, $sy, $seccode, $compcode){
		$query = "DELETE FROM nrol_posted WHERE Period = $period AND SY = $sy AND SectionCode = '$seccode' AND CompCode= '$compcode' ";
		$results['query']=$query;
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();	
			$results['msg'] = 'Unpost grade successful';
		}else{
			$results['msg'] = 'Could not unpost';
		}
		return $results;
	}
	public function sendtocgs($sno, $grade, $period, $sy, $seccode, $compcode){
		$count = 0;
		$results=array();
		switch($period){
			case 1:
				$p="FirstGrd";
				break;
			case 2:
				$p="SecondGrd";
				break;
			case 3:
				$p="ThirdGrd";
				break;
			case 4:
				$p="FourthGrd";
				break;
		}
		$sql =  "SELECT COUNT(Sno )FROM tb_finalgrade";
		$sql .= " WHERE     (Sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND (SY = $sy)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();		
		}
		if($count==0){
			$sql  = "INSERT ";
			$sql .=	"INTO tb_finalGrade (Sno, CompCode, SectionCode, SY, $p) ";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode',  $sy, $grade)";
			if ($stmt = $this->db_connection->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($results);
				$stmt->fetch();
				$stmt->close();		
			}
			$results['sql']=$sql;
		}else{
			$sql =  "UPDATE tb_finalGrade ";
			$sql .= "SET   $p = $grade ";
			$sql .= "WHERE      Sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode'AND SY =$sy";
			if ($stmt = $this->db_connection->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($results);
				$stmt->fetch();
				$stmt->close();		
			}
			$results['sql']=$sql;
		}
		return $results;
	}
	//POSTING module
	public function ms_post_grade($sno, $grade, $period, $sy, $seccode, $compcode){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		switch($period){
			case 1:
				$p="FirstGrd";
				break;
			case 2:
				$p="SecondGrd";
				break;
			case 3:
				$p="ThirdGrd";
				break;
			case 4:
				$p="FourthGrd";
				break;
		}
		$sql =  "SELECT sno FROM tb_FinalGrade";
		$sql .= " WHERE     (sno = '$sno') AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND (SY = $sy)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$results = mssql_fetch_assoc(mssql_query($sql));
		if($results==false){
			$sql  = "INSERT ";
			$sql .=	"INTO tb_FinalGrade (sno, CompCode, SectionCode, SY, $p) ";
			$sql .=	"VALUES ('$sno', '$compcode', '$seccode',  $sy, $grade)";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}else{
			$sql =  "UPDATE tb_FinalGrade ";
			$sql .= "SET   $p = $grade ";
			$sql .= "WHERE      sno ='$sno' AND CompCode ='$compcode'AND SectionCode ='$seccode'AND SY =$sy";
			mssql_select_db('egb',$con);
			$results = mssql_query($sql);
		}
		return $results;
	}
	public function hassent( $period, $sy, $seccode, $compcode){
		$sql = "INSERT INTO nrol_Posted (SY, Period, CompCode, SectionCode, StatusCode) ";
		$sql .="VALUES ($sy, $period, '$compcode', '$seccode', 1) ";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
	}
	public function ms_hasPosted( $period, $sy, $seccode, $compcode){
		$sql = "INSERT INTO nrol_Posted (SY, Period, CompCode, SectionCode) ";
		$sql .="VALUES ($sy, $period, '$compcode', '$seccode') ";
		mssql_select_db('egb',$con);
		$results = mssql_query($sql);
	}
	public function isPosted($period, $sy, $seccode, $compcode){
		$sql =  "SELECT Period FROM nrol_Posted";
		$sql .= " WHERE     (Period = $period) AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND (SY = $sy)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($results);
			$stmt->fetch();
			$stmt->close();		
		}
		return $results!=false;
	}
	public function ms_isPosted($period, $sy, $seccode, $compcode){
		$sql =  "SELECT Period FROM nrol_Posted";
		$sql .= " WHERE     (Period = $period) AND (CompCode = '$compcode') AND (SectionCode = '$seccode') AND (SY = $sy)";
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$results = mssql_fetch_assoc(mssql_query($sql));
		return $results!=false;
	}
	public function post_grade($period, $sy, $seccode, $compcode){
		$sql = "UPDATE nrol_Posted ";
		$sql .="SET StatusCode=3 WHERE SY = $sy AND  Period = $period AND CompCode = '$compcode' AND  SectionCode ='$seccode'";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
		return $sql;
	}
	public function getExcelReady($period, $sy, $seccode, $compcode){
		$results = array();
		$sql  ="SELECT    nrol_rawscore.Sno    , nrol_rawscore.RawScore    , nrol_measitem.ColNumber ";
		$sql .="FROM    erb_stsn.nrol_rawscore    INNER JOIN erb_stsn.nrol_measitem         ON (nrol_rawscore.SY = nrol_measitem.SY) AND (nrol_rawscore.CompCode = nrol_measitem.CompCode) AND (nrol_rawscore.HeaderName = nrol_measitem.HeaderName) ";
		$sql.="WHERE (nrol_rawscore.SY =$sy AND nrol_rawscore.Period =$period AND nrol_rawscore.CompCode ='$compcode' AND nrol_rawscore.SectionCode ='$seccode')";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($sno, $rawscore, $colnum);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']= $sno;
				$results[$index]['rawscore']= $rawscore;
				$results[$index]['colnum']= $colnum;
				$index+=1;	
			}
			$stmt->close();		
		}
		return $results;
	}
	public function ms_getExcelReady($period, $sy, $seccode, $compcode){
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		$stmt=mssql_init("sp_ExcelReady", $con);
		mssql_bind($stmt,"@CompCode",$compcode,SQLVARCHAR);
		mssql_bind($stmt,"@SectionCode",$seccode,SQLVARCHAR);
		mssql_bind($stmt,"@Period",$period,SQLINT4);
		mssql_bind($stmt,"@SY",$sy,SQLINT4);
		$r = mssql_execute($stmt);
		$grades = array();
		$result = array();
		while($row=mssql_fetch_array($r)){
			$result['sno']= $row['sno'];
			$result['rawscore']= $row['rawscore'];
			$result['colnum']= $row['colnum'];
			array_push($grades, $result);			
		}
		return $grades;
	}
	public function get_sublist($currid){		
		$sql ="SELECT     tb_subject.Nomenclature , nrol_currdtl.CurrYear,  nrol_currdtl.CompCode ";
		$sql .= "FROM    erb_stsn.nrol_currdtl    INNER JOIN erb_stsn.tb_subject         ON (nrol_currdtl.CompCode = tb_subject.CompCode) ";
		$sql .= "INNER JOIN erb_stsn.nrol_currhdr         ON (nrol_currhdr.CurriculumId = nrol_currdtl.CurriculumId) ";
		$sql .="WHERE (nrol_currhdr.CurriculumId ='$currid') ORDER BY tb_subject.Nomenclature ASC";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($nomen, $curryear, $compcode);
			$index=0;
			$cur_nomen = $prev_nomen ="";
			$subjects =array();
			$subj_dtl = array();
			while($stmt->fetch()){
				$cur_nomen	= $nomen;
				if($cur_nomen != $prev_nomen){
					$subjects[$index]['nomen']=$cur_nomen;
					$subj_dtl = array();
					$index+=1;
				}
				$dtl['year'] =$curryear;
				$dtl['compcode'] = $compcode;
				array_push($subj_dtl, $dtl);
				$subjects[$index-1]['subj_dtl'] =$subj_dtl;
				$prev_nomen = $cur_nomen;
			}
			$stmt->close();		
		}
		return json_encode($subjects);
		
	}
	public function clean_tmplthdg($tmplt_name){
		$sql ="DELETE FROM tb_templatehdg WHERE Description='$tmplt_name'";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
		
	}
	public function upd8_tmplt_ctr(){
		$sql = "UPDATE tb_sysdefa SET template_ctr = template_ctr + 1";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
	}
	public function save_tmplthdg($tmplt_name,$fid,$author, $compcodes, $yrlvls, $sy, $status, $deptcode){
		$tmp_id=0;
		$this->upd8_tmplt_ctr();
		$sql1="SELECT template_ctr FROM tb_sysdefa ";
			if ($stmt1 = $this->db_connection->prepare($sql1)) {
				$stmt1->execute();
				$stmt1->bind_result($tmp_id);
				$stmt1->fetch();
				$stmt1->close();
			}
			$i=0;
			$tmplt['query'][$i]=$yrlvls. '~'. count($yrlvls);
			for($ctr =0; $ctr < count($yrlvls); $ctr++){
				$tname =$tmplt_name;
				$sql ="INSERT INTO tb_templatehdg (TemplateId, Description, CreatedBy, FacultyId, CompCode, YrLevel,  DeptCode, SY, StatusCode) VALUES($tmp_id, '$tname', '$author', '$fid', '$compcodes[$ctr]', $yrlvls[$ctr],  '$deptcode', $sy, '$status')";
				$i+=1;
				$tmplt['query'][$i]=$sql;
				if ($stmt = $this->db_connection->prepare($sql)) {
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();			
				}
			}
		$tmplt['id']=$tmp_id;
		return 	$tmplt;
	}
	public function clean_tmpltdtl($tmp_id){
		$sql ="DELETE FROM tb_templatedtl WHERE TemplateId='$tmp_id'";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
	}
	public function save_tmpltdtl($tmp_id, $rownum, $ccode, $percnt){
		$sql ="INSERT INTO tb_templatedtl (TemplateId, RowNumber, ClassCode, Percentage) VALUES($tmp_id, $rownum, '$ccode', $percnt)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
	}
		public function clean_tmplmes($tmp_id){
		$sql ="DELETE FROM  tb_templatemes WHERE TemplateId='$tmp_id'";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
	}
	public function save_tmplmes($tmp_id, $colnum, $ccode, $hdr, $base){
		$sql ="INSERT INTO tb_templatemes (TemplateId, ColNumber, ClassCode, HeaderName, Base) VALUES ($tmp_id, $colnum, '$ccode', '$hdr', $base)";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $sql;
	}
	public function get_advisory($faculty_id){
		$results=array();
		$query  ="SELECT    nrol_advisory_class.SectionCode    , tb_mastersection.Level    , tb_mastersection.DeptCode    , tb_mastersection.Section ,  nrol_advisory_class.SY ";
		$query .="FROM    erb_stsn.nrol_advisory_class    INNER JOIN erb_stsn.tb_mastersection         ON (nrol_advisory_class.SectionCode = tb_mastersection.SectionCode) ";
		$query .="WHERE (nrol_advisory_class.FacultyId =$faculty_id) ";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($seccode, $lvl, $deptcode,$sec, $sy);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['level'] =$lvl;
				$results[$index]['dept']=$deptcode;
				$results[$index]['sec_code']= $seccode;
				$results[$index]['sec']= $sec;
				$results[$index]['sy']= $sy;
				$index+=1;
			}
			$stmt->close();	
		}
		
		return $results;
	}
	public function get_cgs_scores($sy, $period, $seccode){
		$query  ="SELECT    tb_finalgrade.Sno    ,tb_finalgrade.FirstGrd     , nrol_currdtl.CompCode ";
		$query .="FROM    erb_stsn.nrol_advisory_class    INNER JOIN erb_stsn.tb_mastersection         ON (nrol_advisory_class.SectionCode = tb_mastersection.SectionCode) ";
		$query .="INNER JOIN erb_stsn.nrol_currhdr         ON (tb_mastersection.DeptCode = nrol_currhdr.ProgramId)    INNER JOIN erb_stsn.nrol_currdtl         ON (tb_mastersection.Level = nrol_currdtl.CurrYear) AND (nrol_currhdr.CurriculumId = nrol_currdtl.CurriculumId) ";
		$query .="INNER JOIN erb_stsn.tb_subject         ON (tb_subject.CompCode = nrol_currdtl.CompCode)    LEFT JOIN erb_stsn.tb_finalgrade         ON (tb_finalgrade.SectionCode = nrol_advisory_class.SectionCode) AND (tb_finalgrade.CompCode = tb_subject.CompCode) ";
		$query .="WHERE (nrol_advisory_class.SY =$sy    AND nrol_advisory_class.Period =$period    AND nrol_advisory_class.SectionCode ='$seccode')";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($sno, $grade, $comp_code);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['comp_code']=$comp_code;
				$results[$index]['grade']=$grade;
				$results[$index]['sno']=$sno;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	public function list_get($item){
	
		if($item=="country"){
			$sql ="SELECT id, country FROM lk_country ORDER BY seq ASC";
			if ($stmt = $this->db_connection->prepare($sql)) {			
				$stmt->execute();
				$stmt->bind_result($id, $country);
				$index=0;
				while($stmt->fetch()){
					$results[$index]['id']=$id;
					$results[$index]['c']=$results[$index]['country']=htmlspecialchars($country, ENT_QUOTES);
					$index+=1;
				}
				$stmt->close();		
			}
		}
		else if($item=="province"){
			$sql ="SELECT id, province, country_id FROM lk_province ORDER BY seq ASC";
			if ($stmt = $this->db_connection->prepare($sql)) {			
				$stmt->execute();
				$stmt->bind_result($id, $province, $country_id);
				$index=0;
				while($stmt->fetch()){
					$results[$index]['id']=$id;
					$results[$index]['c'] =$results[$index]['province']=htmlspecialchars($province, ENT_QUOTES);
					$results[$index]['fk']=$results[$index]['country_id']=$country_id;
					$index+=1;
				}
				$stmt->close();		
			}
		}
		else if($item=="municipality"){
			$sql ="SELECT id, municipality, province_id FROM lk_municipality ORDER BY municipality ASC";
			if ($stmt = $this->db_connection->prepare($sql)) {			
				$stmt->execute();
				$stmt->bind_result($id, $municipality, $province_id);
				$index=0;
				while($stmt->fetch()){
					$results[$index]['id']=$id;
					$results[$index]['c'] = $results[$index]['municipality']=htmlspecialchars($municipality, ENT_QUOTES);
					$results[$index]['fk'] = $results[$index]['province_id']=$province_id;
					
					$index+=1;
				}
				$stmt->close();		
			}
		}else if($item=="barangays"){
			$sql ="SELECT id, barangay, municipality_id FROM lk_barangay ORDER BY barangay ASC";
			if ($stmt = $this->db_connection->prepare($sql)) {			
				$stmt->execute();
				$stmt->bind_result($id, $barangay, $municipality_id);
				$index=0;
				while($stmt->fetch()){
					$results[$index]['id']=$id;
					$results[$index]['c'] = $results[$index]['barangay']=htmlspecialchars($barangay, ENT_QUOTES);
					$results[$index]['fk'] = $results[$index]['municipality_id']=$municipality_id;
					$index+=1;
				}
				$stmt->close();	
			}
		}else if($item=="sections"){
			$sql ="SELECT SectionCode, Section, Level, DeptCode FROM tb_mastersection ORDER BY Section ASC";
			if ($stmt = $this->db_connection->prepare($sql)) {			
				$stmt->execute();
				$stmt->bind_result($seccode, $section, $level, $deptcode);
				$index=0;
				while($stmt->fetch()){
					$results[$index]['seccode']=$seccode;
					$results[$index]['section']=$section;
					$results[$index]['level']=$level;
					$results[$index]['deptcode']=$deptcode;
					$index+=1;
				}
				$stmt->close();	
			}
		}else if($item=="faculties"){
			$sql ="SELECT * FROM tb_faculty201";
			if ($stmt = $this->db_connection->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($faculty_id, $last_name, $first_name, $middle_name);
				$index=0;
				while($stmt->fetch()){
					$results[$index]['label']=$last_name. ',' . $first_name . ' ' . $middle_name;
					$results[$index]['value']=$faculty_id;
					$index+=1;
				}
				$stmt->close();		
			}
		}
		return $results;
	}
	public function get_all_stud(){
		$results =array();
		$sno =0;
		$query ="SELECT Sno FROM nrol_masterstud";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($sno);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['sno']=$sno;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	public function get_stud201($sno){
			$sql ="SELECT   Sno, LastName, FirstName, MiddleName, Gender, SectionCode, Birthday, PlaceOfBirth, Citizenship, Religion, Landline, Mobile ";
			$sql .=", Home_Country, Home_Province, Home_Municipality, Home_Barangay, Home_Subdivision, Home_StreetNo, Home_ZipCode ";
			$sql .=", Mail_Country, Mail_Province, Mail_Municipality,Mail_Barangay, Mail_Subdivision, Mail_StreetNo, Mail_ZipCode ";
			$sql .="FROM    erb_stsn.nrol_masterstud WHERE (Sno ='$sno')";
			if ($stmt = $this->db_connection->prepare($sql)) {			
				$stmt->execute();
				$stmt->bind_result($snum, $lname, $fname, $mname, $gender, $seccode, $bday, $pob, $citizen, $reli, $land, $mob, $h_c, $h_p,$h_m, $h_b, $h_sb, $h_sn, $h_z, $m_c, $m_p,$m_m,$m_b, $m_sb, $m_sn, $m_z);
				$stmt->fetch();
				$results['sno']=$snum;
				$results['lname']=$lname; 
				$results['fname']=$fname; 
				$results['mname']=$mname; 
				$results['gender']=$gender; 
				$results['seccode']=$seccode; 
				$results['bday']=$bday; 
				$results['pob']=$pob; 
				$results['citizen']=$citizen; 
				$results['reli']=$reli; 
				$results['land']=$land; 
				$results['mob']=$mob; 
				$results['h_c']=$h_c; 
				$results['h_p']=$h_p; 
				$results['h_m']=$h_m; 
				$results['h_b']=$h_b; 
				$results['h_sb']=$h_sb; 
				$results['h_sn']=$h_sn; 
				$results['h_z']=$h_z; 
				$results['m_c']=$m_c; 
				$results['m_p']=$m_p; 
				$results['m_m']=$m_m; 
				$results['m_b']=$m_b; 
				$results['m_sb']=$m_sb; 
				$results['m_sn']=$m_sn; 
				$results['m_z']=$m_z;
				$stmt->close();	
			}
			return $results;
	}
	public function check_stud201($sno){
	$sql ="SELECT COUNT(Sno) FROM nrol_masterstud WHERE Sno = '$sno'";
		$count=0;
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();
		}
		return $count;
	}
	public function save_stud201($data){
		$sno = $data['sno'];
		$new = $this->check_stud201($sno);
		$lname=$data['lastname'];
		$fname=$data['firstname'];
		$mname=$data['middlename'];
		$sec_code=$data['section'];
		$gender=$data['gender'];
		$bday=$data['dob'];
		$pob=$data['pob'];
		$citizen=$data['citizen'];
		$reli=$data['religion'];
		$h_sn=$data['home_streetno'];
		$h_m=$data['home_muni'];
		$m_sn=$data['mail_streetno'];
		$m_m=$data['mail_muni'];
		$mob=$data['home_mobno'];
		$land=$data['home_landno'];
		if($new==0){
			$h_b=$data['home_brgy'];
			$h_sb=$data['home_subd'];
			$h_c=$data['home_coun'];
			$h_p=$data['home_prov'];
			$h_z=$data['home_zip'];
			$m_c=$data['mail_coun'];
			$m_p=$data['mail_prov'];
			$m_b=$data['mail_brgy'];		
			$m_sb=$data['mail_subd'];
			$m_z=$data['mail_zip'];
			$p_c=$data['parent_coun'];
			$p_p=$data['parent_prov'];
			$p_b=$data['parent_brgy'];		
			$p_sb=$data['parent_subd'];
			$p_z=$data['parent_zip'];
			$s_c=$data['parent2_coun'];
			$s_p=$data['parent2_prov'];
			$s_b=$data['parent2_brgy'];		
			$s_sb=$data['parent2_subd'];
			$s_z=$data['parent2_zip'];
		}
		$p_n = $data['parent_name'];
		$p_r = $data['parent_rel'];
		$p_o = $data['parent_occupation'];
		$p_m=$data['parent_muni'];
		$p_sn=$data['parent_streetno'];
		$s_n = $data['parent2_name'];
		$s_r = $data['parent2_rel'];
		$s_o = $data['parent2_occupation'];
		$s_m=$data['parent2_muni'];
		$s_sn=$data['parent2_streetno'];
		$ovrrd_by = $data['ovrrd_by'];
		$ip = $_SERVER['REMOTE_ADDR'];
		if($new==0){
				$query = "INSERT INTO nrol_masterstud (	Sno
														,LastName
														, FirstName
														, MiddleName
														, Gender
														, Birthday
														, PlaceOfBirth
														, Citizenship
														, Religion
														, Landline
														, Mobile
														, Home_Country
														, Home_Province
														, Home_Municipality
														, Home_Barangay
														, Home_Subdivision
														, Home_StreetNo
														, Home_ZipCode
														, Mail_Country
														, Mail_Province
														, Mail_Municipality
														, Mail_Barangay
														, Mail_Subdivision
														, Mail_StreetNo
														, Mail_ZipCode
														, Primary_Name
														, Primary_Relationship
														, Primary_Occupation
														, Primary_Country
														, Primary_Province
														, Primary_Municipality
														, Primary_Barangay
														, Primary_Subdivision
														, Primary_StreetNo
														, Primary_ZipCode
														, Secondary_Name
														, Secondary_Relationship
														, Secondary_Occupation
														, Secondary_Country
														, Secondary_Province
														, Secondary_Municipality
														, Secondary_Barangay
														, Secondary_Subdivision
														, Secondary_StreetNo
														, Secondary_ZipCode
														, DateCreated ) ";
				$query .="						VALUES(	'$sno',
														'$lname',
														'$fname',
														'$mname',
														'$gender',
														'$bday',
														'$pob',
														'$citizen',
														'$reli',
														'$land',
														'$mob',
														'$h_c',
														'$h_p',
														'$h_m',
														'$h_b',
														'$h_sb',
														'$h_sn',
														'$h_z',
														'$m_c',
														'$m_p',
														'$m_m',		
														'$m_b',
														'$m_sb',
														'$m_sn',
														'$m_z',
														'$p_n',
														'$p_r',
														'$p_o',
														'$p_c',
														'$p_p',
														'$p_m',		
														'$p_b',
														'$p_sb',
														'$p_sn',
														'$p_z',
														'$s_n',
														'$s_r',
														'$s_o',
														'$s_c',
														'$s_p',
														'$s_m',		
														'$s_b',
														'$s_sb',
														'$s_sn',
														'$s_z',
														 NOW() )";
			}else{
				$query = "INSERT INTO nrol_masterstud_temp
													(	Sno
														,LastName
														, FirstName
														, MiddleName
														, SectionCode
														, Gender
														, Birthday
														, PlaceOfBirth
														, Citizenship
														, Religion
														, Landline
														, Mobile
														, Home_Municipality
														, Home_StreetNo
														, Mail_Municipality
														, Mail_StreetNo
														, Primary_Name
														, Primary_Relationship
														, Primary_Occupation
														, Primary_Municipality
														, Primary_StreetNo
														, Secondary_Name
														, Secondary_Relationship
														, Secondary_Occupation
														, Secondary_Municipality
														, Secondary_StreetNo
														, OverrideBy
														, DateCreated
														, IP) ";
				$query .="						VALUES(	'$sno',
														'$lname',
														'$fname',
														'$mname',
														'$sec_code',
														'$gender',
														'$bday',
														'$pob',
														'$citizen',
														'$reli',
														'$land',
														'$mob',
														'$h_m',
														'$h_sn',
														'$m_m',		
														'$m_sn',
														'$p_n',
														'$p_r',
														'$p_o',
														'$p_m',		
														'$p_sn',
														'$s_n',
														'$s_r',
														'$s_o',
														'$s_m',		
														'$s_sn',
														'$ovrrd_by',
														 NOW() ,
														'$ip')";

			}
		
			
		
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		return $query;
	}
	public function get_subjects($deptcode, $gryrlvl){
		$sql ="SELECT nrol_currdtl.CompCode,  nrol_currdtl.Under    , tb_subject.Nomenclature   "; 
		$sql .="FROM    erb_stsn.nrol_currhdr    INNER JOIN erb_stsn.nrol_currdtl         ON (nrol_currhdr.CurriculumId = nrol_currdtl.CurriculumId)    LEFT JOIN erb_stsn.tb_subject  ON (nrol_currdtl.CompCode = tb_subject.CompCode) ";
		$sql .="WHERE (nrol_currhdr.ProgramId ='$deptcode'    AND nrol_currdtl.CurrYear =$gryrlvl) ";
		if ($stmt1 = $this->db_connection->prepare($sql)) {			
			$stmt1->execute();
			$stmt1->bind_result($compcode,$under, $nomen);
			$index=0;
			while($stmt1->fetch()){
				$results1[$index]['compcode']=$compcode;
				$results1[$index]['nomen']=$nomen;
				$results1[$index]['under']=$under;
				$index+=1;
			}
			$stmt1->close();		
		}
		return $results1;
	}
	public function enrol_subject($sno, $sy, $compcode, $seccode){
		$query ="INSERT INTO nrol_enrollsubj (Sno, SY, CompCode, SectionCode) VALUES('$sno', '$sy', '$compcode', '$seccode')";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
		return $query;
	}
	public function add_fac_load($fid , $sy, $compcode, $seccode){
		$query ="INSERT INTO nrol_facultyload (FacultyId, SY, CompCode, SectionCode) VALUES('$fid', '$sy', '$compcode', '$seccode')";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
		return $query;
	}
	public function add_advisory($fid , $sy,  $seccode){
		$query ="INSERT INTO nrol_advisory_class (FacultyId, SY, SectionCode, Period) VALUES('$fid', '$sy', '$seccode', 1)";
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();		
		}
		return $query;
	}
	public function check_fac_load($fid , $sy, $compcode, $seccode){
		$query = "SELECT COUNT(*) FROM nrol_facultyload WHERE SY ='$sy' AND CompCode = '$compcode' AND SectionCode = '$seccode'";
		$count =0;
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();		
		}
		$result = array();
		$result['count'] =$count;
		$result['query']=$query;
		return $result;
	}
	public function check_advisory($fid , $sy, $seccode){
		$query = "SELECT COUNT(*) FROM nrol_advisory_class WHERE SY ='$sy'  AND SectionCode = '$seccode'";
		$count =0;
		if ($stmt = $this->db_connection->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();		
		}
		$result = array();
		$result['count'] =$count;
		$result['query']=$query;
		return $result;
	}
	public function get_curri_subjects($sy, $deptcode, $level){
		$results = array();
		$sql ="SELECT  nrol_currdtl.CompCode FROM    erb_stsn.nrol_currhdr    INNER JOIN erb_stsn.nrol_currdtl         ON (nrol_currhdr.CurriculumId = nrol_currdtl.CurriculumId) ";
		$sql .="WHERE (nrol_currhdr.ProgramId ='$deptcode'    AND nrol_currhdr.SY ='$sy'    AND nrol_currdtl.CurrYear ='$level')";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($compcode);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['comp_code']=$compcode;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
}
$EGB = new EGB($db_username,$db_password,$db_server,$db_name);
?>
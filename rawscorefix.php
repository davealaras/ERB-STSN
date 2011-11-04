<?php
include_once ('database_login.php');
include_once ('header.php');
define("OLD_DB", "erb_stsn"); 
define("CURR_DB", "erb_stsn2"); 
define("RAWSCORES", "nrol_rawscore"); 
define("COMPONENTS", "nrol_gradecomp"); 
define("MEASURABLES", "nrol_measitem"); 
define('HR', "\n--------------------------------------------------------------------------------\n");

class RawScoreFix{
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
	//Database close connection method
	//@param	$connection	boolean variable
	//@return	null
	public function db_close($connection=false) {
		if($connection != false) $connection->close(); 
		$this->db_connection->close();
	}
	
	public function get_meas($subject, $section, $period, $sy){
		$results = array();
		$cond =  "WHERE SY = '$sy' AND Period = '$period' AND CompCode = '$subject' AND ( ";
		$c =0;
		foreach($section as $s){
			$cond .= " SectionCode = '$s'";
			if($c < count($section) -1 ){
				$cond .=" OR ";
			}else{
				$cond .=" ) ";
			}
			$c++;
		}
		$sql ="SELECT MeasKey, HeaderName, CompCode, SectionCode FROM nrol_measitem ". $cond;
		if ($stmt1 = $this->db_connection->prepare($sql)) {			
			$stmt1->execute();
			$stmt1->bind_result($id, $hdr,$compcode, $seccode);
			$index=0;
			while($stmt1->fetch()){
				$results[$index]['id']=$id;
				$results[$index]['hdr']=$hdr;
				$results[$index]['compcode']=$compcode;
				$results[$index]['seccode']=$seccode;
				$index+=1;
			}
			$stmt1->close();		
		}
		return $results;
		
	}
	public function update_rawscore($id, $hdr,$compcode, $seccode, $period){
		$sql = "UPDATE nrol_rawscore SET MeasKey='$id' WHERE Headername='$hdr' AND CompCode='$compcode' AND SectionCode='$seccode' AND Period='$period' ";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
	}
	public function restore($subject, $section, $period, $sy, $old_db, $curr_db, $tbl){
		$perc = number_format((0/2) * 100,2);
		echo "  0.00% Complete 0 of 2\n";
		$sql ="DELETE FROM $curr_db.$tbl WHERE CompCode = '$subject' AND SectionCode ='$section' AND Period='$period' AND SY='$sy'";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
			echo "  50.00 % Complete 1 of 2\n";
		}
		$sql ="INSERT INTO $curr_db.$tbl (SELECT * FROM $old_db.$tbl  WHERE CompCode = '$subject' AND SectionCode ='$section' AND Period='$period' AND SY='$sy')";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
			echo "  100.00 % Complete 2 of 2\n";
		}
	}
	public function get_rec_count($subject, $section, $period, $sy, $db, $tbl){
		$count=0;
		$sql = "SELECT COUNT(*) FROM $db.$tbl WHERE CompCode = '$subject' AND SectionCode ='$section' AND Period='$period' AND SY='$sy'";
		echo "$sql\n\n";
		if ($stmt1 = $this->db_connection->prepare($sql)) {			
			$stmt1->execute();
			$stmt1->bind_result($count);
			$stmt1->fetch();
			$stmt1->close();		
		}
		return $count;
	}
	
}

function getInput($msg){
  fwrite(STDOUT, "$msg");
  $varin = trim(fgets(STDIN));
  return $varin;
}
 
function select_section($EGB){
	do{
		$secname = getInput('LOOK FOR SECTION:');
		$section_obj = $EGB->get_seccode(trim($secname));
		$sections = count($section_obj);
		if($sections){
			$index = 0;
			$ctr =  1 ;
			if($sections>1){
				echo "$sections SECTIONS FOUND\n";
				foreach($section_obj as $s){
					 $dept = $s['dept'];
					 $level = $s['level'];
					 $secname = $s['section'];
					echo "$ctr . $dept $level - $secname \n";
					$ctr++;
				}
				echo "$ctr . QUIT \n";
				do{
				 $val = getInput('SELECT SECTION:');
				 $allow = (is_numeric($val)&&((int)$val<=$sections+1)) && (int)$val!=0;
				 if(!$allow){
					echo "INVALID SELECTION! \n";
				 }
				 if((int) $val == $ctr){
					return null;
				 }
				}while(!$allow);
				$index = (int) $val  - 1;
			}else{
				 $dept = $section_obj[0]['dept'];
				 $level = $section_obj[0]['level'];
				 $secname =$section_obj[0]['section'];
				 $ans = getInput("ARE YOU LOOKING FOR $dept $level - $secname  (Y)  ?: ");
				 if($ans=='Y' ||$ans=='y'){
					$index=0;
				 }else{
					return null;
				 }
			}
			return $section_obj[$index];
		}
			$ans = getInput('CAN\'T FIND SECTION TRY AGAIN ');	
	}while(1);
}

function select_subject($EGB, $subjects){
	echo "SUBJECTS AVAILABLE \n";
	$index=0;
	$ctr = 1;
	foreach($subjects as $s){
		$nomen=$s['nomen'];
		$nomen = $nomen==null? '********************ERR: '.$s['compcode'].'*******************': $nomen;
		echo "$ctr -  $nomen \n";
		$ctr++;
	}
	do{
	 $val = getInput('SELECT SUBJECT:');
	 $allow = (is_numeric($val)&&((int)$val<count($subjects))) && (int)$val!=0;
	 if(!$allow){
		echo "INVALID SELECTION! \n";
	 }
	}while(!$allow);
	$index = (int) $val  - 1;
	return $subjects[$index];
}
$RawScoreFix = new RawScoreFix($db_username,$db_password,$db_server,$db_name);
$RawScoreFix->db_connect();
$EGB->db_connect();

echo "RAWSCORE FIX TO DATA MUTATION/MIGRATION \n";
echo "v 1.1  TSSi \n";
getInput("HIT ANY KEY TO START\n");

do{
	// SELECT A SECTION
	do{
		$sec = select_section($EGB);
	}while($sec ==null);
	//DISPLAY AVAILABLE SUBJECTS
	$subj = select_subject($EGB, $EGB->get_subjects($sec['dept'],$sec['level']));
	
	//GET OTHER PARAMETERS
	$ctr=0;
	$subject_name = $subj['nomen'];
	$section_name = $sec['dept'].' '.$sec['level'].' - '.$sec['section'];
	$subject =$subj['compcode'];	
	$section =array($sec['seccode']);
	$period =getInput('ENTER PERIOD: ');
	$sy =getInput('ENTER SCHOOL YEAR: ');
	$operation = 0;
	do{
		$operation = getInput('CHOOSE OPERATION (1) RESTORE (2) UPDATE :');
		$allow = $operation==1 || $operation==2;
		if(!$allow){
			echo "INVALID OPERATION! \n";
		}
	}while(!$allow);
	
	//OPERATIONS AVAILABLE RESTORE AND UPDATE
	switch((int)$operation){
		
		case 1:
			echo "\t\tRESTORE $subject_name  $section_name PERIOD : $period SY: $sy \n";
			echo HR;
			
			$old_raw_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, OLD_DB, RAWSCORES);
			$curr_raw_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, CURR_DB, RAWSCORES);
			$old_comp_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, OLD_DB, COMPONENTS);
			$curr_comp_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, CURR_DB, COMPONENTS);
			$old_meas_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, OLD_DB, MEASURABLES);
			$curr_meas_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, CURR_DB, MEASURABLES);
			echo "$old_raw_count \tRESULT(s) FOUND on ".OLD_DB.".".RAWSCORES." \n";
			echo "$curr_raw_count \tCURRENT RECORD(s) on ".CURR_DB.".".RAWSCORES." \n";
			echo "$old_comp_count \tRESULT(s) FOUND on ".OLD_DB.".".COMPONENTS ."\n";
			echo "$curr_comp_count \tCURRENT RECORD(s) on ".CURR_DB.".".COMPONENTS ."\n";
			echo "$old_meas_count \tRESULT(s) FOUND on ".OLD_DB.".".MEASURABLES." \n";
			echo "$curr_meas_count \tCURRENT RECORD(s) on ".CURR_DB.".".MEASURABLES." \n";
			
			echo HR;
			$sure = getInput("Are you sure (Y)?");
			if($sure=='Y'||$sure=='y'){
				echo "UPDATING COMPONENTS PLEASE WAIT...\n";
				$RawScoreFix->restore($subject, $section[0], $period,$sy, OLD_DB, CURR_DB, COMPONENTS);
				echo "UPDATING MEASURABEL ITEMS PLEASE WAIT...\n";
				$RawScoreFix->restore($subject, $section[0], $period,$sy, OLD_DB, CURR_DB, MEASURABLES);
				echo "UPDATING RAWSCORES PLEASE WAIT...\n";
				$RawScoreFix->restore($subject, $section[0], $period,$sy, OLD_DB, CURR_DB, RAWSCORES);
			}
			break;
			
		case 2:
			echo "\t\tUPDATE $subject_name  $section_name  PERIOD : $period SY: $sy \n";
			echo HR;
			
			$curr_raw_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, CURR_DB, RAWSCORES);
			$curr_comp_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, CURR_DB, COMPONENTS);
			$curr_meas_count = $RawScoreFix->get_rec_count($subject, $section[0], $period,$sy, CURR_DB, MEASURABLES);
			echo "$curr_raw_count \tCURRENT RECORD(s) on ".CURR_DB.".".RAWSCORES." \n";
			echo "$curr_comp_count \tCURRENT RECORD(s) on ".CURR_DB.".".COMPONENTS ."\n";
			echo "$curr_meas_count \tCURRENT RECORD(s) on ".CURR_DB.".".MEASURABLES." \n";
			
			echo HR;
			$sure = getInput("Are you sure (Y)?");
			
			if($sure=='Y'||$sure=='y'){
				$mes = $RawScoreFix->get_meas($subject, $section, $period,$sy);
				$allow =  $EGB->check_rawscore($subject, $section[0], $period, $sy);
				$count =  count($mes);
				if( $allow && $count>0){
					echo "UPDATING RAWSCORES PLEASE WAIT...\n";
					echo "  0.00 % Complete  $ctr of $count \n  ";
					foreach($mes as $item){
						$ctr++;
						$perc = number_format(($ctr/$count) * 100,2);
						echo "$perc % Complete  $ctr of $count \n  ";
						$RawScoreFix->update_rawscore($item['id'],$item['hdr'],$item['compcode'],$item['seccode'],$period,$sy);	
					}
				}else{
					echo "\nERROR:\nCould not perform update. Reference to a null Header name \n\n";
				}
			}
			break;
	}
	echo "OPERAION DONE!\n";
	$ans = getInput('QUIT ? (Y/N)');
}while($ans=='N' ||$ans=='n');
$RawScoreFix->db_close();
$EGB->db_close();
?>
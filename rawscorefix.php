<?php
include_once ('database_login.php');
include_once ('header.php');
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
		echo $sql;
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
				do{
				 $val = getInput('SELECT SECTION:');
				 $allow = (is_numeric($val)&&((int)$val<=$sections)) && (int)$val!=0;
				 if(!$allow){
					echo "INVALID SELECTION! \n";
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
			$ans = getInput('Can not find section. Try again (Y) ?: ');	
	}while($ans=='Y' ||$ans=='y');
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
	do{
		$sec = select_section($EGB);
	}while($sec ==null);
	$subj = select_subject($EGB, $EGB->get_subjects($sec['dept'],$sec['level']));
	$ctr=0;
	$subject =$subj['compcode'];
	$section =array($sec['seccode']);
	$period =getInput('ENTER PERIOD: ');
	$sy =getInput('ENTER SCHOOL YEAR: ');
	$mes = $RawScoreFix->get_meas($subject, $section, $period,$sy);
	$allow =  $EGB->check_rawscore($subject, $section[0], $period, $sy);
	echo $allow;
	$count =  count($mes);
	if( $allow && $count>0){
		foreach($mes as $item){
			$ctr++;
			$perc = round(($ctr/$count) * 100);
			echo "$perc % Complete  $ctr of $count \n  ";
			$RawScoreFix->update_rawscore($item['id'],$item['hdr'],$item['compcode'],$item['seccode'],$period,$sy);	
		}
	}else{
		echo "Could not perform update. Reference to a null Header name \n";
	}
	$ans = getInput('UPDATE ANOTHER ? (Y)');
}while($ans=='Y' ||$ans=='y');

$RawScoreFix->db_close();
$EGB->db_close();
?>
<?php
include_once ('database_login.php');
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
	
	public function get_meas(){
		$results = array();
		$sql ="SELECT MeasKey, HeaderName, CompCode, SectionCode FROM nrol_measitem ";
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
	public function update_rawscore($id, $hdr,$compcode, $seccode){
		$sql = "UPDATE nrol_rawscore SET MeasKey='$id' WHERE HeaderName='$hdr' AND CompCode='$compcode' AND SectionCode='$seccode' AND Period='2' ";
		if ($stmt = $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
	}
}
$RawScoreFix = new RawScoreFix($db_username,$db_password,$db_server,$db_name);
$RawScoreFix->db_connect();
$ctr=0;
$mes = $RawScoreFix->get_meas();
$count =  count($mes);

foreach($mes as $item){
	$ctr++;
	$perc = round(($ctr/$count) * 100);
	echo "$perc % Complete  $ctr of $count \n  ";
	
	$RawScoreFix->update_rawscore($item['id'],$item['hdr'],$item['compcode'],$item['seccode']);
	
}

$RawScoreFix->db_close();

?>
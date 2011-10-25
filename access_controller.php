<?php
include_once ('database_login.php');
include_once ('acl_constants.php');
class ACL{
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
	public function get_roles(){
		$results = array();
		$query = "SELECT Role, Name FROM tb_roles ";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($role, $name);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['role']=$role;
				$results[$index]['name']=$name;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	public function get_users(){
		$results = array();
		$query = "SELECT Username, Role, Access FROM tb_useraccount ";
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($username, $role, $access);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['username']=$username;
				$results[$index]['role']=$role;
				$results[$index]['access']=$access;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	public function saveacl($data){
		$uid = $data['uid'];
		$rights = $data['rights'];
		$role = $data['role']==null? 1:$data['role'];
		if($rights==CUSTOM){
			$sql='INSERT INTO tb_access_custom (Id, LevelId, ItemId) VALUES';
			$values=array();
			for($ctr=0; $ctr<4; $ctr++){
				for($index =0; $index<count($data['level_'.$ctr]); $index++){
					$item = $data['level_'.$ctr][$index];
					array_push($values, "( '$uid', '$ctr', '$item')");
				}
			}
			$sql.=implode($values,',');
			if ($stmt = $this->db_connection->prepare($sql)) {
				$stmt->execute();
				$stmt->fetch();
				$stmt->close();		
			}
		}		
		$sql = "UPDATE tb_useraccount SET Access = $rights, Role = $role WHERE FacultyId  = $uid";
		if ($stmt2 = $this->db_connection->prepare($sql)) {
			$stmt2->execute();
			$stmt2->fetch();
			$stmt2->close();		
		}
		return $sql;
	}
	public function get_level($level=0, $parent_id=0){
		$results=array();
		switch($level){
			case 0:
				$query="SELECT title, id, url FROM toplevels ORDER BY seq_index";
				break;
			case 1:
				$query="SELECT title, id, url FROM sublevels WHERE parent_id = '$parent_id'ORDER BY seq_index";
				break;
			case 2:
				$query="SELECT title, id, url FROM lowlevels WHERE parent_id = '$parent_id'ORDER BY seq_index";
				break;
			case 3:
				$query="SELECT title, id, url FROM lowestlevels WHERE parent_id = '$parent_id'ORDER BY seq_index";
				break;
		}
		
		if ($stmt = $this->db_connection->prepare($query)) {			
			$stmt->execute();
			$stmt->bind_result($title, $id, $url);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['title']=$title;
				$results[$index]['id']=$id;
				$results[$index]['url']=$url==''? '#': $url;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	public function get_access($uid){
		$results = array();
		$perm = $this->get_access_key($uid);	
		if($perm['access']==FIXED){
			$role = $perm['role'];
			$sql="SELECT LevelId, ItemId FROM tb_access_fixed WHERE Role= '$role' ";
		}
		else if($perm['access']==CUSTOM){
			$sql ="SELECT LevelId, ItemId FROM tb_access_custom WHERE Id= '$uid'";
		}
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($level, $id);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['level']=$level;
				$results[$index]['id']=$id;
				$index+=1;
			}
			$stmt->close();		
		}
		return $results;
	}
	public function get_access_key($uid){
		$results = array();
		$sql ="SELECT Role, Access FROM tb_useraccount WHERE FacultyId ='$uid'";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($role, $access);	
			$stmt->fetch();
			$results['role']=$role;
			$results['access']=$access;
			$stmt->close();	
				
		}
		return $results;
	}
	public function get_acl($acs_type=1, $id=1){
		$results = array();
		if($acs_type==1){
			$sql ="SELECT Role, LevelId, ItemId FROM tb_access_fixed";
		}else if($acs_type==2){
			$sql ="SELECT Role, LevelId, ItemId FROM tb_access_custom WHERE Id =$id";
		}
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($role, $level, $id);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['role']=$role;
				$results[$index]['level']=$level;
				$results[$index]['id']=$id;
				$index+=1;
			}
			$stmt->close();		
		}
		$results['sql']=$sql;
		return $results;
		
	}
	public function get_all_users(){
		$results = array();
		$sql ="SELECT FacultyId, Username FROM  tb_useraccount WHERE Role >0 ";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($fid,$username);
			$index=0;
			while($stmt->fetch()){
				$results[$index]['id']=$fid;
				$results[$index]['username']=$username;
				$index+=1;
			}
			$stmt->close();		
		}
		$results['sql']=$sql;
		return $results;
		
	}
	public function reset_password($password, $id){
		$sql = "UPDATE tb_useraccount SET Password='$password' WHERE FacultyId ='$id'";
		if ($stmt= $this->db_connection->prepare($sql)) {
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();	
			return true;
		}
		return false;
	}
	public function match_password($uid, $password){
		$ok =0;
		$sql = "SELECT COUNT(*) FROM tb_useraccount WHERE FacultyId = '$uid' AND Password = '$password'";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->bind_result($ok);
			$stmt->fetch();
			$stmt->close();	
		}
		$results['sql']=$sql;
		$results['ok']=$ok;
		return $results;
	}
	public function change_password($uid, $password){
		$ok=0;
		$sql = "UPDATE tb_useraccount SET Password='$password' WHERE FacultyId='$uid'";
		if ($stmt = $this->db_connection->prepare($sql)) {			
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();	
			$ok=1;
		}else{
			$ok=0;
		}
		$results['sql']=$sql;
		$results['ok']=$ok;
		return $results;
	}
}
$ACL = new ACL($db_username,$db_password,$db_server,$db_name);
?>


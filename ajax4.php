<?php
session_start();								// Create session
include('header.php');
include('access_controller.php');
$EGB->db_connect();				//Open database connection for EGB
$ACL->db_connect();				//Open database connection for ACL
$func = $_REQUEST['func'];			//Get function code
$response = array();
if(isset($func)){
	if($func=='checkpass'){
		$uid = $_REQUEST['uid'];
		$password = md5($_REQUEST['password']);
		$newpassword = md5($_REQUEST['newpassword']);
		$ismatch= $ACL->match_password($uid, $password) ;
		if(!$ismatch['ok']){
			$response['msg'] ='Password mismatch';
			$response['ok']=0;
		}else{
			$ACL->change_password($uid, $newpassword);
			$response['msg'] = 'Password has been changed.';	
			$response['ok']=1;
		}
	}

	if($func!='export'){
		echo json_encode($response);		//Encode response to JSON format
	}
	
	$EGB->db_close();
	$ACL->db_close();	
}
?>
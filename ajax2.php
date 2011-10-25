<?php
session_start();								// Create session
include('header.php');
include('access_controller.php');
$EGB->db_connect();				//Open database connection for EGB
$ACL->db_connect();				//Open database connection for ACL

$func = $_REQUEST['func'];			//Get function code
$response = array();
if(isset($func)){
	if($func=="getsubjs"){
		$response['deptcode']=$deptcode=$_GET['deptcode'];
		$gryrlvl=$_REQUEST['gryrlvl'];
		$response=$EGB->get_subjects($deptcode, $gryrlvl);
	}else if($func=="saveacl"){
		$response =$_GET;
		$response['sql']=$ACL->saveacl($_GET);
	}else if($func=="getacskey"){
		$uid = $_GET['uid'];
		$response = $ACL->get_access_key($uid);
	}else if($func=="getcustom_acl"){
		$uid = $_GET['uid'];
		$response = $ACL->get_acl(2, $uid);
	}else if($func=='resetpw'){
		$data = $_POST;
		$response = $ACL->reset_password(md5(12345), $data['id']);
	}else if($func=='export'){
		$data = $_POST['dataset'];
		require 'excel/php-excel.class.php';
		$xls = new Excel_XML('UTF-8', true, 'My Test Sheet');
		$xls->addArray($data);
		$xls->generateXML('a-test');
	}
	if($func!='export'){
		echo json_encode($response);		//Encode response to JSON format
	}
	$EGB->db_close();
	$ACL->db_close();	
}
?>
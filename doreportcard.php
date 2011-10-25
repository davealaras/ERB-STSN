<?php
include('header.php');
$EGB->db_connect();
	$code =  explode("-",$_REQUEST['classcode']);
	$compcode ='';
	$version='';
	$sno = isset($_REQUEST['sno'])? $_REQUEST['sno']:'';
	$seccode =$code[0];
	$section= $EGB->get_sec_alias($seccode);
	$deptcode =$section[0]['dept'];
	$level=$section[0]['level'];
	$sy =$_REQUEST['sy'];
	$period = $_REQUEST['period'];
	
	$subjects = $EGB->get_fac_advisory($sy, $deptcode, $level, $seccode, $period);
	$grades = $EGB->get_final_scores($sy, $period, $seccode,$sno);
	
	$attendance_tmplt = $EGB->get_attendance_tmplt($sy, $period);
	$attendance = $EGB->get_attendance($seccode, $sy, $period, $sno);
	
	$conduct_tmplt = $EGB->get_conduct_tmplt($deptcode, $level, $version, $sy);
	$conduct = $EGB->getconduct_details($period, $sy, $seccode, $compcode, $sno);
$EGB->db_close();
?>
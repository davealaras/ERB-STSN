<?php
include_once('../header.php');
include_once('MasterSheet.php');

$master_sheet = new MasterSheet('P','in',array(8.5,13));
$info = json_decode($_POST['info']);
$data =json_decode($_POST['dataset']);
$subjects = json_decode($_POST['alias']);
$info['months'] = json_decode($_POST['months']);
$EGB->db_connect();
$seccode =explode("-",$info[2]);
$section=$EGB->get_sec_alias($seccode[0]);
$gryrlv = array(
				  'PS'=>array('Nursery','Kinder 1','Kinder 2'),
				  'GS'=>array('Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'),
				  'HS'=>array('First Year', 'Second Year', 'Third Year','Fourth Year'),
				);
$year_level =strtoupper( $gryrlv[$section[0]['dept']][$section[0]['level']-1]);
$info['yr_sec']=$section[0]['dept'].'-'.$section[0]['level'].' '.$section[0]['section'];
$section = $section[0]['section'];
$info['year_level']=$year_level;
$info['section']=$section;

$EGB->db_close();
if(count($data)<=45){
	$master_sheet->create($subjects,$info,$data,$master_sheet->record_index,45);
}else{
	while($master_sheet->record_index<count($data)){	
		$master_sheet->create($subjects,$info,$data,$master_sheet->record_index,40);
	}
}
$master_sheet->out();
?>
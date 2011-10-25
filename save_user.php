<?php 
include('header.php');
$EGB->db_connect();		
/* $fp = fopen("faculty201.txt", "r"); // Open the file for reading 
while ($line = fgets($fp)) { 
     $data = split("\t", $line, 6); 
	 $faculty_id=$data[0];
	 $last_name=$data[3];
	 $first_name =$data[4];
	 $middle_name = $data[5];
//echo $faculty_id. $comp_code. $section. $sy;
$EGB->create_fac201($faculty_id, $last_name, $first_name, $middle_name);
 }*/
 $fac_load = $EGB->get_fac_load(1);
 $index=0;
 foreach($fac_load as $load){
	$section= $EGB->get_sec_alias($load['sec_code']);
	foreach($section as $sec){
		$f_load[$index]['section'] =$sec['dept'].'-'.$sec['level'].' '.$sec['section'];
	}
	$f_load[$index]['subject'] = $load['sec_code'];
	$index+=1;
}

 print_r($f_load);
$EGB->db_close();  

 ?> 

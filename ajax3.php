<?php
session_start();								// Create session
include('header.php');
$EGB->db_connect();				//Open database connection
$func = $_REQUEST['func'];			//Get function code
$response = array();
if(isset($func)){
	if($func=='addfacload'){
		$data = $_POST;
		$response['faculty_id'] =$fid = $data['faculty_id'];
		$response['sy'] =$sy =$data['sy'];
		$response['compcodes'] = $compcodes= $data['compcodes'];
		$response['seccodes']=$seccodes=$data['seccodes'];
		$error_count=0;
		for($index=0; $index< count($compcodes);$index++){
			$allow = $EGB->check_fac_load($fid , $sy, $compcodes[$index], $seccodes[$index]);
			if($allow['count']==0){
				$EGB->add_fac_load($fid , $sy, $compcodes[$index], $seccodes[$index]);
			}else{
				$error_count+=1;
			}
		}
		$response['err_ctr'] = $error_count;
	}else if($func=='addadvisory'){
		$data = $_POST;
		$response['faculty_id'] =$fid = $data['faculty_id'];
		$response['sy'] =$sy =$data['sy'];
		$response['seccodes']=$seccodes=$data['seccodes'];
		$error_count=0;
		for($index=0; $index< count($seccodes);$index++){
			$allow = $EGB->check_advisory($fid , $sy, $seccodes[$index]);
			if($allow['count']==0){
				$EGB->add_advisory($fid , $sy, $seccodes[$index]);
			}else{
				$error_count+=1;
			}
		}
		$response['err_ctr'] = $error_count;
	}else if($func=='checkload'){
		$data = $_POST;
		$response['faculty_id'] =$fid = $data['faculty_id'];
		$response['sy'] =$sy =$data['sy'];
		$response['compcode'] = $compcode= $data['compcode'];
		$response['seccode']=$seccode=$data['seccode'];
		$response['available']=$EGB->check_fac_load($fid , $sy, $compcode, $seccode);
	}else if($func=='checkadvisory'){
		$data = $_POST;
		$response['faculty_id'] =$fid = $data['faculty_id'];
		$response['sy'] =$sy =$data['sy'];
		$response['seccode']=$seccode=$data['seccode'];
		$response['available']=$EGB->check_advisory($fid , $sy, $seccode);
	}else if($func=='checktname'){
		$response['count']  = $EGB->check_tname($_GET['tname']);
	}else if($func=='update_gc_tmplt'){
		$response['sy'] = $sy = $_REQUEST['sy'];
		$reponse['yr_level']=$yr_level= $_REQUEST['yr_level'];
		$response['deptcode']=$deptcode=$_REQUEST['deptcode'];
		$response['tmplt_name']=$tmplt_name = $_REQUEST['template_name'];
		$response['comp_code'] = $comp_code =$_REQUEST['comp_code'];;
		$response['classcode'] = $classcode = $_REQUEST['classcode'];
		$response['rownumber'] = $rownumber = $_REQUEST['rownumber'];
		$response['percentage'] = $percentage =$_REQUEST['percentage'];
		$response['template_id'] = $template_id =$_REQUEST['template_id'];
		$status ='S'; // Saved
		$EGB->clean_tmplthdg($tmplt_name);
		$EGB->clean_tmpltdtl($template_id);
		$EGB->clean_tmplmes($template_id);
		$tmpl = $EGB->save_tmplthdg($tmplt_name,$_SESSION['faculty_id'],$_SESSION['username'], $comp_code, $yr_level, $sy, $status, $deptcode);
		$response['tmp_id']=$tmpl['id'];
		$response['tmplt_query']=$tmpl['query'];
		for($i=0;$i<count($rownumber); $i++){
			$response['query'][$i]=$EGB->save_tmpltdtl($tmpl['id'], $rownumber[$i],$classcode[$i],$percentage[$i]);
		}
	}else if($func=='update_ms_tmplt'){
		$response['sy'] = $sy = $_POST['sy'];
		$response['comp_code'] = $comp_code =$_POST['comp_code'];
		$response['tmp_id'] =$tmp_id = $_REQUEST['tmplt_id'];
		$response['colnumber'] = $colnumber = $_POST['colnumber'];
		$response['classcode'] = $classcode = $_POST['classcode'];
		$response['header'] = $header = $_POST['header'];
		//$response['description'] = $description = $_POST['description'];
		//$response['noofitem'] = $noofitem = $_POST['noofitem'];
		$response['base'] = $base = $_POST['base'];
		for($i=0;$i<count($colnumber); $i++){
			$response['query'][$i]=$EGB->save_tmplmes($tmp_id, $colnumber[$i], $classcode[$i], $header[$i], $base[$i]);
		}
	}else if($func=='posttmpl8'){
		$data = $_POST;
		$EGB->post_tmplt($data['tid']);
	
	}else if($func=='deltmpl8'){
		$response['tmplt_name']=$tmplt_name = $_REQUEST['template_name'];
		$response['template_id'] = $template_id =$_REQUEST['template_id'];
		$EGB->clean_tmplthdg($tmplt_name);
		$EGB->clean_tmpltdtl($template_id);
		$EGB->clean_tmplmes($template_id);
	}else if($func=='cleantmphdg'){
		$response['tmplt_name']=$tmplt_name = $_REQUEST['template_name'];
		$EGB->clean_tmplthdg($tmplt_name);
	}else if($func=='checktmpl'){
		$data = $_POST;
		$templates = array();
		$curr_tname='';
		$prev_tname='';
		$ctr=0;
		$items = $EGB->get_components();
		$arr_templates = $EGB->get_templates_all($data['comp_code'], $data['sy']);
		for($index=0; $index<count($arr_templates); $index++){
			$curr_tname = $arr_templates[$index]['desc'];
			if($curr_tname!=$prev_tname){
				$templates[$ctr]['id']= $arr_templates[$index]['id'];
				$templates[$ctr]['name']= $curr_tname;
				$templates[$ctr]['status']= $arr_templates[$index]['status'];
				$templates[$ctr]['author']= $arr_templates[$index]['author'];
				$templates[$ctr]['allow']= $arr_templates[$index]['fid']==$_SESSION['faculty_id'];
				$templates[$ctr]['user']= $_SESSION['faculty_id'];
				$templates[$ctr]['creator']=$arr_templates[$index]['fid'];
				$templates[$ctr]['deptcode']= $data['dept_code'];
				$templates[$ctr]['levels']= array();
				$components = array();
				$template_details = $EGB->get_template_details($templates[$ctr]['id']);
				if($template_details!=null){
					foreach( $template_details['components'] as $details){
						foreach($items as $item){
							if($item['code']==$details['ccode']){
								$details['desc'] = $item['desc'];
							}
						}
						array_push($components,$details);
					}
					$templates[$ctr]['components'] = $components;
					$templates[$ctr]['measurables']= $template_details['measurables'];
				}
				$ctr+=1;
			}
			array_push($templates[$ctr-1]['levels'],  $arr_templates[$index]['level']);
			$prev_tname = $curr_tname;
		}
		$response=$templates;
	}
if($func!='exportraw'){
echo json_encode($response);		//Encode response to JSON format
}
$EGB->db_close();
}
?>
<?php
//sleep(1);
session_start();								// Create session
include('header.php');
$EGB->db_connect();				//Open database connection
$func = $_REQUEST['func'];			//Get function code
$response = array();
if(isset($func)){
	$_SESSION['faculty_id'] = 36;
	if($func=='getSession'){
		if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
			$_SESSION['username']=isset($_COOKIE['username'])?$_COOKIE['username']:' ';
			$_SESSION['password']=isset($_COOKIE['password'])?$_COOKIE['password']:' ';
			$_SESSION['faculty_id'] = $EGB->login($_SESSION['username'],$_SESSION['password']);
			$_SESSION['session_token'] = md5($_SESSION['faculty_id']+time());
		}
		$username =isset($_SESSION['username'])?$_SESSION['username']:' ';
		$password = isset($_SESSION['password'])?$_SESSION['password']:' ';
		if( $EGB->login($username,$password)){
			$response['id']   = $_SESSION['faculty_id'];
			$response['token'] = $_SESSION['session_token']; 
			$response['username']=$_SESSION['username'];
			$response['menu']=$EGB->getmenu();
		}
		if($response!=null){
			echo(json_encode($response));
		}
		return;
	}
	if($func=='login'){
		$user_name= $_POST['user_name'];
		$password = md5($_POST['password']);			//Encrypt password
		$remember = isset($_POST['remember'])?1:0;
		$faculty_id = $EGB->login($user_name,$password);	//Attempt to log-in
		if($faculty_id){							//Successful
			$_SESSION['faculty_id'] = $faculty_id;			//Add faculty_id on the session
			$_SESSION['session_token'] = md5($faculty_id+time());	//Create a session token
			$_SESSION['username']=$user_name;
			$_SESSION['password']=$password;
			if($remember){
				setcookie('username',$user_name, time()+3600*24);
				setcookie('password',$password, time()+3600*24);	
				setcookie('remember',$remember, time()+3600*24);	
			}else{
				setcookie('username',$user_name, time()+300);
				setcookie('password',$password, time()+300);	
				setcookie('remember',$remember, time()+300);	
			}
			$response['status'] = 0;
			$response['type'] = 'OK';
			$response['msg'] = 'Welcome to '.$user_name.' !';
			//$response['access'] = 'Access Granted';
			$response['id']   = $_SESSION['faculty_id'];
			$response['token'] = $_SESSION['session_token']; 
			$response['menu']=$EGB->getmenu();
		}else{											//Denied
			$response['status'] = 1;
			$response['type'] = 'DENIED';
			//$response['access'] = 'Access Denied';
			$response['msg'] = '<strong>Oops!</strong> Username/Password incorrect.';
		}
	}else if($func=='checkposting'){
		$response['sy'] = $sy = $_POST['sy'];
		$response['period'] =$period = $_POST['period'];
		$code = explode("-",$_POST['classcode']);
		$response['section_code'] = $section_code =$code[0];
		$response['comp_code'] = $comp_code =$code[1];
		if($period>1){
			$response['isposted']= $EGB->isPosted(($period-1), $sy, $section_code, $comp_code);
		}else{
			$response['isposted']=true;
		}
	}else if($func=='getInfo'){
		$token = $_GET['token'];
		if($token==$_SESSION['session_token'] ){
			$user = $EGB->get_users($_GET['id']);
			$response['first_name'] = $user['first_name'];
			$response['last_name']= $user['last_name'];
			$response['middle_name'] =isset($user['middle_name'])?$user['middle_name']:' ';
			$response['full_name'] = $user['last_name'].', '.$user['first_name'].' '.$user['middle_name'];
			$response['id']   = $_GET['id'];			
		}
	}else if($func=='getSysDefa'){
		$token = $_GET['token'];
		if($token==$_SESSION['session_token'] ){
			$sys_defa = $EGB->get_sys_defa();
			$periods = $EGB->get_periods();
			$items  = $components = $EGB->get_components();
			//Periods
			$index=0;
			foreach($periods as $period ){
				$p[$index]['id']=$period['id'];
				$p[$index]['desc'] =$period['desc'];
				$p[$index]['alias'] =$period['alias'];
				$index++;
			}
			//Components
			$index=0;
			if(count($components)>0){
				foreach($components as $component ){
					$c[$index]['code']=$component['code'];
					$c[$index]['desc'] =$component['desc'];
					$index++;
				}
			}

			//Faculty Load
			$fac_load = $EGB->get_fac_load($_SESSION['faculty_id']);
			$advisory = $EGB->get_advisory($_SESSION['faculty_id']);
			$index=0;
			$response['fl']=$fac_load;
			$f_load =array();
			if($fac_load != null){
			foreach($fac_load as $load){
				$section= $EGB->get_sec_alias($load['sec_code']);
				foreach($section as $sec){
					if($sec['dept']=='GS'){
						$str="Gr".'-'.$sec['level'];
					}
					else if($sec['dept']=="HS"){
						$str= $sec['level'].'-'."Yr";
						}
					else if($sec['dept']=="PS"){
						$str=$sec['level'];
					}
				$f_load[$index]['section'] =$str.' '.$sec['section'];
				$f_load[$index]['level'] = $sec['level'];
				$f_load[$index]['dept'] = $sec['dept'];
				$f_load[$index]['sy']=$load['sy'];
				}
				$f_load[$index]['sec_code'] = $load['sec_code'];
				$f_load[$index]['subject'] = $load['nomen'];
				$f_load[$index]['comp_code'] = $load['comp_code'];
				$index+=1;
			}
			}
			//Faculty adviser subjects
			//$subjects = $EGB->get_fac_advisory($_SESSION['faculty_id']);
		}		
		$response['title'] = 'System Default';
		$response['period'] =$p	;
		$response['component']=$c;
		//$response['templates'] = $templates;
		$response['faculty_load'] = $f_load;
		$response['active_sy'] = $sys_defa['active_sy'];
		$response['base'] = $sys_defa['base'];
		$response['ztb_w']=$sys_defa['ztable_width'];
		$response['ztb_h']=$sys_defa['ztable_height'];
		//$response['timeout']=$sys_defa['timeout'];
//		$response['subjects'] = $subjects;
		$response['advisory']=$advisory;
	}else if($func=='save_record_gencomp'){
		$response['sy'] = $sy = $_REQUEST['sy'];
		$response['period'] =$period = $_REQUEST['period'];
		$code = explode("-",$_REQUEST['section_code']);
		$response['section_code'] = $section_code =$code[0];
		$response['comp_code'] = $comp_code =$code[1];
		$response['classcode'] = $classcode = $_REQUEST['classcode'];
		$response['rownumber'] = $rownumber = $_REQUEST['rownumber'];
		$response['percentage'] = $percentage =$_REQUEST['percentage'];
		//Delete all Period : p as in Period
			$EGB->prepare_reccord_gencomp($comp_code, $section_code, $sy, $period);
			for($i=0;$i<count($rownumber); $i++){
				$EGB->save_record_gencomp($rownumber[$i],$classcode[$i],$percentage[$i],$sy, $period, $section_code,$comp_code);
			}		
		$EGB->prepare_meas($comp_code, $section_code, $sy, $period);
		
	}else if($func=='save_record_measitem'){
		$response['sy'] = $sy = $_POST['sy'];
		$response['period'] =$period = $_POST['period'];
		$code = explode("-",$_POST['section_code']);
		$response['section_code'] = $section_code =$code[0];
		$response['comp_code'] = $comp_code =$code[1];
		$response['key'] = $key = $_POST['key'];
		$response['classcode'] = $classcode = $_POST['classcode'];
		$response['colnumber'] = $colnumber = $_POST['colnumber'];
		$response['header'] = $header = $_POST['header'];
		$response['description'] = $description = $_POST['description'];
		$response['noofitem'] = $noofitem = $_POST['noofitem'];
		$response['base'] = $base = $_POST['base'];
		$response['queries']=array();
		for($i=0;$i<count($colnumber); $i++){
			$query  = $EGB->save_record_measitem($key[$i], $colnumber[$i],$classcode[$i],$header[$i],$description[$i],$noofitem[$i], $base[$i],$sy, $period, $section_code,$comp_code);
			array_push($response['queries'], $query);
		}
	}else if($func=='del_measitem'){
		$response['key'] = $key = $_POST['key'];
		$response['query']=$EGB->delete_measitem($key);
	}else if($func=='save_ms_tmplt'){
		$response['sy'] = $sy = $_POST['sy'];
		$response['comp_code'] = $comp_code =$_POST['comp_code'];
		$response['tmp_id'] =$tmp_id = $_REQUEST['tmplt_id'];
		$response['colnumber'] = $colnumber = $_POST['colnumber'];
		$response['classcode'] = $classcode = $_POST['classcode'];
		$response['header'] = $header = $_POST['header'];
		$response['base'] = $base = $_POST['base'];
		for($i=0;$i<count($colnumber); $i++){
			$response['query'][$i]=$EGB->save_tmplmes($tmp_id, $colnumber[$i], $classcode[$i], $header[$i], $base[$i]);
		}
	}elseif($func=='save_attendance'){
		$response['sy'] = $sy = $_REQUEST['sy'];
		$response['period'] =$period = $_REQUEST['period'];
		$code = explode("-",$_REQUEST['classcode']);
		$response['section_code'] = $seccode =$code[0];
		$response['hdr']= $hdr = $_REQUEST['hdr'];
		$response['sno']= $sno = $_REQUEST['sno'];
		$response['val']= $val = $_REQUEST['val'];
		$response['mo']=$mo = $_REQUEST['mo'];
		$r = $EGB->save_attendance($sno, $seccode, $period, $sy, $hdr, $val, $mo);
		$response['sql']=$r['sql'];
	}else if($func=='getcomponents'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];
		$sy = $_REQUEST['sy'];
		$period = $_REQUEST['period'];
		$level =$_REQUEST['level'];
		$deptcode = $_REQUEST['deptcode'];
		$version =1;
		$isposted = (bool)$EGB->isPosted($period, $sy, $seccode, $compcode);
		$conduct = $EGB->get_conduct_tmplt($deptcode, $level, $version, $sy);
		$components_rec = $EGB->get_components_rec($compcode,$seccode,$sy, $period);
		$attendance = $EGB->get_attendance_tmplt($sy, $period);
		$templates = $EGB->get_template($compcode, $level, $deptcode, $sy);
		$items = $EGB->get_components();
		$index = 0;
		if($components_rec!=null){
			foreach($components_rec as $component){
				foreach($items as $item){
					if($item['code']==$component['ccode']){
						$components_rec[$index]['desc'] = $item['desc'];
					}
				}
				$index+=1;			
			}
		}
		$index = 0;
		//Build template details
		if($templates !=null){
				foreach($templates as $temphdg){
					$components = array();
					$template_details = $EGB->get_template_details($temphdg['id']);
					if($template_details!=null){
						foreach( $template_details['components'] as $details){
							foreach($items as $item){
								if($item['code']==$details['ccode']){
									$details['desc'] = $item['desc'];
								}
							}
							array_push($components,$details);
						}
						$templates[$index]['id'] = $temphdg['id'];
						$templates[$index]['desc'] = $temphdg['desc'];
						$templates[$index]['components'] = $components;
						$templates[$index]['measurables']= $template_details['measurables'];
						$index+=1;
					}
				}
			}
	
		
		$measurables = $EGB->get_measurables_rec($compcode,$seccode,$sy, $period);
		$response['isposted'] =$EGB->isPosted($period, $sy, $seccode, $compcode);
		$response['postAtt']=$EGB->is_section_posted($period, $sy, $seccode, 'DatePosted_Attendance');
		$response['postCond']=$EGB->is_section_posted($period, $sy, $seccode, 'DatePosted_Conduct');
		$response['components']= $components_rec;
		$response['measurables'] = $measurables;
		$response['conduct'] =$conduct;
		$response['attendance']=$attendance;
		$response['templates']=$templates ;
	}else if($func=='getstudnrol'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];
		$sy =$_REQUEST['sy'];
		$students =  $EGB->get_stud_nrol($compcode, $seccode, $sy);
		$response['students'] = $students;
	}else if($func =="getclass"){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];
		$deptcode = $_REQUEST['dept'];
		$level = $_REQUEST['level'];
		$sy =$_REQUEST['sy'];
		$period = $_REQUEST['period'];
		$attendance = $EGB->get_attendance_tmplt($sy, $period);
		$subjects = $EGB->get_fac_advisory($sy, $deptcode, $level, $seccode, $period);
		for($ctr=0;$ctr<count($subjects);$ctr++){
			$subjects[$ctr]['status'] = $EGB->get_status($period, $sy, $seccode, $subjects[$ctr]['comp_code']);
		}
		$students = $EGB->get_stud_nrol($compcode, $seccode, $sy,1);
		$is_section_posted =$EGB->is_section_posted($period, $sy, $seccode, 'DatePosted_CGS');
		$response['subjects'] =$subjects;
		$response['students']=$students;
		$response['attendance']=$attendance;
		$response['is_section_posted'] = $is_section_posted;
	} else if($func=='searchstud'){
		$key = $_REQUEST['search_string'];
		$limit = 15;
		$response['result']= $EGB->search_stud($key, $limit);
	
	}else if($func=='saveraw'){
		$sno = $_REQUEST['sno'];
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$hdr =$_REQUEST['hdr'];
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$rawscore =$_REQUEST['rawscore'];
		$response['result'] = $EGB->save_rawscore($sno, trim($compcode), $seccode, $hdr, $period, $sy, $rawscore);
	}else if($func=='saveadjustment'){
		$sno = $_REQUEST['sno'];
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$hdr =$_REQUEST['hdr'];
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$adjustment =$_REQUEST['adjustment'];
		$response['result'] = $EGB->save_adjustment($sno, trim($compcode), $seccode, $hdr, $period, $sy, $adjustment);
	}else if($func=='saveconduct'){
		$sno = $_REQUEST['sno'];
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$hdr =$_REQUEST['hdr'];
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$rawscore =$_REQUEST['rawscore'];
		$response['result'] = $EGB->save_conduct($sno, trim($compcode), $seccode, $hdr, $period, $sy, $rawscore);
	
	}else if($func=='getraw+equivalent'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$response['raw'] =  $EGB->getraw_details($period, $sy, $seccode, trim($compcode));
		$response['adjustments'] =  $EGB->getadjustment_details($period, $sy, $seccode, trim($compcode));
		$response['summary'] = $EGB->get_summary($compcode, $seccode, $period, $sy);
		$EGB->db_close();
		$EGB->db_connect();
		$response['equivalent'] =  $EGB->get_equivalent($compcode, $seccode, $period, $sy);		
		$EGB->db_close();
		$EGB->db_connect();
		$response['overall'] = $EGB->get_overall($compcode, $seccode, $sy);
	}else if($func=='getatt+conduct'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$response['attendance']=$EGB->get_attendance($seccode, $sy, $period);
		$response['conduct']=$EGB->getconduct_details($period, $sy, $seccode, trim($compcode));
	}else if($func=='getraw'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$response['result'] =  $EGB->getraw_details($period, $sy, $seccode, $compcode);
	}else if($func=='getconduct'){
		$sy =$_REQUEST['sy'];
		$level =$_REQUEST['level'];
		$deptcode = $_REQUEST['deptcode'];
		$version =$_REQUEST['version'];
		$EGB->get_conduct_tmplt($deptcode, $level, $version, $sy);
	}else if($func=='register'){
		$response['username'] = $username =$_REQUEST['username'];
		$response['password']= $password =$_REQUEST['password'];
		$response['firstname'] = $firstname =$_REQUEST['firstname'];
		$response['middlename'] = $middlename =$_REQUEST['middlename'];
		$response['lastname'] = $lastname =$_REQUEST['lastname'];
		$response['iscoor']=$iscoor= $_REQUEST['iscoor'];
		$response['query']=$EGB->create_fac201($username, md5($password), $lastname, $firstname, $middlename, $iscoor);
		setcookie('register',time()+300, time()+300);
	}else if($func=='sendtocgs'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$sno =$_REQUEST['sno'];
		$grade =$_REQUEST['grade'];
		$response['queries']=array();
		for($i=0; $i<count($sno);$i+=1){
			array_push($response['queries'], $EGB->sendtocgs($sno[$i], $grade[$i], $period, $sy, $seccode, $compcode));
		}		
		$EGB->hassent($period, $sy, $seccode, $compcode);
		
	}else if($func=='postgrade'){
		$code =  explode("-",$_REQUEST['seccode']);
		$response['period']=$period=$_REQUEST['period'];
		$response['sy']=$sy =$_REQUEST['sy'];
		$response['seccode']=$seccode =$code[0];
		$response['compcodes']=$compcodes=$_REQUEST['compcodes'];
		$response['student_records']=$student_records = $_REQUEST['student_records'];
		$queries = array();
		foreach($student_records as $student){
				$record = $student;
				$sno = $record['sno'];
				$grades = $record['grades'];
				foreach($grades as $item){
						array_push($queries,$EGB->post_final_grade($sno,$sy ,$item['comp_code'], $seccode,$period, $item['equivalent'],$item['grade']));
				}
		}
		foreach($compcodes as $compcode){
			array_push($queries, $EGB->post_grade($period, $sy, $seccode, $compcode));
		}
		array_push($queries,$EGB->post_section($period, $sy, $seccode,'DatePosted_CGS'));
		$response['query']=$queries;
	}else if($func=='postAttCond'){
		$code = explode("tb",$_REQUEST['field']);
		$response['field']=$field = 'DatePosted_'.$code[1];
		$code =  explode("-",$_REQUEST['seccode']);
		$response['period']=$period=$_REQUEST['period'];
		$response['sy']=$sy =$_REQUEST['sy'];
		$response['seccode']=$seccode =$code[0];
		$queries = array();
		array_push($queries,$EGB->post_section($period, $sy, $seccode,$field));
		$response['query']=$queries;
	}else if($func=='unpostgrade'){
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $_REQUEST['compcode'];		
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$response['unpost']=$EGB->unpost_grade($period, $sy, $seccode, $compcode);
	}else if($func=='save_gc_tmplt'){
		$response['sy'] = $sy = $_REQUEST['sy'];
		$reponse['yr_level']=$yr_level= $_REQUEST['yr_level'];
		$response['deptcode']=$deptcode=$_REQUEST['deptcode'];
		$response['tmplt_name']=$tmplt_name = $_REQUEST['template_name'];
		$response['comp_code'] = $comp_code =$_REQUEST['comp_code'];;
		$response['classcode'] = $classcode = $_REQUEST['classcode'];
		$response['rownumber'] = $rownumber = $_REQUEST['rownumber'];
		$response['percentage'] = $percentage =$_REQUEST['percentage'];
		$status ='S'; // Saved
		$tmpl = $EGB->save_tmplthdg($tmplt_name,$_SESSION['faculty_id'],$_SESSION['username'], $comp_code, $yr_level, $sy, $status, $deptcode);
		$response['tmp_id']=$tmpl['id'];
		$response['tmplt_query']=$tmpl['query'];
		for($i=0;$i<count($rownumber); $i++){
			$response['query'][$i]=$EGB->save_tmpltdtl($tmpl['id'], $rownumber[$i],$classcode[$i],$percentage[$i]);
		}
	}else if($func=="get_cgs_scores"){
		$code =  explode("-",$_REQUEST['classcode']);
		$response['seccode'] =$seccode = $code[0];	
		$response['period'] =$period =$_REQUEST['period'];
		$response['sy'] =$sy =$_REQUEST['sy'];
		$scores=$EGB->get_cgs_scores($sy, $period, $seccode);
		for($index = 0 ; $index<count($scores);$index+=1){
			if($scores[$index]['isletter']){
				$scores[$index]['display']=$EGB->get_letter_grade($scores[$index]['grade']);
			}else{
				$scores[$index]['display'] = $scores[$index]['grade'];
			}
		}
		$response['cgs_grades']=$scores;
		$response['deportment']=$EGB->get_deportment($seccode, $sy, $period);
		$response['attendance']=$EGB->get_attendance($seccode, $sy, $period);
	}else if($func=="get_final_scores"){
		$code =  explode("-",$_REQUEST['classcode']);
		$response['seccode'] =$seccode = $code[0];	
		$response['period'] =$period =$_REQUEST['period'];
		$response['sy'] =$sy =$_REQUEST['sy'];
		$scores=$EGB->get_final_scores($sy, $period, $seccode);
		$response['cgs_grades']=$scores;
		$response['deportment']=$EGB->get_deportment($seccode, $sy, $period);
		$response['attendance']=$EGB->get_attendance($seccode, $sy, $period);
	}elseif($func=='getstudent201'){
		$response=$EGB->get_stud201($_REQUEST['sno']);
		$response['alias']=$EGB->get_sec_alias($response['seccode']);
	}elseif($func=="savestudent201"){
		$response = $_POST;
		$sno = $_POST['sno'];
		$sy = $_POST['sy'];
		$deptcode =$_POST['educlvl'];
		$level =$_POST['gryrlvl'];
		$seccode=$_POST['section'];
		$response['sql']=$EGB->save_stud201($_POST);
		$response['section']=$EGB->change_section($sno,$sy,$seccode, $deptcode,$level);
	}else if($func=="check201"){
		$sno = $_REQUEST['sno'];
		$response = $EGB->check_stud201($sno);
	}else if($func=='exportraw'){
		require 'excel/php-excel.class.php';
		$code =  explode("-",$_REQUEST['classcode']);
		$seccode = $code[0];
		$compcode  = $code[1];		
		$period =$_REQUEST['period'];
		$sy =$_REQUEST['sy'];
		$raw =  $EGB->getExcelReady($period, $sy, $seccode, $compcode);
		$students = $EGB->get_stud_nrol($compcode, $seccode, $sy);
		$data = array(1 => array ('Student Number', 'Name of student'));
		$measurables = $EGB->get_measurables_rec($compcode,$seccode,$sy, $period);	
		$mcount= count($measurables);
		foreach($measurables as $m){			
			array_push($data[1],$m['hdr']);
		}

		$k=0;
		foreach($students as $student){
			//Initialize record
			$item = array();
			$SN = $student['sno'];
			$item[0] = $SN;			//Student number
			$item[1] = $student['fullname'];	//Full Name
			for($i = 0;$i<$mcount; $i++ ){
				$item[$i+2] =$student['status']!=-1 ? 0: 'D';				//Grade holder set to zero or D if DROP
			}
			foreach($raw as $r){
				if($r['sno']==$SN){
					
					$item[$r['colnum']+1] = $r['rawscore']== -3? 'ING': $r['rawscore'];
				}
			}
			array_push($data,$item);
		}
		//generate file (constructor parameters are optional)
		$xls = new Excel_XML('UTF-8', true, 'My Test Sheet');
		$xls->addArray($data);
		$xls->generateXML('a-test');
	}
if($func!='exportraw'){
echo json_encode($response);		//Encode response to JSON format
}
$EGB->db_close();
}
?>
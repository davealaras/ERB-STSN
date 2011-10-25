<?php
include_once('../header.php');
include_once('ReportCard.php');


$EGB->db_connect();
	$sno =$_REQUEST['prnt_sno'];
	$classcode=$_REQUEST['prnt_classcode'];
	$sy =$_REQUEST['prnt_sy'];
	$period = $_REQUEST['prnt_period'];
	$mode =  $_REQUEST['prnt_mode'];
	if($classcode!=0){
		$classcode =$_REQUEST['prnt_classcode'];
	}else{
		$classcode = $EGB->get_nrol_section($sno);
	}
	if($sno){
		$sno = $_REQUEST['prnt_sno'];
	}
	$code =  explode("-",$classcode);
	$compcode ='';
	$version=1;
	
	$report_card = new ReportCard('P','in',array(8.5,11));
	for($a=0;$a<count($code);$a++){
		 $seccode=$code[$a];
		if($seccode!=null&& $seccode!=''){
			if($mode=='batch'){
				$sno=null;
			}
			$subjects=null;
			$attendance=null;
			$conduct_tmplt=null;
			$grades =null;
			$addtosubjects=null;
			$section= $EGB->get_sec_alias($seccode);
			$deptcode =$section[0]['dept'];
			$level=$section[0]['level'];
			$section_alias = $deptcode.'-'.$level.' '.$section[0]['section'];
			if( $deptcode=='PS'){
				$dept_alias = 'Pre School';
				if($level==1){
					$dept_alias = 'Nusery';
				}else{
					$dept_alias = 'Kndergarten';
				}
				$addtosubjects =false;
			}else if($deptcode=='GS'){
				$dept_alias = 'Grade School Department';
				$addtosubjects =true;
			}else if($deptcode=='HS'){
				$dept_alias = 'High School Department';
				$addtosubjects =true;
			}
			$adviser =$EGB->get_users($EGB->get_adviser($seccode, $sy, $period));
			$students = $EGB->get_stud_nrol($compcode, $seccode, $sy,-1, $sno);
			
			$default_subjects = $EGB->get_fac_advisory($sy, $deptcode, $level, $seccode, 1);
			
			$final_grades =  array();
			$conduct_grades = array();
			$attendance_tmplt = array();
			$attendance = array();
			
			foreach($students as $student){
				$sno =  $student['sno'];
				$period_ctr =  1;
				do{
					$att_t = $EGB->get_attendance_tmplt($sy, $period_ctr);
					$att = $EGB->get_attendance($seccode, $sy, $period_ctr, $sno);
					foreach($att as $a){
						array_push($attendance , $a);
					}
					foreach($att_t as $a_t){
						array_push($attendance_tmplt , $a_t);
					}
					
					$grades = $EGB->get_final_scores($sy, $period_ctr, $seccode,$sno);
					$subjects = $default_subjects;
					// MAP GRADES
					for($subject_ctr=0;$subject_ctr<count($subjects);$subject_ctr++){
						$subjects[$subject_ctr]['grades']='NG';
						if(count($grades)){
							for($grade_ctr=0;$grade_ctr<count($grades);$grade_ctr++){
								if($grades[$grade_ctr]['comp_code']==$subjects[$subject_ctr]['comp_code']){
									if(isset($grades[$grade_ctr]['display'])){
										$subjects[$subject_ctr]['grades']=$grades[$grade_ctr]['display'];
									}else{
										$subjects[$subject_ctr]['grades'] ='NG';
									}
								}
							}
						}
					}
					
					if(!$addtosubjects){
						array_push($subjects, array('nomen'=>'GENERAL AVERAGE','grades'=>'XX', 'under'=>'NULL', 'comp_code'=>''));
					}
					
					$conduct_tmplt = $EGB->get_conduct_tmplt($deptcode, $level, $version, $sy);
					$conduct = $EGB->getconduct_details($period_ctr, $sy, $seccode, $compcode, $sno);
					
					//MAP CONDUCT
					for($contmpl_ctr=0;$contmpl_ctr<count($conduct_tmplt);$contmpl_ctr++){
						if($conduct){
							for($conduct_ctr=0;$conduct_ctr<count($conduct);$conduct_ctr++){
								if($conduct[$conduct_ctr]['hdr']==$conduct_tmplt[$contmpl_ctr]['hdr']){
									$conduct_tmplt[$contmpl_ctr]['score']=$conduct[$conduct_ctr]['score'];
									if($conduct_tmplt[$contmpl_ctr]['hdr']=='Rating' && count($subjects)<=count($default_subjects)){
										if($addtosubjects){
											array_push($subjects, array('nomen'=>strtoupper($conduct_tmplt[$contmpl_ctr]['desc']),'grades'=>$conduct[$conduct_ctr]['score'], 'under'=>'NULL', 'comp_code'=>''));
										}
									}
								}
							}
						}else{
							$conduct_tmplt[$contmpl_ctr]['score'] = 'XX';
							if($addtosubjects){
								if(count($subjects)<=count($default_subjects)){
									array_push($subjects, array('nomen'=>strtoupper($conduct_tmplt[count($conduct_tmplt)-1]['desc']),'grades'=>'XX', 'under'=>'NULL', 'comp_code'=>''));
								}
							}
						}
					}
					
					array_push($final_grades , $subjects);
					array_push($conduct_grades, $conduct_tmplt);

					$period_ctr++;
					
			}while($period_ctr<=$period);
			
				
			
				
				//MAP ATTENDANCE
				$good=true;
				if(!$attendance){
					$attendance = array();
					$good=false;
				}
				
				for($atttmpl_ctr=0;$atttmpl_ctr<count($attendance_tmplt);$atttmpl_ctr++){
					if($attendance && $good){
						for($attendance_ctr=0;$attendance_ctr<count($attendance);$attendance_ctr++){
							if($attendance[$attendance_ctr]['month']==$attendance_tmplt[$atttmpl_ctr]['month']){
								$attendance[$attendance_ctr]['school_days']=$attendance_tmplt[$atttmpl_ctr]['days'];
								$attendance[$attendance_ctr]['present']=$attendance_tmplt[$atttmpl_ctr]['days'] - $attendance[$attendance_ctr]['absent'];
							}
						}
						
					}else{
						$arr= array();
						$arr['sno']='';
						$arr['school_days']='XX';
						$arr['present']='XX';
						$arr['absent']='XX';
						$arr['month']=$attendance_tmplt[$atttmpl_ctr]['month'];
						array_push($attendance,$arr);
					}
				}
				$report_card->create($final_grades,$attendance,$conduct_grades,$student,$adviser,$section_alias,$dept_alias, $deptcode,$level);
			}
		}
	}
	
	$report_card->out();
	
	$EGB->db_close();
?>
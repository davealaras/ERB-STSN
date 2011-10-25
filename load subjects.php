		
		
		<?php
		include ('header.php');
		$EGB->db_connect();
		$index =0;
		
		foreach($EGB->get_all_stud() as $pupil){
	
			$index++;
			$sno = $pupil['sno'];
			$student = $EGB->get_stud201($sno);
			$SECTION_CODE = $student['seccode'];
			$section= $EGB->get_sec_alias($SECTION_CODE);
			$sy=2011;
			$deptcode =$section[0]['dept'];
			$level =$section[0]['level'];
			echo $SECTION_CODE;
			echo $deptcode;
			echo $level;
			$subjects = $EGB->get_curri_subjects($sy, $deptcode, $level);
			$s = array();
			foreach($subjects  as $subject){
				array_push($s, $EGB->enrol_subject($sno, $sy, $subject['comp_code'], $SECTION_CODE));
			}
			
		}
		$EGB->db_close();
			?>
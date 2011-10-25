<?php
require('fpdf.php');
class MasterSheet extends FPDF{
	var $line_ctr=0;
	var $record_index=0;
	var $sheet_max_record =45;
	var $paper_width = 8.5;
	var $PASS = array('r'=>0,'g'=>0, 'b'=>0);
	var $FAIL = array('r'=>255,'g'=>0, 'b'=>0);
	function create($subjects,$info, $data, $start_record_at,$record_max){
		$ENABLE_COLOR_FORMATTING = false;
		$SHEET_TYPE =strtoupper($info[4]);
		$YR_LEVEL= $info['year_level'];
		$PERIOD = strtoupper($info[1]);
		$SY = 'SY '.$info[0];
		$YR_SECTION_LBL = 'YEAR/SECTION:';
		$YR_SECTION = $info['yr_sec'];
		$MAKABAYAN = "MAKABAYAN";
		$SPECIAL_HDRS = array('D',' ','  ','  ');
		foreach($info['months'] as $m){
			array_push($SPECIAL_HDRS,strtoUpper($m));
		}
		$ADVISER = strtoupper($info[3]);
		$PRINCIPAL = 'Nerissa M. Guillermo    ';
		$ADVISER_LABEL = 'Class Adviser';
		$PRINCIPAL_LABEL = "Principal";
		$REF_NO = time();
		$TIME_STAMP = 'Date Printed: '.date("d/m/Y;H:i:s A", time()).'/'.$REF_NO ;
		
		//Prepare page
		$this->AddPage();
		$this->SetMargins(0,0);

		//Prepare Graphics
		$this->SetFillColor(255); // Color white
		$this->SetFont('Arial','',6); //Font

		//Start Special Box
		$this->Rect(0.40,2.13,2.88,0.54); // Year/Section Box
		$this->Rect(3.36,2.13,2.16,0.54); // SY Box
		$this->Rect(5.62,2.13,2.38,0.54); // Period Box
		$this->Rect(0.26,2.73,5.77,0.61);  // Header Box
		$this->Rect(6.16,2.73,0.83,0.61); // Final Box
		$this->Rect(7.04,2.73,1.09,0.61); // Attendance Box
		//End Special Box

		// Special Points
		$record_width = 5.77;
		$record_height = 0.17;
		$record_x = 0.26;
		$record_y = 3.34;
		$offset_x = 0.58-0.26;
		$offset_y = 0.03;
		$offset_name =0.7;
		$final_g_x = 6.16;
		$final_g_y = 3.34;
		$final_g_width = 0.83;
		$att_x = 7.04;
		$att_y = 3.34;
		$att_width = 1.09;
		$student_ctr=0;
		$this->line_ctr = 0;
		$curr_g_flg = '';
		$prev_g_flg='';
		
		//Loop to Plot Cells
		for($ctr =0;$ctr<$this->sheet_max_record;$ctr+=1){
			$this->Rect($record_x,$record_y+($record_height*$ctr),$record_width,$record_height,'FD'); // Grades
			$this->Rect($final_g_x,$final_g_y+($record_height*$ctr),$final_g_width,$record_height,'FD'); // Final Grade
			$this->Rect($att_x,$att_y+($record_height*$ctr),$att_width,$record_height,'FD'); // Attendance
			
		}
		
		//  Start Vertical lines
		$line_start = 2.73;
		$line_end =3.17+( $record_height*($this->sheet_max_record-1));
		$line_x = array(0.58);
		$cell_width =3.69/(count($subjects)+1);
		
		for($index = 0; $index<count($subjects)+2;$index++){
			array_push($line_x,(2.34 + ($cell_width*$index)));
		}
		array_push($line_x,6.16,6.56,6.99,7.40,7.75,8.13);	

		foreach($line_x as $x){
			$this->Line($x,$line_start,$x,$line_end);
		}
		$this->Rect(2.34+($cell_width*4),2.73,$cell_width*5,0.24,'FD'); // Makabayan Box
		$this->Rect(6.16,2.73,0.83,0.61,'FD'); // Final Box
		$this->Rect(7.04,2.73,1.09,0.24,'FD'); // Attendance MiniBox

		//  End Vertical lines

		//Place logo and Labels

		$this->Image('template/images/logo.gif',(($this->paper_width/2 )-(4.08/2)),0.68,4.08,0.59);
		$this->SetFont('Times','',18);
		$this->Text((($this->paper_width/2 )-($this->GetStringWidth($SHEET_TYPE)/2)),1.46, $SHEET_TYPE);
		$this->SetFont('Arial','',15);
		$this->Text((($this->paper_width/2 )-($this->GetStringWidth($YR_LEVEL)/2)),1.80,$YR_LEVEL);
		$this->SetFont('Helvetica','',6);
		$this->Text(0.47,2.30,$YR_SECTION_LBL);
		$this->SetFont('Helvetica','',16);
		$this->Text(0.40+(2.88/2)-($this->GetStringWidth($YR_SECTION)/2),2.50,$YR_SECTION);
		$this->SetFont('Helvetica','',20);
		$this->Text(3.48,2.47,$SY);
		$this->SetFont('Helvetica','',18);
		$this->Text((5.62 + (2.38/2))-($this->GetStringWidth($PERIOD)/2),2.47,$PERIOD);
		$this->SetFont('Helvetica','',11);
		$this->Text(1.20,3.31,'NAME');
		$this->SetFont('Helvetica','',7);
		$this->Text(((2.34+($cell_width*4))+($cell_width*5/2))-($this->GetStringWidth($MAKABAYAN)/2),2.92,$MAKABAYAN);
		$this->SetFont('Helvetica','B',9);
		$this->Text(6.39	,3.30,'FINAL');
		$this->SetFont('Helvetica','',7);
		$this->Text(7.22	,2.93,'ATTENDANCE');
		$this->SetFont('Arial','',10);
		$this->Text(0.26,11.50,'Printed:');
		$this->SetFont('Arial','',9);
		$this->Text(0.91,11.98,$ADVISER_LABEL);
		$this->SetFont('Arial','',10);
		$disp_x = (0.91+($this->GetStringWidth($ADVISER_LABEL)/2))-($this->GetStringWidth($ADVISER)/2);
		$disp_y = 11.82-0.15;
		$this->Text($disp_x,11.82,$ADVISER);
		$this->Line($disp_x,$disp_y,$disp_x+$this->GetStringWidth($ADVISER),$disp_y );
		$this->SetFont('Arial','',10);
		$this->Text(6.16,11.50,'Noted:');
		$this->SetFont('Arial','',9);
		$this->Text(6.73,11.98,$PRINCIPAL_LABEL);
		$this->SetFont('Arial','',10);
		$disp_x = (6.73+($this->GetStringWidth($PRINCIPAL_LABEL)/2))-($this->GetStringWidth($PRINCIPAL)/2);
		$disp_y = 11.82-0.15;
		$this->Text($disp_x,11.82,$PRINCIPAL);
		$this->Line($disp_x,$disp_y,$disp_x+$this->GetStringWidth($PRINCIPAL),$disp_y );
		$this->SetFont('Arial','',7);
		$this->Text(0.26,12.71,$TIME_STAMP);
		foreach($SPECIAL_HDRS as $hdr){
			array_push($subjects, $hdr);
		}
		$subj_arr =$subjects;
		while(count($subj_arr)<10){
			array_push($subj_arr,'');
		}
		
		$subj_x =2.46;
		$subj_y = 3.31;
		$subj_width = 0.12;
		$offset = 0.07;
		$ctr=0;
		$this->SetFont('Helvetica','',6);
		foreach($subj_arr as $subj){
			$offset = (($line_x[$ctr+1]-$line_x[$ctr+2])*0.5) +($this->GetStringWidth($subj)*0.5);
			$this->Text($line_x[$ctr+1]-$offset,$subj_y ,$subj);
			$ctr+=1;
		}
		
		//Plot names and grades
		for($ctr =0;$ctr<count($data);$ctr+=1){
			if($ctr<$record_max && $start_record_at<count($data)){
				$curr_g_flg = $data[$start_record_at][0];
				if($curr_g_flg != $prev_g_flg ){
					$gender_label= $curr_g_flg=='M'?'Boys' : 'Girls'; 
					$student_ctr=0;
					$this->line_ctr+=1;
					$this->SetFont('Helvetica','B',6);
					$this->Text($offset_name,$record_y+($record_height*($this->line_ctr))-$offset_y,$gender_label); // Boy-Girl 
				}
				$prev_g_flg = $curr_g_flg; 
				
				if(is_array($data[$ctr])){
					$this->SetTextColor($this->PASS['r'], $this->PASS['g'], $this->PASS['b']); // BLACK
					$this->SetFont('Helvetica','',6);
					$student_ctr+=1;
					$this->line_ctr+=1;
					$disp_y = $record_y+($record_height*($this->line_ctr))-$offset_y;
					$disp_x =(0.24 +( $offset_x /2) ) - ($this->GetStringWidth(''.$student_ctr)/2);
					$this->Text($disp_x,$disp_y,$student_ctr); // Number
					$this->Text($offset_name,$disp_y,$data[$start_record_at][2]); // Name
					//Plot grades
					for($subj_ctr=0;$subj_ctr<count($subjects);$subj_ctr+=1){
						
						$subj_grade = $data[$ctr][$subj_ctr+3];
						if($ENABLE_COLOR_FORMATTING){
							$this->SetTextColor($this->PASS['r'], $this->PASS['g'], $this->PASS['b']); // BLACK
							if($subj_grade<75 &&  is_numeric($subj_grade) && $subj_ctr<count($subjects)-count($SPECIAL_HDRS)){
								$this->SetTextColor($this->FAIL['r'], $this->FAIL['g'], $this->FAIL['b']); // CRIMSON
							}
						}
						$offset = (($line_x[$subj_ctr+1]-$line_x[$subj_ctr+2])*0.5) +($this->GetStringWidth($subj_grade)*0.5);
						$this->Text($line_x[$subj_ctr+1]-$offset,$disp_y ,$subj_grade);
					}
					$start_record_at+=1;
					$this->record_index+=1;
					
				}
				
			}
		}
	}
	public function out(){
		$this->Output();
	}
}
?>
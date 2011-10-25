<?php

$info = json_decode($_POST['info']);
$data =json_decode($_POST['dataset']);
error_reporting(E_ALL);
require_once 'Classes/PHPExcel/IOFactory.php';

//Prepare TEMPLATE
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("templates/hta_temp_cgs.xlsx");
$objWorksheet = $objPHPExcel->setActiveSheetIndex(0)->setShowGridlines(false);

//BUILD META
include('../header.php');
$EGB->db_connect();
$section_obj= $EGB->get_sec_alias($info[2]);
$objPHPExcel->getActiveSheet()->setCellValue('D4', $info[0]);
$objPHPExcel->getActiveSheet()->setCellValue('J4', $info[1]);
$objPHPExcel->getActiveSheet()->setCellValue('V4',$section_obj[0]['dept'].' '.$section_obj[0]['level'] );
$objPHPExcel->getActiveSheet()->setCellValue('Z4', $section_obj[0]['section']);
$objPHPExcel->getActiveSheet()->setCellValue('D36', $info[3]);

$EGB->db_close();

//BUILD DATA SET

$row_ctr =6;
$col_ctr = 9;
$row_one = $data[0];

// Populate Column with Subjects
for($index=2; $index<count($row_one);$index++){
	$objWorksheet->setCellValueByColumnAndRow($col_ctr, $row_ctr, $row_one[$index]);
	$col_ctr+=1;
}


$actual_data = 1;
$row_ctr = 11;
$student_count=0;
//Popuate cells with student's data
for($index =$actual_data; $index< count($data); $index++){
	$student_row = $data[$index];
	$col_ctr = 9;
	for($ctr=0; $ctr<count($student_row);$ctr++){
		if($ctr==0){
			// B11
			//Student Number 
			if($student_row[0]=='Boys' ||$student_row[0]=='Girls' ){
				$student_count=0;
			}
			$objWorksheet->setCellValueByColumnAndRow(1, $row_ctr, $student_row[0]);
		}else if($ctr==1){
			//D11
			//Student Name			
			$objWorksheet->setCellValueByColumnAndRow(0, $row_ctr,$student_count+=1);
			$objWorksheet->setCellValueByColumnAndRow(3, $row_ctr, $student_row[1]);
		}else {
			//K11
			//Actual Grade
			$objWorksheet->setCellValueByColumnAndRow($col_ctr, $row_ctr, $student_row[$ctr]);
			$col_ctr+=1;
		}
	}
	$row_ctr+=1;
}
$token='';
for($c=1;$c<40;$c++){
	$token .= '>';
}

$objWorksheet->mergeCells('J'.$row_ctr.':AB'.$row_ctr);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row_ctr, $token);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row_ctr, $token);
$objPHPExcel->getActiveSheet()->setCellValue('J' . $row_ctr, $token.'Nothing follows'.$token);
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="01simple.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


?>
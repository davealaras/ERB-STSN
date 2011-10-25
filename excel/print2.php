<?php

$info = json_decode($_POST['info']);
$data =json_decode($_POST['dataset']);
error_reporting(E_ALL);
require_once 'Classes/PHPExcel/IOFactory.php';

//Prepare TEMPLATE
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("templates/cgs_temp.xlsx");
$objWorksheet = $objPHPExcel->setActiveSheetIndex(0)->setShowGridlines(0);

//BUILD META
include('../header.php');
$EGB->db_connect();
$section_obj= $EGB->get_sec_alias($info[2]);
$objPHPExcel->getActiveSheet()->setCellValue('C4', $info[0]); // SY
$objPHPExcel->getActiveSheet()->setCellValue('H4', $info[1]); //Period
$objPHPExcel->getActiveSheet()->setCellValue('O4',$section_obj[0]['dept'].' '.$section_obj[0]['level'] ); // Grade Year
$objPHPExcel->getActiveSheet()->setCellValue('S4', $section_obj[0]['section']); // Section
$objPHPExcel->getActiveSheet()->setCellValue('B36', $info[3]); //Adviser

$EGB->db_close();

//BUILD DATA SET

$row_ctr =5;
$col_ctr = 7;
$row_one = $data[0];

// Populate Column with Subjects
for($index=2; $index<count($row_one);$index++){
	$objWorksheet->setCellValueByColumnAndRow($col_ctr, $row_ctr, $row_one[$index]);
	$col_ctr+=1;
}
$actual_data = 1;
$row_ctr = 6;
$student_count=0;

/*
//Popuate cells with student's data
for($index =$actual_data; $index< count($data); $index++){
	$student_row = $data[$index];
	$col_ctr = 7;
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
for($c=1;$c<20;$c++){
	$token .= '>';
}
*/
/*
$objWorksheet->mergeCells('H'.$row_ctr.':Z'.$row_ctr);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row_ctr, '>>>>>>>');
$objPHPExcel->getActiveSheet()->setCellValue('H' . $row_ctr, $token.'Nothing follows'.$token);
/*
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="01simple.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
*/
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);

header('Content-Type: application/pdf');
header('Content-Disposition: inline;filename="01simple.pdf"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
$objWriter->save('php://output');
exit;

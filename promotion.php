<?php
/** This is the same script as loading an Excel workbook to PHP Excel in the previous section*/
include('header.php');
$EGB->db_connect();

error_reporting(E_ALL);
require_once 'excel/Classes/PHPExcel/IOFactory.php';
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("excel/templates/promotion.xlsx");
$objWorksheet = $objPHPExcel->setActiveSheetIndex(0)->setShowGridlines(false);
$objWorksheet->getCell('D7')->setValue('HS2011');
$start=12;
foreach($EGB->get_stud_nrol('', $_GET['seccode'], 2011) as $student){
	$objWorksheet->getCell('B'.$start)->setValue($student ['fullname']);
	$stud_201 = $EGB->get_stud201($student ['sno']);
	$address = $stud_201['h_sn']. ', '. $stud_201['h_m'];
	$objWorksheet->getCell('E'.$start)->setValue($address);
	$start+=1;
}
header('Content-Type: application/pdf');
header('Content-Disposition: inline;filename="01simple.pdf"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
$objWriter->save('php://output');
exit;


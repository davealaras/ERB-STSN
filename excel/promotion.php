<?php
/** This is the same script as loading an Excel workbook to PHP Excel in the previous section*/
error_reporting(E_ALL);
require_once 'Classes/PHPExcel/IOFactory.php';
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("templates/promotion.xlsx");
$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
$objWorksheet->getCell('A1')->setValue('Jack and Jill went up the hill to fetch a pail of water.');
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="01simple.pdf"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel , 'PDF');

header('php://output');
?>

<?php
$data =json_decode($_POST['dataset']);
require 'php-excel.class.php';
$xls = new Excel_XML('UTF-8', true, 'My Test Sheet');
$xls->addArray($data);
$xls->generateXML('a-test');

?>
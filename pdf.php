<?php
include('header.php');
$students = $EGB->get_stud_nrol('KINDSES', 1002, 2011);
$mypdf = PDF_new();
PDF_open_file($mypdf, "");
PDF_begin_page($mypdf, 900, 1000);
$myfont = PDF_findfont($mypdf, "Times-Roman", "host", 0);
PDF_setfont($mypdf, $myfont, 10);
$ctr =10;
foreach($students as $student){
	PDF_show_xy($mypdf, $student['fullname'], 50, $ctr);
	$ctr+=9;
}

PDF_end_page($mypdf);
PDF_close($mypdf);

$mybuf = PDF_get_buffer($mypdf);
$mylen = strlen($mybuf);
header("Content-type: application/pdf");
header("Content-Length: $mylen");
header("Content-Disposition: inline; filename=gen01.pdf");
print $mybuf;

PDF_delete($mypdf);
?>
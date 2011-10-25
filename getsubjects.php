<?php
	include('header.php');
	$EGB->db_connect();
	print_r($EGB->get_subjects('HS', 1, 2011));
	$EGB->db_close();
	
?>
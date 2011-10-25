<?php
		$con = mssql_connect("DAVE-PC\DBASE","sa","12345");
		mssql_select_db('egb',$con);
		
		$stmt=mssql_init("sp_Compute", $con);
		$compcode='VALUESX';
		$sectioncode='1002';
		$sy=2011;
		$period=1;
		mssql_bind($stmt,"@CompCode",$compcode,SQLVARCHAR);
		mssql_bind($stmt,"@SectionCode",$sectioncode,SQLVARCHAR);
		mssql_bind($stmt,"@SY",$sy,SQLINT4);
		mssql_bind($stmt,"@Period",$period,SQLINT4);
		$result = mssql_execute($stmt);
		while($row=mssql_fetch_array($result)){
		print_r($row);
		}
		
		
?>
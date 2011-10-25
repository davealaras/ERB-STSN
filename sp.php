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
		$r = mssql_execute($stmt);
		$equivalent = array();
		$result = array();
		while($row=mssql_fetch_array($r)){
			$result['sno']= $row['sno'];
			$result['hdr']= $row['hdr'];
			$result['equivalent']= $row['equivalent'];
			array_push($equivalent, $result);			
		}
		echo json_encode($equivalent);
		
		
?>
<div class="tab" >
	<div class="tab-header">
	 Temporary Consolidated
	</div>
	<div class="tab-content">
	<form id="ex-cgs" action="excel/export.php" method="POST">
					<input type="hidden" class="dataset" name="dataset"/>
				</form>
				<form id="pr-cgs" action="fpdf17/createpdf.php" method="POST"  target="_blank">
					<input type="hidden" class="info" name="info"/>
					<input type="hidden" class="dataset" name="dataset"/>
					<input type="hidden" class="alias" name="alias"/>
					<input type="hidden" class="months" name="months"/>
				</form>
					<div class="clear-div"></div>
						<span class="art-button-wrapper left" >
								<span class="l"> </span>
								<span class="r"> </span>
								<span class="art-button export_btn" datasource="gradesheet" form="ex-cgs" ><img src="img/export-excel.png" /> Export to Excel</span>
						</span>
						<span class="art-button-wrapper right">
								<span class="l"> </span>
								<span class="r"> </span>
								<span class="art-button print_btn" datasource="gradesheet" form="pr-cgs" ><img src="icons/printer.png" /> Print</span>
						</span>					
				<div class="clear-div"></div>
	<div id="div-rawscore">
						<table id="gradesheet"  class="datagrid">
							<tr class="label">
								<td class="mini head">
								ID No.
								</td>
							   <td class="head" id="name_student">
								<div class="jumbo">Name of Student</div>
								</td>
								<td class="eof head">                               
								</td>
							</tr>
							<tr class="list">
							</tr>
						</table>
					<div class="clear-div"></div>
					<div class=" post_btn">
						<span class="art-button-wrapper right">
								<span class="l"> </span>
								<span class="r"> </span>
								<span class="art-button " >POST GRADES</span>
						</span>
					</div>
				<div class="clear-div"></div>
				</div>
<hr>
</div>
</div>

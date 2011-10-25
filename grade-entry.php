<div class="tab" >
	<div class="tab-header">
		Grade Entry
	</div>
	<div class="tab-content">
		
		<div id="top_tabs">
			<ul>
				<li><a href="#tabs-1">Raw Score</a></li>
				<li><a href="#tabs-2">Equivalent</a></li>
				<li><a href="#tabs-3">Summary</a></li>
				<li><a href="#tabs-4">Overall</a></li>
			</ul>
			<div id="tabs-1">
			
				
				<form id="ex0-eq" action="excel/export.php" method="POST">
					<input type="hidden" class="dataset" name="dataset"/>
				</form>
				<div class="exporter">
					<span class="art-button-wrapper left">
							<span class="l"> </span>
							<span class="r"> </span>
							<span class="art-button export_btn" datasource="rawscores" form="ex0-eq" ><img src="img/export-excel.png" /> Export to Excel</span>
					</span>
				</div>
				<div class="neatbox" id="grade-sheet">
					<div id="div-rawscore">
						<table id="rawscores"  class="datagrid">
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
                    </div>
                 </div>
			</div>
			<div id="tabs-2">
				<form id="ex4-eq" action="excel/export.php" method="POST">
					<input type="hidden" class="dataset" name="dataset"/>
				</form>
				<div class="exporter">
					<span class="art-button-wrapper left">
							<span class="l"> </span>
							<span class="r"> </span>
							<span class="art-button export_btn" datasource="equivalent" form="ex4-eq" ><img src="img/export-excel.png" /> Export to Excel</span>
					</span>
				</div>
				<div class="neatbox">
            			<div  id="div-equivalent">
                		<table id="equivalent"  class="datagrid" >
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
                        </div>
                 </div>
			</div>
			<div id="tabs-3">
				<form id="ex4-sm" action="excel/export.php" method="POST">
					<input type="hidden" class="dataset" name="dataset"/>
				</form>
				<div class="exporter">
					<span class="art-button-wrapper left">
							<span class="l"> </span>
							<span class="r"> </span>
							<span class="art-button export_btn" id="export-summary" datasource="summary" form="ex4-sm"><img src="img/export-excel.png" /> Export to Excel</span>
					</span>
				</div>
				<div class="neatbox">
            		<div id="div-summary">
                		<table id="summary"  class="datagrid"  >
                        	<tr class="label">
                            	<td class="mini head">
                                ID No.
                                </td>
                               <td class="head" id="name_student">
                                <div class="jumbo">Name of Student</div>
                                </td>
                                <td class="eos head">
								<div class="small">Total</div>
                                </td>								
                            </tr>
							<tr class="list">
							</tr>
                        </table>
                       </div>
                 </div>
			</div>
			<div id="tabs-4">
				<form id="ex4-oa" action="excel/export.php" method="POST">
					<input type="hidden" class="dataset" name="dataset"/>
				</form>
				<div class="exporter">
					<span class="art-button-wrapper left">
							<span class="l"> </span>
							<span class="r"> </span>
							<span class="art-button export_btn" id="export-overall" datasource="overall" form="ex4-oa" ><img src="img/export-excel.png" /> Export to Excel</span>
					</span>
				</div>
				<div class="neatbox">
					<div id="div-overall">
						<table id="overall"  class="datagrid" >
							<tr class="label">
								<td class="mini head">
								ID No.
								</td>
							   <td class="head" id="name_student">
								<div class="jumbo">Name of Student</div>
								</td>
								<td class="eoo head">
									<div style="height:20px;width:60px;overflow:hidden;">
										<div style="width:150px;overflow:hidden;height:15px;text-align:left;">Equivalent</div>
									</div>									
								</td>
							</tr>
							<tr class="list">
							</tr>
						</table>
				   </div>
			   </div>
			</div>
		</div>
	</div>
</div>

<div class="tab" >
	<div class="tab-header">
		Adjustments
	</div>
	<div class="tab-content">
		<div id="div-adjustments">
			<table id="adjustments"  class="datagrid">
				<tr class="label">
					<td class="mini head">
					ID No.
					</td>
				   <td class="head" id="name_student">
					<div class="jumbo">Name of Student</div>
					</td>
					<td class="eos head">
					<div class="small">Total</div>
					</td>								
				</tr>
				<tr class="list">
				</tr>
			</table>
        </div>
	</div>
</div>	
		
<span class="art-button-wrapper left" id="close_btn" style="opacity:0">
					<span class="l"> </span>
					<span class="r"> </span>
					<a class="art-button " href="javascript:void(0)"   >Close</a>
				</span>	
	<?php $allow = true; if($allow){ ?>
<span class="art-button-wrapper right">
					<span class="l"> </span>
					<span class="r"> </span>
					<a class="art-button " href="javascript:void(0)"  id="post-grades"  style="display:none;"><!--<img src="img/export-excel.png" />--> Send to CGS</a>
				</span>	
				
				<?php } ?>
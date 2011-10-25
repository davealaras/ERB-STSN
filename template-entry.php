<div class="tab" id="tab_tmpl8_vw">
	<div class="tab-header">
		Template(s)
	</div>
	<div class="tab-content">
		<table id="tbTemplates"class="datagrid">
				<thead>
			<tr class="labels">
				<td class="large head">Template Name</td>
				<td class="large head">Created By</td>
				<td class="large head">Year/Level</td>
				<td class="mini head">Status</td>
				<td class="mini head">&nbsp;</td>
			</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div class="tab" id="tab_tmpl8_entry" >
	<div class="tab-header">
		Template Entry
	</div>
	<div class="tab-content">
		<div class="tab" id="general-components">
			<div class="tab-header">
				General Components
			</div>
			<div class="tab-content">
	        	<div class="neatbox">	
					<div class="column left nopad">
                        <span id="error-terminal" style="display:none"></span>
                		<table id="tbGenComp"  class="datagrid"  cellpadding="0" cellspacing="0" width="50px">
                        	<thead>
							<tr class="label">
                            	<td class="mini head">
                                Row
                                </td>
                                <td class="small head">
                                CCLASS
                                </td>
                                <td class="large head">
                                DESCRIPTION
                                </td>
                                <td class="mini head">
                                %
                                </td>
                            </tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr class="label" id="input-source">
									<td class="mini">
									<span id="row-counter">1</span>
									</td>
									<td class="small">
									 <span id="class-code">####</span>
									</td>
									<td class="xlarge">
										 <select id="description"class="xlarge">
											<option id="default"> Select a component</option>
											<option value="QUIZ">Quizzes</option>
											<option value="MJEX">Major Exam</option>
											<option value="PROJ">Projects</option>
										</select>
									</td>
									<td class="mini">
										<input type="text" class="mini" id="percentage"/>
									</td>
									<td class="mini noborder">
										<a href="javascript:void();" class="add-btn disable-text" inside="tbGenComp"><img src="img/add.png"/></a>
									</td>
								</tr>
								<tr class="label">
									<td class="mini noborder">
									
									</td>
									<td class="small noborder">
									
									</td>
									<td class="large noborder">
									<div id="total_label">Total</div>
									</td>
									<td class="mini noborder">
										  <div id="total">###</div>
									</td>
								</tr>
							</tfoot>
                        </table>
                	</div>
              </div>
			</div>
		</div>

		 <span class="art-button-wrapper">
			<span class="l"> </span>
			<span class="r"> </span>
			<a class="art-button" id="close_btn" inside="tbMeasItem" href="javascript:void(0);" >Close</a>
		 </span>
		 <div class="right">
							 <span class="art-button-wrapper" style="display:none">
                                <span class="l"> </span>
                                <span class="r"> </span>
                                <a class="art-button update-record" inside="tbMeasItem" href="javascript:void(0);" >Update</a>
                             </span>
							 <span class="art-button-wrapper" style="display:none">
                                <span class="l"> </span>
                                <span class="r"> </span>
                                <a class="art-button save-record" inside="tbMeasItem" href="javascript:void(0);" >Save</a>
                             </span>
							 <span class="art-button-wrapper" style="display:none">
                                <span class="l"> </span>
                                <span class="r"> </span>
                                <a class="art-button cancel-action" inside="tbMeasItem" href="javascript:void(0);" >Cancel</a>
                             </span>
                        </div>
	</div>
</div>
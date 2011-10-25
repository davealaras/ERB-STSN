<div class="tab">
	<div class="tab-header">
		Components
	</div>
	<div class="tab-content">
		<div class="tab" id="general-components">
			<div class="tab-header">
				General Components
			</div>
			<div class="tab-content">
	        	<div class="neatbox">
				<div class="column right nopad" style="margin-left:30px;">
                    		Copy Form
                		<table style="margin-left:10px; margin-top:10px;" >
                        	<tr>
                                <td>
                                    Template
								</td>
                                <td>
                                	<input type="hidden" value="" id="template_components" />
                                    <select class="large" id="templates">
                                	</select>
								</td>
                            </tr>
                        </table>
                        
                	</div>

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
										 <select id="description"class="xlarge" disabled="disabled">
											<option id="default"> Select a component</option>
											<option value="QUIZ">Quizzes</option>
											<option value="MJEX">Major Exam</option>
											<option value="PROJ">Projects</option>
										</select>
									</td>
									<td class="mini">
										<input type="text" class="mini" id="percentage" disabled="disabled"/>
									</td>
									<td class="mini noborder">
									<!--
										<a href="javascript:void();" class="add-btn disable-text" inside="tbGenComp"><img src="img/add.png"/></a>
										-->
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
		<div class="tab" id="measurable-items">
			<div class="tab-header">
				Measureable Items
			</div>
			<div class="tab-content">
				<div id="nodata"></div>
            	<div class="neatbox">
					<div class="column left nopad">
						<table id="tbMeasItem"  class="datagrid" cellpadding="0" cellspacing="0"  width="50px">
						<thead>
                        	<tr class="label">
                            	<td class="mini head">
                                Col
                                </td>
                                <td class="small head">
                               CCode
                                </td>
                                <td class="small head">
                               Header
                                </td>
                                <td class="large head">
                                DESCRIPTION
                                </td>
                                <td class="small head">
                                No of item
                                </td>
                                <td class="small head">
                                Base
                                </td>
                            </tr>
							<thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr class="label" id="data-source">
									<td class="mini">
								   <span id="col-counter"> 1</span>
									</td>
									<td class="small head">
								   <select id="classcodes" class="small">
								   </select>
									</td>
									<td class="small head">
								   <input type="text" class="small" id="item_header" disabled="disabled"/>
									</td>
									<td class="large head">
									 <input type="text" class="large" id="item-desc" disabled="disabled"/>
									</td>
									<td class="small head">
									 <input type="text" class="small" id="items" disabled="disabled"/>
									</td>
									<td class="small head">
									 <input type="text" class="small" id="base" disabled="disabled"/>
									</td>
									<td class="mini noborder">
										<!-- <a href="javascript:void();" class="add-btn disable-text" inside="tbMeasItem"><img src="img/add.png"/></a> -->
									</td>
								</tr>
							</tfoot>
                           </table>
					</div>
				</div>
			</div>
		</div>	
		 <div class="right">
                        	<span class="art-button-wrapper">
                                <span class="l"> </span>
                                <span class="r"> </span>
                                <a class="art-button modify-record" inside="tbMeasItem" href="javascript:void(0);" >Modify</a>
                             </span>
							 <span class="art-button-wrapper">
                                <span class="l"> </span>
                                <span class="r"> </span>
                                <a class="art-button save-record" inside="tbMeasItem" href="javascript:void(0);" >Save</a>
                             </span>
							 <span class="art-button-wrapper">
                                <span class="l"> </span>
                                <span class="r"> </span>
                                <a class="art-button cancel-action" inside="tbMeasItem" href="javascript:void(0);" >Cancel</a>
                             </span>
                        </div>
		 <span class="art-button-wrapper">
			<span class="l"> </span>
			<span class="r"> </span>
			<a class="art-button" id="close_btn" inside="tbMeasItem" href="javascript:void(0);" >Close</a>
		 </span>
	</div>
</div>
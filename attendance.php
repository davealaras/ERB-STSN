<?php
error_reporting(0);
session_start();
$ses = $_SESSION;
 include('top.php');?>
	
		 <script type="text/javascript" src="js/attendance.js"></script>
		 
	</head>
	<body>
	<div id="art-page-background-simple-gradient">
			<div id="art-page-background-gradient"></div>
		</div>
		<div id="art-main">
			<div class="art-sheet">
				<div class="art-sheet-tl"></div>
				<div class="art-sheet-tr"></div>
				<div class="art-sheet-bl"></div>
				<div class="art-sheet-br"></div>
				<div class="art-sheet-tc"></div>
				<div class="art-sheet-bc"></div>
				<div class="art-sheet-cl"></div>
				<div class="art-sheet-cr"></div>
				<div class="art-sheet-cc"></div>
				<div class="art-sheet-body">
					<div class="art-header">
						<div class="art-header-png"></div>
						<div class="art-header-jpeg"></div>
						<script type="text/javascript" src="js/swfobject.js"></script>
						<div id="art-flash-area">
						<div id="art-flash-container">
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="876" height="150" id="art-flash-object">
							<param name="movie" value="container.swf" />
							<param name="quality" value="high" />
							<param name="scale" value="default" />
							<param name="wmode" value="transparent" />
							<param name="flashvars" value="color1=0xFFFFFF&amp;alpha1=.50&amp;framerate1=24&amp;clip=images/flash.swf&amp;radius=4&amp;clipx=-8&amp;clipy=-36&amp;initalclipw=900&amp;initalcliph=225&amp;clipw=893&amp;cliph=223&amp;width=876&amp;height=150&amp;textblock_width=0&amp;textblock_align=no" />
							<param name="swfliveconnect" value="true" />
							<!--[if !IE]>-->
							<object type="application/x-shockwave-flash" data="container.swf" width="876" height="150">
								<param name="quality" value="high" />
								<param name="scale" value="default" />
								<param name="wmode" value="transparent" />
								<param name="flashvars" value="color1=0xFFFFFF&amp;alpha1=.50&amp;framerate1=24&amp;clip=images/flash.swf&amp;radius=4&amp;clipx=-8&amp;clipy=-36&amp;initalclipw=900&amp;initalcliph=225&amp;clipw=893&amp;cliph=223&amp;width=876&amp;height=150&amp;textblock_width=0&amp;textblock_align=no" />
								<param name="swfliveconnect" value="true" />
							<!--<![endif]-->
								<div class="art-flash-alt"><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></div>
							<!--[if !IE]>-->
							</object>
							<!--<![endif]-->
						</object>
						</div>
						</div>
						<script type="text/javascript">swfobject.switchOffAutoHideShow();swfobject.registerObject("art-flash-object", "9.0.0", "expressInstall.swf");</script>
						<div class="art-logo">
							<div id="logo">
							<img src="img/STSN Logo.png" />
							</div>
							<h1 id="name-text" class="art-logo-name"><a href="#"><img src="img/ERB2.png" style="margin-left:0px;height:50px;margin-top:-10px;" /></a></h1>
							<div id="slogan-text" class="art-logo-text" style="display:none"> </div>
							<br />
							<br /><br /><br /><br />
							<div id="slogan-text" class="art-logo-text2"> </div>
						</div>
						
						
				  </div>
					<div class="art-nav">
						<div class="l"></div>
						<div class="r"></div>
					<?php
						include('menu.php');
						?>
					</div>
					<div class="art-content-layout">
						<div class="art-content-layout-row">
							<div class="art-layout-cell art-content">
								<div class="art-post">
									<div class="art-post-tl"></div>
									<div class="art-post-tr"></div>
									<div class="art-post-bl"></div>
									<div class="art-post-br"></div>
									<div class="art-post-tc"></div>
									<div class="art-post-bc"></div>
									<div class="art-post-cl"></div>
									<div class="art-post-cr"></div>
									<div class="art-post-cc"></div>
									<div class="art-post-body">
								<div class="art-post-inner">
									<div class="art-postcontent">
									<!-- article-content -->
									<div class="cleared"></div>
										<div class="art-content-layout overview-table">
											<div class="art-content-layout-row">
												<div class="art-layout-cell login-holder" id="LEFT-CONTENT">
													<div class="overview-table-inner">
														<?php include('loginform.php');?>
													</div>
												</div><!-- end cell -->
												<div class="art-layout-cell" id="RIGHT-CONTENT">
													<div class="overview-table-inner">
														<!--	<div id="debug" >
															</div>-->
															  <table border="0">
													  <tr valign="top">
														   <td><img src="img/RegisterIcon.jpg" /></td>
														<td><h1>Attendance</h1></td>
													  </tr>
													</table>
															  <hr />
															<table cellpadding="0" cellspacing="0" class="container" width="100%">
																<tr>
																	<td>
																	<div align="right">Faculty ID: </div></td>
																	<td>
																		<input class="large disable-text" id="faculty_id" type="text" disabled="disabled"/ >
																	</td>
																	 <td>
																	<div class="spacer"></div>
																	</td>
																	<td>
																	<div align="right">Faculty Name: </div>
																	</td>
																	<td>
																		<input class="xlarge disable-text" id="full_name" type="text" disabled="disabled"/>
																	</td>
																	<td>
																	</td>
																	<td>
																	</td>
																	<td>
																	</td>
																</tr>
																<tr>
																	<td>
																	<div align="right">SY: </div></td>
																	 <td>
																		<select id="sy" class="large">
																		</select>
																	</td>
																	  <td>
																	<div class="spacer"></div>
																	</td>
																	<td>
																	<div align="right">Subject/Section: </div>
																	</td>
																	<td>
																		<select type="text"  id="load" class="xlarge">
																		</select>
																	</td>
																	<td>
																	<div class="spacer"></div>
																	</td>
																	<td>
																	<div align="right">Period: </div>
																	</td>
																	<td>
																		<select id="period" class="large">
																		</select>
																	</td>
																</tr>
																<tr>
																  <td>&nbsp;</td>
																  <td>&nbsp;</td>
																  <td>&nbsp;</td>
																  <td>&nbsp;</td>
																  <td>&nbsp;</td>
																  <td>&nbsp;</td>
																  <td>&nbsp;</td>
																  <td>
																	<div align="right">
																		<span class="art-button-wrapper">
																			<span class="l"> </span>
																			<span class="r"> </span>
																			<a class="art-button load_btn" href="javascript:void(0)" >Load</a>
																		</span>
																	</div>
																  </td>
															  </tr>
															</table>
															<div id="entry-form">
															<div id="progress-bar"></div>
															<?php include('attendance-sheet.php');?>
															</div>   
													</div>
												</div><!-- end cell -->
											</div><!-- end row -->
										</div><!-- end table -->
														
													<!-- /article-content -->
												</div>
												<div class="cleared"></div>
								</div>
								
										<div class="cleared"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="cleared"></div><div class="art-footer">
						<div class="art-footer-inner">
							<div class="art-footer-text">
								<p><a href="#">Contact Us</a> | <a href="#">Terms of Use</a> | <a href="#">Trademarks</a>
									| <a href="#">Privacy Statement</a><br />
									Copyright &copy; 2011 ---. All Rights Reserved.</p>
							</div>
						</div>
						<div class="art-footer-background"></div>
					</div>
					<div class="cleared"></div>
				</div>
			</div>
			<div class="cleared"></div>
			<p class="art-page-footer"></p>
		</div>
		
	</body>
	</html>

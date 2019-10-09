<?php
$PAGETITLE = "Server Information";
// initializing
GLOBAL $directory, $user_agent;
REQUIRE "TemplateHeader.php";
?>
<style>
.phpinfo pre {margin: 0; font-family: monospace;}
.phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
.phpinfo a:hover {text-decoration: underline;}
.phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
.phpinfo .center {text-align: center;}
.phpinfo .center table {margin: 1em auto; text-align: left;}
.phpinfo .center th {text-align: center !important;}
.phpinfo td, .phpinfo th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
.phpinfo h1 {font-size: 150%;}
.phpinfo h2 {font-size: 125%;}
.phpinfo .p {text-align: left;}
.phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
.phpinfo .h {background-color: #99c; font-weight: bold;}
.phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto;}
.phpinfo .v i {color: #999;}
.phpinfo img {float: right; border: 0;}
.phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
</style>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <a href="<?php print $config->base_url(); ?>Users" title="List all Admin users" class="tip-bottom"> <i class="icon-list"></i> Admin Users </a> <i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
  </div>
<!--End-breadcrumbs-->
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-content" >
	  <div class="row-fluid">
		
		<div class="span12">
		  <div class="widget-box">
		  <div class='modify_result'></div>
		  <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<h5><?php print $PAGETITLE; ?></h5>
          </div>
          <div class="widget-content nopadding">			
				
				<div class="span6">
				<div>
					<div method="post" class="j-pro">
					<div class="j-content" style="padding-bottom:00px;">
						<table class="table table-bordered">
						  <thead>
							<tr>
							  <td class="alert alert-primary" colspan="2" colspan="2"><strong>RELEVANT INFORMATION ABOUT THIS SERVER</strong></td>
							</tr>
						  </thead>
						  <tbody>
							<tr>
								<td width="40%">Server Status</td>
								<td>
									<?php 
									// get the user file upload status
									$server_status = 1;
									?>
									<div id="server_status"><span class="btn <?php print ($server_status) ? "btn-success" : "btn-danger"; ?>"><?php print ($server_status) ? "<i class='icon icon-thumbs-up'></i> ACTIVE" : "<i class='icon icon-thumbs-down'></i> INACTIVE"; ?></span></div>
								</td>
							</tr>
							<tr>
								<td>Base URL:</td>
								<td>
									<?php print $config->base_url(); ?>
								</td>
							</tr>
							<tr>
								<td>Operating System:</td>
								<td>
									<?php print php_uname(); ?>
								</td>
							</tr>
							<tr>
								<td>PHP Version:</td>
								<td>
									<?php print phpversion(); ?>
								</td>
							</tr>
							<tr>
								<td>phpMyAdmin Version:</td>
								<td>
									<?php print mysqli_get_client_info(); ?>
								</td>
							</tr>
							<tr>
								<td>Database Server Version:</td>
								<td>
									<?php print mysqli_get_client_version(); ?>
								</td>
							</tr>
							<tr>
								<td>Webserver Software:</td>
								<td>
									<?php print $server->get_server_software(); ?>
								</td>
							</tr>
							<tr>
								<td>WebServer - PHP Interface: </td>
								<td>
									<?php print php_sapi_name(); ?>
								</td>
							</tr>
							<tr>
								<td>MatrixFileManager Version: </td>
								<td>
									<?php print config_item('serverversion'); ?>
								</td>
							</tr>
							<tr>
								<td>Browser Version: </td>
								<td>
									<?php print $user_agent->browser(). " ". $user_agent->version(); ?>
								</td>
							</tr>
							<tr><td class="alert alert-primary" colspan="2"><strong>IMPORTANT PHP SETTINGS: </strong></td></tr>
							<tr>
								<td colspan="2">
									<table width="100%" cellspacing="1" cellpadding="1" border="0">
										<tr>
											<td valign="top">
												<?php echo  'Safe Mode'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('safe_mode', 0); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Open basedir'; ?>:
											</td>
											<td>
											<?php echo (($ob = ini_get('open_basedir')) ? $ob : 'none'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'PHP Errors'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('display_errors', 0 ); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Short Open Tags'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('short_open_tag', 0 ); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'File Uploads'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('file_uploads'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'File Upload Max Size'; ?>:
											</td>
											<td>
											<?php echo ini_get('upload_max_filesize'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Form Post Max Size'; ?>:
											</td>
											<td>
											<?php echo ini_get('post_max_size'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Maximum Files to Upload'; ?>:
											</td>
											<td>
											<?php echo ini_get('max_file_uploads'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Magic Quotes'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('magic_quotes_gpc'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Register Globals'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('register_globals', 0); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Output Buffer'; ?>:
											</td>
											<td>
											<?php echo $server->get_php_setting('output_buffering', 0); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Session Savepath'; ?>:
											</td>
											<td>
											<?php echo (( $sp=ini_get( 'session.save_path' )) ? $sp : 'none'); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Session auto start'; ?>:
											</td>
											<td>
											<?php echo intval( ini_get( 'session.auto_start' ) ); ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'XML enabled'; ?>:
											</td>
											<td>
												<?php echo extension_loaded('xml') ? '<font style="color: green;">Yes</font>' : '<font style="color: red;">No</font>'; ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'ZLIB enabled'; ?>:
											</td>
											<td>
											<?php echo extension_loaded('zlib') ? '<font style="color: green;">Yes</font>' : '<font style="color: red;">No</font>'; ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Curl enabled'; ?>:
											</td>
											<td>
											<?php echo extension_loaded('curl') ? '<font style="color: green;">Yes</font>' : '<font style="color: red;">No</font>'; ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo  'Disabled functions'; ?>:
											</td>
											<td>
											<?php echo (( $df=ini_get('disable_functions' )) ? $df : 'none'); ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						  </tbody>
						</table>					
					</div>
					</div>
					
					
					
					<div method="post" class="j-pro">
					<div class="j-content" style="padding-bottom:00px;">
						<table class="table table-bordered">
						  <thead>
							<tr>
							  <td colspan="2" class="alert alert-primary"><strong>CHANGELOGS</strong></td>
							</tr>
						  </thead>
						  <tbody>
							<tr>
								<td colspan="2">
									<?php PRINT NL2BR(FILE_GET_CONTENTS('changelogs.txt')); ?>
								</td>
							</tr>
						  </tbody>
						</table>					
					</div>
					</div>
					
				</div>
				</div>
				<div class="span6">
				<div method="post" class="j-pro">					
					<?php echo $server->php_info(); ?>					
				</div>
				</div>
				
		</div>
	  </div>
	</div>
  </div>
</div>
</div>
</div>
</div>

<!--End-Chart-box-->
<?php 
REQUIRE "TemplateFooter.php";
?>
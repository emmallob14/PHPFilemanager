<?php
$PAGETITLE = "Update Office Info";
// initializing
GLOBAL $directory;
$FILE_FOUND = FALSE;
$office_id = $session->userdata("officeID");
REQUIRE "TemplateHeader.php";
# confirm that the person trying to view the details is an admin
# and also the id does not match his or her credentials
IF(!ISSET($SITEURL[1])) {
	$office_id = $office_id;
# return the username of the person who is currently logged in
} ELSEIF(ISSET($SITEURL[1]) AND !$admin_user->confirm_super_user() AND $SITEURL[1] != $office_id) {
	$office_id = $office_id;
} ELSE {
	$office_id = xss_clean($SITEURL[1]);
}
// set some new sessions
$session->set_userdata("office_id_to_update", $office_id);
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
	<?php if($admin_user->confirm_super_user()) { ?>
	<a href="<?php print $config->base_url(); ?>OfficesList" title="List all Registered Offices" class="tip-bottom"> <i class="icon-list"></i> Offices </a>
	<?php } ?>
	<i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
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
		  
		  <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<h5>Update Office Information</h5>
          </div>
          <div class="widget-content nopadding">			
				<?php 
				
				#fetch the admin information 
				$office_info = $DB->query("SELECT * FROM _offices WHERE unique_id='$office_id' OR id='$office_id'");
				#count number found 
				if(count($office_info) > 0) {
					foreach($office_info as $office_results) {
						// assign variables 
						// total disk usage
						$usage = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "office_id='{$office_results["id"]}'", 'item_size', 'ORDER BY id ASC')*1024);
						$usage_today = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "office_id='{$office_results["id"]}' AND item_date=CURDATE()", 'item_size', 'ORDER BY id ASC')*1024);
						// total assigned usage
						$total = $office_results["disk_space"];
						
				?>
				<div class="span6">
				<div class="j-wrapper j-wrapper-640">
					<form action="<?php print $config->base_url(); ?>doUpdateOffice/doUpdate" method="post" autocomplete="off" class="j-pro" id="editForm">
					<div class="j-content">
					<div>
					<label class="j-label">Office Unique ID</label>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="office_key">
					<i class="icofont icon-user"></i>
					</label>
					<input readonly style="text-transform:;" type="text" required value="<?php print $office_results["unique_id"]; ?>" id="office_key" name="office_key">
					</div>
					</div>
					</div>
					<div>
					<label class="j-label">Office Name</label>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="office_name">
					<i class="icofont icon-user"></i>
					</label>
					<input style="text-transform:;" type="text" required value="<?php print $office_results["office_name"]; ?>" id="office_name" name="office_name">
					</div>
					</div>
					</div>
					<div>
					<label class="j-label">Office Contact</label>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="office_contact">
					<i class="icofont icon-phone"></i>
					</label>
					<input style="text-transform:;" type="text" required value="<?php print $office_results["office_contact"]; ?>" id="office_contact" name="office_contact">
					</div>
					</div>
					</div>
					<div>
					<div>
					<label class="j-label">Office Email</label>
					</div>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="email">
					<i class="icon icon-envelope"></i>
					</label>
					<input style="text-transform:;" type="email" value="<?php print $office_results["office_email"]; ?>" id="office_email" name="office_email">
					</div>
					</div>
					</div>
					<div>
					<div>
					<label class="j-label ">Office Address</label>
					</div>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="office_address">
					<i class="icofont icon-check"></i>
					</label>
					<input type="text" required value="<?php print $office_results["office_address"]; ?>" id="office_address" name="office_address">
					</div>
					</div>
					</div>
					<div>
					<div>
					<label class="j-label ">Office Description</label>
					</div>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="office_description">
					<i class="icofont icon-check"></i>
					</label>
					<textarea id="office_description" name="office_description"><?php print $office_results["office_description"]; ?></textarea>
					</div>
					</div>
					</div>
					</div>
					<div class="j-footer">
					<?php if($admin_user->confirm_admin_user()) { ?>
					<button type="submit" id="submitButton" class="btn btn-success"><li class="icon icon-save"></li> Update Details</button>
					<?php if(!$admin_user->confirm_super_user()) { ?>
						<span onclick="window.location.href='<?php print $config->base_url(); ?>Upgrade'" class="btn btn-primary pull-right"><li class="icon icon-lock"></li> Upgrade Account</span>
					<?php } ?>
					<?php } ?>
					</div>
					</form>
					<div id="loading_div"><div class="alert alert-warning alert-md alert-block">Please wait <img src="<?php print $config->base_url(); ?>assets/images/loadings.gif" align="absmiddle" /></div></div>
					<div id="result_div"></div>
				</div>
				</div>
				<div class="span6">
				<div class="j-wrapper j-wrapper-640">
					<div method="post" autocomplete="off" class="j-pro">
					<div class="j-content" style="padding-bottom:20px;">
					
						<table class="table table-bordered">
						  <thead>
							<tr>
							  <th colspan="4">OTHER RELEVANT INFORMATION ABOUT THE OFFICE</th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
								<td>Office Status</td>
								<td>
									<?php 
									// get the user file upload status
									$office_status = $office_results["status"];
									?>
									<div id="office_status"><span onclick="change_office_status('<?php print $office_results["id"]; ?>', '<?php print ($office_status) ? '0' : '1'; ?>')" class="btn <?php print ($office_status) ? "btn-success" : "btn-danger"; ?>"><?php print ($office_status) ? "<i class='icon icon-thumbs-up'></i> ACTIVE" : "<i class='icon icon-thumbs-down'></i> INACTIVE"; ?></span></div>
								</td>
							</tr>
							<tr>
								<td>Account Created On</td>
								<td>
									<?php print date("l jS F, Y", strtotime($office_results["created_on"])); ?>
								</td>
							</tr>
							<tr>
								<td>Account Activated On</td>
								<td>
									<?php print date("l jS F, Y", strtotime($office_results["activated_on"])); ?>
								</td>
							</tr>
							<?php if($admin_user->confirm_super_user()) { ?>
							<tr>
								<td>Activated By</td>
								<td>
									<a href="<?php print $config->base_url(); ?>Profile/<?php print $office_results["activated_by"]; ?>"><?php print $office_results["activated_by"]; ?></a>
								</td>
							</tr>
							<?php } ?>
							<tr>
								<td>First File Upload Date</td>
								<td>
									<?php print date("l jS F, Y H:iA", strtotime($directory->user_disk_info('date_added', "office_id='{$office_results["id"]}'", 'date_added', 'ORDER BY id ASC LIMIT 1'))); ?>
								</td>
							</tr>
							<tr>
								<td>Last File Upload Date</td>
								<td><?php print date("l jS F, Y H:iA", strtotime($directory->user_disk_info('date_added', "office_id='{$office_results["id"]}'", 'date_added', 'ORDER BY id DESC LIMIT 1'))); ?></td>
							</tr>
							<tr>
								<td>Office Daily Usage Limit</td>
								<td><input type="text" maxlength="8" style="width:180px" id="daily_usage" class="form-control pull-left" value="<?php print round(($office_results["daily_upload"]/(1024*1024)), 2); ?>">
								<?php if($admin_user->confirm_admin_user()) { ?>
								<button id="modifyItem" class="btn btn-success pull-right" onclick="update_total_disk_usage('daily','<?php print $office_id; ?>', 'daily_update_div')" style="margin-right:20px"><i class="icon icon-save"></i> Update</button><?php } ?><br clear="both"><small class="daily_update_div"> <?php print file_size_convert($usage_today) ?> used out of <strong><?php print file_size_convert($office_results["daily_upload"]) ?></strong> today</small>
								</td>
							</tr>
							<tr>
								<td>Office Total Disk Usage</td>
								<td><input type="text" maxlength="8" style="width:180px" id="overall_usage" class="form-control pull-left" value="<?php print round(($total/(1024*1024)), 2); ?>">
								<?php if($admin_user->confirm_super_user()) { ?>
								<button id="rename_Item" class="btn btn-success pull-right" onclick="update_total_disk_usage('overall','<?php print $office_id; ?>', 'overall_update_div')" style="margin-right:20px"><i class="icon icon-save"></i> Update</button><?php } ?><br clear="both"><small class="overall_update_div"><?php print "<strong>".file_size_convert($usage) ."</strong> out of <strong>".file_size_convert($total) ." </strong> (". round(((($usage)/$total) * 100), 2). "%) used"; ?></small>
								</td>
							</tr>
						  </tbody>
						</table>					
					</div>
					</div>
				</div>
				</div>
				<br clear="both">
				<div class='modify_result'></div>
				<br clear="both">
				<div class="row-fluid">
				<div class="widget-content nopadding">
					<table class="table table-bordered data-">
					  <thead>
						<tr>
						  <th>Id</th>
						  <?php IF($admin_user->confirm_super_user()) { ?>
						  <th>User Office</th>
						  <?php } ?>
						  <th>Fullname</th>
						  <th>Username</th>
						  <th>Email Address</th>
						  <th>User Role</th>
						  <th>Last Access</th>
						  <th>User Status</th>
						  <th>Action</th>
						</tr>
					  </thead>
					  <tbody>
						<?php
						# query based on the user role that has currently been set on the database
						$QueryUsers = $DB->query("
							SELECT * FROM _admin WHERE admin_deleted='0' AND 
							office_id='{$session->userdata("officeID")}' AND role != '1001'
						");
						
						# using foreach loop to list the items 
						foreach($QueryUsers as $Users) {
						?>
						<tr id="admin_<?php print $Users["id"]; ?>" <?php if($Users["activated"] == 0) print "class='alert alert-danger'"; ?>>
						  <td><?php print $Users["id"]; ?></td>
						  <?php IF($admin_user->confirm_super_user()) { ?>
						  <td><a href="<?php print $config->base_url(); ?>Offices/<?php print $offices->item_by_id('unique_id', $Users["office_id"]); ?>"><?php print $offices->item_by_id('office_name', $Users["office_id"]); ?></a></td>
						  <?php } ?>
						  <td><?php print $Users["firstname"]; ?> <?php print $Users["lastname"]; ?></td>
						  <td><?php print $Users["username"]; ?></td>
						  <td><?php print $Users["email"]; ?></td>
						  <td>
							<?php 
							if($Users["role"] == 1001)
								print "<span class='btn btn-warning'>DEVELOPER</span>";
							elseif($Users["role"] == 1)
								print "<span class='btn btn-success'>ADMINISTRATOR</span>";
							else
								print "<span class='btn btn-primary'>MODERATOR</span>";
							?>
						  </td>
						  <td><?php print $Users["lastaccess"]; ?></td>
						  <td class="user_status_<?php print $Users["id"]; ?>">
							<?php print ($Users["activated"]) ? "<span class='btn btn-success'>ACTIVE</span>" : "<span class='btn btn-danger'>INACTIVE</span>"; ?>
						  </td>
						  <td>
							<?php if($admin_user->confirm_admin_user() OR ($Users["username"] == $admin_user->return_username())) { ?>
								<a href='<?php print $config->base_url(); ?>Profile/<?php print $Users["username"]; ?>' type="button" class="btn btn-success "><i class="icon icon-eye-open"></i></a>
								<?php if($admin_user->confirm_admin_user()) { ?>
								<?php if($Users["activated"] == 0) { ?>
								<a id="modifyItem" href='javascript:modify_account("<?php print $Users["id"]; ?>","Activate");' title="Activate this Administrator Account?"  type="button" class="btn btn-info"><i class="icon icon-play"></i></a>
								<?php  } else { ?>
								<a id="modifyItem" href='javascript:modify_account("<?php print $Users["id"]; ?>","Disable");' title="Disable this Administrator Account?" type="button" class="btn btn-warning"><i class="icon icon-stop"></i></a>
								<?php } ?>
								<a id="modifyItem" href='javascript:modify_account("<?php print $Users["id"]; ?>","Delete");' title="Click to delete this Administrator" type="button" class="btn btn-danger"><i class="icon icon-trash"></i></a>
								<?php } ?>
							<?php } ?>
						  </td>
						</tr>
						<?php } ?>
					</table>
					
					
					
				</div>
				
				</div>
				<?php } }  else { ?>
					<?php show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404'); ?>
				<?php  } ?>
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
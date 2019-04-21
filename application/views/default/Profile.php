<?php
$PAGETITLE = "User Information";
// initializing
GLOBAL $directory;
$FILE_FOUND = FALSE;
$item_id = 0;
REQUIRE "TemplateHeader.php";
#initializing
$user_access = true;
$admin_access = false;
$super_admin_access = false;
$super_super_admin_access = false;
# confirm that the person trying to view the details is an admin
# and also the id does not match his or her credentials
if(isset($SITEURL[1]) and ($admin_user->return_username() != $SITEURL[1]) and ($admin_user->confirm_admin_user() == true)) {
	$user_id = ucfirst(xss_clean($SITEURL[1]));
	
	// confirm if the user is a super admin to update the details of 
	// the super user. If not then return the username of the one 
	// who is currently logged into the system.
	if(!$admin_user->confirm_super_user() and ($admin_user->get_details_by_id($user_id)->urole == 1001)) {
		$user_id = xss_clean($admin_user->return_username());
	}
# return the username of the person who is currently logged in
} else {
	$user_id = xss_clean($admin_user->return_username());
}

#confirm that an administrator has logged in
if($admin_user->confirm_admin_user()) {
	$user_access = true;
	$admin_access = true;
}
// confirm that a super admin is logged in
if($admin_user->confirm_super_user()) {
	$user_access = true;
	$admin_access = true;
	$super_admin_access = true;
	$super_super_admin_access = true;
}

// set some new sessions
$session->set_userdata("user_id", $user_id);
$session->set_userdata("admin_access", $admin_access);
?>
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
			<h5>Update Admin User Information</h5>
          </div>
          <div class="widget-content nopadding">			
				<?php 
				
				#fetch the admin information
				if($admin_user->confirm_super_user()) {
					$admin_info = $DB->query("SELECT * FROM _admin WHERE username='$user_id'");
				} else {
					$admin_info = $DB->query("SELECT * FROM _admin WHERE username='$user_id' AND activated='1'");
				}
				#another if statement to ensure that the user does not view the details of another person
				if(($admin_user->confirm_admin_user() == true) or ($admin_user->return_username() == $user_id)){
				#count number found 
				if(count($admin_info) > 0) {
					foreach($admin_info as $admin_results) {
				?>
				<div class="span6">
				<div class="j-wrapper j-wrapper-640">
					<form action="<?php print $config->base_url(); ?>doUpdateUser/doUpdate" method="post" autocomplete="off" class="j-pro" id="doProcessUser">
					<div class="j-content">
					<div>
					<label class="j-label">FirstName</label>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="firstname">
					<i class="icofont icon-user"></i>
					</label>
					<input style="text-transform:;" type="text" required value="<?php print $admin_results["firstname"]; ?>" id="firstname" name="firstname">
					</div>
					</div>
					</div>
					<div>
					<label class="j-label">LastName</label>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="lastname">
					<i class="icofont icon-user"></i>
					</label>
					<input style="text-transform:;" type="text" required value="<?php print $admin_results["lastname"]; ?>" id="lastname" name="lastname">
					</div>
					</div>
					</div>
					<div>
					<div>
					<label class="j-label">Email</label>
					</div>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="email">
					<i class="icon icon-envelope"></i>
					</label>
					<input style="text-transform:;" type="email" value="<?php print $admin_results["email"]; ?>" id="email" name="email">
					<input style="text-transform:;" type="hidden" readonly value="<?php print $admin_results["email"]; ?>" id="oldemail" name="oldemail">
					</div>
					</div>
					</div>
					<div>
					<div>
					<label class="j-label ">Username</label>
					</div>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="username">
					<i class="icofont icon-check"></i>
					</label>
					<input type="text" required value="<?php print $admin_results["username"]; ?>" id="user_id" name="user_id">
					</div>
					</div>
					</div>
					<input type="hidden" name="office_id" id="office_id" readonly value="<?php print $admin_results["office_id"]; ?>">
					<div>
					<div>
					<label class="j-label ">User Role</label>
					</div>
					<div class="j-unit">
					<div class="j-input">
					<label class="j-icon-right" for="admin_role">
					<i class="icon icon-file"></i>
					</label>
					<select <?php if($admin_access == false) print "disabled"; ?> name="admin_role" id="admin_role">						
						<option value="<?php print $admin_results["role"]; ?>">Select User Role</option>
						<?php if($admin_user->confirm_super_user()) { ?>
						<option <?php if($admin_results["role"] == 1001) print "selected"; ?> value="1001">Developer</option>
						<?php } ?>
						<option <?php if($admin_results["role"] == 1) print "selected"; ?> value="1">Administrator</option>
						<option <?php if($admin_results["role"] == 2) print "selected"; ?> value="2">Content Moderator</option>
					</select>
					</div>
					</div>
					</div>					
					</div>
					<div class="j-footer">
					<?php if($user_access == true) { ?>
					<button type="submit" class="btn btn-success"><li class="icon icon-save"></li> Update Details</button>
					<span onclick="window.location.href='<?php print $config->base_url(); ?>ChangeAccountPassword'" class="btn btn-primary pull-right"><li class="icon icon-lock"></li> Change Password</span>
					<?php } ?>
					</div>
					</form>
					<div class="j-response"></div>
				</div>
				</div>
				<div class="span6">
				<div class="j-wrapper j-wrapper-640">
					<div method="post" autocomplete="off" class="j-pro">
					<div class="j-content" style="padding-bottom:20px;">
					
						<table class="table table-bordered">
						  <thead>
							<tr>
							  <th colspan="4">OTHER RELEVANT INFORMATION ABOUT THE USER</th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
								<td>User Status</td>
								<td>
									<?php 
									// get the user file upload status
									$user_status = $admin_results["activated"];
									?>
									<div id="user_status"><span class="btn <?php print ($user_status) ? "btn-success" : "btn-danger"; ?>"><?php print ($user_status) ? "<i class='icon icon-thumbs-up'></i> ACTIVE" : "<i class='icon icon-thumbs-down'></i> INACTIVE"; ?></span></div>
								</td>
							</tr>
							<tr>
								<td>Account Created On</td>
								<td>
									<?php print date("l jS F, Y", strtotime($admin_user->item_by_id($user_id, "date_added"))); ?>
								</td>
							</tr>
							<tr>
								<td>Created By</td>
								<td>
									<?php print ($admin_user->item_by_id($user_id, "added_by")) ? "<a class='btn btn-primary' href='".$config->base_url()."Profile/{$admin_user->item_by_id($user_id, "added_by")}'><i class='icon icon-user'></i> ".$admin_user->item_by_id($user_id, "added_by")."</a>" : "<span class='btn btn-primary'><i class='icon icon-user'></i> Self</span>"; ?>
								</td>
							</tr>
							<tr>
								<td>User Office</td>
								<td>
									<a href="<?php print $config->base_url(); ?>Offices/<?php print $offices->item_by_id('unique_id', $admin_results["office_id"]); ?>"><?php print $offices->item_by_id('office_name', $admin_results["office_id"]); ?></a>
								</td>
							</tr>
							<tr>
								<td>First File Upload Date</td>
								<td>
									<?php print date("l jS F, Y H:iA", strtotime($directory->user_disk_info('date_added', "user_id='{$admin_results["id"]}'", 'date_added', 'ORDER BY id ASC LIMIT 1'))); ?>
								</td>
							</tr>
							<tr>
								<td>Last File Upload Date</td>
								<td><?php print date("l jS F, Y H:iA", strtotime($directory->user_disk_info('date_added', "user_id='{$admin_results["id"]}'", 'date_added', 'ORDER BY id DESC LIMIT 1'))); ?></td>
							</tr>
							<tr>
								<td>Total File Uploads</td>
								<td class="total_file_uploads">
									<?php
									// assign variables 
									// total disk usage
									$usage = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "user_id='{$admin_results["id"]}'", 'item_size', 'ORDER BY id ASC')*1024);
									// total assigned usage
									$total = $admin_user->item_by_id($user_id, "uploads_limit");
									// print message
									print file_size_convert($usage) ." out of ".file_size_convert($total) ." (". round(((($usage)/$total) * 100), 2). "% used)";
									?>
								</td>
							</tr>
							<tr>
								<td>File Upload Status</td>
								<td>
									<?php 
									// get the user file upload status
									$upload_status = $admin_user->item_by_id($user_id, "uploads_status");
									?>
									<div id="file_upload_status"><span <?php if($admin_user->confirm_admin_user()) { ?>onclick="change_upload_status('<?php print $admin_results["id"]; ?>', '<?php print ($upload_status) ? '0' : '1'; ?>')"<?php } ?> class="btn <?php print ($upload_status) ? "btn-success" : "btn-danger"; ?>"><?php print ($upload_status) ? "<i class='icon icon-thumbs-up'></i> ACTIVE" : "<i class='icon icon-thumbs-down'></i> INACTIVE"; ?></span></div>
								</td>
							</tr>							
							<tr>
								<td>Limit User Disk Usage</td>
								<td><input type="text" maxlength="8" style="width:180px" id="limit_num" class="form-control pull-left" value="<?php print round(($total/(1024*1024)), 2); ?>">
								<?php if($admin_user->confirm_admin_user()) { ?>
								<button id="modifyItem" class="btn btn-success pull-right" onclick="update_user_disk_usage('<?php print $user_id; ?>')" style="margin-right:20px"><i class="icon icon-save"></i> Update</button>
								<?php } ?>
								<br clear="both"><small> Usage in <strong>MEGABYTES.</strong></small></td>
							</tr>
						  </tbody>
						</table>					
					</div>
					</div>
				</div>
				</div>
				<?php } }  else { ?>
					<?php show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404'); ?>
				<?php  } } ?>
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
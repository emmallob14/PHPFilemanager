<?php
$PAGETITLE = "Admin Users";
// initializing
GLOBAL $directory;
$FILE_FOUND = FALSE;
$item_id = 0;
REQUIRE "TemplateHeader.php";
load_helpers('url_helper');	
#initializing
$user_access = true;
$admin_access = false;
$super_admin_access = false;
$super_super_admin_access = false;
#confirm that a user id is parsed 
$admin_logged = ucfirst($admin_user->return_username());

#confirm that an administrator has logged in
if($admin_user->confirm_admin_user()) {
	$user_access = true;
	$admin_access = true;
}

#confirm that a supper administrator has logged in
if($admin_user->confirm_super_user()) {
	$user_access = true;
	$admin_access = true;
	$super_admin_access = true;
	$super_super_admin_access = true;
}
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
			<h5>Listing all admin users</h5>
          </div>
          <div class="widget-content nopadding">
			
			<?php 
			#confirm that an admin wants to use this form 
			if($admin_access ==  true) {
			?>
			<div class="j-wrapper j-wrapper-640">
			<form novalidate action="<?php print SITE_URL; ?>/doAddUser/doAdd" id="doProcessUser" method="post" autocomplete="off" class="j-pro">
			<div class="j-content">
			<div>
			<label class="j-label">FirstName</label>
			<div class="j-unit">
			<div class="j-input">
			<label class="j-icon-right" for="firstname">
			<i class="icofont icon-user"></i>
			</label>
			<input style="text-transform:;" type="text" required id="firstname" name="firstname">
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
			<input style="text-transform:;" type="text" required id="lastname" name="lastname">
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
			<i class="icofont icon-envelope"></i>
			</label>
			<input style="text-transform:;" type="email" id="email" name="email">
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
			<input style="text-transform:;" type="text" required id="username" name="username">
			</div>
			</div>
			</div>

			<div>
			<div>
			<label class="j-label ">Password</label>
			</div>
			<div class="j-unit">
			<div class="j-input">
			<label class="j-icon-right" for="password">
			<i class="icofont icon-lock"></i>
			</label>
			<input type="text" value="<?php print random_string('alnum', 10); ?>" placeholder="Enter Password" id="password" name="password">
			<small>This is a randomly generated password that can be used. <span style="font-weight:bolder;cursor:pointer;" id="regeratePassword">Regenerate Password?</span></small>
			</div>
			</div>
			</div>
			<div>
			<div>
			<label class="j-label ">Admin Role</label>
			</div>
			<div class="j-unit">
			<div class="j-input">
			<label class="j-icon-right" for="admin_role">
			<i class="fa icon-shekel"></i>
			</label>
			<select name="admin_role" id="admin_role">
				<option value="2">Select Admin Role</option>
				<?php if($super_admin_access) { ?>
				<option value="1001">Developer</option>
				<?php } ?>
				<option value="1">Administrator</option>
				<option selected value="2">Content Moderator</option>
			</select>
			</div>
			</div>
			</div>			
			</div>
			<div class="j-footer">
			<button type="submit" class="btn btn-success addbutton"><li class="fa icon-save"></li> Add Record</button>
			</div>
			</form>
			<div class="j-response"></div>
			</div>
			
			<?php } else { ?>

			<?php show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404'); ?>

			<?php } ?>
			
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
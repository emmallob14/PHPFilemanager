<?php
$PAGETITLE = "Admin Users";
// initializing
GLOBAL $directory;
$FILE_FOUND = FALSE;
$item_id = 0;
REQUIRE "TemplateHeader.php";
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
			<table class="table table-bordered data-table">
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
				IF(!$admin_user->confirm_admin_user()) {
					$QueryUsers = $DB->query("
						SELECT * FROM _admin WHERE admin_deleted='0' AND 
						office_id='{$session->userdata("officeID")}' AND role != '1001'
					");
				}
				
				IF($admin_user->confirm_admin_user()) {
					$QueryUsers = $DB->query("
						SELECT * FROM _admin WHERE admin_deleted='0' AND 
						office_id='{$session->userdata("officeID")}' AND role != '1001'
					");
				}
				
				IF($admin_user->confirm_super_user()) {
					$QueryUsers = $DB->query("
						SELECT * FROM _admin WHERE admin_deleted='0'
					");
				}
				
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
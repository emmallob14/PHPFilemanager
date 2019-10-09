<?php
$PAGETITLE = "Activities Logs";
// initializing
GLOBAL $directory;
$FILE_FOUND = FALSE;
$item_id = 0;
REQUIRE "TemplateHeader.php";

if(isset($_GET["u"]) and ($admin_user->return_username() != $_GET["u"]) and ($admin_user->confirm_admin_user() == true)) {
	$current_user = xss_clean($_GET["u"]);
} else {
	$current_user = xss_clean($admin_user->return_username());
}
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
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
			<h5>Listing all Registered Offices</h5>
          </div>
          <div class="widget-content nopadding">
			<div class="dt-responsive table-responsive">
				<div class="dt-responsive table-responsive col-sm-12">
					<table class="table table-striped table-bordered  data-table">
					<thead>
					<tr>
					<th>ID</th>
					<th>TIME</th>
					<th>ACTIVITY DETAILS</th>
					<th>DESCRIPTION</th>
					</tr>
					</thead>
					<tbody>
					<?php
					#initializing
					$where_clause = "status='1'";
					#check the page that will be used for the filtering
					if(isset($_GET["p"])) {
						$where_clause = " and activity_page='".xss_clean($_GET["p"])."'";
					}

					#check the item id that will be used for the filtering
					if(isset($_GET["id"])) {
						$where_clause = " and activity_id='".xss_clean($_GET["id"])."'";
					}

					#list the user activities over the period
					if($admin_user->confirm_admin_user()) {
						$activites_list = $DB->query("select * from _activity_logs where (admin_id='$current_user') or (activity_id='$current_user') order by id desc");
					} else {
						$activites_list = $DB->query("select * from _activity_logs where (admin_id='$current_user') or (activity_id='$current_user') order by id desc");
					}

					foreach($activites_list as $results):
					?>
					<tr id="logid<?php print $results["id"]; ?>">
					<td><?php print $results["id"]; ?></td>
					<td><?php print date("d F Y H:ia", strtotime($results["date_recorded"])); ?></td>
					<td>
						
						<?php 
						if($results["activity_page"] == "login-notice") {
							?>
							Login Attempts Notification
							<?php
						}
						?>
						<?php 
						if($results["activity_page"] == "password-change-notice") {
							?>
							Password Change Request Notification
							<?php
						}
						?>
						<?php 
						if($results["activity_page"] == "password") {
							?>
							Password changed <br>
							<?php
						}
						?>
						<?php 
						if($results["activity_page"] == "password-changed") {
							?>
							Admin: Password changed
							<Br><a href="<?php print SITE_URL; ?>/Profile/<?php print $results["activity_id"]; ?>"><?php print $results["activity_id"]; ?></a>
							<?php
						}
						?>
					</td>
					<td>
						<?php if(($results["activity_page"] == "password-changed") and ($results["activity_id"] == $current_user)) { ?>
						Your password was recently changed by an Administrator
						<?php } else { ?>
						<?php print $results["activity_description"]; ?>
						<?php } ?>
						<br>by <small><a href="<?php print SITE_URL; ?>/Profile/<?php print $results["admin_id"]; ?>"><?php print $results["admin_id"]; ?></a></small>
					</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
					</table>
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
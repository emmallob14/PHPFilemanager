<?php
$PAGETITLE = "Registered Offices";
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
		  <?php IF($admin_user->confirm_super_user()) { ?>
		  <div class='modify_result'></div>
		  <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<h5>Listing all Registered Offices</h5>
          </div>
          <div class="widget-content nopadding">
			<table class="table table-bordered data-table">
              <thead>
                <tr>
				  <th>Id</th>				  
				  <th>Office Name</th>				  
                  <th>Contact Number</th>
                  <th>Email Address</th>
				  <th>Address</th>
				  <th>Created On</th>
				  <th>Activated On</th>
				  <th>Disk Usage</th>
				  <th>Status</th>
				  <th>Action</th>
                </tr>
              </thead>
              <tbody>
				<?php
				IF($admin_user->confirm_super_user()) {
					$QueryOffices = $DB->query("
						SELECT * FROM _offices WHERE 1
					");
				}
				
				# using foreach loop to list the items 
				foreach($QueryOffices as $Offices) {
					// assign variables 
					// total disk usage
					$usage = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "office_id='{$Offices["id"]}'", 'item_size', 'ORDER BY id ASC')*1024);
					$usage_today = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "office_id='{$Offices["id"]}' AND item_date=CURDATE()", 'item_size', 'ORDER BY id ASC')*1024);
					// total assigned usage
					$total = $Offices["disk_space"];
				?>
                <tr id="admin_<?php print $Offices["id"]; ?>" <?php if($Offices["status"] == 0) print "class='alert alert-danger'"; ?>>
				  <td><?php print $Offices["id"]; ?></td>
				  <td><a href="<?php print $config->base_url(); ?>Offices/<?php print $Offices["unique_id"]; ?>"><?php print $Offices["office_name"]; ?></a></td>
				  <td><?php print $Offices["office_contact"]; ?></td>
                  <td><?php print $Offices["office_email"]; ?></td>
				  <td><?php print $Offices["office_address"]; ?></td>
				  <td><?php print date("l jS M, Y", strtotime($Offices["created_on"])); ?></td>
				  <td><?php print date("l jS M, Y", strtotime($Offices["activated_on"]));?></td>
				  <td><?php print "<strong>".file_size_convert($usage) ."</strong> out of <strong>".file_size_convert($total) ." </strong> (". round(((($usage)/$total) * 100), 2). "%) used"; ?></td>
				  <td>
					<?php print ($Offices["status"]) ? "<span class='btn btn-success'>ACTIVE</span>" : "<span class='btn btn-danger'>INACTIVE</span>"; ?>
				  </td>
				  <td>
					<a href='<?php print $config->base_url(); ?>Offices/<?php print $Offices["unique_id"]; ?>' type="button" class="btn btn-success "><i class="icon icon-eye-open"></i></a>
				  </td>
                </tr>
				<?php } ?>
			</table>
			
		</div>
		<?PHP } ELSE { ?>
		<?PHP show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404'); ?>
		<?PHP } ?>
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
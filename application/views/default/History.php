<?php
$PAGETITLE = "Login History";
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
		  <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<h5>Listing all Registered Offices</h5>
          </div>
          <div class="widget-content nopadding">
			<div class="dt-responsive table-responsive">
				<table class="table table-bordered data-table">
					<thead>
						<tr>
						<th>ID</th>
						<th>Platform</th>
						<th>Ip Address</th>
						<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$history_list = $DB->query("select * from _admin_log_history where username='{$admin_user->return_username()}'");
						foreach($history_list as $results):
						?>
						<tr id="logid<?php print $results["id"]; ?>">
						<td><?php print $results["id"]; ?></td>
						<td><?php print $results["log_platform"]; ?></td>
						<td><?php print $results["log_ipaddress"]; ?></td>
						<td><?php print date("d F Y H:ia", strtotime($results["lastlogin"])); ?></td>
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

<!--End-Chart-box-->
<?php 
REQUIRE "TemplateFooter.php";
?>
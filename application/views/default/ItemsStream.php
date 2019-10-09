<?php
$PAGETITLE = "Files & Folder Listing";
REQUIRE "TemplateHeader.php";
GLOBAL $notices;
$session->unset_userdata(ROOT_FOLDER);
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
<div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <i class="icon-list"></i> <?php print $PAGETITLE; ?></div>
</div>
<!--End-breadcrumbs-->
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>Files and Folders</h5>
	</div>
	<div class="widget-content" >
	  <div class="row-fluid">
		<div class="span12">
			<div class="listing-details"></div>
			<div class="loading-more"></div>
			<div class="loading-div"></div>
		</div>
		<div class="span12">
			<div class="row" id="more-div">
				<div align="center" class="text-center home-list-pop-desc inn-list-pop-desc">
					<div class="list-enqu-btn" align="center">
						<button class="load-more btn btn-success">Load More Results</button>
						<div class="no-more btn btn-default">No More Results</div>
						<input readonly type="hidden" id="_c_row" value="0">
						<input readonly type="hidden" id="_pg_url" value="<?php print base64_encode(xss_clean($_SERVER["REQUEST_URI"]."&")); ?>">
						<input readonly type="hidden" id="_p_limit" value="<?php print config_item('rowsperpage'); ?>">
						<input readonly type="hidden" id="_allcount" value="0">
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
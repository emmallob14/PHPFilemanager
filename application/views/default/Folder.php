<?php
$PAGETITLE = "Add New Folder";
REQUIRE "TemplateHeader.php";

load_helpers('directory_helper');
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <i class="icon-pluss"></i> Create Folder</div>
  </div>
<!--End-breadcrumbs-->
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>Create New Folder</h5>
	</div>
	<div class="widget-content" >
	  <div class="row-fluid">
		<div class="span12">
		  
			<div class="widget-box">
				<div class="widget-title"> <span class="icon"> <i class="icon-plus"></i> </span>
				  <h5>Add Folder</h5>
				</div>
				
				<div class="widget-content nopadding">
				  <form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doAdd/addFolder" autocomplete="Off" name="addFolder" id="addFolder" novalidate="novalidate">
					<div class="control-group">
					  <label class="control-label">Select Parent Folder</label>
					  <div class="controls">
						<?php print $directory->item_by_id('item_title', $session->userdata('RootFolder')); ?> <span><a class='btn btn-primary' id='changeFolder' href="<?php print SITE_URL; ?>/ItemsStream">Change Folder?</a></span>
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label">Folder Name</label>
					  <div class="controls">
						<input type="text" name="folder_name" id="folder_name" />
					  </div>
					</div>
					<div class="control-group">
						<div class="controls">
						  <textarea style="width:900px" name="folder_description" class="span12" rows="6" placeholder="Enter text ..."></textarea>
						</div>
					</div>
					<div class="form-actions">
					  <input type="submit" id="submitButton" value="Add Folder" class="btn btn-success">
					</div>
				  </form>
				</div>
				<div id="loading_div"><div style="width:100%" class="alert alert-warning alert-md alert-block">Please wait <img src="<?php print $config->base_url(); ?>assets/images/loadings.gif" align="absmiddle" /></div></div>
				<div id="result_div"></div>
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
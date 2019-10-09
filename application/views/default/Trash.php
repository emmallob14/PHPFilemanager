<?php
$PAGETITLE = "Trashed Files";
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
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
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
		  <a class="btn btn-success selectButton float-right" href="#" onclick="selectAll();" title="Click to select all"><i class="icon icon-check"></i> Select All</a>
		  <div class="widget-box">          
		  <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<h5>List of all trashed files</h5>
          </div>
		  <div class="delete_restore_div"></div>
         
		  <div class="widget-content nopadding">
			<form action="<?php print $config->base_url(); ?>/doEmptyTrash" method="POST" id="trashTable">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>ID</th>
				  <th>File Name</th>
				  <th>Item Type</th>
				  <th>File Extension</th>
				  <th>File Size</th>
				  <th>Parent Folder</th>
				  <th>Select Item</th>
				  <th>Action</th>
                </tr>
              </thead>
              <tbody>
				<?php 
				# query the database _shared_listing table for the files that has been shared
				$deleted_Files = $DB->query("SELECT * FROM _item_listing
					WHERE 
					(user_id='{$session->userdata(UID_SESS_ID)}' AND item_status='0' AND item_deleted='0')
				");
				$I = 0;
				# using foreach loop to list the items 
				foreach($deleted_Files as $Files) {
					$I++;
				?>
                <tr class="gradeX" id="item_list_<?php print $Files["id"]; ?>">
                  <td><?php print $I; ?></td>
				  <td width="40%"><?php print $Files["item_title"]; ?></td>
                  <td style="text-align:center"><?php print $Files["item_type"]; ?></td>
				  <td style="text-align:center"><?php print $Files["item_ext"]; ?></td>
				  <td style="text-align:center"><?php print $Files["item_size"]; ?></td>
				  <td><?php print $directory->parent_info($Files["id"], 'trash')->parent_back_link; ?></td>
				  <td style="text-align:center"><input type="checkbox" name="trashID[]" value="<?php print $Files["id"]; ?>"></td>
                  <td>
				  <a class="btn btn-success restoreButton" href="#" value="<?php print $Files["id"]; ?>" title="View full details of file" type="<?php print $Files["item_type"]; ?>"><i class="icon icon-play"></i> Retore <?php print ($Files["item_type"] == "FILE") ? "File" : "Folder"; ?></a>
				  <a href="#" class="btn btn-danger deleteButton" title="Delete this file permanently from the system?" type="<?php print $Files["item_type"]; ?>" value="<?php print $Files["id"]; ?>"><i class="icon icon-trash"></i> Delete</a>
				  </td>
                </tr>
				<?php } ?>
			</table>
			</form>
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
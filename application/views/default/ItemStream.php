<?php
$PAGETITLE = "File Stream";
REQUIRE "TemplateHeader.php";
GLOBAL $directory;
// initializing
$FILE_FOUND = FALSE;
$item_id = 0;
// check the url that has been parsed
if(confirm_url_id(1, 'Id')) {
	if(confirm_url_id(2)) {
		$item_slug = xss_clean($SITEURL[2]);
		IF($DB->num_rows(
			$DB->where(
			'_item_listing', '*', 
			ARRAY(
				'item_unique_id'=>"='$item_slug'",
				'user_id'=>"='".$session->userdata(":lifeID")."'",
				'item_status'=>"='1'",
		))) == 1) {
			# ASSIGN A TRUE VALUE TO THE USER FOUND 
			$FILE_FOUND = TRUE;
			$item_id = $directory->item_by_id('id', $item_slug);
			$FOLDER_TREE = $directory->item_by_id('item_folder_tree', $item_id);
			$ITEM_TYPE = $directory->item_by_id('item_type', $item_id);
			// change the file download link each time the user views the file 
			$directory->change_download_link($item_slug);
			# CONFIRM THAT THE CURRENT ITEM IS A FOLDER 
			IF($directory->item_by_id('item_type', $item_id) == "FOLDER") {
				$session->set_userdata('RootFolder', $item_id);
			}
		}
	}	
}
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <a href="<?php print $config->base_url(); ?>ItemsStream" title="List all files and folders" class="tip-bottom"> <i class="icon-list"></i> File / Folders Stream </a> <i class="icon-download"></i> File Stream</div>
  </div>
<!--End-breadcrumbs-->
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>File Details</h5>
	</div>
	<div class="widget-content" >
	  <div class="row-fluid">
		<?PHP IF( $FILE_FOUND ) { ?>
		<div class="span4">
			<div id="drag-and-drop-zone" class="dm-uploader p-5" align="center">
				<ul class="thumbnails">
					<li class="span12">
						<a>
						<img src="<?php print $config->base_url().$directory->item_by_id('item_thumbnail', $item_id); ?>" alt="">
						</a>
						<?php if(in_array($directory->item_by_id('item_ext', $item_id), array("jpg", "png", "gif", "jpeg","bmp","jpg2"))) { ?>
						<div class="actions">
						<a class="lightbox_trigger" href="<?php print $config->base_url().config_item('upload_path').$directory->item_by_id('item_unique_id', $item_id); ?>"><i class="icon-search"></i></a>
						</div>
						<?php } ?>
					</li>
				</ul>
				
			</div>
			<div id="edit_item_div">
			<?php if(confirm_url_id(3, 'Edit')) { ?><script>$("#edit_item_div").show();</script><?php } else { ?><script>$("#edit_item_div").hide();</script><?php } ?>
			<form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doUpdate/changeName" autocomplete="Off" name="editForm" id="editForm" novalidate="novalidate">
				<div class="control-group">
				<label class="control-label">Item New Name</label>
				<div class="controls">
					<input type="text" name="item_name" id="item_name" />
				</div>
				</div>
				<div class="control-group">
				<label class="control-label">Item Description</label>
				<div class="controls">
					<textarea style="width:500px" name="desc" class="desc span12" rows="2" placeholder="Enter text ..."></textarea>
				</div>
				</div>
				<div class="form-actions" align="center">
					<input type="hidden" name="itemName" id="itemName">
					<input type="hidden" name="itemId" id="itemId" value="<?php print $item_id; ?>">
					<input type="hidden" value="<?php print current_url(); ?>" name="href">
					<input type="submit" id="submitButton" value="Edit File" class="btn btn-success">
					<input type="button" id="cancelButton" value="Cancel" class="btn btn-danger">
				</div>
			</form>
			<div id="result_div"></div>
			<div id="loading_div"><div style="width:100%" class="alert alert-warning alert-md alert-block">Please wait <img src="<?php print $config->base_url(); ?>assets/images/loadings.gif" align="absmiddle" /></div></div>
			</div>
		</div>
		<div class="span8">
		  <div class="card h-100">
            <div class="card-header">
              ITEM Details
            </div>

            <ul class="list-unstyled p-2 d-flex flex-column col" id="files" style="min-height:250px;max-height:250px;overflow:scroll;">
              <li><strong>ITEM NAME: </strong> <span class='item_name'><?php print $directory->item_by_id('item_title', $item_id); ?></span></li>
			  <li><strong>ITEM SIZE: </strong> <?php print ($ITEM_TYPE == "FOLDER") ? file_size_convert($directory->item_full_size($item_id)) : $directory->item_by_id('item_size', $item_id); ?></li>
			  <li><strong>ITEM TYPE: </strong> <?php print $ITEM_TYPE; ?></li>
			  <li><strong>ITEM EXT: </strong> <?php print $directory->item_by_id('item_ext', $item_id); ?></li>
			  <li><strong>ITEM DESCRIPTION: </strong> <?php print $directory->item_by_id('item_description', $item_id); ?></li>
			  <li><strong>DATE UPLOADED: </strong> <?php print $directory->item_by_id('date_added', $item_id); ?></li>
			  <li><strong>UPLOADED BY: </strong> <?php print $directory->item_by_id('item_users', $item_id); ?></li>
			  <li><strong>DOWNLOADS: </strong> <?php print $directory->item_by_id('item_downloads', $item_id); ?></li>
            </ul>
          </div>
		  <div class="row-fluid">
		  <br clear="both">
		  <div class="">
		  <?php if($ITEM_TYPE == "FILE") { ?>
			<a target="_blank" class="btn btn-block btn-primary span2" href="<?php print $config->base_url().'Download/'.$directory->item_by_id('item_download_link', $item_id); ?>"><i class="icon-download"></i> DOWNLOAD</a>
			<?php } ?>
			<?php if($ITEM_TYPE == "FOLDER") { ?>
			<a class="btn btn-block span3 btn-primary" href="<?php print $config->base_url().'Folder'; ?>"><i class="icon-plus"></i> ADD FOLDER</a>
			<?php } ?>
			<a class="btn btn-block span3 btn-info" href="<?php print $config->base_url().'Upload'; ?>"><i class="icon-upload"></i> UPLOAD FILES</a>
			<a class="btn btn-block span2 btn-success" href="#" id="rename_Item" value="<?php print $item_id ?>"><i class="icon-edit"></i> EDIT</a>
			<a class="btn btn-block span2 btn-warning share_Item" href="#" value="<?php print $config->base_url()."Share/Id/$item_id"; ?>"><i class="icon-share"></i> SHARE</a>
			<a onclick='process_item("delete", "<?php print $item_id; ?>", "<?PHP PRINT $ITEM_TYPE; ?>", "<?php PRINT $session->userdata(":lifeID"); ?>", "REDIR")' class="btn btn-block span2 btn-danger" href="#" id="deleteItem" value="<?php print $item_id ?>"><i class="icon-trash"></i> DELETE</a>
		  </div>
		</div>
		</div>
		<?php if($ITEM_TYPE == "FOLDER") { ?>
		<div class="row-fluid">
			<div class="span12">
			   <div class="card h-100">
				<div class="card-header">
				  Folder Contents
				</div>
				<div>
					<?PHP
					#INITIALIZING
					$NO_ITEM = FALSE;
					#FETCH ALL FOLDERS IN THE DATABASE THAT 
					$listFolders = $DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(':lifeID')."' AND item_type='FOLDER' AND item_status='1' AND item_deleted='0' AND item_folder_id='$item_id'");
					$listFiles = $DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(':lifeID')."' AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_folder_id='$item_id'" );
					
					IF(!$NO_ITEM) {
						
						#USING THE FOREACH LOOP 
						FOREACH($listFolders AS $Folders) {
							$fileName = $Folders["item_title"];
							$Id = $Folders["id"];
							$Uid = $Folders["item_unique_id"];
							
							echo "<div class='file' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'><a href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url()."assets/images/icons/folder.jpg'><br>$fileName</a></div>";
						}
						FOREACH($listFiles AS $Files) {
							$file_ext = $Files["item_ext"];
							$fileName = $Files["item_title"];
							$Id = $Files["id"];
							$Uid = $Files["item_unique_id"];
							$DLink = $Files["item_download_link"];
							
							PRINT "<div class='file' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'><a href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Files['item_thumbnail']."'><br>$fileName</a><br>";
							PRINT "<div class='file_option' id='option_$Id'>";
							// CONFIRM THAT THE FILE IS A ZIP FILE
							IF($file_ext == "zip") {
								PRINT "<span title='Extract File' onclick='extract_zip(\"$Uid\");' class='btn btn-primary'><i class='icon-bookmark'></i></span> ";
							} ELSE {
								PRINT "<span onclick='process_item(\"edit\", \"$Uid\", \"FILE\");' class='btn btn-primary'><i class='icon-edit'></i></span> ";
							}
							PRINT "<span title='Download this file' class='btn btn-success'><a style='color:#fff' href='".$config->base_url()."Download/$DLink' target='_blank'><i class='icon-download'></i></a></span> ";
							PRINT "<span title='Add File to Share List' onclick='add_share_item(\"$Uid\",\"$fileName\");' class='btn btn-warning'><i class='icon-plus'></i></span> ";
							PRINT "<span onclick='process_item(\"delete\", \"$Id\", \"FILE\");' class='btn btn-danger'><i class='icon-trash'></i></span>";
							PRINT "</div></div>";
						}
					}
				  ?>
				</div>
			  </div>
			</div>
		</div><!-- /debug -->
		<?php } ?>
		<?PHP } ELSE { ?>
		<?PHP show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404'); ?>
		<?PHP } ?>
	  </div>
	</div>
  </div>
</div>
</div>
<!--End-Chart-box-->
<?php 
REQUIRE "TemplateFooter.php";
?>
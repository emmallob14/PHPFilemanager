<?php
$PAGETITLE = "File Stream";
REQUIRE "TemplateHeader.php";
GLOBAL $directory;
// initializing
$FILE_FOUND = FALSE;
$ITEM_TITLE = NULL;
$ITEM_DESCRIPTION = NULL;
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
				'(user_id'=>"='".$session->userdata(UID_SESS_ID)."'",
				'OR item_users'=>"LIKE '%/".$admin_user->return_username()."/%')",
				'item_status'=>"='1'",
		))) == 1) {
			# ASSIGN A TRUE VALUE TO THE USER FOUND 
			$FILE_FOUND = TRUE;
			$item_id = $directory->item_by_id('id', $item_slug);
			$ITEM_TITLE = $directory->item_by_id('item_title', $item_slug);
			$ITEM_DESCRIPTION = $directory->item_by_id('item_description', $item_slug);
			$FOLDER_TREE = $directory->item_by_id('item_folder_tree', $item_id);
			$ITEM_TYPE = $directory->item_by_id('item_type', $item_id);
			$ITEM_EXT = $directory->item_by_id('item_ext', $item_id);
			$ITEM_USERS = $directory->item_by_id('item_users', $item_id);
			// change the file download link each time the user views the file 
			$directory->change_download_link($item_slug);
			# CONFIRM THAT THE CURRENT ITEM IS A FOLDER 
			IF($directory->item_by_id('item_type', $item_id) == "FOLDER") {
				$session->set_userdata(ROOT_FOLDER, $item_id);
			}
			$session->set_userdata('ItemID', $item_slug);
			$session->set_userdata('ITEM_EXT', $ITEM_EXT);
		}
	}	
}
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
	<a href="<?php print $config->base_url(); ?>ItemsStream" title="List all files and folders" class="tip-bottom"> <i class="icon-list"></i> File / Folders Stream </a>
	<i class="icon-download"></i> File Stream</div>
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
		<div class="span3">
			<div id="drag-and-drop-zone" class="dm-uploader p-5" align="center">
				<ul class="thumbnails">
					<li class="span12">
						<img src="<?php print $config->base_url().$directory->item_by_id('item_thumbnail', $item_id); ?>" alt="" style="border-radius:5px;width:150px;">
					</li>
					<?php if($ITEM_TYPE == "FILE") { ?>
					<a target="_blank" title="Click to download this <?php print $ITEM_TYPE; ?>." class="btn btn-block span12 btn-primary" href="<?php print $config->base_url().'Download/'.$directory->item_by_id('item_download_link', $item_id); ?>"><i class="icon-download"></i> DOWNLOAD FILE</a>
					<?php } ?>			
				</ul>
				
			</div>
		</div>
		<div class="span9">
		  <div class="card h-100">
            <div class="card-header">
              ITEM Details
            </div>

            <ul class="list-unstyled p-2 d-flex flex-column col" id="files" style="min-height:250px;max-height:250px;overflow:scroll;">
              <li><strong>ITEM NAME: </strong> <span class='item_name'><?php print $directory->item_by_id('item_title', $item_id); ?></span></li>
			  <li><strong>ITEM SIZE: </strong> <span class='item_size'><?php print ($ITEM_TYPE == "FOLDER") ? file_size_convert($directory->item_full_size($item_id)) : $directory->item_by_id('item_size', $item_id); ?></span></li>
			  <li><strong>ITEM TYPE: </strong> <?php print $directory->item_by_id('file_type', $item_id); ?></li>
			  <li><strong>ITEM EXT: </strong> <?php print $ITEM_EXT; ?></li>
			  <li><strong>ITEM DESCRIPTION: </strong> <span class='item_description'><?php print $directory->item_by_id('item_description', $item_id); ?></span></li>
			  <li><strong>DATE UPLOADED: </strong> <?php print $directory->item_by_id('date_added', $item_id); ?></li>
			  <li><strong>ITEM USERS: </strong> <span class='file_access_users'></span></li>
			  <?php if($ITEM_TYPE == "FILE") { ?>
			  <li><strong>DOWNLOADS: </strong> <?php print $directory->item_by_id('item_downloads', $item_id); ?></li>
			  <?php } ?>
			  <span><?php print $directory->parent_info($item_id)->parent_back_link; ?></span>
            </ul>
          </div>
		  <div class="row-fluid">
		  <div class="">
			<a title="Click to rename this <?php print $ITEM_TYPE; ?>." data-toggle="modal" data-target="#renameItem" class="btn btn-block span2 btn-success" href="#" id="rename_Item" value="<?php print $item_id ?>"><i class="icon-pencil"></i> RENAME</a>
			<?php if($ITEM_TYPE == "FILE") { ?>
			<?php IF(IN_ARRAY($ITEM_EXT, config_item("microsoft_docs"))) { ?>
			<a title="Click to view this <?php print $ITEM_TYPE; ?> with Google Docs Application." class="btn btn-block span2 btn-info" href="#" data-toggle="modal" data-target="#viewItem"><i class="icon-eye-open"></i> VIEW</a>
			<?php } ?>
			<?php IF(IN_ARRAY($ITEM_EXT, config_item("zipped_files"))) { ?>
			<a title="Click to extract this <?php print $ITEM_TYPE; ?> into a directory of choice."  class="btn btn-block span2 btn-info extract_zip" href="#" value='<?php print $item_id ?>' data-toggle="modal" data-target="#extractZippedItem"><i class="icon-bookmark"></i> EXTRACT</a>
			<?php } ?>
			<?php IF(IN_ARRAY(".".$ITEM_EXT, config_item("editable_ext"))) { ?>
			<a title="Click to edit the contents of this <?php print $ITEM_TYPE; ?>." class="btn btn-block span2 btn-info" href="#" data-toggle="modal" data-target="#editItem"><i class="icon-edit"></i> EDIT</a>
			<?php } ?>
			<a title="Click to share this <?php print $ITEM_TYPE; ?> with other users." class="btn btn-block span2 btn-warning share_Item" href="#" value="<?php print $config->base_url()."Share/Id/$item_id"; ?>"><i class="icon-share"></i> SHARE</a>
			<?php } ?>
			<a title="Click to download this <?php print $ITEM_TYPE; ?>." class="btn btn-block span2 btn-default" href="#" data-toggle="modal" data-target="#moveItem"><i class="icon-move"></i> MOVE</a>
			<a title="Click to add users to access this <?php print $ITEM_TYPE; ?>." data-toggle="modal" data-target="#addUserToFile" class="btn btn-block span2 btn-success" href="#" id="rename_Item" value="<?php print $item_id ?>"><i class="icon-plus"></i> ADD USER</a>
			<a title="Click to move this <?php print $ITEM_TYPE; ?> into the recycle bin." onclick='process_item("delete", "<?php print $item_id; ?>", "<?PHP PRINT $ITEM_TYPE; ?>", "<?php PRINT $session->userdata(UID_SESS_ID); ?>", "REDIR")' class="btn btn-block span2 btn-danger" href="#" id="deleteItem" value="<?php print $item_id ?>"><i class="icon-remove"></i> TRASH</a>			
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
					$listFolders = $DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(UID_SESS_ID)."' AND item_type='FOLDER' AND item_status='1' AND item_deleted='0' AND item_folder_id='$item_id'");
					
					$listFiles = $DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(UID_SESS_ID)."' AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_folder_id='$item_id'" );
					
					$NO_ITEM =((COUNT($listFiles) < 1) AND (COUNT($listFolders) < 1)) ? TRUE : FALSE;
					
					IF(!$NO_ITEM) {
						
						#USING THE FOREACH LOOP 
						FOREACH($listFolders AS $Folders) {
							$fileName = $Folders["item_title"];
							$Id = $Folders["id"];
							$Uid = $Folders["item_unique_id"];
							
							PRINT "<div class='file File_Info_$Id' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'><a href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Folders['item_thumbnail']."'><br>$fileName</a> <br>
							<div class='file_option' id='option_$Id' align='center'>
								<span onclick='process_item(\"delete\", \"$Id\", \"FOLDER\", \"".$session->userdata(UID_SESS_ID)."\");' class='btn btn-danger'><i class='icon-trash'></i> Delete Folder</span>
							</div>
							</div>";
						}
						FOREACH($listFiles AS $Files) {
							$file_ext = $Files["item_ext"];
							$fileName = $Files["item_title"];
							$Id = $Files["id"];
							$Uid = $Files["item_unique_id"];
							$DLink = $Files["item_download_link"];
							
							PRINT "<div class='file File_Info_$Id' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'>";
							
							PRINT "<a title='Click to view full details of this file.' href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Files['item_thumbnail']."'><br>$fileName</a><br>";
							
							PRINT "<div class='file_option' id='option_$Id'>";
							
							// CONFIRM THAT THE FILE IS A ZIP FILE
							IF($file_ext == "zip") {
								PRINT "<span value='$Id' title='Extract File' data-toggle=\"modal\" data-target=\"#extractZippedItem\" class='btn btn-primary extract_zip'><i class='icon-bookmark'></i></span> ";
							} ELSE {
								// CHECK IF THE FILE IS PART OF THE THE LIST OF EDITABLE FILES
								IF(IN_ARRAY(".".$file_ext, config_item("editable_ext"))) {
									PRINT "<span title='Edit the contents of this file.' class='btn btn-primary'><a style='color:#fff' href='".$config->base_url()."ItemStream/Id/$Uid/Edit'><i class='icon-edit'></i></a></span> ";
								} ELSE {							
									PRINT "<span title='Click to view the full contents of this file.' onclick='process_item(\"edit\", \"$Uid\", \"FILE\", \"".$session->userdata(UID_SESS_ID)."\");' class='btn btn-primary'><i class='icon-eye-open'></i></span> ";
								}
							}
							PRINT "<span title='Download this file' class='btn btn-success'><a style='color:#fff' href='".$config->base_url()."Download/$DLink' target='_blank'><i class='icon-download'></i></a></span> ";
							PRINT "<span title='Add File to Share List' onclick='add_share_item(\"$Uid\",\"$fileName\");' class='btn btn-warning'><i class='icon-plus'></i></span> ";
							PRINT "<span title='Click to delete this file.' onclick='process_item(\"delete\", \"$Id\", \"FILE\", \"".$session->userdata(UID_SESS_ID)."\");' class='btn btn-danger'><i class='icon-trash'></i></span>";
							PRINT "</div></div>";
						}
					}
				  ?>
				</div>
			  </div>
			</div>
		</div><!-- /debug -->
		<?php } ?>
		<?php IF(IN_ARRAY($ITEM_EXT, config_item("audio_files")) OR IN_ARRAY($ITEM_EXT, config_item("video_files"))) { ?>
		<script type="text/javascript">
			//<![CDATA[
			$(document).ready(function(){

				$("#jquery_jplayer_1").jPlayer({
					ready: function () {
						$(this).jPlayer("setMedia", {
							title: "<?php print $directory->item_by_id('item_title', $item_id); ?>",
							<?php print $ITEM_EXT; ?>: "<?php print $config->base_url().config_item('upload_path').$item_slug; ?>",
							m4v: "<?php print $config->base_url().config_item('upload_path').$item_slug; ?>",
						});
					},
					swfPath: "<?php print $config->base_url(); ?>assets/js/dist/jplayer",
					supplied: "<?php PRINT (IN_ARRAY($ITEM_EXT, config_item("video_files"))) ? "m4v," : NULL; ?><?php print $ITEM_EXT; ?>",
					<?php IF(IN_ARRAY($ITEM_EXT, config_item("video_files"))) { ?>
					size: {
						width: "640px",
						height: "460px",
						cssClass: "jp-video-360p"
					},
					<?php } ELSEIF(IN_ARRAY($ITEM_EXT, config_item("audio_files"))) { ?>
					wmode: "window",
					<?php } ?>
					useStateClassSkin: true,
					autoBlur: false,
					smoothPlayBar: true,
					keyEnabled: true,
					remainingDuration: true,
					toggleDuration: true
				});
			});
			//]]>
		</script>
		<div class="row-fluid">
		<br clear="both"><br clear="both">
		<div class="span12">
		<div id="jquery_jplayer_1" class="jp-jplayer"></div>
			<div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
				<div class="jp-type-single">
					<div class="jp-gui jp-interface">
						<div class="jp-controls">
							<button class="jp-play" role="button" tabindex="0">play</button>
							<button class="jp-stop" role="button" tabindex="0">stop</button>
						</div>
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
						<div class="jp-volume-controls">
							<button class="jp-mute" role="button" tabindex="0">mute</button>
							<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
						</div>
						<div class="jp-time-holder">
							<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
							<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
							<div class="jp-toggles">
								<button class="jp-repeat" role="button" tabindex="0">repeat</button>
							</div>
						</div>
					</div>
					<div class="jp-details">
						<div class="jp-title" aria-label="title">&nbsp;</div>
					</div>
					<div class="jp-no-solution">
						<span>Update Required</span>
						To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
					</div>
				</div>
			</div>
		</div>
		</div>
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
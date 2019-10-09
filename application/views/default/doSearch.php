<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $directory, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
			
		IF(($SITEURL[1] == "doSearch") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "searchUser")) {
			#get the items and their values
			$user_name = xss_clean($_POST["Name"]);
			$user_id = $session->userdata(UID_SESS_ID);
			$office_id = $session->userdata(OFF_SESSION_ID);
			
			$Query = $DB->query("SELECT * FROM _admin WHERE fullname LIKE '%$user_name%' AND status='1' AND activated='1' AND office_id='$office_id' AND id !='$user_id'");
			
			IF(COUNT($Query) < 1) {
				PRINT "<div class='alert alert-danger btn-block'>Sorry! No user found with the specified name.</div>";
			} ELSE {
				?>
				<div class="widget-content">
				<div class="todo">
				  <ul>
					<?PHP FOREACH($Query as $Result) { ?>
					<li class="clearfix">
					  <div class="txt"> <span class="by label">Add</span> <?php print $Result["fullname"]; ?> </div>
					  <div class="pull-right"><a class="tip add_user btn btn-primary" href="javascript:add_user('<?php print $Result["id"]; ?>', '<?php print $Result['fullname']; ?>');" title="Add User"><i class="icon-plus"></i> ADD USER</a></div>
					</li>
					<?PHP } ?>
				  </ul>
				</div>
			  </div>
		  <?PHP 
			}
		} ELSEIF(($SITEURL[1] == "doReturnResults") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "doSearchForItem") AND ISSET($_POST["q"])) {
			#get the items and their values
			$SearchTerm = xss_clean($_POST["q"]);
			$SearchTerm = PREG_REPLACE('/\\.[^.\\s]{3,4}$/', '', $SearchTerm);
			$user_id = $session->userdata(UID_SESS_ID);
			$office_id = $session->userdata(OFF_SESSION_ID);
			
			$listFiles = $DB->query("SELECT * FROM _item_listing WHERE (user_id='$user_id' AND item_status='1' AND item_deleted='0') AND item_title LIKE '%$SearchTerm%'");
			
			IF((COUNT($listFiles) < 1)) {
				PRINT "<div class='alert alert-danger btn-block'>Sorry! No results was found for your search term <strong>$SearchTerm</strong>.</div>";
			} ELSE {
				?>
				<?PHP FOREACH($listFiles as $Result) { ?>
				<li>
					<div style="width:200px;font-weight:bolder">
						<a href="<?php print $config->base_url()."ItemStream/Id/{$Result["item_unique_id"]}/Edit"; ?>" class="btn btn-primary btn-mini">
							<?php print ($Result["item_ext"]) ? $Result["item_title"].'.'.$Result["item_ext"] : $Result["item_title"]; ?>
						</a>
					</div>
					<div class="article-post">
					  <div class="fr">
						<?php IF(IN_ARRAY($Result["item_ext"], config_item("audio_files")) OR IN_ARRAY($Result["item_ext"], config_item("video_files"))) { ?>
						<a href="<?php print $config->base_url()."MediaPlayer/{$Result["item_unique_id"]}"; ?>" class="btn btn-info btn-mini"><i class="icon icon-play"></i> Play</a>
						<?php } ?>
						<span title='Click to view the full contents of this file.' onclick='process_item("edit", "<?php print $Result["item_unique_id"]; ?>", "<?php print $Result["item_type"]; ?>", "<?php PRINT $session->userdata(UID_SESS_ID); ?>");' class='btn btn-success btn-mini'><i class='icon-eye-open'></i> View</span>
						<?php IF(IN_ARRAY(".".$Result["item_ext"], config_item("editable_ext"))) { ?>
						<a href="<?php print $config->base_url()."ItemStream/Id/{$Result["item_unique_id"]}/Edit"; ?>" class="btn btn-primary btn-mini"><i class="icon icon-edit"></i> Edit</a>
						<?php } ?>
						<?php IF ($Result["item_type"] == "FILE") { ?>
						<a target="_blank" class="btn btn-warning btn-mini" href="<?php print $config->base_url().'Download/'.$Result['item_download_link']; ?>"><i class="icon-download"></i> DOWNLOAD</a>
						<?php } ?>
						<a  onclick='process_item("delete", "<?php print $Result["id"]; ?>", "<?PHP PRINT $Result["item_type"]; ?>", "<?php PRINT $session->userdata(UID_SESS_ID); ?>", "REDIR")' class="btn btn-danger btn-mini"><i class="icon icon-trash"></i> Delete</a>
					  </div>
					  <span class="user-info">
						<strong>By:</strong> <?php print $Result["item_users"]; ?> /
						<strong>Date:</strong> <?php print date("D M Y", strtotime($Result["date_added"])); ?> /
						<strong>Time:</strong> <?php print date("H:i A", strtotime($Result["date_added"])); ?>
					  </span>
					  <p>
						<strong>Item Type</strong>: <?PHP PRINT $Result["item_type"]; ?>
						<?PHP PRINT ($Result["item_type"] == "FILE") ? " / <strong>File Type</strong>: {$Result["item_ext"]}" : NULL; ?> / 
						<strong>Parent Folder</strong>: <?PHP PRINT ($directory->item_by_id('item_title', $Result["item_parent_id"])) ? $directory->item_by_id('item_title', $Result["item_parent_id"]) : "Root"; ?>
					  </p>
					</div>
				  </li>
				<?PHP } ?>
		  <?PHP 
			}
		}
		
		
		
		
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
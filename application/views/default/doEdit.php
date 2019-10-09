<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices, $directory;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		// CONFIRM THAT THE USER WANTS TO SAVE THE FILE
		IF(($SITEURL[1] == "doSaveFile")) {
			IF(ISSET($_POST["content_area"])) {
				# get the items and their values
				$content_area = htmlspecialchars_decode($_POST["content_area"]);
				if( get_magic_quotes_runtime()) {
					$content_area = stripslashes( $content_area );
				}
				$n_FileName = $session->userdata("ItemID");
				
				# insert the new data into the file
				file_put_contents(config_item('upload_path').$n_FileName, $content_area);
				
				// GET THE NEW FILE SIZE
				$n_FileSize = file_size_convert(config_item('upload_path')."$n_FileName");
				$n_FileSize_KB = file_size(config_item('upload_path')."$n_FileName");
				
				# update the database with the new information 
				$DB->query("UPDATE _item_listing SET 
					user_id='{$session->userdata(UID_SESS_ID)}',
					item_size='$n_FileSize', item_size_kilobyte='$n_FileSize_KB'
					WHERE item_unique_id='$n_FileName'
				");
				# print success message 
				PRINT "<div class='alert alert-success'>The file was successfully saved.</div>";
				
				# print success message 
				PRINT "<script>$('.item_size').html('".$directory->item_by_id('item_size', $n_FileName)."');</script>";
			} ELSE {
				PRINT "HELLO";
			}
		}
		
		
		// CONFIRM THAT THE USER WANTS TO REOPEN THE FILE
		ELSEIF(($SITEURL[1] == "doReloadContent")) {
			IF(ISSET($_POST["Action"]) AND ($_POST["Action"] == "doReloadContent")) {
				# get the items and their values
				$n_FileName = $session->userdata("ItemID");
				# PRINT THE CONTENT OF THIS FILE
				$content = file_get_contents( config_item('upload_path').$n_FileName );
				if( get_magic_quotes_runtime()) {
					$content = stripslashes( $content );
				}
				print html_entity_decode($content);
			}
		}
		
		
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
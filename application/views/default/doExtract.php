<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		$zipped = load_class('Zipper', 'models');
		
		IF(($SITEURL[1] == "extractZip") AND ISSET($_POST["zipped_item"])) {
			#get the items and their values
			$zipped_item = xss_clean($_POST["zipped_item"]);
			$extract_folder = xss_clean($_POST["extract_folder"]);
			$user_id = $session->userdata(UID_SESS_ID);
			
			$Query = $DB->query("SELECT * FROM _item_listing WHERE id='$zipped_item' AND item_status='1' AND user_id='$user_id'");
			
			IF(COUNT($Query) < 1) {
				PRINT "<div class='alert alert-danger btn-block'>Sorry! No user found with the specified name.</div>";
			} ELSE {
				// GET THE ITEM NAME
				$filename = config_item('upload_path').$directory->item_by_id('item_unique_id', $zipped_item);
				// CREATE RANDOM FOLDER TO EXTRACT FILES TO
				$zipped_folder = "assets/zipped/".random_string('alnum', 10);
				MKDIR($zipped_folder, 0777);
				// EXTRACT THE ITEMS TO THE NEW FOLDER
				IF($zipped->unzip($filename, $zipped_folder)) {
					// SET ADDITIONAL INFORMATION FOR THE FILE
					
					FOREACH(get_dir_file_info($zipped_folder, FALSE) AS $file_info) {
						$n_ItemName = $file_info["name"];
						//CONFIRM THE ITEM TYPE
						IF(IS_DIR($n_ItemName)) {
							$itemType = "FOLDER";
						} ELSE {
							$itemType = "FILE";
						}
						
						$n_FileExt = STRTOLOWER(PATHINFO($n_ItemName, PATHINFO_EXTENSION));
						$n_ThumbNail = $directory->get_thumbnail_by_ext($n_FileExt);
						$n_FileType = get_file_mime($n_FileExt, 1);
						$n_FileSize = file_size_convert($zipped_folder."/".$n_ItemName);
						$n_FileSize_KB = file_size($zipped_folder."/".$n_ItemName);
						$n_FileTitle_Real = PREG_REPLACE('/\\.[^.\\s]{3,4}$/', '', $n_ItemName);
						// RENAME THE FILE AND THEN MOVE IT TO THE DESTINATION FOLDER
						$n_FileName = random_string('alnum', MT_RAND(10, 30));
						$n_Thumb = random_string('alnum', MT_RAND(45, 70));
						$n_Download_Link = random_string('alnum', MT_RAND(25, 40));
						// MOVE THE FILES 
						$DB->query("INSERT INTO _item_listing SET 
							user_id='{$session->userdata(UID_SESS_ID)}',
							office_id='{$session->userdata(OFF_SESSION_ID)}',
							item_users='{$admin_user->return_username()}', 
							item_title='$n_FileTitle_Real', item_unique_id='$n_FileName',
							item_type='$itemType', item_ext='$n_FileExt',
							item_download_link='$n_Download_Link',file_type='$n_FileType',
							item_date=now(), item_thumbnail='$n_ThumbNail',
							item_parent_id='$extract_folder', item_folder_id='$extract_folder',
							item_size='$n_FileSize', item_size_kilobyte='$n_FileSize_KB'
						");
						// INSERT THE DETAILS INTO THE DATABASE						
						RENAME($zipped_folder."/".$n_ItemName, config_item('upload_path').$n_FileName);
					}
					// DELETE THE FOLDER AND ALL ITEMS THEREIN WHEN COMPLETE
					delete_files($zipped_folder, FALSE);
					RMDIR($zipped_folder);
					// PRINT SUCCESS MESSAGE
					PRINT "<div class='alert alert-success btn-block'>The zipped file was successfully extracted into the <a href='".$config->base_url()."ItemStream/Id/".$directory->item_by_id('item_unique_id', $extract_folder)."'><strong>Specified Folder</strong></a>.</div>";
				} ELSE {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! There was a problem while trying to extract the zipped file.</div>";
				}
			}
		}
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
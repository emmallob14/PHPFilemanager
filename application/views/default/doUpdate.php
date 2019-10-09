<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		IF(($SITEURL[1] == "changeName") AND ISSET($_POST["item_name"])) {
			#check if the user is logged in
			IF(ISSET($_POST["href"]) AND ISSET($_POST["itemId"])) {
				#get the items and their values
				$href = xss_clean($_POST["href"]);
				$name = xss_clean($_POST["item_name"]);
				$desc = NL2BR(xss_clean($_POST["desc"]));
				$item_id = xss_clean($_POST["itemId"]);
				
				IF(EMPTY($name)) {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! You must enter a filename.</div>";
				} ELSE {
					// write the queryString
					$queryString = $DB->where(
						'_item_listing', '*', 
						ARRAY(
							'id'=>"='$item_id'",
							'user_id'=>"='{$session->userdata(UID_SESS_ID)}'"
					));
					// confirm that the current user owns the file / folder to be renamed
					IF($DB->num_rows($queryString) == 1) {
						// GO AHEAD AND UPDATE THIS FILE / FOLDER NAME AND DESCRIPTION
						$DB->execute("UPDATE _item_listing SET item_title='$name', item_description='$desc' WHERE id='$item_id' AND user_id='".$session->userdata(UID_SESS_ID)."'");
						
						IF($directory->item_by_id('item_type', $item_id) == "FILE") {
							// GET THE FILE INFORMATION AND THEN UPDATE THE DATABASE TABLE
							$n_FileTitle_Real = PREG_REPLACE('/\\.[^.\\s]{3,4}$/', '', $name);
							
							// SET ADDITIONAL INFORMATION FOR THE FILE
							$n_Thumb = random_string('alnum', MT_RAND(45, 70));
							$n_Download_Link = random_string('alnum', MT_RAND(25, 40));
							$n_FileExt = STRTOLOWER(PATHINFO($name, PATHINFO_EXTENSION));
							$n_FileInfo = get_file_mime($n_FileExt, 1);
							$n_ThumbNail = $directory->get_thumbnail_by_ext($n_FileExt);
							
							// UPDATE RECORDS
							$DB->query("UPDATE _item_listing SET 
								item_title='$n_FileTitle_Real',
								item_type='FILE', item_ext='$n_FileExt',
								item_download_link='$n_Download_Link',
								file_type='$n_FileInfo',
								item_thumbnail='$n_ThumbNail'
								WHERE id='$item_id'
							");
						}
						# update the user activity logs
						$DB->execute("INSERT INTO _activity_logs SET full_date=now(), date_recorded=now(), admin_id='".$admin_user->return_username()."', activity_page='updated-item', activity_id='$item_id', activity_details='$item_id', office_id='".$session->userdata(OFF_SESSION_ID)."', activity_description='{ADMIN_FULLNAME} updated the File with name {FILE_NAME}.'");
					
						// PRINT OUT THE NEW INFORMATION
						PRINT "<script> $('.item_name').html('".$directory->item_by_id('item_title', $item_id)."'); $('.item_description').html('".$directory->item_by_id('item_description', $item_id)."');</script>";
						PRINT "<div class='alert alert-success'>File successfully renamed.</div>";
					} ELSE {
						PRINT "<div class='alert alert-danger btn-block'>Sorry! You are not permitted to rename this file.</div>";
					}
				}
			}
		}
		
		#CHANGE THE USER UPLOADS STATUS
		ELSEIF(($SITEURL[1] == "doEffectChange") AND ISSET($SITEURL[2])) {
			IF(($SITEURL[2] == "uploadStatus") AND ISSET($_POST["Action"])) {
				// ASSIGN SOME VARIABLES
				$user_id = (INT)xss_clean($_POST["user_id"]);
				$status = xss_clean($_POST["status"]);
				// UPDATE THE USER UPLOAD STATUS
				$DB->just_exec("UPDATE _admin SET uploads_status='$status' WHERE id='$user_id'");
				// GET THE CURRENT USER UPLOAD STATUS
				$user_info = $admin_user->item_by_id($user_id, "uploads_status");
				$upload_status = ($user_info) ? 0 : 1;
				$upload_button = (!$upload_status) ? "btn-success" : "btn-danger";
				$upload_comment = (!$upload_status) ? "<i class='icon icon-thumbs-up'></i> ACTIVE" : "<i class='icon icon-thumbs-down'></i> INACTIVE";
				// PRINT THE NEW INFORMATION
				PRINT "<span onclick=\"change_upload_status('$user_id', '$upload_status')\" class=\"btn $upload_button\">$upload_comment</span>";
			}
		}
		
		
		#CHANGE THE USER UPLOADS LIMIT
		ELSEIF(($SITEURL[1] == "doChangeUserDiskLimit") AND ISSET($SITEURL[2]) AND ISSET($_POST["Action"])) {
			IF(($SITEURL[2] == "uploadLimit") AND $_POST["Action"] == "doUploadLimit") {
				// ASSIGN SOME VARIABLES
				$user_id = xss_clean($_POST["user_id"]);
				$user_id2 = $session->userdata("user_id");
				$usage_limit = ($_POST["usage_limit"]);
				// CONFIRM THAT THE USAGE IS A NUMERIC CHARACTER
				IF(!PREG_MATCH("/^[0-9]+$/", $usage_limit)) {
					// PRINT ERROR MESSAGE
					PRINT "<div class='alert alert-danger'>Sorry! The quota must be a valid numeric integer.</div>";
				} ELSE {
					$limit = $usage_limit*(1024*1024);
					$office_id = $session->userdata(OFF_SESSION_ID);
					// GET THE OFFICE OVERALL USAGE
					$overall_used = $directory->return_usage()->used_size;
					$overall_permitted = $offices->item_by_id('disk_space', $office_id);
					// CONFIRM THAT THE USER ID PARSED MATCHES THE ONE SET IN THE SESSION
					IF($user_id != $user_id2) {
						PRINT "<div class='alert alert-danger'>Sorry! An invalid session token parsed.</div>";
						// CONFIRM THAT THE USER NEW USAGE DOES NOT EXCEED THE AVAILABLE USAGE
					} ELSEIF(($limit+$overall_used) > $overall_permitted) {
						// UPDATE THE USER UPLOAD STATUS
						PRINT "<div class='alert alert-danger'>Sorry! The quota set exceeds your overall disk space limit.</div>";
					} ELSE {
						// UPDATE THE USER INFORMATION
						$DB->just_exec("UPDATE _admin SET uploads_limit='$limit' WHERE username='$user_id'");
						// PRINT THE SUCCESS MESSAGE TO THE ADMIN USER
						PRINT "<script>";
						PRINT "$('.gritter-item-wrapper').css('display','block');
						$.gritter.add({
							title:	'Update Notification',
							text:	'Success! <strong>$user_id</strong> will now upload up to <strong>".file_size_convert($limit)."</strong>',
							sticky: false
						});";
						PRINT "</script>";
						// FETCH THE USER NEW INFORMATION AND DISPLAY
						$uid = $admin_user->item_by_id($user_id, "id");
						$usage = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "user_id='$uid'", 'item_size', 'ORDER BY id ASC')*1024);
						// print message
						PRINT file_size_convert($usage) ." out of ".file_size_convert($limit) ." (". ROUND(((($usage)/$limit) * 100), 2). "% used)";
					}
				}
			}
		}
		
		#CHANGE THE UPLOAD FOLDER
		ELSEIF(($SITEURL[1] == "doChangeUploadPath") AND ISSET($SITEURL[2])) {
			IF(($SITEURL[2] == "uploadFolder") AND ISSET($_POST["Action"])) {
				// ASSIGN SOME VARIABLES
				$folder_id = xss_clean($_POST["folder_id"]);
				// UPDATE THE USER UPLOAD STATUS
				$session->set_userdata(ROOT_FOLDER, $folder_id);
				// PRINT THE NEW INFORMATION
				PRINT ($session->userdata(ROOT_FOLDER)) ? "<a href='".$config->base_url()."ItemStream/Id/".$directory->item_by_id('item_unique_id', $session->userdata(ROOT_FOLDER))."'>".STRTOUPPER($directory->item_by_id('item_title', $session->userdata(ROOT_FOLDER)))."</a>" : "<a href='".$config->base_url()."ItemsStream'>ROOT FOLDER</a>";
			}
		}

	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
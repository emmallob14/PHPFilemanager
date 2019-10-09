<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		//CONFIRM THAT THE USER WANTS TO CREATE A NEW FILE OR FOLDER 
		IF(($SITEURL[1] == "addFolderFile")) {
			#check if the user is logged in
			IF(ISSET($_POST["item_type"]) AND ISSET($_POST["item_name"])) {
				#get the items and their values
				$root_folder = $directory->item_by_id('item_title', xss_clean($session->userdata(ROOT_FOLDER)));
				$item_type = xss_clean($_POST["item_type"]);
				$item_name = xss_clean($_POST["item_name"]);
				$parent_folder = (INT)xss_clean($_POST["parent_folder"]);
				
				IF(!IN_ARRAY($item_type, ARRAY('FILE', 'FOLDER'))) {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! You must select a valid Item Type.</div>";
				} ELSEIF(EMPTY($item_name)) {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! You must enter an item name.</div>";
				} ELSEIF(!IN_ARRAY($item_type, ARRAY('FILE', 'FOLDER'))) {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! You must select a valid Item Type.</div>";
				} ELSE {
					$n_FileName = random_string('alnum', MT_RAND(25, 45));
					// GO AHEAD AND UPDATE THIS FILE / FOLDER NAME AND DESCRIPTION
					$DB->execute("INSERT INTO _item_listing SET item_title='$item_name', user_id='".$session->userdata(UID_SESS_ID)."', office_id='{$session->userdata(OFF_SESSION_ID)}', item_type='$item_type', item_unique_id='$n_FileName', item_parent_id='$parent_folder', item_users='".$admin_user->return_username()."', item_date=now(), item_folder_id='$parent_folder'");
					// GET THE LAST ID INSERTED 
					$last_id = $DB->max_all('id', '_item_listing');
					
					// PRINT THE SUCCESS MESSAGE 
					PRINT "<div class='alert alert-success btn-block'>Success! Your folder <strong><a href='".$config->base_url()."ItemStream/Id/".$directory->item_by_id('item_unique_id', $last_id)."'><strong>$item_name</strong></a></strong> has been created.";
					
					IF($item_type == "FOLDER") {
						// PRINT THE UPLOAD LINK IF IT IS A FOLDER
						PRINT "<span style='cursor:pointer' onclick=\"update_upload_folder('$last_id', 'redir')\"> <strong>UPLOAD TO THIS FOLDER?</strong></div>";
					} ELSE {
						// GET THE FILE INFORMATION AND THEN UPDATE THE DATABASE TABLE
						$n_FileTitle_Real = PREG_REPLACE('/\\.[^.\\s]{3,4}$/', '', $item_name);
						
						// SET ADDITIONAL INFORMATION FOR THE FILE
						$n_Thumb = random_string('alnum', MT_RAND(45, 70));
						$n_Download_Link = random_string('alnum', MT_RAND(25, 40));
						$n_FileExt = STRTOLOWER(PATHINFO($item_name, PATHINFO_EXTENSION));
						$n_FileInfo = get_file_mime($n_FileExt, 1);
						$n_ThumbNail = $directory->get_thumbnail_by_ext($n_FileExt);
						
						// CREATE NEW FILE
						$myFile = FOPEN(config_item('upload_path').$n_FileName, "w");
						FWRITE($myFile, "");
						FCLOSE($myFile);
						
						// UPDATE RECORDS
						$DB->query("UPDATE _item_listing SET 
							item_title='$n_FileTitle_Real',
							item_type='FILE', item_ext='$n_FileExt',
							item_download_link='$n_Download_Link',
							file_type='$n_FileInfo',
							item_thumbnail='$n_ThumbNail',
							item_size='0KB', item_size_kilobyte='0'
							WHERE id='$last_id'
						");
		
						// PRINT THE FILE LINK
						PRINT "<a href='".$config->base_url()."ItemStream/Id/".$directory->item_by_id('item_unique_id', $last_id)."'><strong>VIEW THIS FILE</strong></a>";
					}
					
					# update the user activity logs
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='{$admin_user->return_username()}', activity_page='created-item', office_id='".$session->userdata(OFF_SESSION_ID)."', activity_id='$last_id', activity_details='$last_id', activity_description='{ADMIN_FULLNAME} created the $item_type with name $item_name'");
					
					// reload the page
					PRINT "<script> $('#addFolderFile')[0].reset();</script>";
				}
			}
		}
		
		// CONFIRM THAT THE USER WANTS TO RELOAD THE LIST OF FOLDERS
		ELSEIF(($SITEURL[1] == "doListFolders")) {
			#check if the user is logged in
			IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "doFoldersList") {
				
				PRINT "<select style=\"height:;padding-top:5px;width:350px\" class=\"form-control\" id=\"parent_folder\" name=\"parent_folder\" onchange=\"update_upload_folder(this.value)\">
					<option value=\"0\">Root Folder</option>";
					// CALL THE FUNCTION TO LIST ALL THE FOLDERS AND SET 
					// THE ROOT FOLDER AS THE NEW ID
					$directory->display_folders(0, 1, $session->userdata(ROOT_FOLDER));
				PRINT "</select>";
			}
		}
		
		
		// CONFIRM THAT THE USER WANTS TO MOVE A FILE OR FOLDER
		ELSEIF(($SITEURL[1] == "doMoveItem")) {
			#check if the user is logged in
			IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "doMoveItem") {
				$parent_folder = (INT)xss_clean($_POST["parent_folder"]);
				$item_id = $session->userdata("ItemID");
				
				// UPDATE RECORDS
				// ENSURE THAT THE USER IS NOT UPLOADING INTO THE SAME DIRECTORY
				IF($parent_folder != $directory->item_by_id('id', $item_id)) {
					$DB->query("UPDATE _item_listing SET 
						item_parent_id='$parent_folder',item_folder_id='$parent_folder'
						WHERE item_unique_id='$item_id'
					");
					
					# update the user activity logs
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='{$admin_user->return_username()}', activity_page='moved-item', office_id='".$session->userdata(OFF_SESSION_ID)."', activity_id='$item_id', activity_details='$parent_folder', activity_description='{ADMIN_FULLNAME} moved <strong>{$directory->item_by_id('item_title', $item_id)}</strong> into <strong>{$directory->item_by_id('item_title', $parent_folder)}</strong> Directory'");
					
					// PRINT THE UPLOAD LINK IF IT IS A FOLDER
					PRINT "<span class='alert alert-success' style='cursor:pointer'> The File/Folder was sucessfully moved into the <strong>";
					PRINT ($directory->item_by_id('item_title', $parent_folder)) ? " <a href='".$config->base_url()."ItemStream/Id/".$directory->item_by_id('item_unique_id', $parent_folder)."'><strong>".$directory->item_by_id('item_title', $parent_folder)."</strong></a> " : " <a href='".$config->base_url()."ItemsStream'>ROOT</a> ";
					PRINT "</strong>Directory. </div>";
				} ELSE {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! You cannot move File/Folder into the same Directory.</div>";
				}
				
			}
		}
		
		# ADD USERS TO THE LIST OF USERS WHO CAN ACCESS THE FILE OR FOLDER
		ELSEIF(($SITEURL[1] == "doAddUser")) {
			
			IF(ISSET($_POST["Action"]) AND ($_POST["Action"] == "doAddUser")) {
				// ASSIGN SOME VARIABLES
				$admin_id = $admin_user->return_username();
				$user_id = xss_clean($_POST["user_id"]);
				// CONFIRM THAT A VALID USERNAME WAS PARSED
				IF($admin_user->get_details_by_id($user_id)->found) {
					$item_id = BASE64_DECODE(xss_clean($_POST["item_id"]));
					// CONFIRM THAT THE ITEM_ID HAS NOT BE ALTERED
					IF($session->userdata('ItemID') != $item_id) {
						// PRINT ERROR MESSAGE
						PRINT "<div class='alert alert-danger btn-block'>Sorry! An invalid Item ID was parsed.</div>";
					} ELSE {
						$item_users = $directory->item_by_id('item_users', $item_id);
						// EXPLODE THE LIST OF USERS WHO ALREADY HAVE ACCESS TO THE FILE
						$_explode_users = EXPLODE("/", $item_users);
						// CONFIRM THAT THE USER ID PARSED IS NOT ALREADY PART OF THE LIST OF 
						// FILE ACCESS USERS.
						IF(IN_ARRAY($user_id,  $_explode_users)) {
							PRINT "<div class='alert alert-danger btn-block'>Sorry! This user already has access to this Item.</div>";
						} ELSE {
							// ADD THE USER TO THE LIST OF USERS
							$new_user_list = $item_users.$user_id."/";
							// UPDATE THE DATABASE WITH THE NEW LIST OF USERS
							IF($DB->just_exec("UPDATE _item_listing SET item_users='$new_user_list' WHERE item_unique_id='$item_id' AND user_id='".$admin_user->return_id()."'")) {
								PRINT "<div class='alert alert-success btn-block'>Success! The user was successfully added to the access list.</div>";
								PRINT "<script>$('#doAddUser')[0].reset();</script>";
							} ELSE {
								PRINT "<div class='alert alert-danger btn-block'>Sorry! There was an error while trying to add user to the list.</div>";
							}
						}
					}
				} ELSE {
					PRINT "<div class='alert alert-danger btn-block'>Sorry! An invalid user id was parsed.</div>";
				}
			}
		}		
		
		# LIST ALL USERS WHO CAN ACCESS THE FILE OR FOLDER
		ELSEIF(($SITEURL[1] == "doList") AND ISSET($SITEURL[2]) AND ($SITEURL[2] == "listUsers")) {
			
			IF(ISSET($_POST["Action"]) AND ($_POST["Action"] == "listUsers")) {
				// ASSIGN SOME VARIABLES
				$ITEM_USERS = $directory->item_by_id('item_users', $session->userdata('ItemID'));
				// PRINT THE RESULTS TO THE PAGE
				PRINT $directory->array_listing($ITEM_USERS, $session->userdata('ItemID'), "users")->list_users;
			}
		}
		
		
		# REMOVE USERS TO THE LIST OF USERS WHO CAN ACCESS THE FILE OR FOLDER
		ELSEIF(($SITEURL[1] == "modifyAccess") AND ($SITEURL[2] == "removeUser")) {
			
			IF(ISSET($_POST["Action"]) AND ($_POST["Action"] == "removeUser")) {
				// ASSIGN SOME VARIABLES
				$user_id = xss_clean($_POST["user_id"]);
				// CONFIRM THAT A VALID USERNAME WAS PARSED
				IF($admin_user->get_details_by_id($user_id)->found) {
					$item_id = BASE64_DECODE(xss_clean($_POST["item_id"]));
					// CONFIRM THAT THE ITEM_ID HAS NOT BE ALTERED
					IF($session->userdata('ItemID') != $item_id) {
						// PRINT ERROR MESSAGE
						PRINT "<div class='alert alert-danger btn-block'>Sorry! An invalid Item ID was parsed.</div>";
					} ELSE {
						$item_users = $directory->item_by_id('item_users', $item_id);
						// EXPLODE THE LIST OF USERS WHO ALREADY HAVE ACCESS TO THE FILE
						$_explode_users = EXPLODE("/", $item_users);
						// CONFIRM THAT THE USER ID PARSED IS NOT ALREADY PART OF THE LIST OF 
						// FILE ACCESS USERS.
						IF(!IN_ARRAY($user_id,  $_explode_users)) {
							PRINT "<div class='alert alert-danger btn-block'>Sorry! This user already no access to this Item.</div>";
						} ELSE {
							// REMOVE THE USER TO THE LIST OF USERS
							$new_user_list = str_replace($user_id."/", "", $item_users);
							// UPDATE THE DATABASE WITH THE NEW LIST OF USERS
							IF($DB->just_exec("UPDATE _item_listing SET item_users='$new_user_list' WHERE item_unique_id='$item_id' AND user_id='".$admin_user->return_id()."'")) {
								PRINT "Success!";
							} ELSE {
								PRINT "Error!";
							}
						}
					}
				} ELSE {
					PRINT "Error!";
				}
			}
		}
		
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
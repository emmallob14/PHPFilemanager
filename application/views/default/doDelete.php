<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		// CONFIRM THAT THE USER WANTS TO DELETE AN ITEM
		IF(($SITEURL[1] == "doDelete") AND ISSET($_POST["Action"]) AND ISSET($_POST["Uid"])) {
			$Id = xss_clean($_POST["Id"]);
			$Type = xss_clean($_POST["Type"]);
			$Uid = xss_clean($_POST["Uid"]);
			$Action = xss_clean($_POST["Action"]);
			$UserId = xss_clean($session->userdata(UID_SESS_ID));
			
			// CONFIRM THE ITEM TYPE
			IF(!IN_ARRAY($Type, ARRAY('FILE', 'FOLDER', 'Shared_File', 'Shared_Folder'))) {
				PRINT 'error';
			} ELSEIF($UserId != $Uid) {
				PRINT 'error';
			} ELSEIF($Action == "delete") {
				// CONFIRM THAT THE USER WANTS TO DELETE ONLY A FILE OR FOLDER AND NOT A SHARED ITEM
				IF(IN_ARRAY($Type, ARRAY('FILE', 'FOLDER'))) {
					IF($DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='$Id' AND user_id='$UserId'")) {
						// CONFIRM WHETHER THE ITEM IS A FILE OR FOLDER
						IF($Type == 'FOLDER') {
							// GET ALL FILES ATTACHED TO THIS FOLDER RECURSIVELY
							FOREACH($directory->list_attached_files($Id) AS $QueryList) {
								IF($QueryList["item_type"] == "FOLDER") {
									// RUN ANOTHER SUB QUERY FOR THE FOLDER
									FOREACH($directory->list_attached_files($QueryList["id"]) AS $QueryList2) {
										IF($QueryList2["item_type"] == "FOLDER") {
											FOREACH($directory->list_attached_files($QueryList2["id"]) AS $QueryList3) {
												// DELETE THE QUERY SET FROM THE DATABASE
												$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='{$QueryList3["id"]}' AND user_id='$UserId'");
											}
											// FINALLY DELETE THE MAIN QUERY FROM THE DATABASE
											$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='{$QueryList2["id"]}' AND user_id='$UserId'");
										} ELSE {
											// DELETE THE QUERY SET FROM THE DATABASE
											$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='{$QueryList2["id"]}' AND user_id='$UserId'");
										}
									}
									// DELETE THE QUERY SET FROM THE DATABASE
									$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='{$QueryList["id"]}' AND user_id='$UserId'");
								} ELSE {
									// DELETE THE QUERY SET FROM THE DATABASE
									$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='{$QueryList["id"]}' AND user_id='$UserId'");
								}
							}
							$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='$Id' AND user_id='$UserId'");
						} ELSE {
							// ELSE IF ITS A FILE THEN
							// DELETE THE FILE FROM THE SYSTEM
						}
						// FINALLY DELETE THE MAIN QUERY FROM THE DATABASE
						$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='$Id' AND user_id='$UserId'");
						// PRINT THE SUCCESS MESSAGE
						PRINT 'success';
					} ELSE {
						PRINT 'error';
					}
				}
				
				// CONFIRM THAT THE USER WANTS TO DELETE A SHARED ITEM
				IF(IN_ARRAY($Type, ARRAY('Shared_File', 'Shared_Folder'))) {
					IF($DB->just_exec("UPDATE _shared_listing SET shared_deleted='1' WHERE id='$Id' AND shared_by='$UserId'")) {
						// update the file
						$DB->query("INSERT INTO _shared_comments SET file_id='$Id', user_id='{$session->userdata(UID_SESS_ID)}', comment='The share file is no longer available, it has been Deleted by the user.', class='danger'");
						// print success message
						PRINT 'success';
					} ELSE {
						PRINT 'error';
					}
				}
			} ELSE {
				IF($Action == "start") {
					// UPDATE THE SHARING STATUS
					$DB->just_exec("UPDATE _shared_listing SET shared_status='1' WHERE id='$Id' AND shared_by='$UserId'");
					// PRINT SUCCESS MESSAGE
					PRINT "The file sharing has been continued";
				} ELSEIF($Action == "stop") {
					// UPDATE THE SHARING STATUS
					$DB->just_exec("UPDATE _shared_listing SET shared_status='0' WHERE id='$Id' AND shared_by='$UserId'");
					// PRINT SUCCESS MESSAGE
					PRINT "The file sharing has been stopped";
				}
			}
		}
	}
	
	
	// CONFIRM THAT THE USER WANTS TO PERMANENTLY DELETE AN ITEM
	IF(($SITEURL[1] == "permanentlyDeleteItem") AND ISSET($_POST["Action"]) AND ISSET($_POST["Type"])) {
		$Id = xss_clean($_POST["Id"]);
		$Type = xss_clean($_POST["Type"]);
		$Action = xss_clean($_POST["Action"]);
		$UserId = xss_clean($session->userdata(UID_SESS_ID));
		
		// CONFIRM THE ITEM TYPE
		IF($Action == "deleteItem") {
			// CONFIRM THAT THE USER WANTS TO DELETE ONLY A FILE OR FOLDER AND NOT A SHARED ITEM
			IF(IN_ARRAY($Type, ARRAY('FILE', 'FOLDER'))) {
				IF($DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='$Id' AND user_id='$UserId'")) {
					# update the user activity logs
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='{$admin_user->return_username()}', activity_page='deleted-item', office_id='".$session->userdata(OFF_SESSION_ID)."', activity_id='$Id', activity_details='$Id', activity_description='{ADMIN_FULLNAME} deleted the $Type with name ".$directory->item_by_id('item_title', $Id).".'");
					
					// CONFIRM WHETHER THE ITEM IS A FILE OR FOLDER
					IF($Type == 'FOLDER') {
						// GET ALL FILES ATTACHED TO THIS FOLDER RECURSIVELY
						FOREACH($directory->list_attached_files($Id) AS $QueryList) {
							IF($QueryList["item_type"] == "FOLDER") {
								// RUN ANOTHER SUB QUERY FOR THE FOLDER
								FOREACH($directory->list_attached_files($QueryList["id"]) AS $QueryList2) {
									IF($QueryList2["item_type"] == "FOLDER") {
										FOREACH($directory->list_attached_files($QueryList2["id"]) AS $QueryList3) {
											// DELETE THE FILE RECORD FROM THE DATABASE
											@UNLINK(config_item('upload_path').$QueryList3["item_unique_id"]);
											// DELETE THE QUERY SET FROM THE DATABASE
											$DB->just_exec("DELETE FROM _item_listing WHERE id='{$QueryList3["id"]}' AND user_id='$UserId'");
										}
										// FINALLY DELETE THE MAIN QUERY FROM THE DATABASE
										$DB->just_exec("DELETE FROM _item_listing WHERE id='{$QueryList2["id"]}' AND user_id='$UserId'");
									} ELSE {
										// DELETE THE FILE RECORD FROM THE DATABASE
										@UNLINK(config_item('upload_path').$QueryList2["item_unique_id"]);
										// DELETE THE QUERY SET FROM THE DATABASE
										$DB->just_exec("DELETE FROM _item_listing WHERE id='{$QueryList2["id"]}' AND user_id='$UserId'");
									}
								}
								// DELETE THE QUERY SET FROM THE DATABASE
								$DB->just_exec("DELETE FROM _item_listing WHERE id='{$QueryList["id"]}'");
							} ELSE {
								// DELETE THE FILE RECORD FROM THE DATABASE
								@UNLINK(config_item('upload_path').$QueryList["item_unique_id"]);
								// DELETE THE QUERY SET FROM THE DATABASE
								$DB->just_exec("DELETE FROM _item_listing WHERE id='{$QueryList["id"]}'");
							}
						}
						$DB->just_exec("DELETE FROM _item_listing WHERE id='$Id' AND user_id='$UserId'");
					} ELSE {
						// ELSE IF ITS A FILE THEN
						// DELETE THE FILE FROM THE SYSTEM
						UNLINK(config_item('upload_path').$directory->item_by_id('item_unique_id', $Id));
					}
					// FINALLY DELETE THE MAIN QUERY FROM THE DATABASE
					$DB->just_exec("DELETE FROM _item_listing WHERE id='$Id' AND user_id='$UserId'");
					// PRINT THE SUCCESS MESSAGE
					PRINT 'success';
				} ELSE {
					PRINT 'error';
				}
			}
			
		}
	}
	
	// CONFIRM THAT THE USER WANTS TO RESTORE A DELETED ITEM
	IF(($SITEURL[1] == "restoreItem") AND ISSET($_POST["Action"]) AND ISSET($_POST["Type"])) {
		$Id = xss_clean($_POST["Id"]);
		$Type = xss_clean($_POST["Type"]);
		$Action = xss_clean($_POST["Action"]);
		$UserId = xss_clean($session->userdata(UID_SESS_ID));
		
		// CONFIRM THE ITEM TYPE
		IF($Action == "restoreItem") {
			// CONFIRM THAT THE USER WANTS TO DELETE ONLY A FILE OR FOLDER AND NOT A SHARED ITEM
			IF(IN_ARRAY($Type, ARRAY('FILE', 'FOLDER'))) {
				IF($DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='$Id' AND user_id='$UserId'")) {
					// CONFIRM WHETHER THE ITEM IS A FILE OR FOLDER
					IF($Type == 'FOLDER') {
						// GET ALL FILES ATTACHED TO THIS FOLDER RECURSIVELY
						FOREACH($directory->list_attached_files($Id) AS $QueryList) {
							IF($QueryList["item_type"] == "FOLDER") {
								// RUN ANOTHER SUB QUERY FOR THE FOLDER
								FOREACH($directory->list_attached_files($QueryList["id"]) AS $QueryList2) {
									IF($QueryList2["item_type"] == "FOLDER") {
										FOREACH($directory->list_attached_files($QueryList2["id"]) AS $QueryList3) {
											// UPDATE THE FILE STATUS
											$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='{$QueryList3["id"]}' AND user_id='$UserId'");
										}
										// FINALLY DELETE THE MAIN QUERY FROM THE DATABASE
										$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='{$QueryList2["id"]}' AND user_id='$UserId'");
									} ELSE {
										// DELETE THE QUERY SET FROM THE DATABASE
										$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='{$QueryList2["id"]}' AND user_id='$UserId'");
									}
								}
								// DELETE THE QUERY SET FROM THE DATABASE
								$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='{$QueryList["id"]}'");
							} ELSE {
								// DELETE THE QUERY SET FROM THE DATABASE
								$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='{$QueryList["id"]}'");
							}
						}
						$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='$Id' AND user_id='$UserId'");
					}
					// FINALLY DELETE THE MAIN QUERY FROM THE DATABASE
					$DB->just_exec("UPDATE _item_listing SET item_status='1' WHERE id='$Id' AND user_id='$UserId'");
					// PRINT THE SUCCESS MESSAGE
					PRINT 'success';
				} ELSE {
					PRINT 'error';
				}
			}
			
		}
	}
	
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
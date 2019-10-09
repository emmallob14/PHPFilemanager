<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		// ADD USER TO THE SHARE LIST BEFORE PROCESSING
		IF(($SITEURL[1] == "doAdd") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "doAddUser")) {
			IF(($SITEURL[2] == "doUser") AND ISSET($_POST["Uid"])) {
				#get the items and their values
				$Uid = (INT)xss_clean($_POST["Uid"]);
				$office_id = $session->userdata(OFF_SESSION_ID);
				$user_name = xss_clean($_POST["UName"]);
				
				#start a new session
				IF (!ISSET($_SESSION)) {
					session_start();
				}

				IF (!ISSET($_SESSION["shareList"]) || COUNT($_SESSION["shareList"]) < 1) {
					$_SESSION["shareList"] = ARRAY();
					// RUN IF THE SHARE LIST IS EMPTY OR NOT SET
					$_SESSION["shareList"][] = $Uid;
					// PRINT SUCCESS MESSAGE
					PRINT "$user_name was successfully added to the list.";
				} ELSE {
					FOREACH($_SESSION["shareList"] AS $key=>$value) {
						IF($value == $Uid) {
							PRINT "$user_name has already been added to the list.";
							BREAK;
						} ELSE {
							$_SESSION["shareList"][] = $Uid;
							PRINT "$user_name was successfully added to the list.";
							BREAK;
						}
					}
				}
				
			}			
		}
		
		// LIST USERS THAT HAVE BEEN ADDED TO THE SHARED LIST SESSION
		IF(($SITEURL[1] == "doList") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "listUsers")) {
			IF(($SITEURL[2] == "listUsers")) {
				#get the items and their values
				$_share_Users = $session->userdata("shareList");
				$users = load_class('users', 'models');
				
				IF (!ISSET($_SESSION["shareList"]) || COUNT($_SESSION["shareList"]) < 1) {
					// PRINT ERROR MESSAGE
					PRINT "Sorry! You have not yet added any users to the list.";
				} ELSE {
					?>
					<div class="widget-content">
					<div class="todo">
						<ul>
						<?PHP FOREACH($_SESSION["shareList"] AS $key=>$value) { ?>
							<?php $fullname = $users->get_details_by_id($value)->funame; ?>
							<li class="clearfix">
							  <div class="txt"> <span class="by label">Added</span> <?php print $fullname; ?> </div>
							  <div class="pull-right"><a class="tip remove_user btn btn-danger" href="javascript:remove_user('<?php print $value; ?>', '<?php print $fullname; ?>');" title="Remove User"><i class="icon-trash"></i> REMOVE USER</a></div>
							</li>
						<?PHP  } ?>
						</ul>
					</div>
					</div>
				  <?PHP
				}
				
			}			
		}
		
		
		// COUNT THE NUMBER OF USERS THAT HAVE BEEN ADDED TO THE LIST
		IF(($SITEURL[1] == "doList") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "countUsers")) {
			IF(($SITEURL[2] == "countUsers")) {
				#get the items and their values
				$_share_Users = $session->userdata("shareList");
				
				IF (!ISSET($_SESSION["shareList"]) || COUNT($_SESSION["shareList"]) < 1) {
					// PRINT ERROR MESSAGE
					PRINT "Sorry! You have not yet added any users to the list.";
				} ELSE {
					//PRINT THE NUMBER OF USERS ADDED TO THE LIST
					PRINT "<div class='alert alert-primary'>Are you sure you want to send the file to the ".COUNT($_SESSION["shareList"])." users selected?</div>";
				}
				
			}			
		}
		
		// REMOVE USER FROM THE SHARE LIST
		IF(($SITEURL[1] == "doRemove") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "doRemove")) {
			IF(($SITEURL[2] == "execRemove") AND $session->userdata("shareList")) {
				#get the items and their values
				$_share_Users = $session->userdata("shareList");
				$Uid = (INT)xss_clean($_POST["Uid"]);
				$user_name = xss_clean($_POST["UName"]);
				
				FOREACH($_SESSION["shareList"] AS $key=>$value) {
					IF($value == $Uid) {
						UNSET($_SESSION["shareList"][$key]);
						PRINT "$user_name was successfully removed from the list.";
						BREAK;
					}
				}
			}
		}
		
		// REMOVE USER FROM THE SHARE LIST
		IF(($SITEURL[1] == "doShare") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "shareFile")) {
			IF($session->userdata("shareList") AND ISSET($_POST["share_Length"])) {
				#get the items and their values
				$user_id = $session->userdata(UID_SESS_ID);
				$_users = $session->userdata("shareList");
				$_users_list = "/$user_id";
				foreach($_users as $user) {
					$_users_list .= "/$user";
				}
				$_users_list = $_users_list."/";
				
				// get the user office id from the session 
				$office_id = $session->userdata(OFF_SESSION_ID);
				
				// confirm if a single item has been targetted to be shared or multiple files 
				IF($session->userdata("shareItemId")) {
					$_file_id = $session->userdata('shareItemId');
					$_file_id = $_file_id;
					$_file_type = $session->userdata('shareItemType');
					// get the item type to be shared either a file or a folder
					IF($_file_type == "FILE") {
						$_file_type = "Shared_File";
					} ELSEIF($_file_type == "FOLDER") {
						$_file_type = "Shared_Folder";
					}
					$shared_many = "FALSE";
				} ELSEIF($session->userdata("shareItemList")) {
					$_file_id = $session->userdata("shareItemList");
					$_file_id = "ArrayList";
					$_file_type = "Shared_File";
					$shared_many = "TRUE";
				}
				
				// get other variables for the insertion of the data
				$_file_length =  (INT)($_POST["share_Length"]);
				$_file_comment =  NL2BR(xss_clean($_POST["share_Comments"]));
				$_file_slug = random_string('alnum', mt_rand(15, 30));
				$_file_replace = (xss_clean($_POST["replace_permission"]));
				$_file_download = (xss_clean($_POST["download_permission"]));
				$_file_download_link = random_string('alnum', mt_rand(15, 30));
				
				
				// insert the information into the database
				$DB->execute("INSERT INTO _shared_listing SET shared_many='$shared_many', shared_slug='$_file_slug', shared_by='$user_id', shared_by_office='$office_id', shared_with='$_users_list', shared_item_id='$_file_id', shared_type='$_file_type', shared_date='".time()."', shared_expiry='$_file_length', shared_comments='$_file_comment', replace_file='$_file_replace', download_file='$_file_download', download_link='$_file_download_link'");
				
				// confirm if a single item is to be inserted into the database
				IF($session->userdata("shareItemList")) {
					// insert the list of items in the session into the list table
					FOREACH($_SESSION["shareItemList"] AS $key=>$value) {
						$DB->execute("INSERT INTO _shared_listing_detail SET shared_slug='$_file_slug', shared_item_slug='$value', shared_item_id='{$directory->item_by_id('id', $value)}'");
					}
				}
				
				// update the user activity logs
				$DB->execute("INSERT INTO _activity_logs SET full_date=now(), date_recorded=now(), admin_id='".$admin_user->return_username()."', activity_page='shared-item', activity_id='$_file_id', activity_details='$_users_list', office_id='".$session->userdata(OFF_SESSION_ID)."', activity_description='{ADMIN_FULLNAME} shared the file {FILE_NAME} with {SHARED_USERS}'");
				
				// unset the session 
				@$session->unset_userdata("shareList");
				@$session->unset_userdata("shareItemType");
				// unset the share item list session
				@$session->unset_userdata("shareItemList");
				// alert a success message to the user
				PRINT "success";
				
			}
		}
		
		// ADD ITEMS TO THE SHARE LIST BEFORE PROCESSING
		IF(($SITEURL[1] == "doAdd") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "doAddItem")) {
			IF(($SITEURL[2] == "addItem") AND ISSET($_POST["Uid"])) {
				#get the items and their values
				$Uid = xss_clean($_POST["Uid"]);
				$Item_Name = xss_clean($_POST["Item_Name"]);
				$office_id = $session->userdata(OFF_SESSION_ID);
				
				#start a new session
				IF (!ISSET($_SESSION)) {
					session_start();
				}

				IF (!ISSET($_SESSION["shareItemList"]) || COUNT($_SESSION["shareItemList"]) < 1) {
					$_SESSION["shareItemList"] = ARRAY();
					// RUN IF THE SHARE LIST IS EMPTY OR NOT SET
					$_SESSION["shareItemList"][] = $Uid;
					// PRINT SUCCESS MESSAGE
					PRINT "$Item_Name was successfully added to the list.";
				} ELSE {
					FOREACH($_SESSION["shareItemList"] AS $key=>$value) {
						IF($value == $Uid) {
							PRINT "$Item_Name has already been added to the share items list.";
							BREAK;
						} ELSE {
							$_SESSION["shareItemList"][] = $Uid;
							PRINT "$Item_Name was successfully added to the share items list.";
							BREAK;
						}
					}
				}
				
			}			
		}
		
		// LIST ITEMS THAT HAVE BEEN ADDED TO THE SHARED LIST SESSION
		IF(($SITEURL[1] == "doList") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "listItems")) {
			IF(($SITEURL[2] == "listItems")) {
				#get the items and their values
				$_share_Users = $session->userdata("shareItemList");
				$users = load_class('users', 'models');
				
				IF (!($session->userdata('shareItemList')) || COUNT($session->userdata('shareItemList')) < 1) {
					// PRINT ERROR MESSAGE
					PRINT "Sorry! You have not yet added any items to the share list.";
				} ELSE {
					PRINT COUNT($session->userdata('shareItemList'))." Items Added To Share List";
				}				
			}
		}
		
		// EMPTY THE ITEMS SHARING CART SESSION
		IF(($SITEURL[1] == "doEmpty") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "removeItems")) {
			IF(($SITEURL[2] == "emptySession") AND ISSET($_POST["item_id"])) {
				#get the items and their values
				IF($_POST["item_id"] == '000') {
					$session->unset_userdata("shareItemList");
				} ELSE {
					FOREACH($_SESSION["shareItemList"] AS $key=>$value) {
						IF($value == $_POST["item_id"]) {
							UNSET($_SESSION["shareItemList"][$key]);
							BREAK;
						}
					}
					
					(COUNT($session->userdata('shareItemList')) < 1) ? $session->unset_userdata("shareItemList") : null;
				}
			}
		}
		
		
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
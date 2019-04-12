<?php
#initial 
global $DB, $functions, $libs;
if($admin_user->logged_InControlled() == true) { 

	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		IF(($SITEURL[1] == "doNotification") AND ISSET($_POST["remove_notice"])) {
			#check if the user is logged in
			if(isset($_POST["type"]) and isset($_POST["item_id"])) {
				#get the items and their values
				$type = xss_clean($_POST["type"]);
				$item_id = xss_clean($_POST["item_id"]);
				
				#check if the type is the login notification
				if($type == "login") {
					#update the user login notification
					$DB->execute("update _admin set last_login_attempts='1' where username='$item_id'");
					#add up the the users activity history
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='$item_id', activity_page='login-notice', activity_id='$item_id', activity_details='$item_id', activity_description='A login attempt notification that was sent to you was removed'");
				}
				if($type == "pass_request") {
					#update the user login notification
					$DB->execute("delete from _admin_request_change where username='$item_id'");
					#add up the the users activity history
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='$item_id', activity_page='password-change-notice', activity_id='$item_id', activity_details='$item_id', activity_description='A password change notification that was sent to you was removed.'");
				}
				#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
				if($type == "multiple_attempt") {
					#update the user login notification
					$DB->execute("update _login_attempt set lastlogin=now(), attempts='0' where username='$item_id'");
				}
			}
		}
	
	
		IF(($SITEURL[1] == "doSearch") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "searchUser")) {
			#get the items and their values
			$user_name = xss_clean($_POST["Name"]);
			$user_id = $session->userdata(":lifeID");
			$office_id = $session->userdata("officeID");
			
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
		}
		
		// ADD USER TO THE SHARE LIST BEFORE PROCESSING
		IF(($SITEURL[1] == "doAdd") AND ISSET($_POST["Action"]) AND ($_POST["Action"] == "doAddUser")) {
			IF(($SITEURL[2] == "doUser") AND ISSET($_POST["Uid"])) {
				#get the items and their values
				$Uid = (INT)xss_clean($_POST["Uid"]);
				$office_id = $session->userdata("officeID");
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
				$user_id = $session->userdata(":lifeID");
				$_users = $session->userdata("shareList");
				$_users_list = "/$user_id";
				foreach($_users as $user) {
					$_users_list .= "/$user";
				}
				$_users_list = $_users_list."/";
				
				$office_id = $session->userdata("officeID");
				$_file_id = $session->userdata('shareItemId');
				$_file_type = $session->userdata('shareItemType');
				
				IF($_file_type == "FILE") {
					$_file_type = "Shared_File";
				} ELSEIF($_file_type == "FOLDER") {
					$_file_type = "Shared_Folder";
				}
				$_file_length =  (INT)($_POST["share_Length"]);
				$_file_comment =  NL2BR(xss_clean($_POST["share_Comments"]));
				$_file_slug = random_string('alnum', mt_rand(10, 25));
				$_file_permission = (xss_clean($_POST["replace_permission"]));
				
				// insert the information into the database
				$DB->query("INSERT INTO _shared_listing SET shared_slug='$_file_slug', shared_by='$user_id', shared_by_office='$office_id', shared_with='$_users_list', shared_item_id='$_file_id', shared_type='$_file_type', shared_date='".time()."', shared_expiry='$_file_length', shared_comments='$_file_comment', replace_file='$_file_permission'");
				
				# update the user activity logs
				$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='shared-item', activity_id='$_file_id', activity_details='$_users_list', activity_description='".$session->userdata(":lifeFullname")." shared the file {FILE_NAME} with {SHARED_USERS}'");
		
				// alert a success message to the user
				PRINT "success";
				
				// unset the session 
				$session->unset_userdata("shareList");
				$session->unset_userdata("shareItemType");
				
			}
		}
		
	}
}
?>
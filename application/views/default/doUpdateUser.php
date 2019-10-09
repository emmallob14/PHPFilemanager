<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		$encrypt = load_class('encrypt', 'libraries');
		load_helpers(ARRAY('string_helper','email_helper','url_helper'));

		#REQUEST A PASSWORD CHANGE
		IF(($SITEURL[1] == "doUpdate") AND ISSET($_POST["admin_role"]) AND $admin_user->confirm_admin_user()) {
			
			#confirm that the form has been submitted
			IF(ISSET($_POST["firstname"]) AND !EMPTY($_POST["firstname"])) {
				#initializing
				$user_available = TRUE;
				$available = TRUE;
				#assign variables 
				$lastname = xss_clean($_POST["lastname"]);
				$firstname = xss_clean($_POST["firstname"]);
				$email = xss_clean($_POST["email"]);
				$oldemail = xss_clean($_POST["oldemail"]);
				$office_id = (INT)xss_clean($_POST["office_id"]);
				$user_id = $session->userdata("user_id");
				$admin_access = $session->userdata("admin_access");
				$admin_logged = UCFIRST($admin_user->return_username());
				
				# validate the user information
				IF(STRLEN($firstname) < 2) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user firstname.</div>";
				} ELSEIF(STRLEN($lastname) < 2) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user lastname.</div>";
				} ELSEIF(STRLEN($email) < 5) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user email.</div>";
				} ELSEIF(!valid_email($email)) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter a valid email.</div>";
				} ELSE {					
					IF(ISSET($_POST["admin_role"]))
						$admin_role = xss_clean($_POST["admin_role"]);
					ELSE
						$admin_role = (INT)$session->userdata(ROLE_SESS_ID);
					
					$new_user_id = UCFIRST(xss_clean($_POST["user_id"]));
					$old_user_id = UCFIRST(xss_clean($user_id));
					
					#mechanism for the user level
					IF($admin_role == 1001) {
						$level = "Developer";
					} ELSEIF($admin_role == 1) {
						$level = "Administrator";
					} ELSEIF($admin_role == 2) {
						$level = "Content Moderator";
					}
									
					#confirm that the username does not already exists in the database 
					IF($new_user_id != $old_user_id) {
						#confirm that the username is available
						IF(COUNT($DB->query("SELECT * FROM _admin WHERE username='$new_user_id' AND admin_deleted='0'")) > 0) {
							PRINT "<div class='alert alert-danger'>Sorry! The username <strong>($new_user_id)</strong> is not available.</div>";
							$user_available = FALSE;
						} ELSE {
							$user_available = TRUE;
						}

					}
					
					#confirm that the username is available
					IF($oldemail != $email) {
						IF(COUNT($DB->query("SELECT * FROM _admin WHERE email='$email' AND admin_deleted='0'")) > 0) {
							PRINT "<div class='alert alert-danger'>Sorry! The email <strong>($email)</strong> is not available.</div>";
							$available = FALSE;
						} ELSE {
							$available = TRUE;
						}
					}
										
					#confirm if the username is available
					IF($available == TRUE) {
						#still maintain the username and cause it to be unchanged
						IF($admin_access == FALSE) {
							$new_user_id = $old_user_id;
						}
						
						#update the information 
						$DB->just_exec("update _admin set firstname='$firstname', lastname='$lastname', fullname='$firstname $lastname', email='$email', level='$level', role='$admin_role' where username='$old_user_id'");
						
						#update the username information 
						IF($user_available == TRUE) {
							$DB->just_exec("update _admin set username='$new_user_id' where username='$old_user_id'");
						}
						
						#update all other information that relates to this administrator 
						#if the username will indeed change as issued by the administrator
						IF(($admin_access == TRUE) AND ($new_user_id != $old_user_id) AND ($user_available == TRUE)) {
							#change the administrator activity logs
							$DB->just_exec("update _activity_logs set admin_id='$new_user_id' where admin_id='$old_user_id'");
							$DB->just_exec("update _activity_logs set activity_details='$new_user_id' where activity_details='$old_user_id'");
							$DB->just_exec("update _admin_log_history set username='$new_user_id' where username='$old_user_id'");
							$DB->just_exec("update _admin_request_change set username='$new_user_id' where username='$old_user_id'");
							$DB->just_exec("update _item_listing set item_users='$new_user_id' where item_users='$old_user_id'");
						}
								
						#update the current session of the user
						IF(($admin_user->return_username() == $old_user_id)) {
							$session->set_userdata(ROLE_SESS_ID, $admin_role);
							$session->set_userdata(UNAME_SESS_ID, $new_user_id);
							$session->set_userdata(USER_EMAIL, $email);
							$session->set_userdata(USER_FULLNAME, $firstname." ".$lastname);
						}
						
						PRINT "<div class='alert alert-success'>User information successfully updated.</div>.";
						
						#insert the new information into the activity logs table
						$DB->just_exec("insert into _activity_logs set office_id='$office_id', date_recorded=now(), admin_id='$admin_logged', activity_page='admin', activity_id='$new_user_id', activity_details='$new_user_id', activity_description='Admin details of $firstname $lastname has been updated.'");
						
						IF($new_user_id != $old_user_id) {
							redirect( $config->base_url()."Profile/$new_user_id", 'refresh:2000');
						}
						
					}
				
				}
				
			}
			
			
		}
	
	}  ELSE {
		show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404');
	}
} ELSE {
	show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404');
}
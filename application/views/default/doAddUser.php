<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		$encrypt = load_class('encrypt', 'libraries');
		load_helpers(ARRAY('string_helper','email_helper','url_helper'));
		
		$password_ErrorMessage = "<div class='alert alert-danger'>Sorry! Please use a stronger password. <br><strong>Password Format</strong><br><ul>
			<li style='padding-left:15px;'>At least 1 Uppercase</li>
			<li style='padding-left:15px;'>At least 1 Lowercase</li>
			<li style='padding-left:15px;'>At least 1 Numeric</li>
			<li style='padding-left:15px;'>At least 1 Special Character</li></ul></div>";
			
		#REQUEST A PASSWORD CHANGE
		IF(($SITEURL[1] == "doAdd") AND ISSET($_POST["admin_role"]) AND $admin_user->confirm_admin_user()) {
			#confirm that the form has been submitted
			IF(ISSET($_POST["firstname"])) {
				#initializing
				$available = TRUE;
				#assign variables 
				$lastname = (xss_clean($_POST["lastname"]));
				$email = (clean_email($_POST["email"]));
				$firstname = (xss_clean($_POST["firstname"]));
				$password = (xss_clean($_POST["password"]));
				$admin_role = xss_clean($_POST["admin_role"]);
				$username = xss_clean($_POST["username"]);
				$office_id = (INT)$session->userdata("officeID");
				
				# validate the user information
				IF(STRLEN($firstname) < 2) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user firstname.</div>";
				} ELSEIF(STRLEN($lastname) < 2) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user lastname.</div>";
				} ELSEIF(STRLEN($email) < 5) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user email.</div>";
				} ELSEIF(!valid_email($email)) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter a valid email.</div>";
				} ELSEIF(STRLEN($username) < 3) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the user username. Should be at least 3 characters long.</div>";
				} ELSEIF(!passwordTest($password)) {
					PRINT $password_ErrorMessage;
				} ELSE {
					#mechanism for the user level
					IF($admin_role == 1001) {
						$level = "Developer";
					} ELSEIF($admin_role == 1) {
						$level = "Administrator";
					} ELSEIF($admin_role == 2) {
						$level = "Content Moderator";
					}
					
					#confirm that the username is available
					IF(COUNT($DB->query("select * from _admin where username='$username' AND admin_deleted='0'")) > 0) {
						PRINT "<div class='alert alert-danger'>Sorry! The username <strong>($username)</strong> is not available.</div>";
						$available = FALSE;
					} ELSE {
						$available = TRUE;
					}
					
					#confirm that the username is available
					IF((STRLEN($email) > 2) AND (COUNT($DB->query("select * from _admin where email='$email' and admin_deleted='0'"))) > 0) {
						PRINT "<div class='alert alert-danger'>Sorry! The email <strong>($email)</strong> is not available.</div>";
						$available = FALSE;
					}
					
					#confirm if the username is available
					IF($available == TRUE) {
						#update the information 
						$DB->just_exec("insert into _admin set office_id='$office_id', firstname='$firstname', lastname='$lastname', fullname='$firstname $lastname', username='$username', email='$email', level='$level', role='$admin_role',date_added=now(),added_by='".$admin_user->return_username()."'");
						
						#update the current session of the user 
						PRINT "<div class='alert alert-success'>Admin information successfully inserted.</div>.";
						
						#confirm that the user has entered a new password 
						IF(STRLEN($password) > 6) {
							#encrypt the user password 
							$npassword = $encrypt->password_hash($password);
							#update the information 
							$DB->just_exec("update _admin set password='$npassword' where username='$username'");
							#log the user out
						}
						
						#insert the user activity logs 
						$DB->just_exec("insert into _activity_logs set date_recorded=now(), admin_id='".$admin_user->return_username()."', activity_page='admin', activity_id='$username', activity_details='$username', activity_description='Admin details of $firstname $lastname has been inserted into the database.'");
						
						#send an email to the new user 
						$message = "Hello, $firstname $lastname,<br><br>";
						$message .= "An acCOUNT has been created at <strong>".config_item('site_name')."</strong> on your behalf by <strong>".$admin_user->return_username()."</strong>.<br><br>";
						$message .= "The details of your user acCOUNT are as follows:<br><br>";
						$message .= "<strong>USERNAME:</strong> $username<br>";
						$message .= "<strong>PASSWORD:</strong> $password<br><br>";
						$message .= "Please not that you will be prompted to change this password after logging in for the first time.<br><br>";
						$message .= "<a href='".SITE_URL."/Login?User=$username'>Click Here</a> to login to your acCOUNT.<br><br>Thank you.";
								
						send_email(
							$email, "[".config_item('site_name')."] Admin AcCOUNT", 
							$message, config_item('site_name'), config_item('site_email'), 
							NULL, 'default', $username
						);
						
						#update the current session of the user 
						PRINT "<div class='alert alert-success'>$password Admin information successfully inserted.</div>.<script>$(\"#doAddUser\")[0].reset();</script>";
						
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
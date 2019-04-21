<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB;

#confirm that the user has parsed this value
IF(ISSET($SITEURL[1])) {
	$encrypt = load_class('encrypt', 'libraries');
	$user_agent = load_class('user_agent', 'libraries');
	load_helpers(ARRAY('string_helper','email_helper','url_helper'));
	
	#FUNCTION TO PROCESS USER LOGIN 
	function confirm_login($username, $password, $href) {
		
		GLOBAL $DB, $session, $encrypt, $user_agent, $config;
		
		IF(ISSET($username) and ISSET($password)) {
			#clean the data parsed
			$username = xss_clean($username);
			$password = xss_clean($password);
			#validate the email 
			IF(empty($username)) {
				PRINT "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Please enter a Valid Username.</div>";
				#validate password 
			} ELSE {
				#query the database for the said user name or password 
				$confirm_username = $DB->query("select * from _admin where username='$username' and activated='1' and status='1'");
				#COUNT the number of rows found 
				IF(COUNT($confirm_username) != 1) {
					#add login attempts
					add_login_attempt($username);
					#PRINT error message 
					PRINT "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Invalid Username/Password.</div>";
				} ELSE {
					
					#check the number of login attempts 
					$log_attempt = confirm_login_attempt($username);
					
					IF($log_attempt == 0) {
						#using the FOREACH loop to get the results 
						FOREACH($confirm_username as $results) {
							
							#confirm that the username is validated 
							IF(($results["activated"] == 1) AND ($results["status"] == 1)) {
								#confirm that the user password is indeed correct
								IF($encrypt->password_verify($password, $results["password"])) {
									
									#get the last login attempts 
									$last_login_attempts = last_login_attempts($username);
									$last_login_attempts_time = last_login_attempts_time($username);
									#clear login attempts 
									clear_login_attempt($username);
									#set some sessions for the user
									$session->set_userdata(
										ARRAY(
											"officeID" => $results["office_id"],
											":logedUsername" => $username,
											":lifeID" => $results["id"],
											":lifeEmail" => $results["email"],
											":lifeLockedOut" => false,
											":lifeAdminRole" => $results["role"],
											":lifeSESS" => random_string('alnum', 45)
										)
									);
									
									$session->set_userdata(':lifeLockedOut', false);
									
									IF($results["role"] == 1001) {
										$session->set_userdata(":life_Supper_Admin", true);
										$session->set_userdata(":lifeAdminRole", 1001);
									}
									
									#update the table 
									$ip = $user_agent->ip_address();
									$br = $user_agent->browser()." ".$user_agent->platform();
																							
									$DB->query("update _admin set lastaccess=now(), log_ipaddress='$ip', log_browser='$br', log_session='".$session->userdata(":lifeSESS")."', last_login_attempts='$last_login_attempts', last_login_attempts_time='$last_login_attempts_time' where id='{$results["id"]}'");
									
									$DB->query("insert into _admin_log_history set username='$username', lastlogin=now(), log_ipaddress='$ip', log_browser='$br', office_id='".$session->userdata("officeID")."', log_platform='".$user_agent->agent_string()."'");
									
									$ROOT_DIR = 'assets/manager';
									// CREATE ROOT DIRECTORY
									IF(!IS_DIR($ROOT_DIR)) {
										MKDIR($ROOT_DIR, 0755);
									}

									$USER_ROOT = $ROOT_DIR.'/'.$session->userdata(":logedUsername");
									// CREATE TABLE FOR USER
									IF(!IS_DIR($USER_ROOT)) {
										MKDIR($USER_ROOT, 0755);
									}

									$session->set_userdata("userDir_Root", $USER_ROOT);
									
									PRINT "<script>$(\"#submitButton\").attr(\"disabled\", true);</script>";
									#redirect the user
									IF($href) {
										redirect( $href, 'refresh:1000');
									} ELSE {
										redirect( $config->base_url() . 'Dashboard', 'refresh:1000');
									}
								} ELSE {
									#add login attempts
									add_login_attempt($username);
									#PRINT error message
									PRINT "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Invalid Username/Password.</div>";
								}
							} ELSE {
								#add login attempts
								add_login_attempt($username);
								#PRINT error message 
								PRINT "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Invalid Username/Password.</div>";
								PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
							}
							
						}
					} ELSE {
						PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! You have been locked out for many trials.</div>";
						PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
					}
				}
			}
		}
	}

	function last_login_attempts($username) {
		
		GLOBAL $DB, $functions;
		
		// increase number of attempts
		// set last login attempt time if required    
		$sql = "SELECT * FROM _login_attempt WHERE `ip` = '".ip_address()."' AND `username`='$username'"; 
			
		$result = $DB->query($sql);
			
		IF(COUNT($result) > 0) {
			FOREACH($result AS $data) {
				$attempts = $data["attempts"];

				RETURN $attempts;
			}
		} ELSE {
			RETURN 1;
		}
	}

	function last_login_attempts_time($username) {
		
		GLOBAL $DB, $functions;
		
		// increase number of attempts
		// set last login attempt time if required    
		$sql = "SELECT * FROM _login_attempt WHERE `ip` = '".ip_address()."' AND `username`='$username'"; 
			
		$result = $DB->query($sql);
			
		IF(COUNT($result) > 0) {
			FOREACH($result AS $data) {
				$attempts_time = $data["lastlogin"];

				RETURN $attempts_time;
			}
		} ELSE {
			RETURN 1;
		}
	}

	function add_login_attempt($username) {
		
		GLOBAL $DB, $functions;
		
		// increase number of attempts
		// set last login attempt time if required    
		$sql = "SELECT * FROM _login_attempt WHERE `ip` = '".ip_address()."' AND `username`='$username'"; 
			
		$result = $DB->query($sql);
			
		IF(COUNT($result) > 0) {
			FOREACH($result AS $data) {
				$attempts = $data["attempts"]+1;

				IF($attempts == ATTEMPTS_NUMBER) {
					$q = "UPDATE _login_attempt SET attempts='$attempts', lastlogin=NOW() WHERE `ip` = '".ip_address()."' AND `username`='$username'";
					$result = $DB->execute($q);
				} ELSE {
					$q = "UPDATE _login_attempt SET attempts='$attempts' WHERE `ip` = '".ip_address()."' AND `username`='$username'";
					$result = $DB->execute($q);
				}
			}
		} ELSE {
			$q = "INSERT INTO _login_attempt (username,ip,attempts,lastlogin) values ('$username','".ip_address()."',1,NOW())";
			$result = $DB->execute($q);
		}
	}

	function clear_login_attempt($username) {
			
			GLOBAL $DB, $functions;
			
			$q = "UPDATE _login_attempt SET attempts = '0', lastlogin=now() WHERE `ip` = '".ip_address()."' AND `username`='$username'";
			
			RETURN $DB->execute($q);
	}

	function confirm_login_attempt($username) {
		
		GLOBAL $DB;
			
		$sql = "SELECT 
			attempts, 
				(CASE when lastlogin is not NULL and DATE_ADD(lastlogin, INTERVAL ".TIME_PERIOD." MINUTE) 
					> 
				NOW() then 1 ELSE 0 end) as Denied 
			FROM 
				_login_attempt WHERE `username` = '$username' and `ip` = '".ip_address()."'";
		 
		$result = $DB->query($sql);
		
		FOREACH($result AS $data) {
			if ($data["attempts"] >= ATTEMPTS_NUMBER):
				IF($data["Denied"] == 1):
					RETURN 1;
				ELSE:
					clear_login_attempt($username);
					RETURN 0;
				endif;
			endif;
		}
		RETURN 0;
	}
	
	
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doLogin") AND ISSET($_POST["login_user_yea"]) AND ISSET($_POST["username"])) {
		#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
		IF(ISSET($_POST["login_user_yea"])) {
			IF(ISSET($_POST["username"]) AND ISSET($_POST["password"])) {
				#clean the data parsed
				$username = xss_clean($_POST["username"]);
				$password = xss_clean($_POST["password"]);
				$href = NULL;
				IF(ISSET($_POST["href"]))
					$href = xss_clean($_POST["href"]);
				#call the login function 
				confirm_login($username, $password, $href);
			}
		}
	}
	
	#CHECK IF THE USER WANT TO LOGOUT OF THE SYSTEM 
	IF(($SITEURL[1] == "doLogout") AND $session->userdata(":lifeID")) {
		# update the system
		$DB->execute("INSERT INTO _activity_logs SET full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='logout', activity_id='".$session->userdata(":lifeFullname")."', activity_details='".$session->userdata(":lifeUsername")."', activity_description='You have successfully logged out of the system.', office_id='".$session->userdata("officeID")."'");
		// remove the user log session from the table
		$DB->execute("UPDATE _admin SET log_session=NULL WHERE id='".$session->userdata(":lifeID")."'");
		# clean all user session data 
		$session->sess_destroy();
		# redirect the user
		redirect( $config->base_url() . 'Login/doLogout');
		exit(-1);
	}
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doUnlock") AND ISSET($_POST["lock_password"])) {
		IF(ISSET($_POST["unlock_screen"])) {
			IF(ISSET($_POST["lock_password"]) and ($session->userdata(":logedUsername")) and !ISSET($_POST["username"])) {
				#clean the data parsed
				$username = xss_clean($session->userdata(":logedUsername"));
				$password = xss_clean($_POST["lock_password"]);
				#call the login function 
				confirm_login($username, $password);
			}
		}
	}
	
	#CHECK IF THE USER WANT TO GENERATE A NEW RANDOM PASSWORD
	IF(($SITEURL[1] == "doGeratePassword") AND $session->userdata(":logedUsername")) {
		IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "doGeratePassword") {
			PRINT random_string('alnum', mt_rand(8, 12));
		}
	}
	
	#CHECK IF AN ACCOUNT NEEDS TO BE MODIFIED
	IF(($SITEURL[1] == "doModify") AND ISSET($_POST["modify_account"]) AND ISSET($_POST["id"])) {
		IF(ISSET($_POST["modify_account"])) {
			IF(ISSET($_POST["type"]) AND ($session->userdata(":logedUsername")) AND ISSET($_POST["id"])) {
				#clean the data parsed
				$username = xss_clean($session->userdata(":logedUsername"));
				$type = xss_clean($_POST["type"]);
				$id = xss_clean($_POST["id"]);
				$office_id= $session->userdata("officeID");
				
				// confirm that a super admin has been logged in
				IF($session->userdata(":life_Supper_Admin")) {
					$office_content = "";
				} ELSE {
					$office_content = "and office_id='$office_id'";
				}
				#what do you want to do?
				IF($type == "Disable") {
					$DB->query("update _admin set activated='0', status='0' where id='$id' $office_content");
					PRINT "<div style='width:100%' class='alert alert-success'>Admin Account successfully deactivated.</div>";
					PRINT "<script>$('#admin_$id').addClass('alert alert-danger');";
					PRINT "$('.user_status_$id').html('<span class=\"btn btn-danger\"> INACTIVE </span>');</script>";
				}
				IF($type == "Delete") {
					$DB->query("update _admin set admin_deleted='1', status='0' where id='$id' $office_content");
					PRINT "<div style='width:100%' class='alert alert-success'>Admin Account successfully deleted.</div>";
					PRINT "<script>$('#admin_$id').hide();";
					PRINT "$('.user_status_$id').html('<span class=\"btn btn-danger\"> INACTIVE </span>');</script>";
					
				}
				IF($type == "Activate") {
					$DB->query("update _admin set activated='1', status='1' where id='$id' $office_content");
					PRINT "<div style='width:100%' class='alert alert-success'>Admin Account successfully activated.</div>";
					PRINT "<script>$('#admin_$id').removeClass('alert alert-danger');";
					PRINT "$('.user_status_$id').html('<span class=\"btn btn-success\"> ACTIVE </span>');</script>";
				}
			}
		}
	}
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT
	IF(($SITEURL[1] == "doFirstChange") AND ISSET($_POST["change_password_first"])) {
		IF(ISSET($_POST["change_password_first"])) {
			IF(ISSET($_POST["password"]) AND ($session->userdata(":logedUsername")) AND ISSET($_POST["password1"])) {
				#clean the data parsed
				$password1 = xss_clean($_POST["password"]);
				$password2 = xss_clean($_POST["password1"]);
				$username = xss_clean($session->userdata(":logedUsername"));
				IF($password1 != $password2) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords do not match.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSEIF(strlen($password1) < 6) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords should be at least 6 characters.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSEIF($admin_user->compare_password($password1)) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! Your password is too simple. Please change it.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSEIF(strtolower($password1) == strtolower($username)) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! You cannot use your username as password.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSE {
					#encrypt the password 
					$new_pass = $encrypt->password_hash($password1);
					#update the password 
					$DB->query("update _admin set password='$new_pass', changed_password='1' where username='$username' and office_id='".$session->userdata("officeID")."'");
					#set the first user notification
					$DB->query("insert into _activity_logs set full_date=now(), office_id='".$session->userdata("officeID")."', date_recorded=now(), admin_id='$username', activity_page='password', activity_id='$username', activity_details='$username', activity_description='You changed your password from the default.'");
					#reload the page
					redirect( $config->base_url() . 'Dashboard', 'refresh:2000');
				}
			} ELSE {
				PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords cannot be empty.</div>";
				PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
			}
		}
	}
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doChangePassword") AND ISSET($_POST["change_user_password_"])) {
		IF(ISSET($_POST["change_user_password_"])) {
			IF(ISSET($_POST["password1"]) AND ($session->userdata(":logedUsername")) AND ISSET($_POST["password2"])) {
				#clean the data parsed
				$password1 = xss_clean($_POST["password1"]);
				$password2 = xss_clean($_POST["password2"]);
				$admin_id = xss_clean($session->userdata(":logedUsername"));
				$username = xss_clean($admin_id);
				IF($password1 != $password2) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords do not match.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSEIF(strlen($password1) < 6) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords should be at least 6 characters.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSEIF($admin_user->compare_password($password1) == true) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! Your password is too simple. Please change it.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSEIF(strtolower($password1) == strtolower($username)) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! You cannot use your username as password.</div>";
					PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} ELSE {
					#encrypt the password 
					$new_pass = $encrypt->password_hash($password1);
					#update the password 
					$DB->query("update _admin set password='$new_pass', changed_password='1', last_login_attempts='0' where username='$username' and office_id='".$session->userdata("officeID")."'");
					$DB->query("delete from _admin_request_change where username='$username' and office_id='".$session->userdata("officeID")."'");
					#set the first user notification
					$DB->query("insert into _activity_logs set full_date=now(), office_id='".$session->userdata("officeID")."', date_recorded=now(), admin_id='$admin_id', activity_page='password-changed', activity_id='$username', activity_details='$username', activity_description='You have successfully changed your password.'");
					//FORM THE MESSAGE TO BE SENT TO THE USER
					$message = 'Hello '.$username.',<br><br>Your password was successfully changed at '.config_item('site_name');
					$message .= '<br><br><strong>EMAIL:</strong> '.$username;
					$message .= '<br><br><strong>Browser:</strong> '.$user_agent->browser()." ".$user_agent->platform();
					$message .= '<br><strong>IP Address:</strong> '.$user_agent->ip_address();
					$message .= '<br><strong>Server Host:</strong> '.$_SERVER["HTTP_HOST"];
					$message .= "<br><br>Please do Contact <a href='".SITE_URL."/Support'>Support</a> if you did not initiate this Password Change.";
					//send the mail to the administrator
					send_email(
						$session->userdata(":lifeEmail"), "[".config_item('site_name')."] Password Changed", 
						$message, config_item('site_name'), config_item('site_email'), 
						NULL, 'default', $username
					);
					#reload the page
					redirect( $config->base_url() . 'Dashboard', 'refresh:100');
				}
			} ELSE {
				PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords cannot be empty.</div>";
				PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
			}
		}
	}
	
	#REQUEST A PASSWORD CHANGE
	IF(($SITEURL[1] == "doRequestPasswordChange") AND ISSET($_POST["request_password_change"])) {
		IF(ISSET($_POST["request_password_change"])) {
			IF(ISSET($_POST["request_username"]) AND !EMPTY($_POST["request_username"])) {
				#clean the data parsed
				$user_email = FILTER_VAR($_POST["request_username"], FILTER_SANITIZE_EMAIL);
				#confirm that its a valid email address
				IF(!validate_email($user_email)) {
					PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! Please specify a valid email address.</div>";
				} ELSE {
					#confirm that the username is available
					$email_confirm = $DB->query("SELECT * FROM _admin WHERE email='$user_email' and admin_deleted='0'");
					#COUNT the number of rows 
					IF(COUNT($email_confirm) > 0) {
						#confirm that the user has not requested a password already 
						FOREACH($email_confirm AS $request_res) {
							#assign variable 
							$office_id = $request_res["office_id"];
							$fullname = $request_res["fullname"];
							$username = $request_res["username"];
								
							#create the reset password token
							$request_token = random_string('alnum', mt_rand(40, 75));
							
							#set the token expiry time to 6 hours from the moment of request
							$expiry_time = TIME()+(60*60*6);
							
							#update the table 
							$ip = $user_agent->ip_address();
							$br = $user_agent->browser()." ".$user_agent->platform();
							
							#process the form 
							$DB->query("INSERT INTO _admin_request_change SET username='$user_email', office_id='$office_id', request_token='$request_token', user_agent='$br:$ip', expiry_time='$expiry_time'");
							
							#record the activity
							$DB->query("INSERT INTO _activity_logs SET full_date=now(), office_id='$office_id', date_recorded=now(), admin_id='$username', activity_page='password', activity_id='$username', activity_details='$username', activity_description='You requested a change of password.'");
							
							//FORM THE MESSAGE TO BE SENT TO THE USER
							$message = 'Hi '.$fullname.'<br>You have requested to reset your password at '.config_item('site_name');
							$message .= '<br><br>The following are your login details:<br>';
							$message .= '<strong>Email Address:</strong> '.$user_email;
							$message .= '<strong>Username:</strong> '.$username;
							$message .= '<br><br>Before you can reset your password please follow this link.<br><br>';
							$message .= '<a class="alert alert-success" href="'.$config->base_url().'ResetPassword/'.$user_email.'/'.$request_token.'">Click Here to Reset Password</a>';
							$message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
							$message .= $config->base_url().'ResetPassword/'.$user_email.'/'.$request_token;
							
							#send email to the user
							send_email(
								$user_email, "[".config_item('site_name')."] Change Password", 
								$message, config_item('site_name'), config_item('site_email'), 
								NULL, 'default', $username
							);
					
							#record the password change request 
							PRINT "<div class='alert alert-success'>Password change request was successfully parsed. Please check your email for the request token.</div>";
							PRINT "<script>$(\"#request_username\").val(\"\");</script>";
						}
					} ELSE {
						#PRINT error message
						PRINT "<div class='alert alert-danger'>Sorry! The credentials <strong>($username)</strong> does not match our system records.</div>";
					}
				}
			} ELSE {
				PRINT "<div style='width:100%' class='alert alert-danger'>Sorry! Please specify a valid email address.</div>";
				PRINT "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
			}
		}
	}

} ELSE {
	show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404');
}
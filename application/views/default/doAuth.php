<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB;
#confirm that the user has parsed this value
IF(ISSET($SITEURL[1])) {
	$encrypt = load_class('encrypt', 'libraries');
	$user_agent = load_class('user_agent', 'libraries');
	
	#FUNCTION TO PROCESS USER LOGIN 
	function confirm_login($username, $password, $href) {
		
		GLOBAL $DB, $session, $encrypt, $user_agent, $config, $offices;
		
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
											OFF_SESSION_ID => $results["office_id"],
											UNAME_SESS_ID => $username,
											UID_SESS_ID => $results["id"],
											USER_FULLNAME => $results["firstname"]." ".$results["lastname"],
											USER_EMAIL => $results["email"],
											ROLE_SESS_ID => $results["role"],
											MAIN_SESS => random_string('alnum', 45)
										)
									);
									
									$session->set_userdata(LOCKED_OUT, false);
									
									IF($results["role"] == 1001) {
										$session->set_userdata(ROLE_SUPER_ROLE, true);
										$session->set_userdata(ROLE_SESS_ID, 1001);
									}
									
									#update the table 
									$ip = $user_agent->ip_address();
									$br = $user_agent->browser()." ".$user_agent->platform();
																							
									$DB->query("update _admin set lastaccess=now(), log_ipaddress='$ip', log_browser='$br', log_session='".$session->userdata(MAIN_SESS)."', last_login_attempts='$last_login_attempts', last_login_attempts_time='$last_login_attempts_time' where id='{$results["id"]}'");
									
									$DB->query("insert into _admin_log_history set username='$username', lastlogin=now(), log_ipaddress='$ip', log_browser='$br', office_id='".$session->userdata(OFF_SESSION_ID)."', log_platform='".$user_agent->agent_string()."'");
									
									PRINT "<script>$(\"#submitButton\").attr(\"disabled\", true);</script>";
									#redirect the user
									IF($href) {
										redirect( base64_decode($href), 'refresh:1000');
									} ELSE {
										redirect( config_item('manager_dashboard') . 'Dashboard', 'refresh:1000');
									}
								} ELSE {
									#add login attempts
									add_login_attempt($username);
									#PRINT error message
									PRINT "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Invalid Username/Password.ddds</div>";
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
				$username = UCFIRST(xss_clean($_POST["username"]));
				$password = xss_clean($_POST["password"]);
				$href = NULL;
				IF(ISSET($_POST["href"]))
					$href = xss_clean($_POST["href"]);
				#call the login function 
				confirm_login($username, $password, $href);
			}
		}
	}
	
	# CHECK IF THE USER WANT TO SIGN UP FOR A NEW ACCOUNT 
	IF(($SITEURL[1] == "doRegisterAccount") AND ISSET($_POST["register_account"])) {
		# CHECK IF THE USER WANT TO SIGN UP FOR A NEW ACCOUNT 
		IF(ISSET($_POST["admin_username"])) {
			IF(ISSET($_POST["admin_email"]) AND ISSET($_POST["package"])) {
				#clean the data parsed
				$admin_username = UCFIRST(xss_clean($_POST["admin_username"]));
				$package = STRTOLOWER(xss_clean($_POST["package"]));
				$admin_email = xss_clean($_POST["admin_email"]);
				$office_address = xss_clean($_POST["office_address"]);
				$office_contact = xss_clean($_POST["office_contact"]);
				$office_name = xss_clean($_POST["office_name"]);
				
				# validate the information submitted by the user
				IF(!min_length($office_name, 4)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! The Office Name should be at least 4 characters long.</div>";
				} ELSEIF(!min_length($office_contact, 10)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! The Office Contact should be at least 10 characters long.</div>";
				} ELSEIF(!max_length($office_contact, 15)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! The Office Contact should be at most 15 characters long.</div>";
				} ELSEIF(!valid_contact($office_contact)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! Please enter a valid contact number. (+233550107770)</div>";
				} ELSEIF(!min_length($office_address, 10)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! The Office Address cannot be empty.</div>";
				} ELSEIF(!IN_ARRAY($package, ARRAY("standard","silver","golden","platinum"))) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! Please select a package type.</div>";
				} ELSEIF(!min_length($admin_username, 5)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! The Admin Username cannot be empty.</div>";
				} ELSEIF(!valid_email($admin_email)) {
					PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! Please enter a valid Email Address.</div>";
				} ELSE {
					// CONFIRM THAT THE USERNAME DOES NOT ALREADY EXIST
					IF(COUNT($DB->query("SELECT * FROM _admin WHERE username='$admin_username'")) > 0) {
						PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! This Username already exists in our Records.</div>";
					} ELSEIF(COUNT($DB->query("SELECT * FROM _admin WHERE email='$admin_email'")) > 0) {
						PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! This Email Address already exists in our Records.</div>";
					} ELSE {
						// ASSIGN MORE VARIABLES
						$office_slug = random_string('alnum', mt_rand(15, 25));
						$office_disk_space = $offices->office_space('total_upload', UCFIRST($package));
						$office_daily_space = $offices->office_space('daily_upload', UCFIRST($package));
						$office_users_limit = $offices->office_space('users_limit', UCFIRST($package));
						// PROCESS THE FORM AND INSERT THE RECORD
						IF($DB->touch(
							"_offices",
							ARRAY(
								'unique_id'=>$office_slug,
								'account_type'=>$package,
								'office_name'=>$office_name,
								'office_contact'=>$office_contact,
								'office_address'=>$office_address,
								'office_email'=>$admin_email,
								'office_description'=>NULL,
								'disk_space'=>$office_disk_space,
								'daily_upload'=>$office_daily_space,
								'users_limit'=>$office_users_limit,
								'status'=>0
							), NULL, 'INSERT'
						)) {
							// GET THE LAST INSERTED ROW
							$last_row_id = $DB->max_all('id','_offices');
							// INSERT THE ADMIN USER INFORMATION
							$DB->touch(
								"_admin",
								ARRAY(
									'office_id'=>$last_row_id,
									'username'=>$admin_username,
									'email'=>$admin_email,
									'password'=>$encrypt->encode(random_string('alnum', 10)),
									'level'=>'Administrator',
									'role'=>1,
									'lastaccess'=>'now()',
									'date_added'=>'now()',
									'activated'=>0,
									'added_by'=>'CREATE_ROBOT'
								), NULL, 'INSERT'
							);
							// SEND AN EMAIL
							$message = "Hello, $office_name Admin,<br><br>";
							$message .= "Your Account has successfully been created at <strong>".config_item('site_name')."</strong>.<br><br>";
							$message .= "Our Service personnel will get in touch shortly to process the form and continue with the setup process.<br><br>";
							$message .= "You are free to contact the <a href='".SITE_URL."#contactForm'>Support Section</a> for any information or help.";
									
							send_email(
								$admin_email, "[".config_item('site_name')."] Office Account Created", 
								$message, config_item('site_name'), config_item('site_email'), 
								NULL, 'default', $admin_username
							);
							
							// CLEAR THE FORM DATA
							PRINT "<script>$(\"#registerForm\")[0].reset();</script>";
							
							// PRINT SUCCESS MESSAGE
							PRINT "<div style='width:100%' class='alert alert-success alert-md alert-block'>Success! Your form has successfully been processed. Our Service Personnel will get in touch shortly.</div>";
						} ELSE {
							// PRINT ERROR MESSAGE 
							PRINT "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! There was an error while trying to process the form.</div>";
						}
						
					}
				}
			}
		}
	}
	
	#CHECK IF THE USER WANT TO LOGOUT OF THE SYSTEM 
	IF(($SITEURL[1] == "doLogout") AND $session->userdata(UID_SESS_ID)) {
		# update the system
		$DB->execute("INSERT INTO _activity_logs SET full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='logout', activity_id='".$session->userdata(":lifeFullname")."', activity_details='".$session->userdata(":lifeUsername")."', activity_description='You have successfully logged out of the system.', office_id='".$session->userdata(OFF_SESSION_ID)."'");
		// remove the user log session from the table
		$DB->execute("UPDATE _admin SET log_session=NULL WHERE id='".$session->userdata(UID_SESS_ID)."'");
		# clean all user session data 
		$session->sess_destroy();
		# redirect the user
		redirect( config_item('manager_dashboard') . 'Login/doLogout');
		exit(-1);
	}
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doUnlock") AND ISSET($_POST["lock_password"])) {
		IF(ISSET($_POST["unlock_screen"])) {
			IF(ISSET($_POST["lock_password"]) and ($session->userdata(UNAME_SESS_ID)) and !ISSET($_POST["username"])) {
				#clean the data parsed
				$username = xss_clean($session->userdata(UNAME_SESS_ID));
				$password = xss_clean($_POST["lock_password"]);
				#call the login function 
				confirm_login($username, $password, null);
			}
		}
	}
	
	#CHECK IF THE USER WANT TO GENERATE A NEW RANDOM PASSWORD
	IF(($SITEURL[1] == "doGeratePassword") AND $session->userdata(UNAME_SESS_ID)) {
		IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "doGeratePassword") {
			PRINT random_string('alnum', mt_rand(8, 12));
		}
	}
	
	#CHECK IF AN ACCOUNT NEEDS TO BE MODIFIED
	IF(($SITEURL[1] == "doModify") AND ISSET($_POST["modify_account"]) AND ISSET($_POST["id"])) {
		IF(ISSET($_POST["modify_account"])) {
			IF(ISSET($_POST["type"]) AND ($session->userdata(UNAME_SESS_ID)) AND ISSET($_POST["id"])) {
				#clean the data parsed
				$username = xss_clean($session->userdata(UNAME_SESS_ID));
				$type = xss_clean($_POST["type"]);
				$id = xss_clean($_POST["id"]);
				$office_id= $session->userdata(OFF_SESSION_ID);
				
				// confirm that a super admin has been logged in
				IF($session->userdata(ROLE_SUPER_ROLE)) {
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
					// ASSIGN VARIABLES
					$n_username = $admin_user->get_details_by_id($id)->uname;
					$n_email = $admin_user->get_details_by_id($id)->uemail;
					$n_password = random_string('alnum', mt_rand(9, 12));
					
					#encrypt the password 
					$n_pass = $encrypt->password_hash($n_password);
					
					#update the password 
					$DB->query("update _admin set password='$n_pass' where id='$id'");
					
					//FORM THE MESSAGE TO BE SENT TO THE USER
					$message = 'Hello '.$n_username.',<br><br>Your User Account has successfully been activated at '.config_item('site_name');
					$message .= '<br><br><strong>EMAIL:</strong> '.$n_email;
					$message .= '<br><strong>USERNAME:</strong> '.$n_username;
					$message .= '<br><strong>PASSWORD:</strong> '.$n_password;
					$message .= "<br><br>Please do <a href='".config_item('manager_dashboard')."Login'>Click Here</a> to Sign into your Account.";
					
					//send the mail to the administrator
					send_email(
						$n_email, "[".config_item('site_name')."] Account Activation", 
						$message, config_item('site_name'), config_item('site_email'), 
						NULL, 'default', $n_username
					);
					
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
			IF(ISSET($_POST["password"]) AND ($session->userdata(UNAME_SESS_ID)) AND ISSET($_POST["password1"])) {
				#clean the data parsed
				$password1 = xss_clean($_POST["password"]);
				$password2 = xss_clean($_POST["password1"]);
				$username = xss_clean($session->userdata(UNAME_SESS_ID));
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
					$DB->query("update _admin set password='$new_pass', changed_password='1' where username='$username' and office_id='".$session->userdata(OFF_SESSION_ID)."'");
					#set the first user notification
					$DB->query("insert into _activity_logs set full_date=now(), office_id='".$session->userdata(OFF_SESSION_ID)."', date_recorded=now(), admin_id='$username', activity_page='password', activity_id='$username', activity_details='$username', activity_description='You changed your password from the default.'");
					#reload the page
					redirect( config_item('manager_dashboard') . 'Dashboard', 'refresh:2000');
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
			IF(ISSET($_POST["password1"]) AND ($session->userdata(UNAME_SESS_ID)) AND ISSET($_POST["password2"])) {
				#clean the data parsed
				$password1 = xss_clean($_POST["password1"]);
				$password2 = xss_clean($_POST["password2"]);
				$admin_id = xss_clean($session->userdata(UNAME_SESS_ID));
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
					$DB->query("update _admin set password='$new_pass', changed_password='1', last_login_attempts='0' where username='$username' and office_id='".$session->userdata(OFF_SESSION_ID)."'");
					$DB->query("delete from _admin_request_change where username='$username' and office_id='".$session->userdata(OFF_SESSION_ID)."'");
					#set the first user notification
					$DB->query("insert into _activity_logs set full_date=now(), office_id='".$session->userdata(OFF_SESSION_ID)."', date_recorded=now(), admin_id='$admin_id', activity_page='password-changed', activity_id='$username', activity_details='$username', activity_description='You have successfully changed your password.'");
					//FORM THE MESSAGE TO BE SENT TO THE USER
					$message = 'Hello '.$username.',<br><br>Your password was successfully changed at '.config_item('site_name');
					$message .= '<br><br><strong>EMAIL:</strong> '.$username;
					$message .= '<br><br><strong>Browser:</strong> '.$user_agent->browser()." ".$user_agent->platform();
					$message .= '<br><strong>IP Address:</strong> '.$user_agent->ip_address();
					$message .= '<br><strong>Server Host:</strong> '.$_SERVER["HTTP_HOST"];
					$message .= "<br><br>Please do Contact <a href='".SITE_URL."#contactForm'>Support</a> if you did not initiate this Password Change.";
					//send the mail to the administrator
					send_email(
						$session->userdata(managerEmail), "[".config_item('site_name')."] Password Changed", 
						$message, config_item('site_name'), config_item('site_email'), 
						NULL, 'default', $username
					);
					#reload the page
					$session->set_userdata(LOCKED_OUT, true);
					redirect( config_item('manager_dashboard') . 'Dashboard', 'refresh:100');
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
				IF(!valid_email($user_email)) {
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
							$message .= '<br><strong>Username:</strong> '.$username;
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
						PRINT "<div class='alert alert-danger'>Sorry! The credentials <strong>($user_email)</strong> does not match our system records.</div>";
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
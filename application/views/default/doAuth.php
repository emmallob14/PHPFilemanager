<?php
#call the global function 
global $SITEURL, $config, $DB;

#confirm that the user has parsed this value
IF(ISSET($SITEURL[1])) {
	$encrypt = load_class('encrypt', 'libraries');
	$user_agent = load_class('user_agent', 'libraries');
	
	
	#FUNCTION TO PROCESS USER LOGIN 
	function confirm_login($username, $password, $href) {
		
		global $DB, $session, $stores, $user_agent, $config;
		
		if(isset($username) and isset($password)) {
			#clean the data parsed
			$username = ucfirst(xss_clean($username));
			$password = xss_clean($password);
			#validate the email 
			if(empty($username)) {
				print "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Please enter a Valid Username.</div>";
				#validate password 
			} else {
				#query the database for the said user name or password 
				$confirm_username = $DB->query("select * from _admin where username='$username' and activated='1' and status='1'");
				#count the number of rows found 
				if(count($confirm_username) != 1) {
					#add login attempts
					add_login_attempt($username);
					#print error message 
					print "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Invalid Username/Password.</div>";
				} else {
					
					#check the number of login attempts 
					$log_attempt = confirm_login_attempt($username);
					
					if ($log_attempt == 0) {
						#using the foreach loop to get the results 
						foreach($confirm_username as $results) {
							
							#confirm that the username is validated 
							if(($results["activated"] == 1) and ($results["status"] == 1)) {
								$enc_password =  sha1(md5($password));
								#confirm that the user password is indeed correct
								if($enc_password == $results["password"]) {
									
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
											":lifeFullname" => $results["fullname"],
											":lifeLockedOut" => false,
											":lifeAdminRole" => $results["role"],
											":lifeSESS" => random_string('alnum', 45)
										)
									);
									
									$session->set_userdata(':lifeLockedOut', false);
									
									if($results["office_id"] == 0) {
										$session->set_userdata(":life_Supper_Admin", true);
										$session->set_userdata(":lifeAdminRole", 1043);
									}
									
									#update the table 
									$ip = $user_agent->ip_address();
									$br = $user_agent->browser()." ".$user_agent->platform();
																							
									$DB->query("update _admin set lastaccess=now(), log_ipaddress='$ip', log_browser='$br', log_session='".$session->userdata(":lifeSESS")."', last_login_attempts='$last_login_attempts', last_login_attempts_time='$last_login_attempts_time' where id='{$results["id"]}'");
									
									$DB->query("insert into _admin_log_history set username='$username', lastlogin=now(), log_ipaddress='$ip', log_browser='$br', log_platform='".$user_agent->agent_string()."'");
									
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
									
									print "<script>$(\"#submitButton\").attr(\"disabled\", true);</script>";
									#redirect the user
									if($href) {
										redirect( $href, 'refresh:1000');
									} else {
										redirect( $config->base_url() . 'Dashboard', 'refresh:1000');
									}
								} else {
									#add login attempts
									add_login_attempt($username);
									#print error message
									print "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Invalid Username/Password.</div>";
								}
							} else {
								#add login attempts
								add_login_attempt($username);
								#print error message 
								print "<div style='width:100%' class='alert alert-danger alert-md btn-block'>Sorry! Login Failed. Invalid Username/Password.</div>";
								print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
							}
							
						}
					} else {
						print "<div style='width:100%' class='alert alert-danger alert-md alert-block'>Sorry! You have been locked out for many trials.</div>";
						print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
					}
				}
			}
		}
	}

	function last_login_attempts($username) {
		
		global $DB, $functions;
		
		// increase number of attempts
		// set last login attempt time if required    
		$sql = "SELECT * FROM _login_attempt WHERE `ip` = '".ip_address()."' AND `username`='$username'"; 
			
		$result = $DB->query($sql);
			
		if(count($result) > 0) {
			foreach($result as $data) {
				$attempts = $data["attempts"];

				return $attempts;
			}
		} else {
			return 1;
		}
	}

	function last_login_attempts_time($username) {
		
		global $DB, $functions;
		
		// increase number of attempts
		// set last login attempt time if required    
		$sql = "SELECT * FROM _login_attempt WHERE `ip` = '".ip_address()."' AND `username`='$username'"; 
			
		$result = $DB->query($sql);
			
		if(count($result) > 0) {
			foreach($result as $data) {
				$attempts_time = $data["lastlogin"];

				return $attempts_time;
			}
		} else {
			return 1;
		}
	}

	function add_login_attempt($username) {
		
		global $DB, $functions;
		
		// increase number of attempts
		// set last login attempt time if required    
		$sql = "SELECT * FROM _login_attempt WHERE `ip` = '".ip_address()."' AND `username`='$username'"; 
			
		$result = $DB->query($sql);
			
		if(count($result) > 0) {
			foreach($result as $data) {
				$attempts = $data["attempts"]+1;

				if($attempts == ATTEMPTS_NUMBER) {
					$q = "UPDATE _login_attempt SET attempts='$attempts', lastlogin=NOW() WHERE `ip` = '".ip_address()."' AND `username`='$username'";
					$result = $DB->execute($q);
				} else {
					$q = "UPDATE _login_attempt SET attempts='$attempts' WHERE `ip` = '".ip_address()."' AND `username`='$username'";
					$result = $DB->execute($q);
				}
			}
		} else {
			$q = "INSERT INTO _login_attempt (username,ip,attempts,lastlogin) values ('$username','".ip_address()."',1,NOW())";
			$result = $DB->execute($q);
		}
	}

	function clear_login_attempt($username) {
			
			global $DB, $functions;
			
			$q = "UPDATE _login_attempt SET attempts = '0', lastlogin=now() WHERE `ip` = '".ip_address()."' AND `username`='$username'";
			
			return $DB->execute($q);
	}

	function confirm_login_attempt($username) {
		
		global $DB;
			
		$sql = "SELECT 
			attempts, 
				(CASE when lastlogin is not NULL and DATE_ADD(lastlogin, INTERVAL ".TIME_PERIOD." MINUTE) 
					> 
				NOW() then 1 else 0 end) as Denied 
			FROM 
				_login_attempt WHERE `username` = '$username' and `ip` = '".ip_address()."'";
		 
		$result = $DB->query($sql);
		
		foreach($result as $data) {
			if ($data["attempts"] >= ATTEMPTS_NUMBER):
				if($data["Denied"] == 1):
					return 1;
				else:
					clear_login_attempt($username);
					return 0;
				endif;
			endif;
		}
		return 0;
	}
	
	
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doLogin") AND ISSET($_POST["login_user_yea"]) AND ISSET($_POST["username"])) {
		#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
		if(isset($_POST["login_user_yea"])) {
			if(isset($_POST["username"]) and isset($_POST["password"])) {
				#clean the data parsed
				$username = xss_clean($_POST["username"]);
				$password = xss_clean($_POST["password"]);
				$href = null;
				if(isset($_POST["href"]))
					$href = xss_clean($_POST["href"]);
				#call the login function 
				confirm_login($username, $password, $href);
			}
		}
	}
	
	#CHECK IF THE USER WANT TO LOGOUT OF THE SYSTEM 
	IF(($SITEURL[1] == "doLogout")) {
		# update the system
		$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='logout', activity_id='".$session->userdata(":lifeFullname")."', activity_details='".$session->userdata(":lifeUsername")."', activity_description='You have successfully logged out of the system.'");
		# clean all user session data 
		$session->sess_destroy();
		# redirect the user
		header('Location: '.$config->base_url().'Login/doLogout');
		exit(-1);
	}
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doUnlock") AND ISSET($_POST["lock_password"])) {
		if(isset($_POST["unlock_screen"])) {
			if(isset($_POST["lock_password"]) and ($session->userdata(":lifeUsername")) and !isset($_POST["username"])) {
				#clean the data parsed
				$username = xss_clean($session->userdata(":lifeUsername"));
				$password = xss_clean($_POST["lock_password"]);
				#call the login function 
				confirm_login($username, $password);
			}
		}
	}
	
	#CHECK IF AN ACCOUNT NEEDS TO BE MODIFIED
	IF(($SITEURL[1] == "doModify") AND ISSET($_POST["modify_account"]) AND ISSET($_POST["id"])) {
		if(isset($_POST["modify_account"])) {
			if(isset($_POST["type"]) and ($session->userdata(":lifeUsername")) and isset($_POST["id"])) {
				#clean the data parsed
				$username = xss_clean($session->userdata(":lifeUsername"));
				$type = xss_clean($_POST["type"]);
				$id = xss_clean($_POST["id"]);
				#what do you want to do?
				if($type == "Disable") {
					$DB->query("update _admin set activated='0' where id='$id' and store_id='".STORE_ID."'");
					print "<div style='width:100%' class='alert alert-success'>Admin Account successfully deactivated.</div>";
					print "<script>$('#admin_$id').addClass('alert alert-danger');</script>";
				}
				if($type == "Delete") {
					$DB->query("update _admin set admin_deleted='0', status='0' where id='$id' and store_id='".STORE_ID."'");
					print "<div style='width:100%' class='alert alert-success'>Admin Account successfully deleted.</div>";
					print "<script>$('#admin_$id').hide();</script>";
				}
				if($type == "Activate") {
					$db->query("update _admin set activated='1' where id='$id' and store_id='".STORE_ID."'");
					print "<div style='width:100%' class='alert alert-success'>Admin Account successfully activated.</div>";
					print "<script>$('#admin_$id').removeClass('alert alert-danger');</script>";
				}
			}
		}
	}
	
	
	#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
	IF(($SITEURL[1] == "doChangePassword") AND ISSET($_POST["change_user_password_"])) {
		if(isset($_POST["change_user_password_"])) {
			if(isset($_POST["password1"]) and ($admin_user->confirm_admin_user() == true) and isset($_POST["password2"])) {
				#clean the data parsed
				$password1 = xss_clean($_POST["password1"]);
				$password2 = xss_clean($_POST["password2"]);
				$admin_id = xss_clean($_SESSION[":lifeUsername"]);
				$username = xss_clean($_POST["username"]);
				if($password1 != $password2) {
					print "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords do not match.</div>";
					print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} elseif(strlen($password1) < 6) {
					print "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords should be at least 6 characters.</div>";
					print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} elseif($admin_user->compare_password($password1) == true) {
					print "<div style='width:100%' class='alert alert-danger'>Sorry! Your password is too simple. Please change it.</div>";
					print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} elseif(strtolower($password1) == strtolower($username)) {
					print "<div style='width:100%' class='alert alert-danger'>Sorry! You cannot use your username as password.</div>";
					print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
				} else {
					#encrypt the password 
					$new_pass = sha1(md5($password1));
					#update the password 
					$DB->query("update _admin set password='$new_pass', changed_password='1' where username='$username' and store_id='".STORE_ID."'");
					$DB->query("delete from _admin_request_change where username='$username' and store_id='".STORE_ID."'");
					#set the first user notification
					$DB->query("insert into _activity_logs set full_date=now(), store_id='".STORE_ID."', date_recorded=now(), admin_id='$admin_id', activity_page='password-changed', activity_id='$username', activity_details='$username', activity_description='Your password was recently changed by an Admin.'");
					$DB->query("insert into _activity_logs set full_date=now(), store_id='".STORE_ID."', date_recorded=now(), admin_id='$username', activity_page='password-changed', activity_id='$username', activity_details='$username', activity_description='You recently changed the password of a Admin.'");
					#reload the page
					print "<script>window.location.href='".SITE_URL."/dashboard'</script>";
				}
			} else {
				print "<div style='width:100%' class='alert alert-danger'>Sorry! The passwords cannot be empty.</div>";
				print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
			}
		}
	}
	
	#REQUEST A PASSWORD CHANGE
	IF(($SITEURL[1] == "doRequestPasswordChange") AND ISSET($_POST["request_password_change"])) {
		if(isset($_POST["request_password_change"])) {
			if(isset($_POST["request_username"]) and !empty($_POST["request_username"])) {
				#clean the data parsed
				$username = xss_clean($_POST["request_username"]);
				#confirm that the username is available
				$username_confirm = $db->just_query("select * from _admin where username='$username' and admin_deleted='0'");
				#count the number of rows 
				if(count($username_confirm) > 0) {
					#confirm that the user has not requested a password already 
					if(count($db->just_query("select * from _admin_request_change where username='$username'")) < 1) {
						#record the request 
						foreach($username_confirm as $request_res) {
							#assign variable 
							$store_id = $request_res["store_id"];
							#process the form 
							$DB->query("insert into _admin_request_change set username='$username', store_id='$store_id'");
							#record the activity
							$DB->query("insert into _activity_logs set full_date=now(), store_id='$store_id', date_recorded=now(), admin_id='$username', activity_page='password', activity_id='$username', activity_details='$username', activity_description='You requested a change of password.'");
							#record the password change request 
							print "<div class='alert alert-success'>Password change request was successful.</div>";
							print "<script>$(\"#request_username\").val(\"\");</script>";
						}
					} else {
						print "<div class='alert alert-danger'>Sorry! you have already requested for a change of password.</div>";
					}
				} else {
					#print error message
					print "<div class='alert alert-danger'>Sorry! The username <strong>($username)</strong> does not exist in the system.</div>";
				}
			} else {
				print "<div style='width:100%' class='alert alert-danger'>Sorry! Please specify your username.</div>";
				print "<script>$(\"#login_user\").removeAttr(\"disabled\", false);</script>";
			}
		}
	}

} ELSE {
	show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404');
}
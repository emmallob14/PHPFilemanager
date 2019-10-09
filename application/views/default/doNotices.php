<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
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
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), office_id='".$session->userdata(OFF_SESSION_ID)."', admin_id='$item_id', activity_page='login-notice', activity_id='$item_id', activity_details='$item_id', activity_description='A login attempt notification that was sent to you was removed'");
				}
				if($type == "pass_request") {
					#update the user login notification
					$DB->execute("delete from _admin_request_change where username='{$admin_user->return_email()}'");
					#add up the the users activity history
					$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), office_id='".$session->userdata(OFF_SESSION_ID)."', admin_id='$item_id', activity_page='password-change-notice', activity_id='$item_id', activity_details='$item_id', activity_description='A password change notification that was sent to you was removed.'");
				}
				#CHECK IF THE USER WANT TO LOGIN TO A NEW ACCOUNT 
				if($type == "multiple_attempt") {
					#update the user login notification
					$DB->execute("update _login_attempt set lastlogin=now(), attempts='0' where username='$item_id'");
				}
			}
		}	
	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
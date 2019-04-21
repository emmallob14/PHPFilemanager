<?php 
#initial 
global $DB, $session, $admin_user, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		#CHANGE THE USER UPLOADS STATUS
		IF(($SITEURL[1] == "doUpdate") AND ISSET($_POST["office_key"])) {
			// CONFIRM THAT THE USER IS AN ADMINISTRATOR
			IF($admin_user->confirm_admin_user()) {
				// ASSIGN SOME VARIABLES
				$office_key = xss_clean($_POST["office_key"]);
				$session_key = xss_clean($session->userdata("office_id_to_update"));
				$office_name = xss_clean($_POST["office_name"]);
				$office_contact = xss_clean($_POST["office_contact"]);
				$office_email = xss_clean($_POST["office_email"]);
				$office_address = xss_clean($_POST["office_address"]);
				$office_description = nl2br(xss_clean($_POST["office_description"]));
				// VALIDATE THE VARIABLES 
				IF($office_key != $session_key) {
					PRINT "<div class='alert alert-danger'>Sorry! The current session has expired! Please reload the page to continue.</div>";
				} ELSEIF(STRLEN($office_name) < 2) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the Office Name.</div>";
				} ELSEIF(STRLEN($office_address) < 2) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the Office Address.</div>";
				} ELSEIF(STRLEN($office_email) < 5) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter the Office Email.</div>";
				} ELSEIF(!valid_email($office_email)) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter a valid Office Email.</div>";
				} ELSEIF(STRLEN($office_contact) < 10) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter a valid Office Contact.</div>";
				} ELSEIF(!PREG_MATCH("/^[0-9+]+$/", $office_contact)) {
					PRINT "<div class='alert alert-danger'>Sorry! Please enter a valid Office Contact.</div>";
				} ELSE {				
					// UPDATE THE OFFICE INFORMATION
					$DB->just_exec("UPDATE _offices SET office_name='$office_name', office_contact='$office_contact', office_address='$office_address', office_email='$office_email', office_description='$office_description' WHERE unique_id='$office_key'");
					// PRINT THE NEW INFORMATION
					PRINT "<div class='alert alert-success'>Office information successfully updated.</div>.";
				}
			} ELSE {
				// PRINT ERROR MESSAGE
				PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
			}			
		}
		
		
		
		#UPDATE THE OFFICE UPLOADS LIMIT
		IF(($SITEURL[1] == "doEffectChange") AND ISSET($SITEURL[2]) AND ISSET($_POST["Action"])) {
			IF(($SITEURL[2] == "uploadLimit") AND $_POST["Action"] == "doUploadLimit") {
				// ASSIGN SOME VARIABLES
				$office_id = xss_clean($_POST["office_id"]);
				$office_id_to_update = $session->userdata("office_id_to_update");
				$daily_usage = xss_clean($_POST["daily_usage"]);
				$overall_usage = xss_clean($_POST["overall_usage"]);
				$update_type = xss_clean($_POST["update_type"]);
				// CONFIRM THAT THE USAGE IS A NUMERIC CHARACTER
				IF(!PREG_MATCH("/^[0-9]+$/", $daily_usage)) {
					// PRINT ERROR MESSAGE
					PRINT "<div class='alert alert-danger'>Sorry! The quota must be a valid numeric integer.</div>";
				} ELSEIF(!PREG_MATCH("/^[0-9]+$/", $overall_usage)) {
					// PRINT ERROR MESSAGE
					PRINT "<div class='alert alert-danger'>Sorry! The quota must be a valid numeric integer.</div>";
				} ELSEIF(!IN_ARRAY($update_type, ARRAY("daily","overall"))) {
					// PRINT ERROR MESSAGE
					PRINT "<div class='alert alert-danger'>Sorry! The quota must be a valid numeric integer.</div>";
				} ELSE {
					$daily_usage = ROUND($daily_usage)*(1024*1024);
					$overall_usage = ROUND($overall_usage)*(1024*1024);
					// GET THE OFFICE OVERALL USAGE
					$overall_permitted = $offices->item_by_id('disk_space', $office_id);
					// CONFIRM THAT THE USER ID PARSED MATCHES THE ONE SET IN THE SESSION
					IF($office_id != $office_id_to_update) {
						PRINT "<div class='alert alert-danger'>Sorry! An invalid session token parsed.</div>";
						// CONFIRM THAT THE USER NEW USAGE DOES NOT EXCEED THE AVAILABLE USAGE
					} ELSEIF($daily_usage > $overall_permitted) {
						// UPDATE THE USER UPLOAD STATUS
						PRINT "<div class='alert alert-danger'>Sorry! The quota set exceeds your overall disk space limit.</div>";
					} ELSE {
						// OFFICE ID
						$of_id = $offices->item_by_id('id', $office_id);
						// total disk usage
						$usage = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "office_id='{$of_id}'", 'item_size', 'ORDER BY id ASC')*1024);
						$usage_today = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "office_id='{$of_id}' AND item_date=CURDATE()", 'item_size', 'ORDER BY id ASC')*1024);
						// CHECK THE UPDATE POINT
						IF($update_type == "daily") {
							IF($admin_user->confirm_admin_user()) {
								// UPDATE THE OFFICE DAILY QUOTA
								$DB->just_exec("UPDATE _offices SET daily_upload='$daily_usage' WHERE unique_id='$office_id'");
								// PRINT NEW INFORMATION
								PRINT file_size_convert($usage_today). " used out of <strong>". file_size_convert($daily_usage)."</strong> today";
								$gritter_msg = "Congrats! Update was successful!";
							} ELSE {
								// PRINT ERROR MESSAGE
								PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
								$gritter_msg = "Sorry! Permission Denied";
							}
						} ELSEIF($update_type == "overall") {
							IF($admin_user->confirm_admin_user()) {
								// UPDATE THE USER UPLOAD STATUS
								$DB->just_exec("UPDATE _offices SET disk_space='$overall_usage' WHERE unique_id='$office_id'");
								// PRINT INFORMATION
								PRINT "<strong>".file_size_convert($usage) ."</strong> out of <strong>".file_size_convert($overall_usage) ." </strong> (". round(((($usage)/$overall_usage) * 100), 2). "%) used";
								$gritter_msg = "Congrats! Update was successful!";
							} ELSE {
								// PRINT ERROR MESSAGE
								PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
								$gritter_msg = "Sorry! Permission Denied";
							}
						}
						// PRINT THE SUCCESS MESSAGE TO THE ADMIN USER
						PRINT "<script>$('.gritter-item-wrapper').css('display','block'); $.gritter.add({ title: 'Update Notification', text:'$gritter_msg', sticky: false });</script>";
					}
				}
			}
		}
		

	}
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
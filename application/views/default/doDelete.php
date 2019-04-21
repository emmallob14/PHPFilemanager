<?php 
#initial 
global $DB, $session, $admin_user, $offices;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		// CONFIRM THAT THE USER WANTS TO DELETE AN ITEM
		IF(($SITEURL[1] == "doDelete") AND ISSET($_POST["Action"]) AND ISSET($_POST["Uid"])) {
			$Id = (INT)xss_clean($_POST["Id"]);
			$Type = xss_clean($_POST["Type"]);
			$Uid = xss_clean($_POST["Uid"]);
			$Action = xss_clean($_POST["Action"]);
			$UserId = xss_clean($session->userdata(":lifeID"));
			
			// CONFIRM THE ITEM TYPE
			IF(!IN_ARRAY($Type, ARRAY('FILE', 'FOLDER', 'Shared_File', 'Shared_Folder'))) {
				PRINT 'error';
			} ELSEIF($UserId != $Uid) {
				PRINT 'error';
			} ELSEIF($Action == "delete") {
				// CONFIRM THAT THE USER WANTS TO DELETE ONLY A FILE OR FOLDER AND NOT A SHARED ITEM
				IF(IN_ARRAY($Type, ARRAY('FILE', 'FOLDER'))) {
					IF($DB->just_exec("UPDATE _item_listing SET item_deleted='1', item_status='0' WHERE id='$Id' AND user_id='$UserId'")) {
						# update the user activity logs
						$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='deleted-item', office_id='".$session->userdata("officeID")."', activity_id='$Id', activity_details='$Id', activity_description='".$session->userdata(":lifeFullname")." deleted the $Type with name {FILE_NAME}.'");
						
						IF($Type == 'FOLDER') {
							$DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE item_parent_id='$Id' AND user_id='$UserId'");
						}
						PRINT 'success';
					} ELSE {
						PRINT 'error';
					}
				}
				
				// CONFIRM THAT THE USER WANTS TO DELETE A SHARED ITEM
				IF(IN_ARRAY($Type, ARRAY('Shared_File', 'Shared_Folder'))) {
					IF($DB->just_exec("UPDATE _shared_listing SET shared_deleted='1' WHERE id='$Id' AND shared_by='$UserId'")) {
						# update the user activity logs
						$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='deleted-item', office_id='".$session->userdata("officeID")."', activity_id='$Id', activity_details='$Id', activity_description='".$session->userdata(":lifeFullname")." deleted the $Type with name {FILE_NAME}.'");
						
						// update the file
						$DB->query("INSERT INTO _shared_comments SET file_id='$Id', user_id='{$session->userdata(":lifeID")}', comment='The share file is no longer available, it has been Deleted by the user.', class='danger'");
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
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
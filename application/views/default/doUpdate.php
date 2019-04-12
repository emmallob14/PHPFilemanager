<?php 
#initial 
global $DB, $functions, $session;

if($admin_user->logged_InControlled() == true) { 
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		IF(($SITEURL[1] == "changeName") AND ISSET($_POST["item_name"])) {
			#check if the user is logged in
			if(isset($_POST["href"]) and isset($_POST["itemId"])) {
				#get the items and their values
				$href = xss_clean($_POST["href"]);
				$name = xss_clean($_POST["item_name"]);
				$desc = nl2br(xss_clean($_POST["desc"]));
				$item_id = xss_clean($_POST["itemId"]);
				
				if(empty($name)) {
					print "<div class='alert alert-danger btn-block'>Sorry! You must enter a filename.</div>";
				} else {
					// write the queryString
					$queryString = $DB->where(
						'_item_listing', '*', 
						ARRAY(
							'id'=>"='$item_id'",
							'user_id'=>"='{$session->userdata(":lifeID")}'"
					));
					// confirm that the current user owns the file / folder to be renamed
					IF($DB->num_rows($queryString) == 1) {
						// GO AHEAD AND UPDATE THIS FILE / FOLDER NAME AND DESCRIPTION
						$DB->execute("UPDATE _item_listing SET item_title='$name', item_description='$desc' WHERE id='$item_id' AND user_id='".$session->userdata(":lifeID")."'");
						
						# update the user activity logs
						$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='updated-item', activity_id='$item_id', activity_details='$item_id', activity_description='".$session->userdata(":lifeFullname")." updated the File with name {FILE_NAME}.'");
					
						// reload the page
						print "<script> $('.item_name').html('".$directory->item_by_id('item_title', $item_id)."'); $('#itemForm')[0].reset();</script>";
					} else {
						print "<div class='alert alert-danger btn-block'>Sorry! You are not permitted to rename this file.</div>";
					}
				}
			}
		}
		
		// CONFIRM THAT THE USER WANTS TO DELETE AN ITEM
		IF(($SITEURL[1] == "doDelete") AND ISSET($_POST["Action"]) AND ISSET($_POST["Uid"])) {
			$Id = (INT)xss_clean($_POST["Id"]);
			$Type = xss_clean($_POST["Type"]);
			$Uid = xss_clean($_POST["Uid"]);
			$UserId = $session->userdata(":lifeID");
			
			// CONFIRM THE ITEM TYPE
			IF(!IN_ARRAY($Type, ARRAY('FILE', 'FOLDER', 'Shared_File', 'Shared_Folder'))) {
				PRINT 'error';
			} ELSEIF($UserId != $Uid) {
				PRINT 'error';
			} ELSE {
				
				// CONFIRM THAT THE USER WANTS TO DELETE ONLY A FILE OR FOLDER AND NOT A SHARED ITEM
				IF(IN_ARRAY($Type, ARRAY('FILE', 'FOLDER'))) {
					IF($DB->just_exec("UPDATE _item_listing SET item_status='0' WHERE id='$Id' AND user_id='$UserId'")) {
						# update the user activity logs
						$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='deleted-item', activity_id='$Id', activity_details='$Id', activity_description='".$session->userdata(":lifeFullname")." deleted the $Type with name {FILE_NAME}.'");
						
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
					IF($DB->just_exec("UPDATE _shared_listing SET shared_expiry='".time()."' WHERE id='$Id' AND shared_by='$UserId'")) {
						# update the user activity logs
						$DB->execute("insert into _activity_logs set full_date=now(), date_recorded=now(), admin_id='".$session->userdata(":lifeUsername")."', activity_page='deleted-item', activity_id='$Id', activity_details='$Id', activity_description='".$session->userdata(":lifeFullname")." deleted the $Type with name {FILE_NAME}.'");
						
						// update the file
						$DB->query("INSERT INTO _shared_comments SET file_id='$Id', user_id='{$session->userdata(":lifeID")}', comment='The share file is no longer available, it has been Deleted by the user.', class='danger'");
						// print success message
						PRINT 'success';
					} ELSE {
						PRINT 'error';
					}
				}
			}
		}

	}
}
?>
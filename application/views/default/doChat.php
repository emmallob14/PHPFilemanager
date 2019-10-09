<?php
#call the GLOBAL function 
GLOBAL $SITEURL, $config, $DB, $admin_user, $session, $user_agent;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	// create some important objects
	$directory = load_class('directories', 'models');
	
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1]) AND ($SITEURL[1] == "doList")) {
		// Confirm that the user wants to list the comments
		IF(ISSET($SITEURL[2]) AND ($SITEURL[2] == "Comments") AND $session->userdata('sharedItemSlug')) {
			#check if the user is logged in
			IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "fetchComments") {
				# query the database _shared_listing table for the files that has been shared
				$shared_slug = $session->userdata('sharedItemSlug');
				$shared_Files = $DB->query("SELECT * FROM _shared_comments WHERE shared_slug='$shared_slug'");
				# using foreach loop to list the items 
				PRINT "<script>";
				PRINT "$('#chat-messages-inner').html('');";
				foreach($shared_Files AS $Comments) {
					PRINT "add_message('{$admin_user->get_details_by_id($Comments["user_id"])->funame}','".SITE_URL."/assets/images/demo/av2.jpg','{$Comments["comment"]}', '".DATE("jS M Y H:iA", STRTOTIME($Comments["date_added"]))."','{$Comments["class"]}');";
				}
				PRINT "</script>";
			}
		}

	}
	
	#confirm that the user wants to add a new comment
	ELSEIF(ISSET($SITEURL[1]) AND ($SITEURL[1] == "doAdd")) {
		#check if the user is logged in
		IF(ISSET($_POST["Data"]) AND STRLEN($_POST["Data"]) > 1) {
			IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "AddComment") {
				# query the database _shared_listing table for the files that has been shared
				$shared_slug = $session->userdata('sharedItemSlug');
				$Data = nl2br(xss_clean($_POST["Data"]));
				$ip = $user_agent->ip_address();
				$br = $user_agent->browser()." ".$user_agent->platform();
				
				$DB->query("INSERT INTO _shared_comments SET shared_slug='$shared_slug', user_id='{$session->userdata(UID_SESS_ID)}', comment='$Data', user_agent='$ip: $br'");
			}
		}
	}
	
	
	// list all the users chats
	ELSEIF(ISSET($SITEURL[1]) AND ($SITEURL[1] == "doListChats")) {
		// Confirm that the user wants to list the comments
		IF(ISSET($SITEURL[2]) AND ($SITEURL[2] == "Chats") AND $session->userdata('chatUnQ_Id')) {
			#check if the user is logged in
			IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "fetchUserChats") {
				# query the database _shared_listing table for the files that has been shared
				$chatUnQ_Id = $session->userdata('chatUnQ_Id');
				$chat_Receiver_Id = $session->userdata('chat_Receiver_Id');
				$chat_Sender_Id = $session->userdata(UID_SESS_ID);
				
				$userChats = $DB->query("SELECT * FROM _messages WHERE 
						((
							sender_id='$chat_Sender_Id' AND sender_deleted='0'
						) OR (
							receiver_id='$chat_Sender_Id' AND receiver_deleted='0')
						) AND deleted='0' AND unique_id='$chatUnQ_Id' ORDER BY id ASC
				");
				// print the initial message script header
				PRINT "<script>";
				PRINT "$('#chat-messages-inner').html('');";
				# using foreach loop to list the items 
				FOREACH($userChats AS $Chats) {
					
					// set the position of the message by using the sender of the message
					IF($Chats["sender_id"] == $session->userdata(UID_SESS_ID)) {
						$position = "chat-right";
					} ELSE {
						$position = "chat-left";
					}
					// print the message
					PRINT "add_message('{$admin_user->get_details_by_id($Chats["sender_id"])->funame}','".SITE_URL."/assets/images/demo/av2.jpg','{$Chats["message"]}', '".time_diff(STRTOTIME($Chats["sent_date"]))." ago','{$Chats["class"]} $position');";
				}
				PRINT "</script>";
			}
		}
	}
	
	#confirm that the user wants to add a new comment
	ELSEIF(ISSET($SITEURL[1]) AND ($SITEURL[1] == "doMessage")) {
		#check if the user is logged in
		IF(ISSET($_POST["Data"]) AND STRLEN($_POST["Data"]) > 1) {
			IF(ISSET($_POST["Action"]) AND $_POST["Action"] == "AddChat") {
				# query the database _shared_listing table for the files that has been shared
				$chatUnQ_Id = $session->userdata('chatUnQ_Id');
				$Receiver_Id = $session->userdata('chat_Receiver_Id');
				$chat_Sender_Id = $session->userdata(UID_SESS_ID);
				$Data = nl2br(xss_clean($_POST["Data"]));
				$ip = $user_agent->ip_address();
				$br = $user_agent->browser()." ".$user_agent->platform();
				
				#INSERT THE MESSAGE OF THE USER
				$DB->just_exec("insert into _messages set unique_id='$chatUnQ_Id', receiver_Id='$Receiver_Id', sender_id='$chat_Sender_Id', message='$Data', sent_date=now(), user_agent='$ip: $br'");
				#UPDATE THE LAST SEEN STATUS
				$DB->just_exec("update _admin set last_seen='".time()."' WHERE id='$chat_Sender_Id'");
				$DB->just_exec("update _messages set seen_status='1', seen_date=now() where receiver_id='$chat_Sender_Id' and seen_status='0'");
				#FETCH A LIST OF SUBJECTS SAVED IN THE DATABASE
				$list_chats = $DB->query("select * from _messages where unique_id='$chatUnQ_Id' order by id desc limit 1");
				#CHECK IF ITS THE VERY FIRST MESSAGE 
				IF($session->userdata("chatFirst_Time")) {
					redirect ( $config->base_url(). "Messages/Id/$chatUnQ_Id'");
				}
				// print the initial message script header
				PRINT "<script>";
				PRINT "$('#chat-messages-inner').html('');";
				# using foreach loop to list the items 
				FOREACH($list_chats AS $Chats) {
					// print the message
					PRINT "add_message('{$admin_user->get_details_by_id($Chats["sender_id"])->funame}','".SITE_URL."/assets/images/demo/av2.jpg','{$Chats["message"]}', '".time_diff(STRTOTIME($Chats["sent_date"]))." ago','{$Chats["class"]} chat-right');";
				}
				PRINT "</script>";
			}
		}
	}
	
	
} ELSE {
	// PRINT ERROR MESSAGE
	PRINT "<div class='alert alert-danger'>Sorry! You to do not have permission to perform this operation.</div>";
}
?>
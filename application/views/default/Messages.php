<?php
$PAGETITLE = "Chat Messages";
// initializing
GLOBAL $directory;
$MESSAGE_FOUND = FALSE;
$receiverId = $item_id = 0;
$class = null;
// call some important files
$chats = load_class("chats", "models");
// unset the shared item id
$session->unset_userdata('sharedItemId');
// check the url that has been parsed
IF(confirm_url_id(1, 'Id')) {
	IF(confirm_url_id(2)) {
		$item_slug = xss_clean($SITEURL[2]);
		// set the query string
		$QueryString = $DB->where(
			'_messages', '*', 
			ARRAY(
				'unique_id'=>"='$item_slug'",
				'deleted'=>"='0'"
		));
		IF($DB->num_rows($QueryString) > 0) {
			# ASSIGN A TRUE VALUE TO THE USER FOUND 
			$MESSAGE_FOUND = TRUE;
			$session->unset_userdata("chatFirst_Time");
			FOREACH($QueryString as $Chat) {
				IF($Chat["sender_id"] == $session->userdata(":lifeID")) {
					$receiverId = $Chat["receiver_id"];
				} ELSE {
					$receiverId = $Chat["sender_id"];
				}
				$session->set_userdata("chatUnQ_Id", $item_slug);
				$session->set_userdata("chat_Receiver_Id", $receiverId);
			}
		}
	}	
}

// check if is a new chat with the user
IF(confirm_url_id(4)) {
	// confirm that the third url parameter is a new 
	IF(confirm_url_id(3, 'New')) {
		// confirm that the user id is not equal to the admin id
		IF($SITEURL[4] != $session->userdata(":lifeID")) {
			// decode the user id and append an (INT) to it
			$user_id = (INT)base64_decode($SITEURL[4]);
			#SET SESSIONS FOR SOME IMPORTANT ITEMS
			$session->set_userdata("chat_Receiver_Id", $user_id);
			$session->set_userdata("chatUnQ_Id", $SITEURL[2]);
			$session->set_userdata("chatFirst_Time", true);
			$MESSAGE_FOUND = TRUE;
		}
	}
}

REQUIRE "TemplateHeader.php";
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
  </div>
<!--End-breadcrumbs-->
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-content" >
	  <div class="row-fluid">
		
		<div class="span12">
		
		<div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
		<?php if( !$MESSAGE_FOUND ) { ?><h5>List of all messages</h5><?php } ?>
		<?php if( $MESSAGE_FOUND ) { ?><h5>Details of message</h5><?php } ?>
	    </div>
		<div class="widget-content nopadding collapse in" id="collapseG4">
		<div class="chat-users panel-right2">
		  <div class="panel-title">
			<h5>Admin Users in the Office</h5>
		  </div>
		  <div class="comments_responses"></div>
		  <div class="panel-content nopadding">
			<ul class="contact-list">
				<?php 
				// fetch all membership list of the user of the same office
				$user_id = $session->userdata(":lifeID");
				$office_id = $session->userdata("officeID");
				
				// query the database admin table
				$Query = $DB->query("SELECT * FROM _admin WHERE status='1' AND activated='1' AND office_id='$office_id' AND id !='$user_id'");
				
				// using foreach loop to get all the users
				foreach($Query as $AdminUsers) {
					$unique_id = $chats->uniqueId($user_id, $AdminUsers["id"])->unique_key;
					
					if($receiverId == $AdminUsers["id"]) {
						$class = "style='background-color:#006dcc;color:#fff'";
					} else {
						$class = "";
					}
				?>
				<li title="Click to chat with <?php print $admin_user->get_details_by_id($AdminUsers["id"])->funame; ?>" id="user-<?php print $AdminUsers["id"]; ?>" class="online"><a <?php print $class; ?> href="<?php print $config->base_url(); ?>Messages/Id/<?php print $unique_id; ?>"><img alt="" src="<?php print SITE_URL; ?>/assets/images/demo/av1.jpg" /> <span><?php print $admin_user->get_details_by_id($AdminUsers["id"])->funame; ?></span></a></li>
				<?php } ?>
			</ul>
		  </div>
		</div>
		<div class="chat-content panel-left2">
		  <div class="chat-messages" id="chat-messages">
			<?php if( !$MESSAGE_FOUND ) { ?>
			<div class="widget-content nopadding">
			<div class="span12">
			<table class="table table-bordered data-table">
			  <thead>
				<tr>
					<th>ID</th>
					<th>FULLNAME</th>
					<th>MESSAGE</th>
					<th>SENT</th>
					<th>STATUS</th>
					<th>ACTION</th>
				</tr>
			  </thead>
			  <tbody>
				<?php 
				# query the database _shared_listing table for the files that has been shared
				$listMessages = $DB->query("
					SELECT * FROM _messages WHERE 
						((
							sender_id='{$session->userdata(":lifeID")}' and sender_deleted='0'
						) OR (
							receiver_id='{$session->userdata(":lifeID")}' and receiver_deleted='0')
						) and deleted='0' group by unique_id order by id desc limit 10000
				");
				#GET A LOOP OF THE NUMBER OF ROWS FOUND
				$i = count($listMessages)+1;
				# using foreach loop to list the items 
				foreach($listMessages as $r_chat) {
					$i--;
					#SET THE UNIQUE ID FOR THIS CHAT
					$chat_UID = $r_chat["unique_id"];
					#GET THE BEST ID OF THE USER
					if($r_chat["sender_id"] == $session->userdata(":lifeID")) {
						$other_Id = $r_chat["receiver_id"];
					} else {
						$other_Id = $r_chat["sender_id"];
					}
				?>
				<tr id="Message_view_<?php print $r_chat["id"]; ?>">
					<td>
						<a href="<?php print SITE_URL; ?>/Messages/Id/<?php print $chat_UID; ?>"><?php print $i; ?></a>
					</td>
					<td>
						<a href="<?php print SITE_URL; ?>/Messages/Id/<?php print $chat_UID; ?>"><?php print $admin_user->get_details_by_id($other_Id)->funame; ?></a>
					</td>
					<td><?php print limit_words($r_chat["message"], 10); ?> [...]</td>
					<td><?php print time_diff(strtotime($r_chat["sent_date"])); ?></td>
					<td>
					<?php IF($r_chat["seen_status"] == 0) { ?>
					<span class="btn btn-danger btn-sm rounded dropdown-toggle"><i class="fa fa-dot-circle-o text-danger"></i> Not Seen</span>
					<?PHP } ELSE { ?>
					<span class="btn btn-success btn-sm rounded dropdown-toggle"><i class="fa fa-dot-circle-o text-success"></i> Seen</span>
					<?PHP } ?>
					</td>
					<td>
						<div class="dropdown">
							<a href="<?php print SITE_URL; ?>/Messages/Id/<?php print $chat_UID; ?>" class="btn btn-success" aria-expanded="false"><i class="icon icon-eye-open"></i></a>
						</div>
					</td>
				</tr>
				<?php } ?>
			  </tbody>
			</table>
			</div>	
			</div>
			<?php } ?>
			<?php if( $MESSAGE_FOUND ) { ?>
			<div id="chat-messages-inner"></div>
			<div id="new-chat-message"></div>
			<?php } ?>
		  </div>
		  <?php if( $MESSAGE_FOUND ) { ?>
			<div class="chat-message well">
				<button class="btn btn-success">Send</button>
				<span class="input-box">
					<input type="text" name="msg-box" id="msg-box" />
				</span>
			</div>
		  <?php } ?>
		</div>
		</div>
		</div>
	</div>
</div>
</div>
</div>
</div>
<!--End-Chart-box-->
<?php 
REQUIRE "TemplateFooter.php";
?>
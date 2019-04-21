<?php 
#initial 
global $DB, $functions, $session;
# confirm that the user is logged in 
IF($admin_user->logged_InControlled()) {
	$directory = load_class('directories', 'models');
	#confirm that the user has parsed this value
	IF(ISSET($SITEURL[1])) {
		
		IF(($SITEURL[1] == "addFolder")) {
			#check if the user is logged in
			if(isset($_POST["folder_description"]) and isset($_POST["folder_name"])) {
				#get the items and their values
				$root_folder = $directory->item_by_id('item_title', xss_clean($session->userdata('RootFolder')));
				$desc = nl2br(xss_clean($_POST["folder_description"]));
				$folder_name = xss_clean($_POST["folder_name"]);
				
				if(empty($folder_name)) {
					print "<div class='alert alert-danger btn-block'>Sorry! You must enter a folder name.</div>";
				} else {
					if(is_dir($session->userdata('userDir_Root')."/$root_folder/".$folder_name)) {
						print "<div class='alert alert-danger btn-block'>Sorry! This sub directory already exists under the parent directory.</div>";
					} else {
						// GO AHEAD AND UPDATE THIS FILE / FOLDER NAME AND DESCRIPTION
						$DB->execute("INSERT INTO _item_listing SET item_title='$folder_name', item_description='$desc', user_id='".$session->userdata(":lifeID")."', item_type='FOLDER', item_unique_id='".random_string('alnum', mt_rand(20, 50))."', item_parent_id='".$session->userdata('RootFolder')."', item_date=now(), item_folder_id='".$session->userdata('RootFolder')."'");
						
						print "<div class='alert alert-success btn-block'>Success! Your folder has been created.</div>";
						
						// reload the page
						print "<script> $('#addFolder')[0].reset();</script>";
						
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
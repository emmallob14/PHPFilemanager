<?php 
#start a new session
global $admin_user, $session;

#check what the user wants to do
if(isset($_POST["dataBackup"]) and ($admin_user->confirm_super_user() == true)) {	
	$backup_system = load_class('backup', 'models');
	if($admin_user->confirm_super_user()) {
		$office_id = 0;
	} else {
		$office_id = $session->userdata(OFF_SESSION_ID);
	}
	if(isset($_POST["backup_system"])){
		if(!is_dir(config_item('update_folder'))) {
			mkdir(config_item('update_folder'));
		}
		$file = config_item('update_folder')."db_$office_id"."_".date("Y-m-d").".sql";
		$backup = $backup_system->backup_system($file);
		if($backup) {
			print "Database System was successfully backedup";
		} else {
			print "There was an error while trying to update the system.";
		}
	}
}
?>
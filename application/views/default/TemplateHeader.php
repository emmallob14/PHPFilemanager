<?php
#FETCH SOME GLOBAL FUNCTIONS
global $SITEURL, $config, $session, $admin_user, $directory;
global $PAGETITLE, $DB, $offices;
#REDIRECT THE USER IF NOT LOGGED IN
if(!$admin_user->logged_InControlled()) {
	require "Login.php";
	exit(-1);
}
#REDIRECT THE USER USER HAS BEEN LOCKED OUT
if($admin_user->lock_user_screen()) {
	require "LockedOutScreen.php";
	exit(-1);
}
$notices = load_class('notifications', 'models');
if(!$admin_user->changed_password_first()) { die(require("ChangePassword.php")); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php print $PAGETITLE; ?>: <?php print config_item('site_name'); ?></title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="author" content="<?php print config_item('developer'); ?>">
<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
<meta name="currentDirectory" id="currentDirectory" value="<?php print $config->base_url(); ?>" content="<?php print current_url(); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap-responsive.min.css" />
<link rel="shortcut icon" href="<?php print $config->base_url(); ?>assets/onepage/assets/ico/favicon.png">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/fullcalendar.css"/>
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-style.css"/>
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-media.css"/>
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/uniform.css"/>
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/font-awesome/css/font-awesome.css"/>
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/js/editarea/edit_area.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/styles.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/jquery.gritter.css" />
<link href="<?php print $config->base_url(); ?>assets/css/jquery.dm-uploader.min.css" rel="stylesheet">
<?php if(in_array(strtolower($SITEURL[0]), ARRAY("profile","adduser","offices","serversettings"))) { ?>
<link href="<?php print $config->base_url(); ?>assets/css/custom.css" rel="stylesheet"> 
<?php } ?>
<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/js/jquery.ui.custom.js"></script> 
<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/js/bootstrap.min.js"></script>
<?php if(in_array(strtolower($SITEURL[0]), ARRAY("itemstream","dashboard","mediaplayer"))) { ?><link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-audio.css" />
<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/js/dist/jplayer/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="<?php print $config->base_url(); ?>assets/js/dist/add-on/jplayer.playlist.min.js"></script>
<?php } ?>
</head>
<body>
<!--Header-part-->
<div id="header">
  <h1><a href="<?php print $config->base_url(); ?>"><?php print config_item('site_name'); ?></a></h1>
</div>
<!--close-Header-part--> 
<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav">
    <li  class="dropdown" id="profile-messages" ><a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle"><i class="icon icon-user"></i>  <span class="text">Welcome <?php print $admin_user->get_details_by_id($session->userdata(UID_SESS_ID))->funame; ?></span><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="<?php print $config->base_url(); ?>Profile"><i class="icon-user"></i> My Profile</a></li>
        <li class="divider"></li>
		<li class="dropdown" id="menu-messages"><a href="<?php print $config->base_url(); ?>Messages"><i class="icon icon-envelope"></i> <span class="text">My Messages</span>
		<?php $CountMessages = $DB->query("
					SELECT * FROM _messages WHERE 
				(
					receiver_id='{$session->userdata(UID_SESS_ID)}' AND receiver_deleted='0'
				) AND deleted='0' AND seen_status='0'
			");
			
			PRINT (COUNT($CountMessages) > 0) ? "<span class=\"label label-important\">".COUNT($CountMessages)."</span>" : ""; ?></a>
		</li>
		<li class="divider"></li>
		<li><a href="<?php print $config->base_url(); ?>Tasks"><i class="icon-check"></i> My Tasks</a></li>
        <li class="divider"></li>
        <li><a href="<?php print $config->base_url(); ?>ChangeAccountPassword"><i class="icon-lock"></i> Change Password</a></li>
		<li class="divider"></li>
		<li <?php if(in_array(($SITEURL[0]), array('History'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>History"><i class="icon icon-info-sign"></i> <span>Login History</span></a></li>		
		<?php IF($admin_user->confirm_admin_user()) { ?>
		<li class="divider"></li>
		<li <?php if(in_array(($SITEURL[0]), array('Activities'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Activities"><i class="icon icon-list"></i> <span>Activity Logs</span></a></li>
		<?php } ?>
        <?php if($admin_user->confirm_admin_user() and !$admin_user->confirm_super_user()) { ?>
		<li class="divider"></li>
		<li class=""><a title="" href="<?php print $config->base_url(); ?>Offices"><i class="icon icon-cog"></i> <span class="text">System Settings</span></a></li>
		<?php } ?>
		<?php if($admin_user->confirm_super_user()) { ?>
		<li class="divider"></li>
		<li class=""><a title="" href="<?php print $config->base_url(); ?>ServerSettings"><i class="icon icon-signout"></i> <span class="text">Server Settings</span></a></li>
		<?php } ?>
		<li class="divider"></li>
		<li><a href="#" title="Click to Logout" class="logoutUser"><i class="icon-key"></i> Log Out</a></li>
      </ul>
    </li>
	<?php if($admin_user->confirm_super_user()) { ?>
	<li class=""><a title="" href="#" id="backup_data"><i class="icon icon-laptop"></i> <span class="text">Backup</span></a></li>
	<?php } ?>
	<li class=""><a title="" href="<?php print $config->base_url(); ?>Upload"><i class="icon icon-upload"></i> <span class="text">Upload Files</span></a></li>
	<li class=""><a title="Create New File / Directory" data-toggle="modal" data-target="#createNewItem" href="#"><i class="icon icon-plus"></i> <span class="text">Create New Item</span></a></li>
	<li class=""><a title="" href="<?php print $config->base_url(); ?>Share/List"><i class="icon icon-share"></i><span class="text share_list"></span></a></li>	
  </ul>
</div>
<!--close-top-Header-menu-->
<!--start-top-serch-->
<div id="search">
  <input style="width:250px;" name="q" id="q" type="text" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div>
<!--close-top-serch-->
<!--sidebar-menu-->
<div id="sidebar"><a href="<?php print $config->base_url(); ?>Dashboard" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
  <ul>
    <li <?php if(in_array(($SITEURL[0]), array('Dashboard','index'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Dashboard"><i class="icon icon-home"></i> <span>Dashboard</span></a> </li>
    <li <?php if(in_array(($SITEURL[0]), array('ItemsStream','ItemStream'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>ItemsStream"><i class="icon icon-signal"></i> <span>Files and Folders</span></a> </li>
    <li <?php if(in_array(($SITEURL[0]), array('Upload'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Upload"><i class="icon icon-plus"></i> <span>Upload Files</span></a> </li>
	<li <?php if(in_array(($SITEURL[0]), array('Shared','Share'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Shared"><i class="icon icon-share"></i> <span>Shared Files</span></a></li>
    <li <?php if(in_array(($SITEURL[0]), array('MediaPlayer'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>MediaPlayer"><i class="icon icon-facetime-video"></i> <span>Media Player</span></a></li>
    <li <?php if(in_array(($SITEURL[0]), array('Messages'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Messages"><i class="icon icon-fullscreen"></i> <span>Messages</span></a></li>
	<li class=" <?php if(in_array(($SITEURL[0]), array('Profile','Users','ChangePassword','History','ChangePasswordRequest','AddUser','Activities'))) { ?>active<?php } ?> submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Account Users</span>  <span style="float:right;margin-right:10px"><i class="icon icon-arrow-down"></i></span></a>
      <ul>
		<li <?php if(in_array(($SITEURL[0]), array('Profile'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Profile"><i class="icon icon-cogs"></i> <span>My Profile</span></a></li>
		<?php if($admin_user->confirm_admin_user()) { ?>
        <li <?php if(in_array(($SITEURL[0]), array('Users'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Users"><i class="icon icon-group"></i> <span>Manage Users</span></a></li>
		<li <?php if(in_array(($SITEURL[0]), array('AddUser'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>AddUser"><i class="icon icon-user"></i> <span>Add New User</span></a></li>
		<?php } ?>	
      </ul>
    </li>	
	<li <?php if(in_array(($SITEURL[0]), array('Trash'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Trash"><i class="icon icon-trash"></i> <span>Trash</span></a></li>
	<?php if($admin_user->confirm_super_user()) { ?>
	<li <?php if(in_array(($SITEURL[0]), array('OfficesList','Offices'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>OfficesList"><i class="icon icon-cogs"></i> <span>Registered Offices</span> </a></li>
	<?php } ?>
	<?php if($admin_user->confirm_admin_user() and !$admin_user->confirm_super_user()) { ?>
	<li <?php if(in_array(($SITEURL[0]), array('Offices'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Offices"><i class="icon icon-sitemap"></i> <span>System Settings</span> </a></li>
	<?php } ?>
	<?php if($admin_user->confirm_super_user()) { ?>
	<li <?php if(in_array(($SITEURL[0]), array('ServerSettings'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>ServerSettings"><i class="icon icon-signout"></i> <span>Server Settings</span> </a></li>
	<?php } ?>
	<li class="content"> <span>User Disk Space</span>
	  <?php
		// assign variables 
		// total disk usage
		$usage = ($directory->user_disk_info('SUM(item_size_kilobyte) AS item_size', "user_id='{$session->userdata(UID_SESS_ID)}'", 'item_size', 'ORDER BY id ASC')*1024);
		// total assigned usage
		$total = ($admin_user->get_details_by_id($session->userdata(UID_SESS_ID))->upload_limit);
		// percent used 
		$percent = ROUND(((($usage)/$total) * 100), 2);
      ?>	 
      <div class="progress progress-mini progress-danger active progress-striped">
        <div style="width: <?PHP PRINT $percent; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $percent; ?>%</span>
      <div class="bandwidth_stat"><?PHP PRINT file_size_convert($usage); ?> / <?php print  file_size_convert($total); ?></div>
    </li>
	<li class="content"> <span>Today Overall Uploads</span>
      <div class="progress progress-mini progress-danger active progress-striped">
        <div style="width: <?PHP PRINT $directory->return_usage()->today_used; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $directory->return_usage()->today_used; ?>%</span>
      <div class="bandwidth_stat"><?PHP PRINT $directory->return_usage()->today_used_size; ?> / <?php print  file_size_convert($offices->item_by_id('daily_upload', $session->userdata(OFF_SESSION_ID))); ?></div>
    </li>
    <li class="content"> <span>Office Disk Space Usage</span>
      <div class="progress progress-mini active progress-striped">
        <div style="width: <?PHP PRINT $directory->return_usage()->percent_used; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $directory->return_usage()->percent_used; ?>%</span>
      <div class="disk_stat"><?PHP PRINT $directory->return_usage()->file_size; ?> / <?php print  file_size_convert($offices->item_by_id('disk_space', $session->userdata(OFF_SESSION_ID))); ?></div>
    </li>
	<?php if($admin_user->confirm_super_user()) { ?>
	<li class="content"> <span>Server Disk Space Used</span>
      <div class="progress progress-mini active progress-striped">
        <div style="width: <?PHP PRINT $directory->return_usage('overall')->percent_used; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $directory->return_usage('overall')->percent_used; ?>%</span>
      <div class="disk_stat"><?PHP PRINT $directory->return_usage('overall')->file_size; ?> / <?php print  file_size_convert(config_item('server_space')); ?></div>
    </li>
	<li class="content"> <span>Server Disk Space Allocated</span>
      <div class="progress progress-mini active progress-striped">
        <div style="width: <?PHP PRINT $offices->allocation()->percent_used; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $offices->allocation()->percent_used; ?>%</span>
      <div class="disk_stat"><?PHP PRINT $offices->allocation()->file_size; ?> / <?php print  file_size_convert(config_item('server_space')); ?></div>
    </li>
	<?php } ?>
  </ul>
</div>
<!--sidebar-menu-->

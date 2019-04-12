<?php
#FETCH SOME GLOBAL FUNCTIONS
global $SITEURL, $config, $session, $admin_user, $directory;
global $PAGETITLE, $DB;
#REDIREC THE USER IF NOT LOGGED IN
if(!$admin_user->logged_InControlled()) {
	require "Login.php";
	exit(-1);
}
#LOAD IMPORTANT FILES
load_helpers('url_helper');
$notices = load_class('notifications', 'models');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php print $PAGETITLE; ?>: <?php print config_item('site_name'); ?></title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap-responsive.min.css" />
<meta name="author" content="<?php print config_item('developer'); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/fullcalendar.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-style.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-media.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/uniform.css" />
<link href="<?php print $config->base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap-wysihtml5.css" />
<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
<meta name="currentDirectory" id="currentDirectory" value="<?php print $config->base_url(); ?>" content="<?php print current_url(); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/jquery.gritter.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.min.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.ui.custom.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/bootstrap.min.js"></script> 
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
    <li  class="dropdown" id="profile-messages" ><a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle"><i class="icon icon-user"></i>  <span class="text">Welcome User</span><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="<?php print $config->base_url(); ?>Profile"><i class="icon-user"></i> My Profile</a></li>
        <li class="divider"></li>
        <li><a href="<?php print $config->base_url(); ?>Tasks"><i class="icon-check"></i> My Tasks</a></li>
        <li class="divider"></li>
        <li><a href="#" title="Click to Logout" class="logoutUser"><i class="icon-key"></i> Log Out</a></li>
      </ul>
    </li>
	<li class=""><a title="" href="<?php print $config->base_url(); ?>Upload"><i class="icon icon-upload"></i> <span class="text">Upload Files</span></a></li>
	<li class=""><a title="" href="<?php print $config->base_url(); ?>Folder"><i class="icon icon-plus"></i> <span class="text">Create Folder</span></a></li>
    <li class="dropdown" id="menu-messages"><a href="<?php print $config->base_url(); ?>Messages"><i class="icon icon-envelope"></i> <span class="text">Messages</span> <span class="label label-important">5</span> <b class="caret"></b></a>
    </li>
    <li class=""><a title="" href="<?php print $config->base_url(); ?>Settings"><i class="icon icon-cog"></i> <span class="text">Settings</span></a></li>
    <li class=""><a href="#" title="Click to Logout" class="logoutUser"><i class="icon icon-share-alt"></i> <span class="text">Logout</span></a></li>
  </ul>
</div>
<!--close-top-Header-menu-->
<!--start-top-serch-->
<div id="search">
  <input name="q" type="text" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div>
<!--close-top-serch-->
<!--sidebar-menu-->
<div id="sidebar"><a href="<?php print $config->base_url(); ?>Dashboard" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
  <ul>
    <li <?php if(in_array(($SITEURL[0]), array('Dashboard'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Dashboard"><i class="icon icon-home"></i> <span>Dashboard</span></a> </li>
    <li <?php if(in_array(($SITEURL[0]), array('ItemsStream'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>ItemsStream"><i class="icon icon-signal"></i> <span>Files and Folders</span></a> </li>
    <li <?php if(in_array(($SITEURL[0]), array('Upload'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Upload"><i class="icon icon-plus"></i> <span>Upload Files</span></a> </li>
	<li <?php if(in_array(($SITEURL[0]), array('Shared'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Shared"><i class="icon icon-share"></i> <span>Shared Files</span></a></li>
    <li <?php if(in_array(($SITEURL[0]), array('Folder'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Folder"><i class="icon icon-th"></i> <span>Create Folder</span></a></li>
    <li <?php if(in_array(($SITEURL[0]), array('Messages'))) { ?>class="active"<?php } ?>><a href="<?php print $config->base_url(); ?>Messages"><i class="icon icon-fullscreen"></i> <span>Messages</span></a></li>
    <li <?php if(in_array(($SITEURL[0]), array('Settings'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>Settings"><i class="icon icon-file"></i> <span>Settings</span> </a></li>
    <li <?php if(in_array(($SITEURL[0]), array('History'))) { ?>class="active"<?php } ?>> <a href="<?php print $config->base_url(); ?>History"><i class="icon icon-info-sign"></i> <span>Log History</span></a></li>
    <li class="content"> <span>Today Overall Uploads</span>
      <div class="progress progress-mini progress-danger active progress-striped">
        <div style="width: <?PHP PRINT $directory->return_usage()->today_used; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $directory->return_usage()->today_used; ?>%</span>
      <div class="bandwidth_stat"><?PHP PRINT $directory->return_usage()->today_used_size; ?> / <?php print  file_size_convert(config_item('daily_upload')); ?></div>
    </li>
    <li class="content"> <span>Disk Space Usage</span>
      <div class="progress progress-mini active progress-striped">
        <div style="width: <?PHP PRINT $directory->return_usage()->percent_used; ?>%;" class="bar"></div>
      </div>
      <span class="percent"><?PHP PRINT $directory->return_usage()->percent_used; ?>%</span>
      <div class="disk_stat"><?PHP PRINT $directory->return_usage()->file_size; ?> / <?php print  file_size_convert(config_item('disk_space')); ?></div>
    </li>
  </ul>
</div>
<!--sidebar-menu-->

<?php
$PAGETITLE = "Dashboard";
REQUIRE "TemplateHeader.php";
GLOBAL $notices;
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
<div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
</div>
<!--End-breadcrumbs-->
<div class="container-fluid">
	<?php if(($notices->login_attempt($admin_user->return_username())->login_alerts == true) and ($notices->login_attempt($admin_user->return_username())->login_number > 1)) { ?>
	<div class="alert alert-danger" id="login_alert_div">
		<strong>LOGIN ATTEMPTS:</strong>
		<span>Hello <?php print $admin_user->return_username(); ?>, An attempt was made to Log into your account for <strong><?php print $notices->login_attempt($admin_user->return_username())->login_number; ?> times. </li></strong> <li class="fa icon-time"></li> <?php print date(SITE_DATE_FORMAT, strtotime($notices->login_attempt($admin_user->return_username())->login_time)); ?> If this wasn't you then <strong><a class="btn btn-info" href="<?php print $config->base_url(); ?>ChangeAccountPassword">SECURE YOUR ACCOUNT </a></strong></span>
		<button type="button" style="cursor:pointer;" class="close alert alert-danger" onclick="return remove_system_notices('login', '<?php print $admin_user->return_username(); ?>', 'login_alert_div')" style="font-size:13px">x</button>
	</div>
	<?php } ?>
	<?php if($notices->password_change($admin_user->return_email())->change_request AND $admin_user->confirm_admin_user()) { ?>
	<div class="alert alert-danger" id="request_change_alert_div">
		<strong>PASSWORD CHANGE NOTICE:</strong>
		<span>You requested for a change of password on <li class="fa icon-time"></li> <?php print date(SITE_DATE_FORMAT, strtotime($notices->password_change($admin_user->return_username())->change_time)); ?>. Report to the Administrator or close this notice</span>
		<button type="button" style="cursor:pointer;" class="close alert alert-danger" onclick="return remove_system_notices('pass_request', '<?php print $admin_user->return_email(); ?>', 'request_change_alert_div')" style="font-size:13px">x</button>
	</div>
	<?php } ?>
</div>
<!--Action boxes-->
<div class="container-fluid">
<div class="quick-actions_homepage">
  <ul class="quick-actions">
	<li class="bg_lb"> <a href="<?php print $config->base_url(); ?>Dashboard"> <i class="icon-dashboard"></i> My Dashboard </a> </li>
	<li class="bg_ly"> <a href="<?php print $config->base_url(); ?>ItemsStream"> <i class="icon-signal"></i> <span class="label label-important"><?PHP PRINT count($DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(':lifeID')."' AND item_type='FOLDER' AND item_status='1' AND item_deleted='0' ORDER BY ID DESC"))+count($DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(':lifeID')."' AND item_type='FILE' AND item_status='1' AND item_deleted='0' ORDER BY ID DESC")); ?></span>Files & Folders</a> </li>
	<li class="bg_lg"> <a href="<?php print $config->base_url(); ?>Shared"> <i class="icon-share"></i> Shared Files</a> </li>
	<li class="bg_ls"> <a href="<?php print $config->base_url(); ?>Messages"> <i class="icon-info-sign"></i> Messages</a> </li>
	<li class="bg_lo"> <a title="Coming Soon" href="#"> <i class="icon-th"></i> Groups</a> </li>
	<li class="bg_lb"> <a title="Coming Soon" href="#"> <i class="icon-calendar"></i> Calendar</a> </li>
	
  </ul>
</div>
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>Site Analytics</h5>
	</div>
	<div class="widget-content" >
	  <div class="row-fluid">
		<div class="span12">
			<!-- LISTING ALL FOLDERS OF THE USER -->
			<?PHP
			#INITIALIZING
			$NO_ITEM = TRUE;
			#FETCH ALL FOLDERS IN THE DATABASE THAT 
			$listFolders = $DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(':lifeID')."' AND item_type='FOLDER' AND item_status='1' AND item_deleted='0' AND item_parent_id='0' ORDER BY ID DESC LIMIT 8");
			$listFiles = $DB->query("SELECT * FROM _item_listing WHERE user_id='".$session->userdata(':lifeID')."' AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_parent_id='0' ORDER BY ID DESC LIMIT 24");
			#COUNT THE NUMBER OF FOLDERS 
			$NO_ITEM =(COUNT($listFolders) < 1) ? TRUE : FALSE;
			$NO_ITEM =(COUNT($listFiles) < 1) ? TRUE : FALSE;	
			
			IF(!$NO_ITEM) {
				#USING THE FOREACH LOOP 
				FOREACH($listFolders AS $Folders) {
					$file_ext = $Folders["item_ext"];
					$fileName = $Folders["item_title"];
					$Id = $Folders["id"];
					$Uid = $Folders["item_unique_id"];
						
					echo "<div class='file File_Info_$Id' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'><a href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Folders['item_thumbnail']."'><br>$fileName</a> <br>
						<div class='file_option' id='option_$Id'>
							<span onclick='process_item(\"delete\", \"$Id\", \"FOLDER\", \"".$session->userdata(":lifeID")."\");' class='btn btn-danger'><i class='icon-trash'></i> Delete Folder</span>
						</div>
						</div>";
				}
				
				FOREACH($listFiles AS $Files) {
					$file_ext = $Files["item_ext"];
					$fileName = $Files["item_title"];
					$Id = $Files["id"];
					$Uid = $Files["item_unique_id"];
					$DLink = $Files["item_download_link"];
					
					PRINT "<div class='file File_Info_$Id' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'><a href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Files['item_thumbnail']."'><br>$fileName</a> <br>
					<div class='file_option' id='option_$Id'>
						<span title='View the full details of this file' onclick='process_item(\"edit\", \"$Uid\", \"FILE\", \"".$session->userdata(":lifeID")."\");' class='btn btn-primary'><i class='icon-edit'></i></span>
						<span title='Download this file' class='btn btn-success'><a style='color:#fff' href='".$config->base_url()."Download/$DLink' target='_blank'><i class='icon-download'></i></a></span>
						<span title='Add File to Share List' onclick='add_share_item(\"$Uid\",\"$fileName\");' class='btn btn-warning'><i class='icon-plus'></i></span>
						<span onclick='process_item(\"delete\", \"$Id\", \"FILE\", \"".$session->userdata(":lifeID")."\");' class='btn btn-danger'><i class='icon-trash'></i></span>
					</div>
					</div>";
				}
			}
			?>
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
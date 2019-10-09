<?php
$PAGETITLE = "Dashboard";
REQUIRE "TemplateHeader.php";
GLOBAL $notices;

#INITIALIZING
$NO_ITEM = TRUE;
#FETCH ALL FOLDERS IN THE DATABASE THAT 
$listFolders = $DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FOLDER' AND item_status='1' AND item_deleted='0' AND item_parent_id='0' ORDER BY ID DESC LIMIT 8");
$listFiles = $DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_parent_id='0' ORDER BY ID DESC LIMIT 24");
#RUN THE QUERY FOR THE MEDIA INFORMATION
$mediaQuery = $DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_ext IN ('mp3','wav','midi','rm','ra','ram','pls','m3u','m3u') ORDER BY ID DESC LIMIT 10");
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
		<button type="button" style="cursor:pointer;" class="close alert alert-danger" onclick="return remove_system_notices('pass_request', '<?php print $admin_user->return_username(); ?>', 'request_change_alert_div')" style="font-size:13px">x</button>
	</div>
	<?php } ?>
</div>
<!--Action boxes-->
<div class="container-fluid">
<div class="quick-actions_homepage">
  <ul class="quick-actions">
	<li class="bg_lb"> <a href="<?php print $config->base_url(); ?>Dashboard"> <i class="icon-dashboard"></i> My Dashboard </a> </li>
	<li class="bg_ly"> <a href="<?php print $config->base_url(); ?>ItemsStream"> <i class="icon-folder-open"></i> <span class="label label-important"><?PHP PRINT count($DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FOLDER' AND item_status='1' AND item_deleted='0' ORDER BY ID DESC"))+count($DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' ORDER BY ID DESC")); ?></span>Files & Folders</a> </li>
	<li class="bg_lg"> <a href="<?php print $config->base_url(); ?>Shared"> <i class="icon-share"></i> Shared Files</a> </li>
	<li class="bg_ls"> <a href="<?php print $config->base_url(); ?>Messages"> <i class="icon-info-sign"></i> Messages</a> </li>
	<li class="bg_lo"> <a href="<?php print $config->base_url(); ?>Users"> <i class="icon icon-group"></i> Users</a> </li>
	<li class="label label-inverse"> <a href="<?php print $config->base_url(); ?>Offices"> <i class="icon-sitemap"></i> System Settings</a> </li>
  </ul>
</div>
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>Site Analytics</h5>
	</div>
	<div class="widget-content">
	  <div class="row-fluid">
		<div class="<?php PRINT (COUNT($mediaQuery) > 0) ? "span7": "span12"; ?>">
			<!-- LISTING ALL FOLDERS OF THE USER -->
			<?PHP
			#COUNT THE NUMBER OF FOLDERS 
			$NO_ITEM =((COUNT($listFiles) < 1) AND (COUNT($listFolders) < 1)) ? TRUE : FALSE;
			
			IF(!$NO_ITEM) {
				#USING THE FOREACH LOOP 
				FOREACH($listFolders AS $Folders) {
					$file_ext = $Folders["item_ext"];
					$fileName = $Folders["item_title"];
					$Id = $Folders["id"];
					$Uid = $Folders["item_unique_id"];
						
					PRINT "<div class='file File_Info_$Id' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'><a href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Folders['item_thumbnail']."'><br>$fileName</a> <br>
						<div class='file_option' id='option_$Id'>
							<span onclick='process_item(\"delete\", \"$Id\", \"FOLDER\", \"".$session->userdata(UID_SESS_ID)."\");' class='btn btn-danger'><i class='icon-trash'></i> Delete Folder</span>
						</div>
						</div>";
				}
				
				FOREACH($listFiles AS $Files) {
					$file_ext = $Files["item_ext"];
					$fileName = $Files["item_title"];
					$Id = $Files["id"];
					$Uid = $Files["item_unique_id"];
					$DLink = $Files["item_download_link"];
					
					PRINT "<div class='file File_Info_$Id' onmouseout='hide_item(\"$Id\")' onmouseover='show_item(\"$Id\")'>";
					
					PRINT "<a title='Click to view full details of this file.' href='".$config->base_url()."ItemStream/Id/$Uid'><img src='".$config->base_url().$Files['item_thumbnail']."'><br>$fileName</a><br>";
					
					PRINT "<div class='file_option' id='option_$Id'>";
					
					// CONFIRM THAT THE FILE IS A ZIP FILE
					IF($file_ext == "zip") {
						PRINT "<span value='$Id' title='Extract File' data-toggle=\"modal\" data-target=\"#extractZippedItem\" class='btn btn-primary extract_zip'><i class='icon-bookmark'></i></span> ";
					} ELSE {
						// CHECK IF THE FILE IS PART OF THE THE LIST OF EDITABLE FILES
						IF(IN_ARRAY(".".$file_ext, config_item("editable_ext"))) {
							PRINT "<span title='Edit the contents of this file.' class='btn btn-primary'><a style='color:#fff' href='".$config->base_url()."ItemStream/Id/$Uid/Edit'><i class='icon-edit'></i></a></span> ";
						} ELSE {							
							PRINT "<span title='Click to view the full contents of this file.' onclick='process_item(\"edit\", \"$Uid\", \"FILE\", \"".$session->userdata(UID_SESS_ID)."\");' class='btn btn-primary'><i class='icon-eye-open'></i></span> ";
						}
					}
					PRINT "<span title='Download this file' class='btn btn-success'><a style='color:#fff' href='".$config->base_url()."Download/$DLink' target='_blank'><i class='icon-download'></i></a></span> ";
					PRINT "<span title='Add File to Share List' onclick='add_share_item(\"$Uid\",\"$fileName\");' class='btn btn-warning'><i class='icon-plus'></i></span> ";
					PRINT "<span title='Click to delete this file.' onclick='process_item(\"delete\", \"$Id\", \"FILE\", \"".$session->userdata(UID_SESS_ID)."\");' class='btn btn-danger'><i class='icon-trash'></i></span>";
					PRINT "</div></div>";
				}
			}
			?>
		</div>
		<?PHP IF(COUNT($mediaQuery) > 0) { ?>
		<div class="span5">
			<div>
			<div class="widget-title"> <span class="icon"><i class="icon-music"></i></span>
				<h5>UPLOADED AUDIO FILES</h5>
			</div>
			<script type="text/javascript">
			//<![CDATA[
			$(document).ready(function(){
			
			new jPlayerPlaylist({
				jPlayer: "#jquery_jplayer_2",
				cssSelectorAncestor: "#jp_container_2"
			}, [
				<?php 
				$audioPlayerList = $mediaQuery;
				
				FOREACH($audioPlayerList as $audioPlayerFile) {
				?>
				{
					title:"<?php print $audioPlayerFile["item_title"]; ?>",
					mp3:"<?php print $config->base_url().config_item('upload_path').$audioPlayerFile["item_unique_id"]; ?>",
					oga:"<?php print $config->base_url().config_item('upload_path').$audioPlayerFile["item_unique_id"]; ?>"
				},
				<?PHP } ?>
			], {
				size: {
					width: "440px",
					cssClass: "jp-audio-360p"
				},
				swfPath: "<?php print $config->base_url(); ?>assets/js/dist/jplayer",
				supplied: "oga, mp3",
				wmode: "window",
				useStateClassSkin: true,
				autoBlur: false,
				smoothPlayBar: true,
				keyEnabled: true
			});
		});


		//]]>
		</script>

			<div id="jquery_jplayer_2" class="jp-jplayer"></div>
				<div id="jp_container_2" class="jp-audio" role="application" aria-label="media player">
				<div class="jp-type-playlist">
					<div class="jp-gui jp-interface">
						<div class="jp-controls">
							<button class="jp-previous" role="button" tabindex="0">previous</button>
							<button class="jp-play" role="button" tabindex="0">play</button>
							<button class="jp-next" role="button" tabindex="0">next</button>
							<button class="jp-stop" role="button" tabindex="0">stop</button>
						</div>
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
						<div class="jp-volume-controls">
							<button class="jp-mute" role="button" tabindex="0">mute</button>
							<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
						</div>
						<div class="jp-time-holder">
							<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
							<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
						</div>
						<div class="jp-toggles">
							<button class="jp-repeat" role="button" tabindex="0">repeat</button>
							<button class="jp-shuffle" role="button" tabindex="0">shuffle</button>
						</div>
					</div>
					<div class="jp-playlist">
						<ul>
							<li>&nbsp;</li>
						</ul>
					</div>
					<div class="jp-no-solution">
						<span>Update Required</span>
						To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
					</div>
				</div>
				</div>
			</div>
		</div>
		<?PHP } ?>
	  </div>
	</div>
  </div>
</div>
</div>
<!--End-Chart-box-->
<?php 
REQUIRE "TemplateFooter.php";
?>
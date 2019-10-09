<?php
$PAGETITLE = "Media Player";
REQUIRE "TemplateHeader.php";
GLOBAL $notices;
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
<div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <i class="icon-list"></i> <?php print $PAGETITLE; ?></div>
</div>
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>Media Player</h5>
	</div>
	<div class="widget-content" >
	  <div class="row-fluid">
		<div class="span6">
			
			<?PHP IF(COUNT($DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_ext IN ('mp4','3gp', 'mpg','mpeg','mov','avi','swf') ORDER BY ID DESC LIMIT 1000")) > 0) { ?>
			<div>
			<div class="widget-title"> <span class="icon"><i class="icon-music"></i></span>
				<h5>UPLOADED VIDEO FILES</h5>
			</div>
			<script type="text/javascript">
			//<![CDATA[
			$(document).ready(function(){
			
			new jPlayerPlaylist({
				jPlayer: "#jquery_jplayer_1",
				cssSelectorAncestor: "#jp_container_1"
			}, [
				<?php 
				$videoPlayerList = $DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_ext IN ('mp4','3gp','mpg','mpeg','mov','avi','swf') ORDER BY ID DESC LIMIT 1000");
				
				FOREACH($videoPlayerList as $videoPlayerFile) {
				?>
				{
					title:"<?php print $videoPlayerFile["item_title"]; ?>",
					m4v:"<?php print $config->base_url().config_item('upload_path').$videoPlayerFile["item_unique_id"]; ?>",
					oga:"<?php print $config->base_url().config_item('upload_path').$videoPlayerFile["item_unique_id"]; ?>"
				},
				<?PHP } ?>
			], {
				size: {
					width: "640px",
					height: "460px",
					cssClass: "jp-video-360p"
				},
				swfPath: "<?php print $config->base_url(); ?>assets/js/dist/jplayer",
				supplied: "webmv, ogv, m4v",
				useStateClassSkin: true,
				autoBlur: false,
				smoothPlayBar: true,
				keyEnabled: true
			});
		});
		//]]>
		</script>

			<div id="jp_container_1" class="jp-video jp-video-270p" role="application" aria-label="media player">
				<div class="jp-type-playlist">
					<div id="jquery_jplayer_1" class="jp-jplayer"></div>
					<div class="jp-gui">
						<div class="jp-video-play">
							<button class="jp-video-play-icon" role="button" tabindex="0">play</button>
						</div>
						<div class="jp-interface">
							<div class="jp-progress">
								<div class="jp-seek-bar">
									<div class="jp-play-bar"></div>
								</div>
							</div>
							<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
							<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
							<div class="jp-controls-holder">
								<div class="jp-controls">
									<button class="jp-previous" role="button" tabindex="0">previous</button>
									<button class="jp-play" role="button" tabindex="0">play</button>
									<button class="jp-next" role="button" tabindex="0">next</button>
									<button class="jp-stop" role="button" tabindex="0">stop</button>
								</div>
								<div class="jp-volume-controls">
									<button class="jp-mute" role="button" tabindex="0">mute</button>
									<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
									<div class="jp-volume-bar">
										<div class="jp-volume-bar-value"></div>
									</div>
								</div>
								<div class="jp-toggles">
									<button class="jp-repeat" role="button" tabindex="0">repeat</button>
									<button class="jp-shuffle" role="button" tabindex="0">shuffle</button>
									<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
								</div>
							</div>
							<div class="jp-details">
								<div class="jp-title" aria-label="title">&nbsp;</div>
							</div>
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
			<?PHP } ?>
			
		</div>
		<div class="span6">
			<?PHP IF(COUNT($DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_ext IN ('mp3','wav','midi','rm','ra','ram','pls','m3u','m3u') ORDER BY ID DESC LIMIT 1000")) > 0) { ?>
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
				$audioPlayerList = $DB->query("SELECT * FROM _item_listing WHERE (user_id='".$session->userdata(UID_SESS_ID)."' OR item_users LIKE '%/".$admin_user->return_username()."/%') AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_ext IN ('mp3','wav','midi','rm','ra','ram','pls','m3u','m3u') ORDER BY ID DESC LIMIT 1000");
				
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
			<?PHP } ?>
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
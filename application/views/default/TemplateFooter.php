<?php GLOBAL $config; ?><!--FOOTER SECTION-->

</div>
<div class="row-fluid">
  <div id="footer" class="span12"> <?php print date('Y'); ?> &copy; <?php print config_item('site_name'); ?>. Brought to you by <a href="https://github.com/emmallob14"><?php print config_item('developer'); ?></a> </div>
</div>

<!--end-Footer-part-->
<link href="<?php print $config->base_url(); ?>assets/css/jquery.dm-uploader.min.css" rel="stylesheet">
<?php if(strtolower($SITEURL[0]) == "upload") { ?>
<link href="<?php print $config->base_url(); ?>assets/css/styles.css" rel="stylesheet"> 
<?php } ?>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("dashboard"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.peity.min.js"></script>
<?PHP } ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.uniform.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/select2.min.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/matrix.dashboard.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/jquery.gritter.min.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("dashboard"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.interface.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/matrix.chat.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/jquery.wizard.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/matrix.popover.js"></script>  
<script src="<?php print $config->base_url(); ?>assets/js/matrix.tables.js"></script>
<?PHP } ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.validate.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/matrix.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.tables.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.script.js"></script>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("folder"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.form_common.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/wysihtml5-0.3.0.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/jquery.peity.min.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/bootstrap-wysihtml5.js"></script> 
<script>
	$('.textarea_editor').wysihtml5();
</script>
<?PHP } ?>
<?php if(in_array(strtolower($SITEURL[0]), array("shared"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.shared.js"></script>
<?php } ?>
<?php if(in_array(strtolower($SITEURL[0]), array("messages")) AND $MESSAGE_FOUND) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.chat.js"></script>
<?php } ?>
<?php if(strtolower($SITEURL[0]) == "upload") { ?>
<script src="<?php print $config->base_url(); ?>assets/js/upload/jquery.dm-uploader.min.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/upload/ui.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/upload/config.js"></script>
<!-- File item template -->
<script type="text/html" id="files-template">
  <li class="media">
	<div class="media-body mb-1">
	  <p class="mb-2">
		<strong>%%filename%%</strong> - Status: <span class="text-muted">Waiting</span>
	  </p>
	  <div class="progress mb-2">
		<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
		  role="progressbar"
		  style="width: 0%;" 
		  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
		</div>
	  </div>
	  <hr class="mb-1"/>
	</div>
  </li>
</script>
<!-- Debug item template -->
<script type="text/html" id="debug-template">
  <li class="list-group-item text-%%color%%"><strong>%%date%%</strong>: %%message%%</li>
</script>
<?php } ?>
<?php if(strtolower($SITEURL[0]) == "dashboard") { ?>
<script>
function remove_system_notices(type, item_id, alert_div) {
	if(confirm("Are you sure you want to remove this notification?")) {
		$.ajax({
			type: "POST",
			data: "remove_notice&type="+type+"&item_id="+item_id,
			url: "<?php print $config->base_url(); ?>doProcess/doNotification",
			success:function(response) {
				$("#"+alert_div).slideUp();
			}
		});
	}
}
</script>
<?php } ?>
</body>
</html>
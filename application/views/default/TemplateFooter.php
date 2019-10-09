<?php GLOBAL $config, $admin_user; ?><!--FOOTER SECTION-->

</div>
<div class="row-fluid">
  <div id="footer" class="span12"> <?php print date('Y'); ?> &copy; <?php print config_item('site_name'); ?>. Brought to you by <a href="https://github.com/emmallob14"><?php print config_item('developer'); ?></a> </div>
</div>
<div class="modal" tabindex="-1" style="width:700px;" role="dialog" id="searchForm" style="overflow-y:auto">
	<div class="" id="container">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-search"></i> Search Results</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">					
					  <div class="alert alert-block alert-success">Displaying search results for the <span class="search_term"></span></div>
					  <div class="widget-title"> <span class="icon"> <i class="icon-file"></i> </span>
						<h5>Search Results</h5>
					  </div>
					  <div class="widget-content nopadding">
						<div class="chast-content">
						  <form method="post" id="search_Item" action="<?php print $config->base_url(); ?>doSearch/doReturnResults" autocomplete="Off">
						  <div class="chat-message well">
							<button type="submit" class="btn btn-success">Search</button>
							<span class="input-box">
							<input placeholder="Enter search term" type="text" name="search-box" id="search-box" class="search-box" />
							</span>
						  </div>
						  </form>
						</div>
						<ul class="recent-posts search_results">
						  <li>
							<button class="btn btn-warning btn-mini">View All</button>
						  </li>
						</ul>
					  </div>
					
					<br clear="both">
					<div style="border:0px solid;" id=""></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1" style="width:600px;" role="dialog" id="createNewItem">
	<div class="" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-plus"></i> Create New Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">
				  <form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doFolderFile/addFolderFile" autocomplete="Off" name="addFolderFile" id="addFolderFile" novalidate="novalidate">
					<div class="control-group">
					  <label class="control-label">Select Item Type</label>
					  <div class="controls">
						<select style="height:;padding-top:5px;width:350px" class="form-control" id="item_type" name="item_type">
							<option value="FOLDER">Create New Folder</option>
							<option value="FILE">Create New File</option>
						</select>
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label">Select Parent Folder</label>
					  <div class="controls reload_folders">
						<select style="height:;padding-top:5px;width:350px" class="form-control" id="parent_folder" name="parent_folder">
							<option value="0">Root Folder</option>
							<?php $directory->display_folders(0, 1, $session->userdata(ROOT_FOLDER)); ?>
						</select>
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label">Item Name</label>
					  <div class="controls">
						<input placeholder="Enter the Item name" style="height:;padding-top:5px;width:350px" type="text" name="item_name" id="item_name" />
					  </div>
					</div>
					<div class="form-actions" align="center">
					  <button type="submit" id="submitButton" class="btn btn-success"><i class="icon icon-save"></i> Add New Item</button>
					</div>
				  </form>
				</div>
				<div id="item_loading_div"><div class="alert alert-warning alert-md alert-block">Please wait <img src="<?php print $config->base_url(); ?>assets/images/loadings.gif" align="absmiddle" /></div></div>
				<div id="item_result_div"></div>
			</div>

			<div class="modal-footer" align="center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php IF(IN_ARRAY(STRTOLOWER($SITEURL[0]), ARRAY("itemstream")) AND $FILE_FOUND) { ?>
<?php IF(IN_ARRAY(".".$ITEM_EXT, config_item("editable_ext"))) { ?>
<div class="modal" tabindex="-1" style="width:1000px;" role="dialog" id="editItem">
	<div class="" id="container">
		<div class="modal-content">
			<div class="modal-body">
				<div class="widget-dcontent ">
					<form id="doSaveFile" action="<?php print $config->base_url(); ?>doEdit/doSaveFile" method="POST">
						<div class="controls">
						  <textarea id="content_area" name="content_area" style="font-size:14px;width:100%;height:450px;" placeholder="Enter text ..."><?php print html_entity_decode(file_get_contents(config_item('upload_path').$item_slug)); ?></textarea>
						</div>
						<div class="controls">
							<button type="submit" class="btn btn-success save_file"><i class="icon icon-save"></i> Save</button>
							<button type="button" class="btn btn-primary reopen_file"><i class="icon icon-refresh"></i> Reopen</button>
							<button type="button" data-dismiss="modal" class="btn btn-danger close_file"><i class="icon icon-folder-close"></i> Close</button>
						</div>
					</form>
					<br clear="both">
					<div style="border:0px solid;" id="saving_result"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?php IF(IN_ARRAY($ITEM_EXT, config_item("microsoft_docs"))) { ?>
<div class="modal" tabindex="-1" style="width:100%;height:90%;" role="dialog" id="viewItem">
	<div class="" id="container">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-eye-open"></i> Viewing Document</h5>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">					
					<iframe width="100%" height="600px" src="https://docs.google.com/viewer?url=<?php print $config->base_url(); ?>assets/uploads/<?php print $item_slug; ?>&embedded=true"></iframe>
				</div>
			</div>
			<div class="modal-footer" align="center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<div class="modal" tabindex="-1" style="width:600px;" role="dialog" id="moveItem">
	<div class="" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-move"></i> Move Item to Different Location</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">
				  <form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doFolderFile/doMoveItem" autocomplete="Off" name="doMoveItem" id="doMoveItem" novalidate="novalidate">
					<div class="control-group">
					  <label class="control-label">Select Destination Folder</label>
					  <div class="controls reload_folders">
						<select style="height:;padding-top:5px;width:350px" class="form-control" id="parent_folder" name="parent_folder">
							<option value="0">Root Folder</option>
							<?php $directory->display_folders(0, 1, $session->userdata(ROOT_FOLDER)); ?>
						</select>
					  </div>
					</div>
					<div class="form-actions" align="center">
						<input name="Action" value="doMoveItem" type="hidden">
						<button type="submit" id="submitButton" class="btn btn-success"><i class="icon icon-save"></i> Move File / Folder</button>
					</div>
				  </form>
				</div>
				<div id="item_move_div"></div>
			</div>

			<div class="modal-footer" align="center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1" style="width:600px;" role="dialog" id="addUserToFile">
	<div class="" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-move"></i> Add users to access this <?php print $ITEM_TYPE; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">
				  <form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doFolderFile/doAddUser/doAdd" autocomplete="Off" name="doAddUser" id="doAddUser" novalidate="novalidate">
					<div class="control-group">
					  <label class="control-label">Select Users</label>
					  <div class="controls">
						<select style="height:;padding-top:5px;width:350px" class="form-control" id="user_id" name="user_id">
							<option value="0">SELECT USER TO ADD TO LIST</option>
							<?php PRINT $admin_user->list_office_users()->list_users; ?>
						</select>
					  </div>
					</div>
					<div class="form-actions" align="center">
						<input name="Action" value="doAddUser" type="hidden">
						<input name="item_id" value="<?php print base64_encode($item_slug); ?>" type="hidden">
						<button type="submit" id="submitButton" class="btn btn-success"><i class="icon icon-save"></i> Add User to List</button>
					</div>
				  </form>
				</div>
				<div id="add_users_to_list"></div>
			</div>
			<div class="modal-footer" align="center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1"  style="width:600px;" role="dialog" id="renameItem">
	<div class="" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-edit"></i> Rename Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">
				  <form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doUpdate/changeName" autocomplete="Off" name="editForm" id="editForm" novalidate="novalidate">
					<div class="control-group">
					<label class="control-label">Item New Name</label>
					<div class="controls">
						<input type="text" value="<?php print $ITEM_TITLE; ?>.<?php print $ITEM_EXT; ?>" name="item_name" id="item_name" />
					</div>
					</div>
					<div class="control-group">
					<label class="control-label">Item Description</label>
					<div class="controls">
						<textarea style="width:300px" name="desc" class="desc span12" rows="2" placeholder="Enter text ..."><?php print $ITEM_DESCRIPTION; ?></textarea>
					</div>
					</div>
					<div class="form-actions" align="center">
						<input type="hidden" name="itemName" id="itemName">
						<input type="hidden" name="itemId" id="itemId" value="<?php print $item_id; ?>">
						<input type="hidden" value="<?php print current_url(); ?>" name="href">
						<input type="submit" id="submitButton" value="Edit File" class="btn btn-success">
						<input data-dismiss="modal" type="reset" id="cancelButton" value="Cancel" class="btn btn-danger">
					</div>
				</form>
				</div>
				<div id="result_div"></div>
				<div id="loading_div"><div style="width:100%" class="alert alert-warning alert-md alert-block">Please wait <img src="<?php print $config->base_url(); ?>assets/images/loadings.gif" align="absmiddle" /></div></div>
			</div>

			<div class="modal-footer" align="center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php PRINT confirm_url_id(3, 'Edit') ? "<script>$('#editItem').modal('show');</script>" : NULL; ?>
<?php } ?>
<div class="modal" tabindex="-1" style="width:600px;" role="dialog" id="extractZippedItem">
	<div class="" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="icon icon-random"></i> Extract Zipped Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="widget-content nopadding">
				  <form class="form-horizontal" method="POST" action="<?php print $config->base_url(); ?>doExtract/extractZip" autocomplete="Off" id="extractZip" novalidate="novalidate">
					<div class="control-group">
					  <label class="control-label">Select Folder To Extract Content</label>
					  <div class="controls reload_folders">
						<select style="height:;padding-top:5px;width:350px" class="form-control" id="extract_folder" name="extract_folder">
							<option value="0">Root Folder</option>
							<?php $directory->display_folders(0, 1, $session->userdata(ROOT_FOLDER)); ?>
						</select>
					  </div>
					</div>
					<div class="form-actions" align="center">
					  <button type="submit" id="submitButton" class="btn btn-success"><i class="icon icon-save"></i> Extract Content</button>
					  <input id="zipped_item" name="zipped_item" readonly type="hidden">
					</div>
				  </form>
				</div>
				<div id="zipped_loading_div"><div class="alert alert-warning alert-md alert-block">Please wait <img src="<?php print $config->base_url(); ?>assets/images/loadings.gif" align="absmiddle" /></div></div>
				<div id="zipped_result_div"></div>
			</div>

			<div class="modal-footer" align="center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!--end-Footer-part-->
<?php if(strtolower($SITEURL[0]) == "upload") { ?>
<link href="<?php print $config->base_url(); ?>assets/css/styles.css" rel="stylesheet"> 
<?php } ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.uniform.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.gritter.min.js"></script>
<?php IF(!IN_ARRAY(strtolower($SITEURL[0]), ARRAY("dashboard"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.tables.js"></script>
<?PHP } ?>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("dashboard"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.flot.min.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/jquery.flot.pie.min.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/matrix.charts.js"></script> 
<script src="<?php print $config->base_url(); ?>assets/js/jquery.flot.resize.min.js"></script>
<?PHP } ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.js"></script>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("dashboard"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/jquery.peity.min.js"></script> 
<?PHP } ?>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("itemstream"))) { ?>
<?php IF( $FILE_FOUND) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/editarea/edit_area_full.js"></script>
<script>
	<?php
	switch (strtolower($ITEM_EXT)) {
		case 'txt':
			$cp_lang = 'basic'; break;
		case 'cs':
			$cp_lang = 'csharp'; break;
		case 'css':
			$cp_lang = 'css'; break;
		case 'html':
		case 'htm':
		case 'xhtml':
			$cp_lang = 'html'; break;
		case 'java':
			$cp_lang = 'java'; break;
		case 'js':
			$cp_lang = 'js'; break;
		case 'pl': 
			$cp_lang = 'perl'; break;
		case 'py': 
			$cp_lang = 'python'; break;
		case 'ruby': 
			$cp_lang = 'ruby'; break;
		case 'sql':
			$cp_lang = 'sql'; break;
		case 'vb':
		case 'vbs':
			$cp_lang = 'vb'; break;
		case 'php':
			$cp_lang = 'php'; break;				
		case 'xml':
			$cp_lang = 'xml'; break;
		default: 
			$cp_lang = '';
	}
	?>
	editAreaLoader.init({
		id : "content_area",
		syntax: "<?php print $cp_lang; ?>",
		start_highlight: true,
		allow_resize: "both",
		font_size: 12,
		min_height: 450,
		cursor_position: "auto",
		word_wrap: true,
		toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help",
		syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck,java,coldfusion,perl,ruby,tsql"
	});
</script>
<?PHP } ?>
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
<script>
<?php if(strtolower($SITEURL[0]) == "dashboard") { ?>
function remove_system_notices(type, item_id, alert_div) {
	if(confirm("Are you sure you want to remove this notification?")) {
		$.ajax({
			type: "POST",
			data: "remove_notice&type="+type+"&item_id="+item_id,
			url: "<?php print $config->base_url(); ?>doNotices/doNotification",
			success:function(response) {
				$("#"+alert_div).slideUp();
				alert(response);
			}
		});
	}
}
<?php } ?>
<?php if($admin_user->confirm_super_user()) { ?>
// this section helps to backup the database
$("#backup_data").on('click', function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		data: "backup_system&dataBackup",
		url: <?php print $config->base_url(); ?>"doBackup",
		success:function(response) {
			$('.gritter-item-wrapper').css("display","block");
			$.gritter.add({
				title:	'Backup Notice',
				text:	response,
				sticky: false
			});
		}
	});
});
<?php } ?>
</script>
<?php IF(IN_ARRAY(strtolower($SITEURL[0]), ARRAY("dashboard"))) { ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.dashboard.js"></script>  
<?PHP } ?>
<script src="<?php print $config->base_url(); ?>assets/js/matrix.script.js"></script>
</body>
</html>
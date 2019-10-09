<?php
$PAGETITLE = "Shared Files";
// initializing
GLOBAL $directory;
$FILE_FOUND = FALSE;
$item_id = 0;
$session->unset_userdata('sharedItemId');
// check the url that has been parsed
if(confirm_url_id(1, 'Slug')) {
	if(confirm_url_id(2)) {		
		if(confirm_url_id(3, 'Id')) {			
			if(confirm_url_id(4)) {
				$item_id = xss_clean($SITEURL[4]);
				$item_slug = xss_clean($SITEURL[2]);
				IF($DB->num_rows(
					$DB->where(
					'_shared_listing', '*', 
					ARRAY(
						'shared_slug'=>"='$item_slug'",
						'shared_with'=>"LIKE '%/".$session->userdata(UID_SESS_ID)."/%'",
						'shared_item_id'=>"='$item_id'",
						'shared_deleted'=>"='0'",
				))) == 1) {
					# ASSIGN A TRUE VALUE TO THE USER FOUND 
					$FILE_FOUND = TRUE;
					$PAGETITLE = $directory->item_by_id2('item_title', $item_id);
					$ITEM_TYPE = $directory->item_by_id2('item_type', $item_id);
					$session->set_userdata('sharedItemId', $item_id);
					$session->set_userdata('sharedItemSlug', $item_slug);
					$session->set_userdata('sharedItemUrl', current_url());
					# query the database _shared_listing table for the files that has been shared
					$shared_Files = $DB->query("SELECT * FROM _shared_listing
						WHERE shared_item_id='$item_id'
					");
				}
			}
		}
	}	
}
REQUIRE "TemplateHeader.php";
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <a href="<?php print $config->base_url(); ?>Shared" title="List all shared files folders" class="tip-bottom"> <i class="icon-list"></i> Shared Files / Folders </a> <i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
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
		 <div class="widget-box">          
		  <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<?php if( !$FILE_FOUND ) { ?><h5>List of all shared files</h5><?php } ?>
			<?php if( $FILE_FOUND ) { ?><h5>Details of shared file</h5><?php } ?>
          </div>		  
          <div class="widget-content nopadding">
            <?php if( !$FILE_FOUND ) { ?>
			<table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>ID</th>
				  <th>File Name</th>
                  <th>Shared By</th>
				  <th>Comments</th>
				  <th>Date Shared</th>
				  <th>Expiry Date</th>
				  <th>Action</th>
                </tr>
              </thead>
              <tbody>
				<?php 
				# query the database _shared_listing table for the files that has been shared
				$shared_Files = $DB->query("SELECT * FROM _shared_listing
					WHERE 
					(
						(shared_by='{$session->userdata(UID_SESS_ID)}') 
					OR 
						(shared_with LIKE '%/{$session->userdata(UID_SESS_ID)}/%')
					) AND shared_deleted='0'
				");
				
				# using foreach loop to list the items 
				foreach($shared_Files as $Files) {
				?>
                <tr class="gradeX">
                  <td><?php print $Files["id"]; ?></td>
				  <td><?php print ($Files["shared_many"] == "FALSE") ? $directory->item_by_id2('item_title', $Files["shared_item_id"]) : "<strong>Shared more than one file.</strong>"; ?></td>
                  <td><?php print $admin_user->get_details_by_id($Files["shared_by"])->funame; ?></td>
				  <td><i class="icon icon-comments"></i> <?php print COUNT($DB->query("SELECT * FROM _shared_comments WHERE shared_slug='{$Files["shared_slug"]}'")); ?> comments</td>
                  <td><?php print date("l jS M Y H:iA", strftime($Files["shared_date"])); ?></td>
				  <td><?php print date("l jS M Y H:iA", strftime($Files["shared_expiry"])); ?></td>
				  <td>
				  <a class="btn btn-success" href="<?php print $config->base_url(); ?>Shared/Slug/<?php print $Files["shared_slug"]; ?>/Id/<?php print $Files["shared_item_id"]; ?>" title="View full details of file"><i class="icon icon-eye-open"></i></a>
				  <?php if($Files["shared_expiry"] > time()) { ?>
					  <?php if($Files["shared_status"] == 0) { ?>
						<?php if($Files["shared_by"] == $session->userdata(UID_SESS_ID)) { ?>
							<a href="#" class="btn btn-warning" title="This file shared has been paused, Do you wish to continue sharing?" id="modifyItem" onclick="process_item('start', '<?php print $Files["id"]; ?>', '<?php print $Files["shared_type"]; ?>', '<?php print $Files["shared_by"]; ?>', 'REDIR', '<?php print $config->base_url(); ?>Shared')"><i class="icon icon-play"></i></a>
						<?php } ?>
					  <?php } ?>
					  <?php if($Files["shared_type"] == 'Shared_File') { ?>
						<?php if($Files["download_file"] == 'ALLOW') { ?>
							<?php if($Files["shared_status"]) { ?>
								<a target="_blank" title="Download this file" class="btn btn-primary" href="<?php print $config->base_url().'Download/'.$directory->item_by_id2('item_download_link', $Files["shared_item_id"]); ?>/Shared/<?php print $Files["download_link"]; ?>"><i class="icon-download"></i></a>
							<?php } ?>
						<?php } ?>
					  <?php } ?>
					  <?php if($Files["shared_by"] == $session->userdata(UID_SESS_ID)) { ?>
						<?php if($Files["shared_status"] == 1) { ?>
							<a href="#" class="btn btn-warning" title="Do you wish to discontinue sharing this file(s)?." id="modifyItem" onclick="process_item('stop', '<?php print $Files["id"]; ?>', '<?php print $Files["shared_type"]; ?>', '<?php print $Files["shared_by"]; ?>', 'REDIR', '<?php print $config->base_url(); ?>Shared')"><i class="icon icon-stop"></i></a>
						<?php } ?>
						<a href="#" class="btn btn-danger" title="Delete this file from the list of shared items." id="deleteItem" onclick="process_item('delete', '<?php print $Files["id"]; ?>', '<?php print $Files["shared_type"]; ?>', '<?php print $Files["shared_by"]; ?>', 'REDIR', '<?php print $config->base_url(); ?>Shared')"><i class="icon icon-trash"></i></a>
					  <?php } ?>
				  <?php } else { ?>
				  <span class="btn btn-danger">SHARING EXPIRED</span>
				  <?php } ?>
				  </td>
                </tr>
				<?php } ?>
			</table>
			<?php } ?>
			
			<?php if( $FILE_FOUND ) { ?>
			<div class="span4">
				<?php foreach($shared_Files as $Files) { ?>
					<div id="drag-and-drop-zone" class="dm-uploader p-5" align="center">
						<?php if($Files["shared_many"] == "FALSE") { ?>
						<a>
							<img src="<?php print $config->base_url().$directory->item_by_id2('item_thumbnail', $item_id); ?>" width="150px;" alt="">
						</a>
						<?php } ?>
					</div>
				<?php } ?>
				
				<div class="card h-100">
					<div class="card-header">
					  <strong>ITEM DETAILS</strong>
					</div>
					<?php foreach($shared_Files as $Files) { ?>
					<?php if($Files["shared_many"] == "FALSE") { ?>
					<ul class="list-unstyled p-2 d-flex flex-column col" id="files" style="min-height:200px;max-height:400px;overflow:scroll;">
					  <li><strong>ITEM NAME: </strong> <span class='item_name'><?php print $directory->item_by_id2('item_title', $item_id); ?></span></li>
					  <li><strong>ITEM SIZE: </strong> <?php print $directory->item_by_id2('item_size', $item_id); ?></li>
					  <li><strong>ITEM TYPE: </strong> <?php print $ITEM_TYPE; ?></li>
					  <li><strong>ITEM EXT: </strong> <?php print $directory->item_by_id('item_ext', $item_id); ?></li>
					  <li><strong>ITEM DESCRIPTION: </strong> <?php print $directory->item_by_id2('item_description', $item_id); ?></li>
					  <li><strong>DATE UPLOADED: </strong> <?php print $directory->item_by_id2('date_added', $item_id); ?></li>
					  <li><strong>UPLOADED BY: </strong> <?php print $directory->item_by_id2('item_users', $item_id); ?></li>
					  <li><strong>DOWNLOADS: </strong> <?php print $directory->item_by_id2('item_downloads', $item_id); ?></li>
					  <li><?php if($directory->item_by_id2('user_id', $item_id) == $session->userdata(UID_SESS_ID)) { ?>
						<span class="span6 pull-left"><a class="btn btn-success" style="color:#fff" href="<?php print $config->base_url(); ?>ItemStream/Id/<?php print $directory->item_by_id2('item_unique_id', $item_id); ?>/Edit">EDIT FILE</a></span>
					  <?php } ?>
					  <?php if($Files["shared_expiry"] > time()) { ?>
					  <?php if($Files["download_file"] == 'ALLOW') { ?>
						<?php if($Files["shared_status"]) { ?>
							<span class="span6 pull-right"><a target="_blank" title="Download this file" class="btn btn-warning" href="<?php print $config->base_url().'Download/'.$directory->item_by_id2('item_download_link', $item_id); ?>/Shared/<?php print $Files["download_link"]; ?>"><i class="icon-download"></i> DOWNLOAD</a></span></li>
						<?php } ?>
					  <?php } ?>
					  <?php } ?>
					  </ul>
					  <?php } ELSE { ?>
					  
						<table class="table table-bordered">
						  <thead>
							<tr>
							  <th>File Name</th>
							  <th>File Size</th>
							  <th>File Type</th>
							  <th width="40%">Description</th>
							</tr>
						  </thead>
						  <tbody>
							<?PHP
							$shareItemList = $DB->query("SELECT * FROM _shared_listing_detail WHERE shared_slug='$item_slug'");
							FOREACH($shareItemList AS $ItemList) {
								PRINT "<tr class='shared_list_{$ItemList["shared_item_id"]}'>";
								PRINT "<td>".$directory->item_by_id2('item_title', $ItemList["shared_item_id"])."<br>";
								
								IF($session->userdata(UID_SESS_ID) == $Files["shared_by"]) {
									PRINT "<a title=\"View this file contents\" class=\"btn btn-warning\" href=\"{$config->base_url()}ItemStream/Id/".$directory->item_by_id2('item_unique_id', $ItemList["shared_item_id"])."\"><i class=\"icon-eye-open\"> </i></a>&nbsp;";
								} ELSE {
									PRINT "<a title=\"View this file contents\" class=\"btn btn-warning\" href=\"https://docs.google.com/viewer?url={$config->base_url()}assets/uploads/".$directory->item_by_id2('item_unique_id', $ItemList["shared_item_id"])."&embedded=true\" target=\"_blank\"><i class=\"icon-eye-open\"> </i></a>&nbsp;";
								}
								
								IF($Files["shared_expiry"] > time()) {
									IF($Files["download_file"] == 'ALLOW') {
										PRINT "<a target=\"_blank\" title=\"Download this file\" class=\"btn btn-primary\" href=\"{$config->base_url()}Download/{$directory->item_by_id2('item_download_link', $ItemList["shared_item_slug"])}/Shared/{$Files["download_link"]}\"><i class=\"icon-download\"> </i></a>&nbsp;";
									}
								}
								
								// TO BE WORKED ON LATER ON IN FUTURE
								//IF($Files["shared_expiry"] > time()) {
									//IF($Files["replace_file"] == 'ALLOW') {
										//PRINT "<a title=\"Replace this file\" class=\"btn btn-warning\" href=\"{$config->base_url()}Upload/Replace/".base64_encode($directory->item_by_id2('id', $ItemList["shared_item_id"]))."/{$ItemList["shared_item_id"]}\"><i class=\"icon-upload\"> </i></a>&nbsp;";
								//	}
								//}
								
								PRINT "</td>";
								PRINT "<td>".$directory->item_by_id2('item_size', $ItemList["shared_item_id"])."</td>";
								PRINT "<td>".$directory->item_by_id2('item_ext', $ItemList["shared_item_id"])."</td>";
								PRINT "<td>".$directory->item_by_id2('item_description', $ItemList["shared_item_id"])."</td>";
								PRINT "</tr>";
							}
							?>
						  </tbody>
						</table>
					<?PHP } ?>
					
					<?php if($Files["shared_many"] == "FALSE") { ?>
						<?php if($Files["replace_file"] == "ALLOW") { ?>
							<?php if($Files["shared_expiry"] > time()) { ?>
								<?php if($Files["shared_status"]) { ?>
									<span class="btn btn-primary"><a style="color:#fff" href="<?php print $config->base_url(); ?>Upload/Replace/<?php print base64_encode($item_id); ?>/<?php print $item_slug; ?>">REPLACE THIS FILE?</a></span>
								<?php } ?>
							<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
			<div class="span8">
				<div id="drag-and-drop-zone" class="dm-uploader p-5" align="center">
					<table class="table table-bordered ">
					  <thead>
						<tr>
						  <th>SHARED BY</th>
						  <th>DATE AND TIME SHARED</th>
						  <th>EXPIRY DATE AND TIME</th>
						  <th>SHARING STATUS</th>
						</tr>
					  </thead>
					  <tbody>
						<?php 
						# using foreach loop to list the items 
						foreach($shared_Files as $Files) {
						?>
						<tr class="gradeX">
						<td class="alert alert-warning"><?php print $admin_user->get_details_by_id($Files["shared_by"])->funame; ?></td>
						<td class="alert alert-info"><?php print date("l jS M Y H:iA", strftime($Files["shared_date"])); ?></td>
						<td class="alert alert-success">
							<?php if($Files["shared_expiry"] > time()) { ?>
								<?php print date("l jS M Y H:iA", strftime($Files["shared_expiry"])); ?>
							<?php } else { ?>
							<span class="btn btn-danger">SHARING EXPIRED</span>
							<?php } ?>
						</td>
						<td>
							<?php PRINT ($Files["shared_status"]) ? "<span class='btn btn-success'>ACTIVE</span>" : "<span class='btn btn-danger'>STOPPED</span>"; ?>
						</td>
						</tr>
						<tr>
							<td><strong>SHARED WITH</strong></td>
							<td colspan="3">
								<?php 
								// get all the users that the file has been shared with 
								$_shared_users = $Files["shared_with"];
								$_explode_users = explode("/", $_shared_users);
								// using foreach loop to get all users 
								foreach($_explode_users as $users) {
									if(preg_match("/^[0-9]+$/", $users) and ($Files["shared_by"] != $users)) {
										print "<span class='btn btn-primary'><a href='".SITE_URL."/Profile/{$admin_user->get_details_by_id($users)->uname}' style='color:#fff'>".$admin_user->get_details_by_id($users)->funame."</a></span>";
									}
								}
								unset($_exploded_users);
								?>
							</td>
						</tr>
						<tr>
							<td colspan="4" class="alert alert-success">
								<?php print $Files["shared_comments"]; ?>
								<input type="hidden" readonly id="first_MSG" value="<?php print $Files["shared_comments"]; ?>">
							</td>
						</tr>
						</tr>
						<?php } ?>
					</table>
				</div>
				<div class="widget-box widget-chat">
				  <div class="widget-title bg_lb"> <span class="icon"> <i class="icon-comment"></i> </span>
					<h5>Shared File Comments History</h5>
				  </div>
				  <div class="widget-content nopadding collapse in" id="collapseG4">
					<div class="chat-users panel-right2">
					  <div class="panel-title">
						<h5>Shared Users</h5>
					  </div>
					  <div class="comments_responses"></div>
					  <div class="panel-content nopadding">
						<ul class="contact-list">
						<?php
						// get all the users that the file has been shared with 
						foreach($_explode_users as $users) {
							if(preg_match("/^[0-9]+$/", $users) and $users != $session->userdata(UID_SESS_ID)) {
						?>
						<li title="Click to reference <?php print $admin_user->get_details_by_id($users)->funame; ?> in the Chat Comment" id="user-<?php print $users; ?>" class="online"><a class="add_user" onclick="reference_name('<?php print $admin_user->get_details_by_id($users)->funame; ?>');" href="#"><img alt="" src="<?php print $config->base_url(); ?>assets/images/demo/av1.jpg" /> <span><?php print $admin_user->get_details_by_id($users)->funame; ?></span></a></li>
						<?php } } ?>
						</ul>
					  </div>
					</div>
					<div class="chat-content panel-left2">
					  <div class="chat-messages" id="chat-messages">
						<div id="chat-messages-inner"></div>
					  </div>
					  <?php if($Files["shared_expiry"] > time()) { ?>
						<?php if($Files["shared_status"]) { ?>
							<div class="chat-message well">
							<button class="btn btn-success">Send</button>
							<span class="input-box">
							<input type="text" name="msg-box" id="msg-box" />
							</span>
							</div>
						<?php } ?>
					  <?php } ?>
					</div>
					
				  </div>
				</div>
		
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
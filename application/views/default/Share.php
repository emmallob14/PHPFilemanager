<?php
$PAGETITLE = "Share File";
REQUIRE "TemplateHeader.php";
GLOBAL $directory;
// initializing
$FILE_FOUND = FALSE;
$FILE_LIST = FALSE;
$item_id = 0;
// check the url that has been parsed
if(confirm_url_id(1, 'Id')) {
	if(confirm_url_id(2)) {
		$item_id = (int)$SITEURL[2];
		IF($DB->num_rows(
			$DB->where(
			'_item_listing', '*', 
			ARRAY(
				'id'=>"='$item_id'",
				'user_id'=>"='".$session->userdata(":lifeID")."'",
				'item_status'=>"='1'",
				'item_type'=>"='FILE'",
		))) == 1) {
			# ASSIGN A TRUE VALUE TO THE USER FOUND 
			$FILE_FOUND = TRUE;
			$ITEM_TYPE = $directory->item_by_id('item_type', $item_id);
			# CONFIRM THAT THE CURRENT ITEM IS A FOLDER 
			$session->set_userdata('shareItemId', $item_id);
			$session->set_userdata('shareItemType', $ITEM_TYPE);
		}
	}	
}
IF(confirm_url_id(1, 'List') AND $session->userdata('shareItemList')) {
	$FILE_FOUND = TRUE;
	$FILE_LIST = TRUE;
	// unset any single item that has been earmarked to be shared.
	$session->unset_userdata('shareItemId');
}
?>
<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php print $config->base_url(); ?>Dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>  <a href="<?php print $config->base_url(); ?>ItemsStream" title="List all files and folders" class="tip-bottom"> <i class="icon-list"></i> File / Folders Stream </a> <i class="icon-share"></i> <?php print $PAGETITLE; ?></div>
  </div>
<!--End-breadcrumbs-->
<!--Action boxes-->
<div class="container-fluid">
<!--End-Action boxes--> 
<!--Chart-box-->    
<div class="row-fluid">
  <div class="widget-box">
	<div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
	  <h5>File Details</h5>
	</div>
	<div class="widget-content" >
		<?php IF (($session->userdata('shareItemList')) AND COUNT($session->userdata('shareItemList')) > 0) { ?>
		<?PHP IF( !$FILE_LIST ) { ?>
			<span class='alert alert-primary share_listing span12'><?php print "You have <strong>".COUNT($_SESSION["shareItemList"])." files</strong> awaiting to be shared"; ?>. <a href="<?php print $config->base_url(); ?>Share/List"><strong>Click Here</strong></a> to share these files or <strong onclick="empty_item_list();" style="cursor:pointer">empty</strong> the session list.</span><br clear="both">
		<?PHP } ?>
		<?php } ?>
		<div class="row-fluid">
		<?PHP IF( $FILE_FOUND ) { ?>
		<div class="span5">
			<?PHP IF( !$FILE_LIST ) { ?>
			<div id="drag-and-drop-zone" class="dm-uploader p-5" align="center">
				<a>
					<img src="<?php print $config->base_url().$directory->item_by_id('item_thumbnail', $item_id); ?>" width="150px;" alt="">
				</a>
			</div>
			<div class="card h-100">
				<div class="card-header">
				  ITEM Details
				</div>
				<ul class="list-unstyled p-2 d-flex flex-column col" id="files" style="min-height:200px;max-height:200px;overflow:scroll;">
				  <li><strong>ITEM NAME: </strong> <span class='item_name'><?php print $directory->item_by_id('item_title', $item_id); ?></span></li>
				  <li><strong>ITEM SIZE: </strong> <?php print $directory->item_by_id('item_size', $item_id); ?></li>
				  <li><strong>ITEM TYPE: </strong> <?php print $ITEM_TYPE; ?></li>
				  <li><strong>ITEM EXT: </strong> <?php print $directory->item_by_id('item_ext', $item_id); ?></li>
				  <li><strong>ITEM DESCRIPTION: </strong> <?php print $directory->item_by_id('item_description', $item_id); ?></li>
				  <li><strong>DATE UPLOADED: </strong> <?php print $directory->item_by_id('date_added', $item_id); ?></li>
				  <li><strong>UPLOADED BY: </strong> <?php print $directory->item_by_id('item_users', $item_id); ?></li>
				  <li><strong>DOWNLOADS: </strong> <?php print $directory->item_by_id('item_downloads', $item_id); ?></li>
				</ul>
			</div>
			<?PHP } ?>
			<?PHP IF( $FILE_LIST ) { ?>
			<h4>LIST OF FILES THAT ARE AWAITING TO BE SHARED</h4>
			<table class="table table-bordered">
              <thead>
                <tr>
                  <th>File Name</th>
                  <th>File Size</th>
				  <th>File Type</th>
				  <th>Action</th>
                </tr>
              </thead>
              <tbody>
				<?PHP
				FOREACH($session->userdata('shareItemList') AS $key => $value) {
					PRINT "<tr class='shared_list_$value'>";
					PRINT "<td>".$directory->item_by_id('item_title', $value)."</td>";
					PRINT "<td>".$directory->item_by_id('item_size', $value)."</td>";
					PRINT "<td>".$directory->item_by_id('item_ext', $value)."</td>";
					PRINT "<td><i title='Remove this item from the list.' class='btn btn-danger icon icon-trash' onclick=\"empty_item_list(null, '$value');\"> Remove</i> </td>";
					PRINT "</tr>";
				}
				?>
			  </tbody>
			</table>
			<a href="#" id="deleteItem" onclick="empty_item_list('reload')" class='btn btn-danger'><i class='icon icon-trash'></i> EMPTY LIST</a>
			<?PHP } ?>
		</div>
		
		<div class="span7">
		  
		  <div class="row-fluid">
		  <br clear="both">
		  <div class="">
			<h3>YOU HAVE OPTED TO SHARE THIS FILE WITH OTHERS</h3>
			<div class="widget-box">
			  <div class="widget-title">
				<ul class="nav nav-tabs">
				  <li class="active"><a data-toggle="tab" href="#tab1">SEARCH FOR USERS</a></li>
				  <li><a data-toggle="tab" href="#tab2">VIEW ADDED USERS</a></li>
				  <li><a data-toggle="tab" href="#tab3">CONFIRM FILE SHARING</a></li>
				</ul>
			  </div>
			  <div class="widget-content tab-content" style="min-height:300px;">
				<div id="tab1" class="tab-pane active">
					<h5>Search for user</h5>
					<div class="chast-content">
					  <form method="post" id="user_Search" autocomplete="Off">
					  <div class="chat-message well">
						<button type="submit" class="btn btn-success" id="search_User">Search</button>
						<span class="input-box">
						<input type="text" name="msg-box" id="users-box" />
						</span>
					  </div>
					  </form>
					</div>
					<div class="users_Found"></div>
				</div>
				<div id="tab2" class="tab-pane">
				  <div class="users_List">Sorry! You have not yet added any users to the list.</div>
				</div>
				<div id="tab3" class="tab-pane">
					<div class="confirm_Text">Sorry! You have not yet added any users to the list.</div>
					<form method="post" id="share_File">
						<div class="form-group">
							<select style="display:none" class="form-control span6" name="share_Length" id="share_Length">
								<option value="<?PHP PRINT TIME()+(60*60); ?>">KEEP FILE SHARED FOR ONE (1) HOUR</option>
								<option value="<?PHP PRINT TIME()+(60*60*3); ?>">KEEP FILE SHARED FOR THREE (3) HOURS</option>
								<option selected value="<?PHP PRINT TIME()+(60*60*6); ?>">KEEP FILE SHARED FOR SIX (6) HOURS</option>
								<option value="<?PHP PRINT TIME()+(60*60*12); ?>">KEEP FILE SHARED FOR TWELVE (12) HOURS</option>
								<option value="<?PHP PRINT TIME()+(60*60*24); ?>">KEEP FILE SHARED FOR ONE (1) DAY</option>
								<option value="<?PHP PRINT TIME()+(60*60*24*3); ?>">KEEP FILE SHARED FOR THREE (3) DAYS</option>
								<option value="<?PHP PRINT TIME()+(60*60*24*7); ?>">KEEP FILE SHARED FOR ONE (1) WEEK</option>
								<option value="<?PHP PRINT TIME()+(60*60*24*12); ?>">KEEP FILE SHARED FOR TWO (2) WEEKS</option>
								<option value="<?PHP PRINT TIME()+(60*60*24*30); ?>">KEEP FILE SHARED FOR ONE (1) MONTH</option>
								<option value="<?PHP PRINT TIME()+(60*60*24*30*3); ?>">KEEP FILE SHARED FOR THREE (3) MONTHS</option>
								<option value="<?PHP PRINT TIME()+(60*60*24*30*6); ?>">KEEP FILE SHARED FOR SIX (6) MONTHS</option>
							</select>
							<select style="display:none" class="form-control span6" name="replace_permission" id="replace_permission">
								<option value="DONT_ALLOW">DONT ALLOW USERS TO REPLACE THIS FILE<option value="ALLOW">ALLOW USERS TO REPLACE THIS FILE</option>
								</option>
							</select>
							<select style="display:none" class="form-control span6" name="download_permission" id="download_permission">
								<option value="ALLOW">ALLOW USERS TO DOWNLOAD THIS FILE</option>
								<option value="DONT_ALLOW">DONT ALLOW USERS TO DOWNLOAD THIS FILE</option>
							</select>
						</div>
						<div class="form-group">
							<textarea style="display:none" class="form-control span12" id="share_Comments" name="share_Comments" placeholder="Add some comments to this file as you share with others."></textarea>
						</div>
						<input type="hidden" name="Action" value="shareFile"> 
						<input type="submit" style="display:none" id="confirm_Share" class="btn btn-success" value="SHARE FILE WITH SELECTED USERS">
					</form>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		</div>
		<?PHP } ELSE { ?>
		<?PHP show_error('Page Not Found', 'Sorry the page you are trying to view does not exist on this server', 'error_404'); ?>
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
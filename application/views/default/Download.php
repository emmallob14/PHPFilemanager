<?php
#LOAD IMPORTANT FILES
GLOBAL $directory, $session, $DB;
load_helpers('url_helper');
$directory = load_class('directories', 'models');
$ERROR_FOUND = TRUE;
#confirm that a variable was parsed
if(confirm_url_id(1)) {
	$item_id = xss_clean($SITEURL[1]);
		
	IF($DB->num_rows(
		$DB->where(
		'_item_listing', '*', 
		ARRAY(
			'item_unique_id'=>"='$item_id'",
			'user_id'=>"='".$session->userdata(":lifeID")."'",
			'item_status'=>"='1'"
	))) == 1) {
		# ASSIGN A TRUE VALUE TO THE ITEM FOUND ERROR
		$ITEM_EXT = $directory->item_by_id('item_ext', $item_id, 'item_unique_id');
		$ITEM_TITLE = $directory->item_by_id('item_title', $item_id, 'item_unique_id');
		$ERROR_FOUND = FALSE;
		$FILE = $directory->prep_download($item_id, $ITEM_TITLE, $ITEM_EXT)->file_path;
		IF($FILE) {
			$FILE_NAME = $directory->prep_download($item_id, $ITEM_TITLE, $ITEM_EXT)->file_name;
			$directory->force_download($FILE);
		} ELSE {
			$ERROR_FOUND = TRUE;
		}
	}
}
#display the results
if($ERROR_FOUND) {
	show_error('Page Not Found', 'Sorry the File you are trying to Download does not exist on this server', 'error_404');
}
?>
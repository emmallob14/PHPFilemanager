<?php
#REDIRECT THE USER IF NOT LOGGED IN
if(!$admin_user->logged_InControlled()) {
	require "Login.php";
	exit(-1);
}
#LOAD IMPORTANT FILES
GLOBAL $directory, $session, $DB;
load_helpers('url_helper');
$directory = load_class('directories', 'models');
$ERROR_FOUND = TRUE;
// unset the file download path link
$session->unset_userdata("file_download_path");
// confirm that a variable was parsed
IF(confirm_url_id(1)) {
	$download_slug = xss_clean($SITEURL[1]);	
	// confirm that it is the user who is trying to download the specified file
	IF(!confirm_url_id(2)) {
		$QueryString = $DB->where(
			'_item_listing', '*', 
			ARRAY(
				'item_download_link'=>"='$download_slug'",
				'user_id'=>"='".$session->userdata(":lifeID")."'",
				'item_status'=>"='1'"
		));
		IF($DB->num_rows($QueryString) == 1) {
			$link_url = "item_by_id";
		}
	} ELSEIF(confirm_url_id(3) AND confirm_url_id(2, 'Shared')) {
		// get the download link
		$download_link = xss_clean($SITEURL[3]);
		// construct the query string
		$QueryString = $DB->query("
			SELECT
				sh.shared_slug, sh.shared_with, sh.shared_expiry, sh.download_link,
				it.item_download_link, it.item_status, sh.shared_status, sh.shared_deleted
			FROM 
				_item_listing it, _shared_listing sh
			WHERE
				sh.download_link='$download_link' AND
				sh.shared_status='1' AND sh.shared_deleted='0' AND
				sh.shared_expiry > ".TIME()."
				it.item_download_link='$download_slug' AND 
				it.item_status='1'
		");
		IF($DB->num_rows($QueryString) == 1) {
			$link_url = "item_by_id2";
		}
	}
	IF($DB->num_rows($QueryString) == 1) {
		# ASSIGN A TRUE VALUE TO THE ITEM FOUND ERROR
		$ITEM_EXT = $directory->$link_url('item_ext', $download_slug, 'item_download_link');
		$ITEM_TITLE = $directory->$link_url('item_title', $download_slug, 'item_download_link');
		$ITEM_NAME = $directory->$link_url('item_unique_id', $download_slug, 'item_download_link');
		$ERROR_FOUND = FALSE;
		// prepare the file for download
		IF($directory->prep_download($ITEM_NAME, $ITEM_TITLE, $ITEM_EXT)->file_name) {
			// add up to the number of downloads
			$directory->add_download($ITEM_NAME);
			// force the file download from the set session
			$directory->force_download($session->userdata("file_download_path"));
		} ELSE {
			$ERROR_FOUND = TRUE;
		}
	}
}
#display the results
if($ERROR_FOUND) {
	show_error('Page Not Found', 'Sorry the file download link that you are trying to access has expired.', 'error_404');
}
?>
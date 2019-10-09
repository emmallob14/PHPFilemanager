<?php
// ensure this file is being included by a parent file
if( !defined( 'SITE_URL' ) && !defined( 'SITE_DATE_FORMAT' ) ) die( 'Restricted access' );
class Recorder {
	
	
	public function __construct() {
		
		global $DB, $session;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = $session;
		
	}
	
	public function record_event() { }
	
}
?>
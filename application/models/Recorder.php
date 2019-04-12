<?php


class Recorder {
	
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = load_class('session', 'libraries\Session');
		
	}
	
	public function record_event() { }
	
}
?>
<?php 

class Notifications {
	
	public $result;
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = load_class('session', 'libraries\Session');
		$this->folder = load_class('Directories', 'models');
		$this->offices = load_class('offices', 'models');
	}
		
	public function login_attempt($username) {
		
		$this->login_alerts = false;
		
		try {			
			$stmt1 = $this->db->query("select * from _admin where username ='$username'");
			
			if ($this->db->num_rows($stmt1)  > 0) {
				foreach($stmt1 as $results1) {
					$this->login_alerts = true;
					$this->login_number = $results1["last_login_attempts"];
					$this->login_time = $results1["last_login_attempts_time"];
				}
			}
		} catch(PDOException $e) {}
		
		return $this;
	}
	
	public function password_change($username) {
		
		$this->change_request = false;
		
		try {			
			$stmt = $this->db->query("select * from _admin_request_change where username ='$username'");
			
			if ($this->db->num_rows($stmt)  > 0) {
				foreach($stmt as $results2) {
					$this->change_request = true;
					$this->change_time = $results2["date_recorded"];
				}
			}
		} catch(PDOException $e) {}
		
		return $this;
	}
	
	public function locked_account() {
		
		$this->locked_acs = false;
		
		try {			
			$stmt = $this->db->query("select * from _login_attempt where attempts > ".ATTEMPTS_NUMBER);
			
			if ($this->db->num_rows($stmt)  > 0) {
				foreach($stmt as $results2) {
					$this->locked_acs = true;
				}
			}
		} catch(PDOException $e) {}
		
		return $this;
	}
	
	public function password_change_requests($username) {
		
		$this->change_request1 = false;
		
		try {			
			$stmt = $this->db->query("select * from _admin_request_change where username !='$username'");
			
			if ($this->db->num_rows($stmt)  > 0) {
				foreach($stmt as $results2) {
					$this->change_request1 = true;
				}
			}
		} catch(PDOException $e) {}
		
		return $this;
	}
	
	public function get_notification($notices) {
		
		$this->can_continue = true;
		
		if($notices == 'disk_full') {
			if(($this->folder->return_usage()->used_size) >= $this->offices->item_by_id('disk_space', $this->session->userdata("officeID"))) {
				$this->result = "<div class='alert btn-block alert-danger'>Sorry! You have reached your maximum disk space capacity. You must delete some of your files to be able to continue.</div>";
				$this->can_continue = false;
			}
		} elseif($notices == 'daily_usage') {
			if(($this->folder->return_usage()->today_used_raw) >= $this->offices->item_by_id('daily_upload', $this->session->userdata("officeID"))) {
				$this->result = "<div class='alert btn-block alert-danger'>Sorry! You have reached your maximum file uploads for today.</div>";
				$this->can_continue = false;
			}
		}
		
		return $this;
		
	}
}
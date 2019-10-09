<?php
// ensure this file is being included by a parent file
if( !defined( 'SITE_URL' ) && !defined( 'SITE_DATE_FORMAT' ) ) die( 'Restricted access' );
class Users {
	
	// set the number of users that can be created by an account
	private $STANDARD_USERS = 3;
	private $SILVER_USERS = 10;
	private $GOLDEN_USERS = 50;
	private $PLATINUM_USERS = 1500000;
	public $list_users;
	
	public function __construct() {
		
		global $DB, $session;
		
		$this->db = $DB;
		$this->encrypt = load_class('encrypt', 'libraries');
		$this->user_agent = load_class('user_agent', 'libraries');
		$this->session = $session;
		
	}
	
	private function users_created() {
		
		return ($this->db->num_rows($this->db->query("
			SELECT * FROM _admin WHERE office_id='{$this->return_office_id()}'
			AND admin_deleted='0'
		")));
		
	}
	
	public function can_create($office_type) {
		
		$this->response = true;
		
		if(($office_type == "Standard") and 
			($this->users_created() >= $this->STANDARD_USERS)) {
			$this->response = false;
		} elseif(($office_type == "Silver") and 
			($this->users_created() >= $this->SILVER_USERS)) {
			$this->response = false;
		} elseif(($office_type == "Golden") and 
			($this->users_created() >= $this->GOLDEN_USERS)) {
			$this->response = false;	
		} elseif(($office_type == "Platinum") and 
			($this->users_created() >= $this->PLATINUM_USERS)) {
			$this->response = false;
		}
		
		return $this->response;
	}
	
	public function logout_user() {
		
		$this->session->unset_userdata(UNAME_SESS_ID);
		$this->session->unset_userdata(UID_SESS_ID);
		$this->session->unset_userdata(ROLE_SESS_ID);
		$this->session->unset_userdata(MAIN_SESS);
		$this->session->unset_userdata(ROLE_SUPER_ROLE);
		$this->session->unset_userdata(OFF_SESSION_ID);
		
		$this->session->sess_destroy();
		
	}
	
	public function lock_user_screen() {
		return ($this->session->userdata(LOCKED_OUT)) ? true : false; 
	}
	
	
	public function confirm_admin_user() {
		return ($this->session->userdata(MAIN_SESS) AND IN_ARRAY($this->session->userdata(ROLE_SESS_ID), array(1, 1001))) ? true : false;
	}
	
	public function confirm_super_user() {
		return ($this->session->userdata(ROLE_SUPER_ROLE) AND IN_ARRAY($this->session->userdata(ROLE_SESS_ID), array(1001))) ? true : false;
	}
	
	public function logged_InControlled() {
		
		return ($this->session->userdata(MAIN_SESS) AND $this->session->userdata(UID_SESS_ID)) ? true : false;	
	}
	
	public function return_username() {		
		#assign variables
		$user_id = xss_clean($this->session->userdata(UID_SESS_ID));
		#fetch the user information
		return $this->get_details_by_id($user_id)->uname;
	}
	
	public function return_email() {		
		#assign variables
		$user_id = xss_clean($this->session->userdata(UID_SESS_ID));
		#fetch the user information
		return $this->get_details_by_id($user_id)->uemail;
	}
	
	public function return_id() {		
		#assign variables
		$user_id = xss_clean($this->session->userdata(UID_SESS_ID));
		#fetch the user information
		return $this->get_details_by_id($user_id)->adid;
	}
	
	public function return_fullname() {
		#assign variables
		$user_id = xss_clean($this->session->userdata(UID_SESS_ID));
		#fetch the user information
		return $this->get_details_by_id($user_id)->funame;
	}
	
	public function return_office_id() {
		#assign variables
		$user_id = xss_clean($this->session->userdata(UID_SESS_ID));
		#fetch the user information
		return $this->get_details_by_id($user_id)->office_id;
	}
	
	public function list_office_users() {
		
		try {
			
			$sql = $this->db->query("SELECT * FROM `_admin` WHERE username !='".$this->return_username()."' AND `admin_deleted`='0' AND office_id='".$this->return_office_id()."'");
			
			if($this->db->num_rows($sql) > 0) {				
				foreach($sql as $res) {					
					$this->list_users .= "<option value='".$res['username']."'>".$res['fullname']."</option>";					
				}
			} 
		} catch(PDOException $e) { }
		
		return $this;
	}
	
	public function get_details_by_id($id) {
		
		global $config;
		
		$this->found = false;
		
		$field = (preg_match("/^[0-9]+$/", $id)) ? "id" : "username";
			
		try {
			
			$sql = $this->db->query("SELECT * FROM `_admin` WHERE `$field`='$id' AND `admin_deleted`='0'");
			
			if($this->db->num_rows($sql) == 1) {
				
				$this->found = true;
				
				foreach($sql as $res) {
					$this->adid = $res['id'];
					$this->fname = $res['firstname'];
					$this->lname = $res['lastname'];
					$this->uname = $res['username'];
					$this->funame = $res['fullname'];
					$this->upload_status = $res['uploads_status'];
					$this->upload_limit = $res['uploads_limit'];
					$this->ulinked = "<a href='".$config->base_url()."profiles/".$this->uname."'>".$this->funame."</a>";
					$this->uemail = $res['email'];
					$this->office_id = $res['office_id'];
					$this->urole = $res['role'];
					$this->lacs = strftime(date("D d M Y, H:i:a", strtotime($res['lastaccess'])));
					
					return $this;
				}
			} 
		} catch(PDOException $e) {
			$this->found = false;
			$this->funame = 'Error';
		}
		
		return $this;
	}

	public function item_by_id($id, $column) {
		
		global $config;
		
		$this->found = false;
		
		$field = (preg_match("/^[0-9]+$/", $id)) ? "id" : "username";
			
		try {
			
			$sql = $this->db->query("SELECT * FROM `_admin` WHERE `$field`='$id' AND `admin_deleted`='0'");
			
			if($this->db->num_rows($sql) == 1) {
				
				$this->found = true;
				
				foreach($sql as $results) {
					# first confirm that the column the user is requesting
					# does results to be a valid column before you return the value
					if(isset($results[$column])) {
						# use the column supplied to fetch the result for the user
						return $results[$column];
					}
					#run the second part of this code to return an empty array set
					else {
						# return an empty result
						return;
					}
				}
			} 
		} catch(PDOException $e) {}
		
		return;
	}
	
	public function changed_password_first() {
		
		try {			
			$stmt1 = $this->db->query("select * from _admin where username ='".$this->return_username()."'");
			
			if ($this->db->num_rows($stmt1)  > 0) {
				foreach($stmt1 as $results1) {
					$this->p_state = $results1["changed_password"];
					if($this->p_state == 1) {
						return true;
					} else {
						return false;
					}
				}
			}
			
		} catch(PDOException $e) {}
		return $this;
	}
	
	public function compare_password($password) {
		#run the user set password against a list of known passwords 
		#to see if there is any match
		#return true if the password was not found in the database table
		try {
			#run the search query
			$stmt = $this->db->query("select * from _users_passwords_log where password='$password'");
			
			#count the number of rows found
			if($this->db->num_rows($stmt) > 0) {
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {}
	}
	
}
?>
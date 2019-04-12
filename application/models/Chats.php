<?php 

class Chats {
	
	public $file_size;
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->session = load_class('session', 'libraries\Session');
		load_file(array('upload_helper'=>'helpers', 'string_helper'=>'helpers'));
	}

	public function uniqueId($senderId, $receiverId) {
		
		$this->unique_key = random_string('alnum', mt_rand(25, 35))."/New/".base64_encode($receiverId);
		
		try {
			#continue process & check if the username/email address exists
			$query = $this->db->query("SELECT * FROM _messages WHERE (sender_id='$senderId' and receiver_id='$receiverId') OR (sender_id='$receiverId' and receiver_id='$senderId') and deleted='0' order by id desc limit 1");
			
			#count the number of rows found 
			if(count($query) > 0) {				
				foreach($query as $results) {
					$this->unique_key = $results["unique_id"];
				}
			}
		} catch(PDOException $e) {}
		
		return $this;		
	}
	
}
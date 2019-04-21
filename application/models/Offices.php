<?php 

class Offices {
	
	public $file_size;
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = load_class('session', 'libraries\Session');
		$this->user_id = $this->session->userdata(":lifeID");
		load_file(array('upload_helper'=>'helpers', 'string_helper'=>'helpers'));
	}
		
	public function item_by_id($column, $item_id = NULL, $field = 'id') {
		# confirm which variable was parsed 
		$field = (preg_match("/^[0-9]+$/", $item_id)) ? "id" : "unique_id";
		# continue processing the form 
		if($item_id) {
			# query the database for the information of the user
			$query = $this->db->where('_offices', '*', 
				array(
					"$field"=>"='{$item_id}'", 'status'=>"='1'"
				)
			);
			
			if($this->db->num_rows($query) == 1) {
				# using foreach loop to fetch the results 
				foreach($query as $results) {
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
		}
		return;
	}
	
	public function allocation() {
		$stmt = $this->db->query("
			SELECT 
				SUM(disk_space) AS item_size
			FROM 
				_offices 
			WHERE 
				status='1'
		");
		
		foreach($stmt as $result) {
			$this->used_size = $result["item_size"];
			$this->file_size = file_size_convert($this->used_size);
		}
		
		$this->percent_used = round(($this->used_size/config_item('server_space'))*100, 2);
		
		return $this;
	}

}
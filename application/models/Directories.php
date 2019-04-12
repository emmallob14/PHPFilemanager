<?php 

class Directories {
	
	public $file_size;
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = load_class('session', 'libraries\Session');
		$this->user_id = $this->session->userdata(":lifeID");
		load_file(array('upload_helper'=>'helpers', 'string_helper'=>'helpers'));
	}
		
	public function directory_tree($parent_id=0) {
		
		$this->folders = array();
		
		try {			
			
			
		} catch(PDOException $e) {}
		
		return $this;
	}
	
	public function list_all_files($queryString, $itemType) {
		
		$stmt = $this->db->query("SELECT * FROM _item_listing WHERE user_id ='{$this->user_id}' AND item_status='1' AND item_deleted='0' $itemType ORDER BY item_type $queryString");
		
		return $stmt;
		
	}
		
	public function list_folders($parent_id) {
		
		$stmt = $this->db->query("SELECT * FROM _item_listing WHERE item_parent_id='$parent_id' AND item_status='1' AND item_deleted='0' ORDER BY id ASC");
		
		return $stmt;
		
	}
	
	public function list_directories() {
		return $this->directory_tree()->folders;
	}
	
	public function return_usage() {
		
		$this->folders = array();
		
		$stmt = $this->db->query("SELECT SUM(item_size_kilobyte) as item_size FROM _item_listing WHERE user_id ='{$this->user_id}' AND item_type='FILE' AND item_status='1' AND item_deleted='0'");
		
		$today_used = $this->db->query("SELECT SUM(item_size_kilobyte) as item_size2 FROM _item_listing WHERE user_id ='{$this->user_id}' AND item_type='FILE' AND item_status='1' AND item_deleted='0' AND item_date=CURDATE()");
		
		foreach($stmt as $result) {
			$this->used_size = $result["item_size"]*1024;
			$this->file_size = file_size_convert($this->used_size);
		}
		
		foreach($today_used as $result2) {
			$this->today_used_raw = $result2["item_size2"]*1024;
			$this->today_used_size = file_size_convert($result2["item_size2"]*1024);
		}
		
		$this->today_used = round((($result2["item_size2"]*1024)/config_item('daily_upload'))*100, 2);
		$this->percent_used = round(($this->used_size/config_item('disk_space'))*100, 2);
		
		return $this;
	}
	
	public function item_by_id($column, $item_id = NULL, $field = 'id') {
		# confirm which variable was parsed 
		$field = (preg_match("/^[0-9]+$/", $item_id)) ? "id" : "item_unique_id";
		# continue processing the form 
		if($item_id) {
			# query the database for the information of the user
			$query = $this->db->where('_item_listing', '*', 
				array(
					"$field"=>"='{$item_id}'", 'user_id'=>"='{$this->user_id}'",
					'item_status'=>"='1'", 'item_deleted'=>"='0'"
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
	
	public function item_by_id2($column, $item_id = NULL, $field = 'id') {
		# confirm which variable was parsed 
		$field = (preg_match("/^[0-9]+$/", $item_id)) ? "id" : "item_unique_id";
		# continue processing the form 
		if($item_id) {
			# query the database for the information of the user
			$query = $this->db->where('_item_listing', '*', 
				array(
					"$field"=>"='{$item_id}'", 
					'item_status'=>"='1'", 'item_deleted'=>"='0'"
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
	
	public function prep_download($filename, $oldname, $file_ext) {
		// assign a new random file name
		$newname = random_string('alnum', 5);
		$this->file_path = NULL;
		$this->file_name = NULL;
		//confirm that the file really exists 
		if(file_exists(config_item('upload_path').$filename)) {
			// new file name
			$file_newname = config_item('upload_path').'download/'.$newname;
			$file_oldname = config_item('upload_path').'download/'.$oldname."_$newname.".$file_ext;
			// first copy the file to a separate folder
			copy(config_item('upload_path').$filename, $file_newname);
			// rename the copied file
			if (!@rename($file_newname, $file_oldname)) {
				if (copy($file_newname, $file_oldname)) {
					unlink($file_newname);
				}
			}
			// return the new file to the browser to be downloaded
			$this->file_path = $file_oldname;
			$this->file_name = $oldname."_$newname.".$file_ext;
		}
		return $this;
	}
	
	public function force_download($n_FileName) {
		redirect( SITE_URL . '/'.$n_FileName);
	}
}
<?php 

class Directories {
	
	public $file_size;
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->user_agent = load_class('User_agent', 'libraries');
		$this->session = load_class('session', 'libraries\Session');
		$this->offices = load_class('offices', 'models');
		$this->user_id = $this->session->userdata(":lifeID");
		$this->office_id = $this->session->userdata("officeID");
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
	
	public function change_download_link($item_id) {
		
		$download_link = random_string('alnum', mt_rand(45, 70));
		
		$this->db->execute("UPDATE _item_listing SET item_download_link='$download_link' WHERE item_unique_id='$item_id' AND item_deleted='0'");
		
	}
	
	/**
	 * Get mime type
	 * @param string $file_path
	 * @return mixed|string
	 */
	public function file_mime_type($file_path) {
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $file_path);
			finfo_close($finfo);
			return $mime;
		} elseif (function_exists('mime_content_type')) {
			return mime_content_type($file_path);
		} elseif (!stristr(ini_get('disable_functions'), 'shell_exec')) {
			$file = escapeshellarg($file_path);
			$mime = shell_exec('file -bi ' . $file);
			return $mime;
		} else {
			return '--';
		}
	}

	public function list_directories() {
		return $this->directory_tree()->folders;
	}
	
	public function return_usage($return_type=NULL) {
		
		if(!$return_type) {
			$return_string = "office_id ='{$this->office_id}' AND";
		} else {
			$return_string = "";
		}
		
		$stmt = $this->db->query("
			SELECT 
				SUM(item_size_kilobyte) AS item_size
			FROM 
				_item_listing 
			WHERE 
				$return_string item_type='FILE' 
			AND 
				item_status='1' AND item_deleted='0'
		");
		
		$today_used = $this->db->query("
			SELECT 
				SUM(item_size_kilobyte) AS item_size2 
			FROM 
				_item_listing 
			WHERE 
				$return_string item_type='FILE' 
			AND 
				item_status='1' AND item_deleted='0' AND item_date=CURDATE()
		");
		
		foreach($stmt as $result) {
			$this->used_size = $result["item_size"]*1024;
			$this->file_size = file_size_convert($this->used_size);
		}
		
		foreach($today_used as $result2) {
			$this->today_used_raw = $result2["item_size2"]*1024;
			$this->today_used_size = file_size_convert($result2["item_size2"]*1024);
		}
		
		if(!$return_type) {
			$userdaily = $this->offices->item_by_id('daily_upload', $this->session->userdata("officeID"));
			$useroverall = $this->offices->item_by_id('disk_space', $this->session->userdata("officeID"));
		} else {
			$userdaily = config_item('daily_uploads');
			$useroverall = config_item('server_space');
		}
		$this->today_used = round(($result2["item_size2"]*1024)/$userdaily*100, 2);
		$this->percent_used = round(($this->used_size/$useroverall)*100, 2);
		
		return $this;
	}
	
	public function item_by_id($column, $item_id = NULL, $field = 'item_unique_id') {
		# confirm which variable was parsed 
		$field = (preg_match("/^[0-9]+$/", $item_id)) ? "id" : $field;
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
	
	public function item_by_id2($column, $item_id = NULL, $field = 'item_unique_id') {
		# confirm which variable was parsed 
		$field = (preg_match("/^[0-9]+$/", $item_id)) ? "id" : $field;
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
	
	public function item_full_size($item_id, $column = 'item_size_kilobyte') {
		# continue processing the form 
		if($item_id) {
			# query the database for the information of the user
			$query = $this->db->query("
				SELECT 
					SUM($column) AS item_size
				FROM 
					_item_listing
				WHERE 
					item_folder_id='{$item_id}' AND user_id='{$this->user_id}' 
				AND 
					item_status='1' AND item_deleted='0'
			");
			
			if($this->db->num_rows($query) == 1) {
				# using foreach loop to fetch the results 
				foreach($query as $results) {
					# first confirm that the column the user is requesting
					# does results to be a valid column before you return the value
					if(isset($results["item_size"])) {
						# use the column supplied to fetch the result for the user
						return $results["item_size"]*1024;
					}
				}				
			}			
		}
		return;
	}
	
	public function disk_used_space($query_term, $user_id) {
		# continue processing the form 
		if($user_id) {
			# get the query terms
			if($query_term == "all_items") {
				# query the database for the information of the user
				$queryString = $this->db->query("
					SELECT 
						SUM(item_size_kilobyte) AS item_size
					FROM 
						_item_listing
					WHERE 
						user_id='{$user_id}' 
					AND 
						item_status='1' AND item_deleted='0'
				");
			}
			if($this->db->num_rows($queryString) == 1) {
				# using foreach loop to fetch the results 
				foreach($queryString as $results) {
					# first confirm that the column the user is requesting
					# does results to be a valid column before you return the value
					if(isset($results["item_size"])) {
						# use the column supplied to fetch the result for the user
						return $results["item_size"]*1024;
					}
				}				
			}			
		}
		return;
	}
	
	
	public function user_disk_info($query_columns = '*', $query_next = 1, $return_column, $order_string= null) {
		# continue processing the form 
		if($query_columns) {
			# query the database for the information of the user
			$query = $this->db->query("
				SELECT 
					$query_columns
				FROM 
					_item_listing
				WHERE 
					$query_next
				AND 
					item_status='1' AND item_deleted='0'
				$order_string
			");
			
			if($this->db->num_rows($query) > 0) {
				# using foreach loop to fetch the results 
				foreach($query as $results) {
					# first confirm that the column the user is requesting
					# does results to be a valid column before you return the value
					if(isset($results[$return_column])) {
						# use the column supplied to fetch the result for the user
						return $results[$return_column];
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
			// set the file to download path as a session
			$this->session->set_userdata("file_download_path", $this->file_path);
			// set the file name 
			$this->file_name = $oldname."_$newname.".$file_ext;
		}
		return $this;
	}
	
	public function force_download($n_FileName) {
		redirect( SITE_URL . '/'.$n_FileName);
		// push to download the zip
		//header('Content-type: application/zip');
		//header('Content-Disposition: attachment; filename="'.$n_FileName.'"');
		//readfile("$n_FileName");
		// delete the temp file downloaded
		//unlink("$n_FileName");
	}
	
	public function add_download($item_id) {
		
		try {
			//fetch the information
			$sql = $this->db->where(
					'_item_listing', '*', 
					ARRAY(
						'item_unique_id'=>"='$item_id'",
						'item_status'=>"='1'"
				));
				
			//count the number of rows
			if($this->db->num_rows($sql) == 1) {
				//fetch results
				foreach($sql as $results) {				
					//new number of views
					$this->ncounts=$results["item_downloads"]+1;
					//update the database
					$this->db->just_exec("UPDATE _item_listing SET item_downloads='{$this->ncounts}' WHERE item_unique_id='$item_id'");
				}
			}
		} catch(PDOException $e) {}
	}
}
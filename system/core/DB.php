<?php
class DB {
	
	private $db;
	
	public function __construct() {
		
		$this->hostname = DB_HOST;
		$this->username = DB_USER;
		$this->password = DB_PASS;
		$this->database = DB_NAME;
		
		if($this->db == null) {
			$this->db = $this->db_connect($this->hostname, $this->username, $this->password, $this->database);
		}
	}
	
	private function db_connect($hostname, $username, $password, $database) {
		
		try {
			$this->conn = "mysql:host=$hostname; dbname=$database; charset=utf8";
			
			$db = new PDO($this->conn, $username, $password);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
			
			return $db;
			
		} catch(PDOException $e) {
			 echo "It seems there was an error.  Please refresh your browser and try again. ".$e->getMessage();
		}
		
	}
	
	public function call_connection() {
		
		if($this->db == null):
			$this->db_connect($this->hostname, $this->username, $this->password, $this->database);
		else:
			return $this->db;
		endif;
	}
	
	public function just_exec($sql) {
			
		try {
			$this->db->exec("$sql");
			return true;
		} catch(PDOException $e) {
			return false;
		}
	
	}
	
	function max_where($column, $table, $where) {
		
		try {
			
			$this->mQuery = $this->db->query("SELECT MAX(`$column`) as ID FROM `$table` WHERE $where");
			
			while($result = $this->mQuery->fetch()) {
				return $result['ID'];
			}
		
		} catch(PDOException $e) { }
    }
	
	public function max_all($column, $table) {
		
		try {
			
			$this->mQuery = $this->db->query("SELECT MAX(`$column`) as ID FROM `$table`");
			while($result = $this->mQuery->fetch()) {
				return $result['ID'];
			}
		} catch(PDOException $e) { }
	}
	
	public function sumOfAll($column, $table, $where_clause, $field="SUM") {
		
		try {
			
			$this->mQuery = $this->db->query("SELECT $field(`$column`) as ID FROM $table $where_clause");
			while($result = $this->mQuery->fetch()) {
				return $result['ID'];
			}
		} catch(PDOException $e) { }
	}
	
	public function lastRowColumn($column, $where_clause) {
		
		try {
			
			$this->mQuery = $this->db->query("select $column as column_name from $where_clause order by id desc limit 1");
			
			while($result = $this->mQuery->fetch()) {
				return $result['column_name'];
			}
		
		} catch(PDOException $e) { }
    }
	
	public function num_rows($query) {
		
		return (count($query) > 0) ? count($query) : null;
		
	}
	
	public function get_all($table_name) {
		
		try {
			
			$stmt = $this->db->prepare("SELECT * FROM $table_name");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
			
		} catch(PDOException $e) {return 0;}
		
	}
	
	public function execute($sql) {
		
		$stmt = $this->db->prepare("$sql");
		$stmt->execute();
	}
	
	public function query($sql) {
		
		try {
			
			$stmt = $this->db->prepare("$sql");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		
		} catch(PDOException $e) {return 0;}
	}
	
	public function where($table, $columns, $where_clause, $additional=NULL) {
		
		try {
			
			if(!empty($where_clause) AND is_array($where_clause)) {
				# pick the data presented by the user and use it to complete the form
				foreach($where_clause as $field=>$value) {
					$fields[] = sprintf("%s %s", $field, $value);				
				}
			
				$where_list = join(' AND ', $fields);
				
				$query_string = sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $where_list, $additional);
				$query_string = str_replace("AND OR", "OR", $query_string);
				
				$stmt = $this->db->prepare("$query_string");
				$stmt->execute();
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				return $results;
			}
			
			return false;
			
		} catch(PDOException $e) {return;}
		
	}
	
	public function custom_where($table, $where_clause = 1) {
		
		try {
			
			if(!empty($where_clause)) {
				# join the various compartments into a single query string
				$query_string = sprintf("SELECT * FROM `%s` WHERE 1 %s", $table, $where_clause);
			} else {
				$query_string = sprintf("SELECT * FROM `%s` WHERE 1", $table);
			}
			$stmt = $this->db->prepare("$query_string");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $results;
			
		} catch(PDOException $e) {return;}
		
	}
	
	public function touch($table, $data, $where_clause = NULL, $to_do = 'UPDATE') {
		
		try {
			
			if(!empty($data) AND is_array($data)) {
				
				# pick the data presented by the user and use it to complete the form
				foreach($data as $field=>$value) {
					if($value=='now()') {
						$fields[] = "`$field` = now()";
					} elseif($value=='NULL') {
						$fields[] = "`$field` = NULL";
					} else {
						$fields[] = sprintf("`%s` = '%s'", $field, $value);
					}
					
				}
				# join the data submitted
				$field_list = join(',', $fields);
				
				# confirm that the $where_clause is an array 
				if(is_array($where_clause)) {
					if(!empty($where_clause)) {
						# write a function for the where parameters
						foreach($where_clause as $where_field=>$where_value) {
							$where[] = sprintf("`%s` = '%s'", $where_field, $where_value);
						}
						
						$where_list = join(' AND ', $where);
					}
				}
				# confirm that the user wants to update the specified
				# table. Check the $to_do variable
				if(strtoupper($to_do) == 'UPDATE') {
					#run this query set
					$query_string = sprintf("UPDATE `%s` SET %s WHERE %s", $table, $field_list, $where_list);
				} elseif(strtoupper($to_do) == 'INSERT') {
					#run this query set
					$query_string = sprintf("INSERT INTO `%s` SET %s", $table, $field_list);
				}
				//print $query_string;
				$stmt = $this->db->prepare("$query_string");
				$stmt->execute();
				
				return true;
			}
			
			return false;
			
		} catch(PDOException $e) { }
	}
	
	public function delete($table, $where_clause) {
		
		try {
			
			if(!empty($where_clause)) {
				
				foreach($where_clause as $field=>$value) {
					$fields[] = sprintf("`%s`='%s'", $field, $value);
				}
				
				# join the fields that the user wants to delete 
				$field_list = join(' AND ', $fields);
				
				# join the various compartments into a single query string
				$query_string = sprintf("DELETE FROM `%s` WHERE %s", $table, $field_list);
				
				
				
				$stmt = $this->db->prepare("$query_string");
				$stmt->execute();
				
				return true;
			}
			
			return false;
			
		} catch(PDOException $e) {}
	}
	
	public function custom_delete($table, $where_clause) {
		
		try {
			
			if(!empty($where_clause)) {
				
				# join the various compartments into a single query string
				$query_string = sprintf("DELETE FROM `%s` WHERE %s", $table, $where_clause);
				
				$stmt = $this->db->prepare("$query_string");
				$stmt->execute();
				
				return true;
			}
			
			return false;
			
		} catch(PDOException $e) {}
	}
}
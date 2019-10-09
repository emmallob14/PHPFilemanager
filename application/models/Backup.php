<?php 

class Backup {
	
	public function __construct() {
		
		global $DB;
		
		$this->db = $DB;
		$this->user_agent = load_class('user_agent', 'libraries');
		$this->session = load_class('session', 'libraries\Session');
		
		$this->config = $this->db->call_connection();
		
	}
	
	public function backup_system($file) {
		
		global $admin_user; 
		
		$output = "-- phpMyAdmin SQL Dump\n";
		$output .= "-- version 4.7.0\n";
		$output .= "-- https://www.phpmyadmin.net/\n";
		$output .= "--\n";
		$output .= "-- Host: ".config_item('site_name')." - ".DB_HOST."\n";
		$output .= "-- Generation Time: " . date("r", time()) . "\n";
		$output .= "-- Server version: 10.1.22-MariaDB\n";
		$output .= "-- PHP Version: " . phpversion() . "\n\n";
		$output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
		$output .= "SET AUTOCOMMIT = 0;\n";
		$output .= "START TRANSACTION;\n";
		$output .= "SET time_zone = \"+00:00\";\n\n";
	
		$output .= "--\n-- Database: `".DB_NAME."`\n--\n";
		$output .= "\nCREATE DATABASE IF NOT EXISTS `".DB_NAME."` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `".DB_NAME."`;\n";
		// get all table names in db and stuff them into an array
		$tables = array();
		$stmt = $this->config->query("SHOW TABLES");
		while($row = $stmt->fetch(PDO::FETCH_NUM)){
			$tables[] = $row[0];
		}
		
		// process each table in the db
		foreach($tables as $table){
			$fields = "";
			$sep2 = "";
			$output .= "\n-- " . str_repeat("-", 60) . "\n\n";
			$output .= "--\n-- Table structure for table `$table`\n--\n\n";
			// get table create info
			$output .= "DROP TABLE IF EXISTS `$table`;\n";
			$stmt = $this->config->query("SHOW CREATE TABLE $table");
			$row = $stmt->fetch(PDO::FETCH_NUM);
			$output.= $row[1].";\n\n";
			// get table data
			$output .= "--\n-- Dumping data for table `$table`\n--\n\n";
			$stmt = $this->config->query("SELECT * FROM $table");
			while($row = $stmt->fetch(PDO::FETCH_OBJ)){
				// runs once per table - create the INSERT INTO clause
				if($fields == ""){
					$fields = "INSERT INTO `$table` (";
					$sep = "";
					// grab each field name
					foreach($row as $col => $val){
						$fields .= $sep . "`$col`";
						$sep = ", ";
					}
					$fields .= ") VALUES";
					$output .= $fields . "\n";
				}
				// grab table data
				$sep = "";
				$output .= $sep2 . "(";
				foreach($row as $col => $val){
					// add slashes to field content
					$val = addslashes($val);
					// replace stuff that needs replacing
					$search = array("\'", "\n", "\r");
					$replace = array("''", "\\n", "\\r");
					$val = str_replace($search, $replace, $val);
					$output .= $sep . "'$val'";
					$sep = ", ";
				}
				// terminate row data
				$output .= ")";
				$sep2 = ",\n";
			}
			// terminate insert data
			$output .= ";\n";
		}   
	
		//open the file
		$fh = @fopen($file,"w");
		
		//write the contents into the file
		@fwrite($fh,$output);
		@fclose($fh);
		
		return true;
	}
	
}
?>
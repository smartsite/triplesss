<?php

namespace  Triplesss\db;
//use \Triplesss\db\DBSettings as DBSettings;
use \Triplesss\settings\Settings as Settings;

//class DB extends DBSettings {
class DB extends Settings {	
	// Connection to the database
	function __construct() {
		// Load settings from parent class
		$settings = $this->getSettings();
		
		$host = $settings['dbhost'];
		$name = $settings['dbname'];
		$user = $settings['dbusername'];
		$pass = $settings['dbpassword'];
		
		$this->link = new \mysqli( $host , $user , $pass , $name );
	}
	
	function query( $query ) 
	{
		$this->classQuery = $query;
		return $this->link->query( $query );
	}
	
	function escapeString( $query )
	{
		return $this->link->escape_string( $query );
	}
	
	function numRows( $result )	{
		return $result->num_rows;
	}
	
	function lastInsertedID() {
		return $this->link->insert_id;
	}
		
	function fetchAssoc( $result ) {
		return $result->fetch_assoc();
	}
	
	function fetchArray( $result , $resultType = MYSQLI_ASSOC ) {
		return $result->fetch_array( $resultType );
	}
	
	function fetchAll( $result , $resultType = MYSQLI_ASSOC ) {
		return $result->fetch_all( $resultType );
	}
	
	function fetchRow( $result ) {
		return $result->fetch_row();
	}

	function updateTableByColumn( $table, $fieldValues, $column, $match ) {
		$fields = '';		
		$qry = "UPDATE ".$table." SET ";
		forEach($fieldValues as $field=>$val){
			$fields.= $field."='".$val."',";           
		}
		
		$qry.= rtrim($fields,',');       
		$qry.= " WHERE ".$column." = '".$match."';";	
		$stmnt =  $this->link->prepare( $qry );

			
		if (!$stmnt) {
			return "Error in query";
		} else {
			$stmnt->execute();
			if ($stmnt->affected_rows != 0) {
				return $stmnt->affected_rows;
			} else {
				if ($stmnt->error != ''){
					return $stmnt->error;
				}
				return 0;	
			}
			return 0;	
		}		
	}	
	
	function freeResult( $result ) {
		$this->link->free_result( $result );
	}
	
	
	function close() {
		$this->link->close();
	}
	
	function sql_error() {
		if( empty( $error ) )
		{
			$errno = $this->link->errno;
			$error = $this->link->error;
		}
		return $errno . ' : ' . $error;
	}
}

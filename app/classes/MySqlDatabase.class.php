<?php

class MySqlDatabase {
	public $Password = '';
	public $Username = '';
	public $Database = '';
	public $Host = '';

	public $QueryId = 0;
	public $Query = '';

	public $LinkId = 0;
	public $Errno = 0;
	public $Error = '';

	private $_errors = array();

	public function __construct($database = NULL, $username = NULL, $password = NULL, $host = NULL) {
		if ( $database && $username && $password && $host ) $this->GetLink($database, $username, $password, $host);
	}

	public function GetLink($database = '', $username = '', $password = '', $host = '') {
		$this->Database = $database;
		$this->Username = $username;
		$this->Password = $password;
		$this->Host     = $host;

		if ( $this->LinkId ) $this->Disconnect();

		$this->LinkId = mysql_connect($this->Host, $this->Username, $this->Password);

		if ( !$this->LinkId ) {
			$this->_addError();
			return FALSE;
		}

		if ( !mysql_query('use ' . $this->Database, $this->LinkId) ) {
			$this->_addError();
			return FALSE;
		}

		return $this->LinkId;
	}

	public function __destruct() {
		$this->Disconnect();
	}

	public function Disconnect() {
		mysql_close($this->LinkId);
		$this->LinkId = 0;
	}

	/*
		Additional arguments will be cleaned and placed into the $query
	*/
	public function GetResult($query) {
		$args = array();
		if ( func_num_args() == 2 && is_array(func_get_arg(1)) ) {
			$args = func_get_arg(1);
		} elseif ( func_num_args() > 2 ) {
			$args = array_slice(func_get_args(), 1);
		}
	
		for ( $i = 0; $i < count($args); $i++ ) {
			$replace = $args[$i];
			$query = preg_replace('/\?/', mysql_escape_string($replace), $query, 1);
		}

		$this->QueryId = mysql_query($query, $this->LinkId);
		if ( !$this->QueryId ) {
			$this->_addError();
			return FALSE;
		}

		return $this->QueryId;
	}

	private function _addError() {
		$error_object = new MySqlDatabaseError();
		$this->Error = $error_object->Error;
		$this->Errno = $error_object->Errno;
		$this->_errors[] = $error_object;
	}

	public function GetErrors() {
		return $this->_errors;
	}
} // MySqlDatabase{}

class MySqlDatabaseError {
	public $Error;
	public $Errno;

	public function __construct() {
		$this->Error = mysql_error();
		$this->Errno = mysql_errno();
	}
}

?>
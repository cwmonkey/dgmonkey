<?php

class MakeOrmDbDatabasetException extends Exception { }
class MakeOrmDbFileException extends Exception { }

class MakeOrmDb {
	private $_TablePrefix;
	private $_DbAddress;
	private $_DbUser;
	private $_DbPassword;
	private $_DbName;

	public $Error;

	private $_Tables = array();
	private $_Antimethods = array();
	private $_PrimaryKeys = array();

	public function FetchTablesMySql($dbaddr, $dbname, $dbuser, $dbpassword, $table_prefix = '') {
		if ( !($link = mysql_connect($dbaddr, $dbuser, $dbpassword)) ) {
			$this->Error = 'Could not connect: ' . mysql_error();
			throw new MakeOrmDbDatabasetException();
		}

		if ( !mysql_select_db($dbname) ) {
			$this->Error = 'Could not select database: ' . mysql_error();
			throw new MakeOrmDbDatabasetException();
		}

		$this->_TablePrefix = $table_prefix;
		$this->_DbAddress = $dbaddr;
		$this->_DbUser = $dbuser;
		$this->_DbPassword = $dbpassword;
		$this->_DbName = $dbname;

		$query = "SHOW TABLES";
		if ( !($table_result = mysql_query($query)) ) {
			$this->Error = 'Query failed: ' . mysql_error();
			throw new MakeOrmDbDatabasetException();
		}
		
		while ( $table = mysql_fetch_array($table_result, MYSQL_NUM) ) {
			if ( preg_match('/^' . $table_prefix . '/', $table[0]) ) {
				$table_name = substr($table[0], strpos($table[0], $table_prefix) + strlen($table_prefix));
				$query = 'SHOW COLUMNS FROM `' . $table[0] . '`';
				$column_result = mysql_query($query);
				$columns = array();
				while ( $column = mysql_fetch_assoc($column_result) ) {
					if ( $column['Key'] == 'PRI' ) $primary_key = $column['Field'];
					$columns[] = $column['Field'];
				}
		
				$this->_PrimaryKeys[$table_name] = $primary_key;
		
				$query = 'SHOW CREATE TABLE `' . $table[0] . '`';
				$create_result = mysql_query($query);
				$create = mysql_fetch_array($create_result, MYSQL_NUM);
				$create = $create[1];
				$methods = array();
				if ( $matches = preg_match_all("/CONSTRAINT `([^`]+)` FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)` \(`([^`]+)`\)/", $create, $foreign_keys) ) {
					for ( $i = 0; $i < $matches; $i++ ) {
						$line = $foreign_keys[0][$i];
						$fk_name = $foreign_keys[1][$i];
						$column_name = $foreign_keys[2][$i];
						$fk_table_name = substr($foreign_keys[3][$i], strpos($foreign_keys[3][$i], $table_prefix) + strlen($table_prefix));
						$fk_column_name = $foreign_keys[4][$i];
		
						if ( !isset($antimethods[$fk_table_name]) ) $antimethods[$fk_table_name] = array();
						$this->_Antimethods[$fk_table_name][$table_name] = $column_name;
		
						$methods[substr($foreign_keys[2][$i], 0, strpos($foreign_keys[2][$i], 'id'))] = substr($foreign_keys[4][$i], 0, strpos($foreign_keys[4][$i], 'id'));
					}
				}
				$this->_AddTable(substr($table[0], strpos($table[0], $table_prefix)+strlen($table_prefix)), $primary_key, $columns, $methods);
			}
		}
	}

	public function WriteOrmDbClass($filename) {
		$buffer = $this->_GetClass();

		if ( is_writable($filename) ) {
			if ( !$handle = fopen($filename, 'w') ) {
				$this->Error = "Cannot open file ($filename)";
				throw new MakeOrmDbFileException();
			}
		
			if ( fwrite($handle, '<' . '?' . "\n" . $buffer) === FALSE ) {
				$this->Error = "Cannot write to file ($filename)";
				throw new MakeOrmDbFileException();
			}
		
			fclose($handle);
		} else {
			$this->Error = "The file $filename is not writable";
			throw new MakeOrmDbFileException();
		}
	}

	private function _AddTable($table_name, $primary_key, $columns, $methods) {
		$table = new OrmTable($table_name, $primary_key, $columns, $methods);
		$this->_Tables[$table_name] = $table;
	}

	private function _GetTable($table_name) {
		return $this->Tables[$table_name];
	}

	private function _GetClass() {
		$table_prefix = $this->_TablePrefix;
		$dbaddr = $this->_DbAddress;
		$dbuser = $this->_DbUser;
		$dbpassword = $this->_DbPassword;
		$dbname = $this->_DbName;

		$buffer = '// Generated via MAkeOrmDb.class.php' . "\n" .
			'class OrmDb {' . "\n" .
			'	private static $_objs = array();' . "\n" .
			'	private static $_link;' . "\n" .
			'	public static function GetLink() {' . "\n" .
			'		if ( !self::$_link ) {' . "\n" .
			'			self::$_link = mysql_connect(\'' . $dbaddr . '\', \'' . $dbuser . '\', \'' . $dbpassword . '\') or die(\'Could not connect: \' . mysql_error());' . "\n" .
			'			mysql_select_db(\'' . $dbname . '\') or die(\'Could not select database\');' . "\n" .
			'		}' . "\n" .
			'		return self::$_link;' . "\n" .
			'	}' . "\n" .
			'	//private $_rows = array();' . "\n" .
			'	protected static $_readonly = FALSE;' . "\n" .
			'	private $_primarykeys = array(' . "\n";

		foreach ( $this->_PrimaryKeys as $table_name => $column_name ) {
			$buffer .= '		\'' . $table_name . '\' => \'' . $column_name . '\',' . "\n";
		}

		$buffer .= '	);' . "\n" .
			'	public static function MakeReadOnly() {' . "\n" .
			'		self::$_readonly = TRUE;' . "\n" .
			'	}' . "\n" .
			'	public static function SetObj($table, $id, $obj) {' . "\n" .
			'		self::$_objs[$table][$id] = $obj;' . "\n" .
			'	}' . "\n" .
			'	public static function GetObj($table, $id) {' . "\n" .
			'		return ( isset(self::$_objs[$table]) && isset(self::$_objs[$table][$id]) ) ? self::$_objs[$table][$id] : NULL;' . "\n" .
			'	}' . "\n" .
			'	protected function GetRow($table, $id = \'\', $where = \'\') {' . "\n" .
			'		if ( $id ) {' . "\n" .
			'			if ( OrmDb::GetObj($table, $id) ) return OrmDb::GetObj($table, $id);' . "\n" .
			'			$primary_key = $this->_primarykeys[$table];' . "\n" .
			'			$query = \'SELECT * FROM `' . $table_prefix . '\' . $table . \'` WHERE `\' . $primary_key . \'`=\' . $id;' . "\n" .
			'		} elseif ( $where ) {' . "\n" .
			'			$primary_key = $this->_primarykeys[$table];' . "\n" .
			'			$query = \'SELECT * FROM `' . $table_prefix . '\' . $table . \'` WHERE \' . $where;' . "\n" .
			'		} else {' . "\n" .
			'			return NULL;' . "\n" .
			'		}' . "\n" .
			'		$result = mysql_query($query, OrmDb::GetLink());' . "\n" .
			'		$row = mysql_fetch_assoc($result);' . "\n" .
			'		$class_name = $table . \'Table\';' . "\n" .
			'		$row_object = new $class_name($row);' . "\n" .
			'		OrmDb::SetObj($table, $row[$primary_key], $row_object);' . "\n" .
			'		return $row_object;' . "\n" .
			'	}' . "\n" .
			'	protected function GetRows($table, $where=\'\', $order=\'\', $limit=\'\') {' . "\n" .
			'		$query = \'SELECT * FROM `' . $table_prefix . '\' . $table . \'`\' . (( $where ) ? \' WHERE \' . $where : \'\') . (( $order ) ? \' ORDER BY \' . $order : \'\') . (( $limit ) ? \' LIMIT \' . $limit : \'\');' . "\n" .
			'		$result = mysql_query($query, OrmDb::GetLink());' . "\n" .
			'		$rows = array();' . "\n" .
			'		$class_name = $table . \'Table\';' . "\n" .
			'		while ( $row = mysql_fetch_assoc($result) ) {' . "\n" .
			'			$row_object = new $class_name($row);' . "\n" .
			'			$rows[] = $row_object;' . "\n" .
			'			OrmDb::SetObj($table, $row[$this->_primarykeys[$table]], $row_object);' . "\n" .
			'		}' . "\n" .
			'		return $rows;' . "\n" .
			'	}' . "\n" .
			'	protected function GetCount($table, $where = \'\', $limit = \'\') {' . "\n" .
			'		$query = \'SELECT COUNT(*) FROM `' . $table_prefix . '\' . $table . \'`\' . (( $where ) ? \' WHERE \' . $where : \'\') . (( $limit ) ? \' LIMIT \' . $limit : \'\');' . "\n" .
			'		$result = mysql_query($query, OrmDb::GetLink());' . "\n" .
			'		$row = mysql_fetch_assoc($result);' . "\n" .
			'		$count = $row[\'COUNT(*)\'];' . "\n" .
			'		return $count;' . "\n" .
			'	}' . "\n" .
			'	/*public function __get($nm) {' . "\n" .
			'		if ( method_exists($this, $nm) ) {' . "\n" .
			'			$tmp = $this->$nm();' . "\n" .
			'			return $tmp;' . "\n" .
			'		} else {' . "\n" .
			'			return $this->GetRows(substr($nm, 0, -1));' . "\n" .
			'		}' . "\n" .
			'		return null;' . "\n" .
			'	}*/' . "\n";

		foreach ( $this->_Tables as $table ) {
			$buffer .= '	public function ' . $table->TableName . '($a) {' . "\n" .
			'		if ( is_array($a) ) {' . "\n" .
			'			$obj = new ' . $table->TableName . 'Table($a);' . "\n" .
			'			return $obj;' . "\n" .
			'		} else {' . "\n" .
			'			return $this->GetRow(\'' . $table->TableName . '\', $a);' . "\n" .
			'		}' . "\n" .
			'	}' . "\n" .
			'	public function ' . $table->TableName . 's($where = \'\', $order = \'\', $limit = \'\') {' . "\n" .
			'		return $this->GetRows(\'' . $table->TableName . '\', $where, $order, $limit);' . "\n" .
			'	}' . "\n" .
			'	public function ' . $table->TableName . 'Count($where = \'\', $limit = \'\') {' . "\n" .
			'		return $this->GetCount(\'' . $table->TableName . '\', $where, $limit);' . "\n" .
			'	}' . "\n";
		}

		$buffer .= '	public function save() {' . "\n" .
			'		if ( OrmDb::$_readonly || get_class($this) == __CLASS__ ) {' . "\n" .
			'			return;' . "\n" .
			'		} else {' . "\n" .
			'			if ( $this->{$this->PrimaryKey} ) {' . "\n" .
			'				$query = \'UPDATE `' . $table_prefix . '\' . $this->_tablename . \'` SET \';' . "\n" .
			'				$updates = array();' . "\n" .
			'				foreach ( $this->Columns as $column ) {' . "\n" .
			'					if ( $this->$column !== NULL ) {' . "\n" .
			'						$updates[] = $column . \'="\' . mysql_escape_string($this->$column) . \'"\';' . "\n" .
			'					} else {' . "\n" .
			'						$updates[] = $column . \'=NULL\';' . "\n" .
			'					}' . "\n" .
			'				}' . "\n" .
			'				$query .= implode(\', \', $updates);' . "\n" .
			'				$query .= \' WHERE \' . $this->PrimaryKey . \'=\' . $this->{$this->PrimaryKey};' . "\n" .
			'				$result = mysql_query($query, OrmDb::GetLink());' . "\n" .
			'			} else {' . "\n" .
			'				$query = \'INSERT INTO `' . $table_prefix . '\' . $this->_tablename . \'` (\';' . "\n" .
			'				$query .= implode(\', \', $this->Columns);' . "\n" .
			'				$query .= \') VALUES(\';' . "\n" .
			'				$values = array();' . "\n" .
			'				foreach ( $this->Columns as $column ) {' . "\n" .
			'					$values[] = ( isset($this->$column) ) ? \'"\' . mysql_escape_string($this->$column) . \'"\' : \'NULL\';' . "\n" .
			'				}' . "\n" .
			'				$query .= implode(\', \', $values);' . "\n" .
			'				$query .= \')\';' . "\n" .
			'				$result = mysql_query($query, OrmDb::GetLink());' . "\n" .
			'				$id = mysql_insert_id();' . "\n" .
			'				$this->{$this->PrimaryKey} = $id;' . "\n" .
			'				//OrmDb::SetObj($this->_tablename, $id, $this);' . "\n" .
			'			}' . "\n" .
			'			return $result;' . "\n" .
			'		}' . "\n" .
			'	}' . "\n" .
			'}' . "\n";

		foreach ( $this->_Tables as $table ) {
			$buffer .= 'class ' . $table->TableName . 'Table extends OrmDb {' . "\n";
			foreach ( $table->Columns as $column ) {
				$buffer .= '	public $' . $column . ';' . "\n";
			}

			$buffer .= '	protected $Columns = array(' . "\n";
			foreach ( $table->Columns as $column ) {
				$buffer .= '		\'' . $column . '\',' . "\n";
			}
			$buffer .= '	);' . "\n";

			$buffer .= '	public $PrimaryKey = \'' . $table->PrimaryKey . '\';' . "\n";
			$buffer .= '	protected $_tablename = \'' . $table->TableName . '\';' . "\n";

			$buffer .= '	public function __construct($columns) {' . "\n";
			foreach ( $table->Columns as $column ) {
				$buffer .= '		$this->' . $column . ' = ( isset($columns[\'' . $column . '\']) ) ? $columns[\'' . $column . '\'] : NULL;' . "\n";
			}
			$buffer .= '	}' . "\n";

			if ( isset($this->_Antimethods[$table->TableName]) ) {
				foreach ( $this->_Antimethods[$table->TableName] as $table_name => $column_name ) {
					$buffer .= '	public function ' . $table_name . 's($where = \'\', $order = \'\', $limit = \'\') {' . "\n" .
						'		return $this->GetRows(\'' . $table_name . '\', \'' . $column_name . '=\' . $this->' . $column_name . ' . (($where) ? \' AND \' . $where : \'\'), $order, $limit);' . "\n" .
						'	}' . "\n" .
						'	public function ' . $table_name . 'Count($where = \'\', $limit = \'\') {' . "\n" .
						'		return $this->GetCount(\'' . $table_name . '\', \'' . $column_name . '=\' . $this->' . $column_name . ' . (($where) ? \' AND \' . $where : \'\'), $limit);' . "\n" .
						'	}' . "\n";
				}
			}

			foreach ( $table->Methods as $name => $key ) {
				$buffer .= '	public function ' . $name . '() {' . "\n" .
					'		return $this->GetRow(\'' . $key . '\', $this->' . $key . 'id);' . "\n" .
					'	}' . "\n";
			}

			$buffer .= '}' . "\n\n";
		}

		return $buffer;
	}
}

class OrmTable {
	public $TableName;
	public $PrimaryKey;
	public $Columns = array();
	public $Methods = array();

	public function __construct($table_name, $primary_key, $columns, $methods) {
		$this->TableName = $table_name;
		$this->PrimaryKey = $primary_key;
		$this->Columns = $columns;
		$this->Methods = $methods;
	}
}
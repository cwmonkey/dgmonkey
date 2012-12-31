<?php

define('table_prefix', 'monkavi_');

$dbaddr = 'localhost';
$dbuser = 'mysmilies';
$dbpassword = 'mys#1357s';
$dbname = 'monki';

$mmt = new MakeMonkaviTableParent();

$link = mysql_connect($dbaddr, $dbuser, $dbpassword) or die('Could not connect: ' . mysql_error());
mysql_select_db($dbname) or die('Could not select database');
$query = "SHOW TABLES";
$table_result = mysql_query($query);

$parent_methods = array();
$antimethods = array();
$primary_keys = array();

while ( $table = mysql_fetch_array($table_result, MYSQL_NUM) ) {
	if ( preg_match('/^' . table_prefix . '/', $table[0]) ) {
		$table_name = substr($table[0], strpos($table[0], table_prefix) + strlen(table_prefix));
		$query = 'SHOW COLUMNS FROM `' . $table[0] . '`';
		$column_result = mysql_query($query);
		$columns = array();
		while ( $column = mysql_fetch_assoc($column_result) ) {
			if ( $column['Key'] == 'PRI' ) $primary_key = $column['Field'];
			$columns[] = $column['Field'];
		}

		$primary_keys[$table_name] = $primary_key;

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
				$fk_table_name = substr($foreign_keys[3][$i], strpos($foreign_keys[3][$i], table_prefix) + strlen(table_prefix));
				$fk_column_name = $foreign_keys[4][$i];

				if ( !isset($antimethods[$fk_table_name]) ) $antimethods[$fk_table_name] = array();
				$antimethods[$fk_table_name][$table_name] = $column_name;

				$methods[substr($foreign_keys[2][$i], 0, strpos($foreign_keys[2][$i], 'id'))] = substr($foreign_keys[4][$i], 0, strpos($foreign_keys[4][$i], 'id'));
			}
		}
		$mmt->AddTable(substr($table[0], strpos($table[0], table_prefix)+strlen(table_prefix)), $primary_key, $columns, $methods);
	}
}
//print_r($antimethods);
$mmt->Antimethods = $antimethods;
$mmt->PrimaryKeys = $primary_keys;

$mmt->RenderClass($dbaddr, $dbuser, $dbpassword, $dbname);
/*exit;

//echo file_get_contents(table_prefix . 'dbclasses.php');
include('OrmDb.class.php');

$mt = new OrmDb;

// The following should work:

$smileys = $mt->smileys();

foreach ( $smileys as $smiley ) {
	echo $smiley->path . $smiley->usr()->dname . "\n";
}

$usr = $mt->usr(1);
$smileys = $usr->smileys();
foreach ( $smileys as $smiley ) {
	echo $smiley->path . "\n";
}

$usr->fname = 'Newfname';
//echo $usr->fname . "\n";
//echo $smileys[0]->usr()->fname . "\n";
$usr->save();

// The following should not work/return NULL:

//echo OrmDb::GetObj('usr', 1)->fname . "\n"; // This does work, unfortunately
OrmDb::MakeReadOnly();
var_dump($usr->save());
echo OrmDb::$_objs['usr'][1]->fname . "\n";
var_dump($usr->forum(1));

// testing
*/
class MakeMonkaviTableParent {
	private $Tables = array();
	private $Buffer;
	public $Antimethods = array();
	public $PrimaryKeys = array();

	public function AddTable($table_name, $primary_key, $columns, $methods) {
		$table = new MonkaviTable($table_name, $primary_key, $columns, $methods);
		$this->Tables[$table_name] = $table;
	}

	public function getTable($table_name) {
		return $this->Tables[$table_name];
	}

	public function RenderClass($dbaddr, $dbuser, $dbpassword, $dbname) {
		$this->Buffer = '// Generated via schematoclass.php' . "\n" .
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

		foreach ( $this->PrimaryKeys as $table_name => $column_name ) {
			$this->Buffer .= '		\'' . $table_name . '\' => \'' . $column_name . '\',' . "\n";
		}

		$this->Buffer .= '	);' . "\n" .
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
			'			$query = \'SELECT * FROM `' . table_prefix . '\' . $table . \'` WHERE `\' . $primary_key . \'`=\' . $id;' . "\n" .
			'		} elseif ( $where ) {' . "\n" .
			'			$primary_key = $this->_primarykeys[$table];' . "\n" .
			'			$query = \'SELECT * FROM `' . table_prefix . '\' . $table . \'` WHERE \' . $where;' . "\n" .
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
			'		$query = \'SELECT * FROM `' . table_prefix . '\' . $table . \'`\' . (( $where ) ? \' WHERE \' . $where : \'\') . (( $order ) ? \' ORDER BY \' . $order : \'\') . (( $limit ) ? \' LIMIT \' . $limit : \'\');' . "\n" .
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
			'		$query = \'SELECT COUNT(*) FROM `' . table_prefix . '\' . $table . \'`\' . (( $where ) ? \' WHERE \' . $where : \'\') . (( $limit ) ? \' LIMIT \' . $limit : \'\');' . "\n" .
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

		foreach ( $this->Tables as $table ) {
			$this->Buffer .= '	public function ' . $table->TableName . '($a) {' . "\n" .
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

		$this->Buffer .= '	public function save() {' . "\n" .
			'		if ( OrmDb::$_readonly || get_class($this) == __CLASS__ ) {' . "\n" .
			'			return;' . "\n" .
			'		} else {' . "\n" .
			'			if ( $this->{$this->PrimaryKey} ) {' . "\n" .
			'				$query = \'UPDATE `' . table_prefix . '\' . $this->_tablename . \'` SET \';' . "\n" .
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
			'				$query = \'INSERT INTO `' . table_prefix . '\' . $this->_tablename . \'` (\';' . "\n" .
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

		foreach ( $this->Tables as $table ) {
			$this->Buffer .= 'class ' . $table->TableName . 'Table extends OrmDb {' . "\n";
			foreach ( $table->Columns as $column ) {
				$this->Buffer .= '	public $' . $column . ';' . "\n";
			}

			$this->Buffer .= '	protected $Columns = array(' . "\n";
			foreach ( $table->Columns as $column ) {
				$this->Buffer .= '		\'' . $column . '\',' . "\n";
			}
			$this->Buffer .= '	);' . "\n";

			$this->Buffer .= '	public $PrimaryKey = \'' . $table->PrimaryKey . '\';' . "\n";
			$this->Buffer .= '	protected $_tablename = \'' . $table->TableName . '\';' . "\n";

			//$this->Buffer .= '	//public function __call($f, $a) { }' . "\n";
			$this->Buffer .= '	public function __construct($columns) {' . "\n";
			foreach ( $table->Columns as $column ) {
				$this->Buffer .= '		$this->' . $column . ' = ( isset($columns[\'' . $column . '\']) ) ? $columns[\'' . $column . '\'] : NULL;' . "\n";
			}
			$this->Buffer .= '	}' . "\n";

			if ( isset($this->Antimethods[$table->TableName]) ) {
				foreach ( $this->Antimethods[$table->TableName] as $table_name => $column_name ) {
					$this->Buffer .= '	public function ' . $table_name . 's($where = \'\', $order = \'\', $limit = \'\') {' . "\n" .
						'		return $this->GetRows(\'' . $table_name . '\', \'' . $column_name . '=\' . $this->' . $column_name . ' . (($where) ? \' AND \' . $where : \'\'), $order, $limit);' . "\n" .
						'	}' . "\n" .
						'	public function ' . $table_name . 'Count($where = \'\', $limit = \'\') {' . "\n" .
						'		return $this->GetCount(\'' . $table_name . '\', \'' . $column_name . '=\' . $this->' . $column_name . ' . (($where) ? \' AND \' . $where : \'\'), $limit);' . "\n" .
						'	}' . "\n";
				}
			}

			foreach ( $table->Methods as $name => $key ) {
				$this->Buffer .= '	public function ' . $name . '() {' . "\n" .
					'		return $this->GetRow(\'' . $key . '\', $this->' . $key . 'id);' . "\n" .
					'	}' . "\n";
			}
			$this->Buffer .= '}' . "\n\n";
		}

		//$this->Buffer .= 'include(\'database.class.php\')';

		$file = '/home/monkey/public_html/subdomains/monkake/app/classes/OrmDb.class.php';
		$handle = fopen($file, 'w');
		fwrite($handle, '<' . '?' . "\n" . $this->Buffer . "\n" . '?' . '>');
		fclose($handle);
		//chmod($file, 0777);
	}
}

class MonkaviTable {
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

?>
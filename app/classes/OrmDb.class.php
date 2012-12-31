<?
// Generated via MAkeOrmDb.class.php
class OrmDb {
	private static $_objs = array();
	private static $_link;
	public static function GetLink() {
		if ( !self::$_link ) {
			self::$_link = mysql_connect('localhost', 'mysmilies', 'mys#1357s') or die('Could not connect: ' . mysql_error());
			mysql_select_db('monki') or die('Could not select database');
		}
		return self::$_link;
	}
	//private $_rows = array();
	protected static $_readonly = FALSE;
	private $_primarykeys = array(
		'blog' => 'blogid',
		'controller' => 'controllerid',
		'form' => 'formid',
		'forum' => 'forumid',
		'grp' => 'grpid',
		'grpright' => 'grprightid',
		'page' => 'pageid',
		'post' => 'postid',
		'right' => 'rightid',
		'scategory' => 'scategoryid',
		'sgenre' => 'sgenreid',
		'smiley' => 'smileyid',
		'smileysgenre' => 'smileysgenreid',
		'usr' => 'usrid',
		'usrgrp' => 'usrgrpid',
		'usrright' => 'usrrightid',
		'worldet' => 'wordletid',
	);
	public static function MakeReadOnly() {
		self::$_readonly = TRUE;
	}
	public static function SetObj($table, $id, $obj) {
		self::$_objs[$table][$id] = $obj;
	}
	public static function GetObj($table, $id) {
		return ( isset(self::$_objs[$table]) && isset(self::$_objs[$table][$id]) ) ? self::$_objs[$table][$id] : NULL;
	}
	protected function GetRow($table, $id = '', $where = '') {
		if ( $id ) {
			if ( OrmDb::GetObj($table, $id) ) return OrmDb::GetObj($table, $id);
			$primary_key = $this->_primarykeys[$table];
			$query = 'SELECT * FROM `monkavi_' . $table . '` WHERE `' . $primary_key . '`=' . $id;
		} elseif ( $where ) {
			$primary_key = $this->_primarykeys[$table];
			$query = 'SELECT * FROM `monkavi_' . $table . '` WHERE ' . $where;
		} else {
			return NULL;
		}
		$result = mysql_query($query, OrmDb::GetLink());
		$row = mysql_fetch_assoc($result);
		$class_name = $table . 'Table';
		$row_object = new $class_name($row);
		OrmDb::SetObj($table, $row[$primary_key], $row_object);
		return $row_object;
	}
	protected function GetRows($table, $where='', $order='', $limit='') {
		$query = 'SELECT * FROM `monkavi_' . $table . '`' . (( $where ) ? ' WHERE ' . $where : '') . (( $order ) ? ' ORDER BY ' . $order : '') . (( $limit ) ? ' LIMIT ' . $limit : '');
		$result = mysql_query($query, OrmDb::GetLink());
		$rows = array();
		$class_name = $table . 'Table';
		while ( $row = mysql_fetch_assoc($result) ) {
			$row_object = new $class_name($row);
			$rows[] = $row_object;
			OrmDb::SetObj($table, $row[$this->_primarykeys[$table]], $row_object);
		}
		return $rows;
	}
	protected function GetCount($table, $where = '', $limit = '') {
		$query = 'SELECT COUNT(*) FROM `monkavi_' . $table . '`' . (( $where ) ? ' WHERE ' . $where : '') . (( $limit ) ? ' LIMIT ' . $limit : '');
		$result = mysql_query($query, OrmDb::GetLink());
		$row = mysql_fetch_assoc($result);
		$count = $row['COUNT(*)'];
		return $count;
	}
	/*public function __get($nm) {
		if ( method_exists($this, $nm) ) {
			$tmp = $this->$nm();
			return $tmp;
		} else {
			return $this->GetRows(substr($nm, 0, -1));
		}
		return null;
	}*/
	public function blog($a) {
		if ( is_array($a) ) {
			$obj = new blogTable($a);
			return $obj;
		} else {
			return $this->GetRow('blog', $a);
		}
	}
	public function blogs($where = '', $order = '', $limit = '') {
		return $this->GetRows('blog', $where, $order, $limit);
	}
	public function blogCount($where = '', $limit = '') {
		return $this->GetCount('blog', $where, $limit);
	}
	public function controller($a) {
		if ( is_array($a) ) {
			$obj = new controllerTable($a);
			return $obj;
		} else {
			return $this->GetRow('controller', $a);
		}
	}
	public function controllers($where = '', $order = '', $limit = '') {
		return $this->GetRows('controller', $where, $order, $limit);
	}
	public function controllerCount($where = '', $limit = '') {
		return $this->GetCount('controller', $where, $limit);
	}
	public function form($a) {
		if ( is_array($a) ) {
			$obj = new formTable($a);
			return $obj;
		} else {
			return $this->GetRow('form', $a);
		}
	}
	public function forms($where = '', $order = '', $limit = '') {
		return $this->GetRows('form', $where, $order, $limit);
	}
	public function formCount($where = '', $limit = '') {
		return $this->GetCount('form', $where, $limit);
	}
	public function forum($a) {
		if ( is_array($a) ) {
			$obj = new forumTable($a);
			return $obj;
		} else {
			return $this->GetRow('forum', $a);
		}
	}
	public function forums($where = '', $order = '', $limit = '') {
		return $this->GetRows('forum', $where, $order, $limit);
	}
	public function forumCount($where = '', $limit = '') {
		return $this->GetCount('forum', $where, $limit);
	}
	public function grp($a) {
		if ( is_array($a) ) {
			$obj = new grpTable($a);
			return $obj;
		} else {
			return $this->GetRow('grp', $a);
		}
	}
	public function grps($where = '', $order = '', $limit = '') {
		return $this->GetRows('grp', $where, $order, $limit);
	}
	public function grpCount($where = '', $limit = '') {
		return $this->GetCount('grp', $where, $limit);
	}
	public function grpright($a) {
		if ( is_array($a) ) {
			$obj = new grprightTable($a);
			return $obj;
		} else {
			return $this->GetRow('grpright', $a);
		}
	}
	public function grprights($where = '', $order = '', $limit = '') {
		return $this->GetRows('grpright', $where, $order, $limit);
	}
	public function grprightCount($where = '', $limit = '') {
		return $this->GetCount('grpright', $where, $limit);
	}
	public function page($a) {
		if ( is_array($a) ) {
			$obj = new pageTable($a);
			return $obj;
		} else {
			return $this->GetRow('page', $a);
		}
	}
	public function pages($where = '', $order = '', $limit = '') {
		return $this->GetRows('page', $where, $order, $limit);
	}
	public function pageCount($where = '', $limit = '') {
		return $this->GetCount('page', $where, $limit);
	}
	public function post($a) {
		if ( is_array($a) ) {
			$obj = new postTable($a);
			return $obj;
		} else {
			return $this->GetRow('post', $a);
		}
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', $where, $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', $where, $limit);
	}
	public function right($a) {
		if ( is_array($a) ) {
			$obj = new rightTable($a);
			return $obj;
		} else {
			return $this->GetRow('right', $a);
		}
	}
	public function rights($where = '', $order = '', $limit = '') {
		return $this->GetRows('right', $where, $order, $limit);
	}
	public function rightCount($where = '', $limit = '') {
		return $this->GetCount('right', $where, $limit);
	}
	public function scategory($a) {
		if ( is_array($a) ) {
			$obj = new scategoryTable($a);
			return $obj;
		} else {
			return $this->GetRow('scategory', $a);
		}
	}
	public function scategorys($where = '', $order = '', $limit = '') {
		return $this->GetRows('scategory', $where, $order, $limit);
	}
	public function scategoryCount($where = '', $limit = '') {
		return $this->GetCount('scategory', $where, $limit);
	}
	public function sgenre($a) {
		if ( is_array($a) ) {
			$obj = new sgenreTable($a);
			return $obj;
		} else {
			return $this->GetRow('sgenre', $a);
		}
	}
	public function sgenres($where = '', $order = '', $limit = '') {
		return $this->GetRows('sgenre', $where, $order, $limit);
	}
	public function sgenreCount($where = '', $limit = '') {
		return $this->GetCount('sgenre', $where, $limit);
	}
	public function smiley($a) {
		if ( is_array($a) ) {
			$obj = new smileyTable($a);
			return $obj;
		} else {
			return $this->GetRow('smiley', $a);
		}
	}
	public function smileys($where = '', $order = '', $limit = '') {
		return $this->GetRows('smiley', $where, $order, $limit);
	}
	public function smileyCount($where = '', $limit = '') {
		return $this->GetCount('smiley', $where, $limit);
	}
	public function smileysgenre($a) {
		if ( is_array($a) ) {
			$obj = new smileysgenreTable($a);
			return $obj;
		} else {
			return $this->GetRow('smileysgenre', $a);
		}
	}
	public function smileysgenres($where = '', $order = '', $limit = '') {
		return $this->GetRows('smileysgenre', $where, $order, $limit);
	}
	public function smileysgenreCount($where = '', $limit = '') {
		return $this->GetCount('smileysgenre', $where, $limit);
	}
	public function usr($a) {
		if ( is_array($a) ) {
			$obj = new usrTable($a);
			return $obj;
		} else {
			return $this->GetRow('usr', $a);
		}
	}
	public function usrs($where = '', $order = '', $limit = '') {
		return $this->GetRows('usr', $where, $order, $limit);
	}
	public function usrCount($where = '', $limit = '') {
		return $this->GetCount('usr', $where, $limit);
	}
	public function usrgrp($a) {
		if ( is_array($a) ) {
			$obj = new usrgrpTable($a);
			return $obj;
		} else {
			return $this->GetRow('usrgrp', $a);
		}
	}
	public function usrgrps($where = '', $order = '', $limit = '') {
		return $this->GetRows('usrgrp', $where, $order, $limit);
	}
	public function usrgrpCount($where = '', $limit = '') {
		return $this->GetCount('usrgrp', $where, $limit);
	}
	public function usrright($a) {
		if ( is_array($a) ) {
			$obj = new usrrightTable($a);
			return $obj;
		} else {
			return $this->GetRow('usrright', $a);
		}
	}
	public function usrrights($where = '', $order = '', $limit = '') {
		return $this->GetRows('usrright', $where, $order, $limit);
	}
	public function usrrightCount($where = '', $limit = '') {
		return $this->GetCount('usrright', $where, $limit);
	}
	public function worldet($a) {
		if ( is_array($a) ) {
			$obj = new worldetTable($a);
			return $obj;
		} else {
			return $this->GetRow('worldet', $a);
		}
	}
	public function worldets($where = '', $order = '', $limit = '') {
		return $this->GetRows('worldet', $where, $order, $limit);
	}
	public function worldetCount($where = '', $limit = '') {
		return $this->GetCount('worldet', $where, $limit);
	}
	public function save() {
		if ( OrmDb::$_readonly || get_class($this) == __CLASS__ ) {
			return;
		} else {
			if ( $this->{$this->PrimaryKey} ) {
				$query = 'UPDATE `monkavi_' . $this->_tablename . '` SET ';
				$updates = array();
				foreach ( $this->Columns as $column ) {
					if ( $this->$column !== NULL ) {
						$updates[] = $column . '="' . mysql_escape_string($this->$column) . '"';
					} else {
						$updates[] = $column . '=NULL';
					}
				}
				$query .= implode(', ', $updates);
				$query .= ' WHERE ' . $this->PrimaryKey . '=' . $this->{$this->PrimaryKey};
				$result = mysql_query($query, OrmDb::GetLink());
			} else {
				$query = 'INSERT INTO `monkavi_' . $this->_tablename . '` (';
				$query .= implode(', ', $this->Columns);
				$query .= ') VALUES(';
				$values = array();
				foreach ( $this->Columns as $column ) {
					$values[] = ( isset($this->$column) ) ? '"' . mysql_escape_string($this->$column) . '"' : 'NULL';
				}
				$query .= implode(', ', $values);
				$query .= ')';
				$result = mysql_query($query, OrmDb::GetLink());
				$id = mysql_insert_id();
				$this->{$this->PrimaryKey} = $id;
				//OrmDb::SetObj($this->_tablename, $id, $this);
			}
			return $result;
		}
	}
}
class blogTable extends OrmDb {
	public $blogid;
	public $usrid;
	public $dname;
	public $enabled;
	protected $Columns = array(
		'blogid',
		'usrid',
		'dname',
		'enabled',
	);
	public $PrimaryKey = 'blogid';
	protected $_tablename = 'blog';
	public function __construct($columns) {
		$this->blogid = ( isset($columns['blogid']) ) ? $columns['blogid'] : NULL;
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', 'blogid=' . $this->blogid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', 'blogid=' . $this->blogid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usr() {
		return $this->GetRow('usr', $this->usrid);
	}
}

class controllerTable extends OrmDb {
	public $controllerid;
	public $path;
	public $description;
	public $enabled;
	protected $Columns = array(
		'controllerid',
		'path',
		'description',
		'enabled',
	);
	public $PrimaryKey = 'controllerid';
	protected $_tablename = 'controller';
	public function __construct($columns) {
		$this->controllerid = ( isset($columns['controllerid']) ) ? $columns['controllerid'] : NULL;
		$this->path = ( isset($columns['path']) ) ? $columns['path'] : NULL;
		$this->description = ( isset($columns['description']) ) ? $columns['description'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function controllers($where = '', $order = '', $limit = '') {
		return $this->GetRows('controller', 'controllerid=' . $this->controllerid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function controllerCount($where = '', $limit = '') {
		return $this->GetCount('controller', 'controllerid=' . $this->controllerid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function controller() {
		return $this->GetRow('controller', $this->controllerid);
	}
}

class formTable extends OrmDb {
	public $formid;
	public $table;
	public $column;
	public $fkeycolumn;
	public $noedit;
	public $validation;
	public $validationdefault;
	public $visible;
	public $list;
	public $type;
	public $label;
	public $order;
	public $default;
	public $usecolumndefault;
	protected $Columns = array(
		'formid',
		'table',
		'column',
		'fkeycolumn',
		'noedit',
		'validation',
		'validationdefault',
		'visible',
		'list',
		'type',
		'label',
		'order',
		'default',
		'usecolumndefault',
	);
	public $PrimaryKey = 'formid';
	protected $_tablename = 'form';
	public function __construct($columns) {
		$this->formid = ( isset($columns['formid']) ) ? $columns['formid'] : NULL;
		$this->table = ( isset($columns['table']) ) ? $columns['table'] : NULL;
		$this->column = ( isset($columns['column']) ) ? $columns['column'] : NULL;
		$this->fkeycolumn = ( isset($columns['fkeycolumn']) ) ? $columns['fkeycolumn'] : NULL;
		$this->noedit = ( isset($columns['noedit']) ) ? $columns['noedit'] : NULL;
		$this->validation = ( isset($columns['validation']) ) ? $columns['validation'] : NULL;
		$this->validationdefault = ( isset($columns['validationdefault']) ) ? $columns['validationdefault'] : NULL;
		$this->visible = ( isset($columns['visible']) ) ? $columns['visible'] : NULL;
		$this->list = ( isset($columns['list']) ) ? $columns['list'] : NULL;
		$this->type = ( isset($columns['type']) ) ? $columns['type'] : NULL;
		$this->label = ( isset($columns['label']) ) ? $columns['label'] : NULL;
		$this->order = ( isset($columns['order']) ) ? $columns['order'] : NULL;
		$this->default = ( isset($columns['default']) ) ? $columns['default'] : NULL;
		$this->usecolumndefault = ( isset($columns['usecolumndefault']) ) ? $columns['usecolumndefault'] : NULL;
	}
}

class forumTable extends OrmDb {
	public $forumid;
	public $dname;
	public $description;
	public $enabled;
	protected $Columns = array(
		'forumid',
		'dname',
		'description',
		'enabled',
	);
	public $PrimaryKey = 'forumid';
	protected $_tablename = 'forum';
	public function __construct($columns) {
		$this->forumid = ( isset($columns['forumid']) ) ? $columns['forumid'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->description = ( isset($columns['description']) ) ? $columns['description'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', 'forumid=' . $this->forumid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', 'forumid=' . $this->forumid . (($where) ? ' AND ' . $where : ''), $limit);
	}
}

class grpTable extends OrmDb {
	public $grpid;
	public $dname;
	public $enabled;
	protected $Columns = array(
		'grpid',
		'dname',
		'enabled',
	);
	public $PrimaryKey = 'grpid';
	protected $_tablename = 'grp';
	public function __construct($columns) {
		$this->grpid = ( isset($columns['grpid']) ) ? $columns['grpid'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function grprights($where = '', $order = '', $limit = '') {
		return $this->GetRows('grpright', 'grpid=' . $this->grpid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function grprightCount($where = '', $limit = '') {
		return $this->GetCount('grpright', 'grpid=' . $this->grpid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usrgrps($where = '', $order = '', $limit = '') {
		return $this->GetRows('usrgrp', 'grpid=' . $this->grpid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function usrgrpCount($where = '', $limit = '') {
		return $this->GetCount('usrgrp', 'grpid=' . $this->grpid . (($where) ? ' AND ' . $where : ''), $limit);
	}
}

class grprightTable extends OrmDb {
	public $grprightid;
	public $rightid;
	public $grpid;
	protected $Columns = array(
		'grprightid',
		'rightid',
		'grpid',
	);
	public $PrimaryKey = 'grprightid';
	protected $_tablename = 'grpright';
	public function __construct($columns) {
		$this->grprightid = ( isset($columns['grprightid']) ) ? $columns['grprightid'] : NULL;
		$this->rightid = ( isset($columns['rightid']) ) ? $columns['rightid'] : NULL;
		$this->grpid = ( isset($columns['grpid']) ) ? $columns['grpid'] : NULL;
	}
	public function grp() {
		return $this->GetRow('grp', $this->grpid);
	}
	public function right() {
		return $this->GetRow('right', $this->rightid);
	}
}

class pageTable extends OrmDb {
	public $pageid;
	public $usrid;
	public $controllerid;
	public $title;
	public $content;
	public $cache;
	public $enabled;
	protected $Columns = array(
		'pageid',
		'usrid',
		'controllerid',
		'title',
		'content',
		'cache',
		'enabled',
	);
	public $PrimaryKey = 'pageid';
	protected $_tablename = 'page';
	public function __construct($columns) {
		$this->pageid = ( isset($columns['pageid']) ) ? $columns['pageid'] : NULL;
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
		$this->controllerid = ( isset($columns['controllerid']) ) ? $columns['controllerid'] : NULL;
		$this->title = ( isset($columns['title']) ) ? $columns['title'] : NULL;
		$this->content = ( isset($columns['content']) ) ? $columns['content'] : NULL;
		$this->cache = ( isset($columns['cache']) ) ? $columns['cache'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', 'pageid=' . $this->pageid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', 'pageid=' . $this->pageid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function worldets($where = '', $order = '', $limit = '') {
		return $this->GetRows('worldet', 'pageid=' . $this->pageid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function worldetCount($where = '', $limit = '') {
		return $this->GetCount('worldet', 'pageid=' . $this->pageid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usr() {
		return $this->GetRow('usr', $this->usrid);
	}
}

class postTable extends OrmDb {
	public $postid;
	public $forumid;
	public $blogid;
	public $rpostid;
	public $pageid;
	public $pusrid;
	public $usrid;
	public $sgenreid;
	public $topic;
	public $created;
	public $title;
	public $body;
	public $enabled;
	protected $Columns = array(
		'postid',
		'forumid',
		'blogid',
		'rpostid',
		'pageid',
		'pusrid',
		'usrid',
		'sgenreid',
		'topic',
		'created',
		'title',
		'body',
		'enabled',
	);
	public $PrimaryKey = 'postid';
	protected $_tablename = 'post';
	public function __construct($columns) {
		$this->postid = ( isset($columns['postid']) ) ? $columns['postid'] : NULL;
		$this->forumid = ( isset($columns['forumid']) ) ? $columns['forumid'] : NULL;
		$this->blogid = ( isset($columns['blogid']) ) ? $columns['blogid'] : NULL;
		$this->rpostid = ( isset($columns['rpostid']) ) ? $columns['rpostid'] : NULL;
		$this->pageid = ( isset($columns['pageid']) ) ? $columns['pageid'] : NULL;
		$this->pusrid = ( isset($columns['pusrid']) ) ? $columns['pusrid'] : NULL;
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
		$this->sgenreid = ( isset($columns['sgenreid']) ) ? $columns['sgenreid'] : NULL;
		$this->topic = ( isset($columns['topic']) ) ? $columns['topic'] : NULL;
		$this->created = ( isset($columns['created']) ) ? $columns['created'] : NULL;
		$this->title = ( isset($columns['title']) ) ? $columns['title'] : NULL;
		$this->body = ( isset($columns['body']) ) ? $columns['body'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', 'rpostid=' . $this->rpostid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', 'rpostid=' . $this->rpostid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usr() {
		return $this->GetRow('usr', $this->usrid);
	}
	public function sgenre() {
		return $this->GetRow('sgenre', $this->sgenreid);
	}
	public function pusr() {
		return $this->GetRow('usr', $this->usrid);
	}
	public function page() {
		return $this->GetRow('page', $this->pageid);
	}
	public function rpost() {
		return $this->GetRow('post', $this->postid);
	}
	public function blog() {
		return $this->GetRow('blog', $this->blogid);
	}
	public function forum() {
		return $this->GetRow('forum', $this->forumid);
	}
}

class rightTable extends OrmDb {
	public $rightid;
	public $dname;
	public $description;
	public $enabled;
	protected $Columns = array(
		'rightid',
		'dname',
		'description',
		'enabled',
	);
	public $PrimaryKey = 'rightid';
	protected $_tablename = 'right';
	public function __construct($columns) {
		$this->rightid = ( isset($columns['rightid']) ) ? $columns['rightid'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->description = ( isset($columns['description']) ) ? $columns['description'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function grprights($where = '', $order = '', $limit = '') {
		return $this->GetRows('grpright', 'rightid=' . $this->rightid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function grprightCount($where = '', $limit = '') {
		return $this->GetCount('grpright', 'rightid=' . $this->rightid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usrrights($where = '', $order = '', $limit = '') {
		return $this->GetRows('usrright', 'rightid=' . $this->rightid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function usrrightCount($where = '', $limit = '') {
		return $this->GetCount('usrright', 'rightid=' . $this->rightid . (($where) ? ' AND ' . $where : ''), $limit);
	}
}

class scategoryTable extends OrmDb {
	public $scategoryid;
	public $dname;
	public $urlname;
	public $enabled;
	protected $Columns = array(
		'scategoryid',
		'dname',
		'urlname',
		'enabled',
	);
	public $PrimaryKey = 'scategoryid';
	protected $_tablename = 'scategory';
	public function __construct($columns) {
		$this->scategoryid = ( isset($columns['scategoryid']) ) ? $columns['scategoryid'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->urlname = ( isset($columns['urlname']) ) ? $columns['urlname'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function smileys($where = '', $order = '', $limit = '') {
		return $this->GetRows('smiley', 'scategoryid=' . $this->scategoryid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function smileyCount($where = '', $limit = '') {
		return $this->GetCount('smiley', 'scategoryid=' . $this->scategoryid . (($where) ? ' AND ' . $where : ''), $limit);
	}
}

class sgenreTable extends OrmDb {
	public $sgenreid;
	public $dname;
	public $dimage;
	protected $Columns = array(
		'sgenreid',
		'dname',
		'dimage',
	);
	public $PrimaryKey = 'sgenreid';
	protected $_tablename = 'sgenre';
	public function __construct($columns) {
		$this->sgenreid = ( isset($columns['sgenreid']) ) ? $columns['sgenreid'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->dimage = ( isset($columns['dimage']) ) ? $columns['dimage'] : NULL;
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', 'sgenreid=' . $this->sgenreid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', 'sgenreid=' . $this->sgenreid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function smileysgenres($where = '', $order = '', $limit = '') {
		return $this->GetRows('smileysgenre', 'sgenreid=' . $this->sgenreid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function smileysgenreCount($where = '', $limit = '') {
		return $this->GetCount('smileysgenre', 'sgenreid=' . $this->sgenreid . (($where) ? ' AND ' . $where : ''), $limit);
	}
}

class smileyTable extends OrmDb {
	public $smileyid;
	public $usrid;
	public $scategoryid;
	public $path;
	public $enabled;
	protected $Columns = array(
		'smileyid',
		'usrid',
		'scategoryid',
		'path',
		'enabled',
	);
	public $PrimaryKey = 'smileyid';
	protected $_tablename = 'smiley';
	public function __construct($columns) {
		$this->smileyid = ( isset($columns['smileyid']) ) ? $columns['smileyid'] : NULL;
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
		$this->scategoryid = ( isset($columns['scategoryid']) ) ? $columns['scategoryid'] : NULL;
		$this->path = ( isset($columns['path']) ) ? $columns['path'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function smileysgenres($where = '', $order = '', $limit = '') {
		return $this->GetRows('smileysgenre', 'smileyid=' . $this->smileyid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function smileysgenreCount($where = '', $limit = '') {
		return $this->GetCount('smileysgenre', 'smileyid=' . $this->smileyid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usr() {
		return $this->GetRow('usr', $this->usrid);
	}
	public function scategory() {
		return $this->GetRow('scategory', $this->scategoryid);
	}
}

class smileysgenreTable extends OrmDb {
	public $smileysgenreid;
	public $smileyid;
	public $sgenreid;
	protected $Columns = array(
		'smileysgenreid',
		'smileyid',
		'sgenreid',
	);
	public $PrimaryKey = 'smileysgenreid';
	protected $_tablename = 'smileysgenre';
	public function __construct($columns) {
		$this->smileysgenreid = ( isset($columns['smileysgenreid']) ) ? $columns['smileysgenreid'] : NULL;
		$this->smileyid = ( isset($columns['smileyid']) ) ? $columns['smileyid'] : NULL;
		$this->sgenreid = ( isset($columns['sgenreid']) ) ? $columns['sgenreid'] : NULL;
	}
	public function sgenre() {
		return $this->GetRow('sgenre', $this->sgenreid);
	}
	public function smiley() {
		return $this->GetRow('smiley', $this->smileyid);
	}
}

class usrTable extends OrmDb {
	public $usrid;
	public $fname;
	public $lname;
	public $email;
	public $pswd;
	public $dname;
	public $urlname;
	public $enabled;
	protected $Columns = array(
		'usrid',
		'fname',
		'lname',
		'email',
		'pswd',
		'dname',
		'urlname',
		'enabled',
	);
	public $PrimaryKey = 'usrid';
	protected $_tablename = 'usr';
	public function __construct($columns) {
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
		$this->fname = ( isset($columns['fname']) ) ? $columns['fname'] : NULL;
		$this->lname = ( isset($columns['lname']) ) ? $columns['lname'] : NULL;
		$this->email = ( isset($columns['email']) ) ? $columns['email'] : NULL;
		$this->pswd = ( isset($columns['pswd']) ) ? $columns['pswd'] : NULL;
		$this->dname = ( isset($columns['dname']) ) ? $columns['dname'] : NULL;
		$this->urlname = ( isset($columns['urlname']) ) ? $columns['urlname'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function blogs($where = '', $order = '', $limit = '') {
		return $this->GetRows('blog', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function blogCount($where = '', $limit = '') {
		return $this->GetCount('blog', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function pages($where = '', $order = '', $limit = '') {
		return $this->GetRows('page', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function pageCount($where = '', $limit = '') {
		return $this->GetCount('page', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function posts($where = '', $order = '', $limit = '') {
		return $this->GetRows('post', 'pusrid=' . $this->pusrid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function postCount($where = '', $limit = '') {
		return $this->GetCount('post', 'pusrid=' . $this->pusrid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function smileys($where = '', $order = '', $limit = '') {
		return $this->GetRows('smiley', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function smileyCount($where = '', $limit = '') {
		return $this->GetCount('smiley', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usrgrps($where = '', $order = '', $limit = '') {
		return $this->GetRows('usrgrp', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function usrgrpCount($where = '', $limit = '') {
		return $this->GetCount('usrgrp', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $limit);
	}
	public function usrrights($where = '', $order = '', $limit = '') {
		return $this->GetRows('usrright', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $order, $limit);
	}
	public function usrrightCount($where = '', $limit = '') {
		return $this->GetCount('usrright', 'usrid=' . $this->usrid . (($where) ? ' AND ' . $where : ''), $limit);
	}
}

class usrgrpTable extends OrmDb {
	public $usrgrpid;
	public $grpid;
	public $usrid;
	protected $Columns = array(
		'usrgrpid',
		'grpid',
		'usrid',
	);
	public $PrimaryKey = 'usrgrpid';
	protected $_tablename = 'usrgrp';
	public function __construct($columns) {
		$this->usrgrpid = ( isset($columns['usrgrpid']) ) ? $columns['usrgrpid'] : NULL;
		$this->grpid = ( isset($columns['grpid']) ) ? $columns['grpid'] : NULL;
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
	}
	public function usr() {
		return $this->GetRow('usr', $this->usrid);
	}
	public function grp() {
		return $this->GetRow('grp', $this->grpid);
	}
}

class usrrightTable extends OrmDb {
	public $usrrightid;
	public $rightid;
	public $usrid;
	protected $Columns = array(
		'usrrightid',
		'rightid',
		'usrid',
	);
	public $PrimaryKey = 'usrrightid';
	protected $_tablename = 'usrright';
	public function __construct($columns) {
		$this->usrrightid = ( isset($columns['usrrightid']) ) ? $columns['usrrightid'] : NULL;
		$this->rightid = ( isset($columns['rightid']) ) ? $columns['rightid'] : NULL;
		$this->usrid = ( isset($columns['usrid']) ) ? $columns['usrid'] : NULL;
	}
	public function usr() {
		return $this->GetRow('usr', $this->usrid);
	}
	public function right() {
		return $this->GetRow('right', $this->rightid);
	}
}

class worldetTable extends OrmDb {
	public $wordletid;
	public $pageid;
	public $reference;
	public $description;
	public $value;
	public $enabled;
	protected $Columns = array(
		'wordletid',
		'pageid',
		'reference',
		'description',
		'value',
		'enabled',
	);
	public $PrimaryKey = 'wordletid';
	protected $_tablename = 'worldet';
	public function __construct($columns) {
		$this->wordletid = ( isset($columns['wordletid']) ) ? $columns['wordletid'] : NULL;
		$this->pageid = ( isset($columns['pageid']) ) ? $columns['pageid'] : NULL;
		$this->reference = ( isset($columns['reference']) ) ? $columns['reference'] : NULL;
		$this->description = ( isset($columns['description']) ) ? $columns['description'] : NULL;
		$this->value = ( isset($columns['value']) ) ? $columns['value'] : NULL;
		$this->enabled = ( isset($columns['enabled']) ) ? $columns['enabled'] : NULL;
	}
	public function page() {
		return $this->GetRow('page', $this->pageid);
	}
}


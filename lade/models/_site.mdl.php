<?php

// requires M object

class _site {
	// Instantiated version of this object here
	public static $view;
	public static $Secured = TRUE;
	public static $Username = 'admin';
	public static $Password = 'p@ssw0rd';

	// View accessible variables/methods
	public $BodyId;
	public $BodyClass;
	public $Section;
	public $Title = '';
	public $ControllerFile;
	public $SelfLink = '';
	public $Url;
	public $MediaUrl;
	public $BackLink;
	public $Ajax = FALSE;
	public $ShowNav = TRUE;
	public $Content = '';
	public $NavLinks = array();

	public $SectionInfo = NULL;

	public static function SetViewModel($object) {
		self::$view = $object;
	}

	public static function SetControllerFile($controller_name) {
		if ( self::$view ) self::$view->Controller = $controller_name;
	}

	/* Initialize site */
	public static function InitializeSite() {
		$view = self::$view;
		if ( isset($_GET['ajax']) ) $view->Ajax = TRUE;
		session_start();

		$view->Url = M::Get('site_url', NULL, TRUE);
		$view->MediaUrl = M::Get('media_url', NULL, TRUE);

		if ( self::$Secured && !self::CheckLogin() ) {
			self::Redirect($view->Url . 'login');
		}

		$result = self::Query("SELECT * FROM ladedgm_asection WHERE enabled=1");
		while ( $row = mysql_fetch_assoc($result) ) {
			$view->NavLinks[] = new Anchor(array('Href' => $view->Url . 'list?section=' . $row['name'], 'Text' => $row['dname']));
		}
	}

	public static function CheckLogin() {
		if ( @$_SESSION['gcmsusername'] && @$_SESSION['gcmspassword'] ) {
			return true;
		}

		return false;
	}

	public static function Redirect($url) {
		header('Location: ' . $url);
		exit;
	}

	public static $DbConn = NULL;
	public static function Query($query) {
		if ( !self::$DbConn ) {
			$DbConn = mysql_connect(M::Get('DB_ADDR'), M::Get('DB_USER'), M::Get('DB_PASS'));
			mysql_select_db(M::Get('DB_NAME'));
		}

		$result = mysql_query($query);
		return $result;
	}

	/* Template members/methods */
	public $WrapperFile;
	public $ViewFile;
	public function SetWrapperFile($name) {
		$this->WrapperFile = self::GetViewFile($name);
	}

	public static function SetViewFile($name) {
		self::$view->ViewFile = self::GetViewFile($name);
	}

	public static function GetViewFile($name) {
		$view_file = M::Get('view_directory', NULL, TRUE) . $name;
		if ( !file_exists($view_file) ) return FALSE;
		return $view_file;
	}

	public static function RenderViewContent() {
		require_once(M::Get('class_directory', TRUE, TRUE) . 'Template.class.php');
		$template = new Template();
		if ( !$template->SetFileName(self::$view->ViewFile) ) M::Error('Could not render template:' . self::$view->ViewFile, TRUE);
		$template->SetPageContent(array('page' => self::$view));
		if ( self::$view->WrapperFile ) $template->SetWrapperFileName(self::$view->WrapperFile);
		$template->RenderOutput(array('page' => self::$view));
	}

	public function RenderView($file_name, $variables) {
		$template = new Template();
		$file_name = self::GetViewFile($file_name);
		if ( !$template->SetFileName($file_name) ) M::Error('Could not render template:' . $file_name);
		$template->RenderOutput($variables);
	}

	public static $_lade;
	public static function Lade() {
		if ( self::$_lade ) return self::$_lade;

		include(M::Get('app_directory') . '/classes/lade.classes.php');

		self::$_lade = new Lade(M::Get('DB_ADDR'), M::Get('DB_USER'), M::Get('DB_PASS'), M::Get('DB_NAME'));

		self::$_lade->AddTables(M::Get('ladedgm_tables'));
		self::$_lade->AddSections(M::Get('ladedgm_sections'));


/*$lade_config = array(
	'connection' => array('type'=>'mysql', 'address'=>'', 'username'=>'', 'password'=>'', 'database'=>''),
	'tables' => array(
		'lade_section' => array('friendly'=>'section', 'display'=>'Site Sections', 'columns' => array(
			'id' => array('primary_key'=>true, 'simple'=>'id', 'display'=>'ID', 'edit'=> false, 'add'=>false, 'list'=>false),
			'name' => array('simple_key'=>true, 'simple'=>'name', 'display'=>'Name', 'edit'=>true, 'add'=>true, 'list'=>true, 'get'=>true),
			'value' => array('simple'=>'value', 'display'=>'Value', 'edit'=>true, 'add'=>true, 'list'=>true),
			'sortorder' => array('simple'=>'sortorder', 'display'=>'Order', 'edit'=>true, 'add'=>true, 'list'=>true, 'numeric'=>true,
				'default_query'=>'select Max(sortorder)+1 from lade_section', 'sort_default'=>true),
			'enabled' => array('simple'=>'enabled', 'display'=>'Enabled', 'edit'=>true, 'add'=>true, 'list'=>true, 'boolean'=>true,
				'default'=>1))
		),
		'lade_wordlet' => array('friendly'=>'wordlet', 'display'=>'Wordlets', 'columns' => array(
			'id' => array('primary_key'=>true, 'simple'=>'id', 'display'=>'ID', 'edit'=> false, 'add'=>false, 'list'=>false),
			'section_id' => array('simple'=>'section_id', 'display'=>'Site Section', 'edit'=> false, 'add'=>false, 'list'=>false, 'fk_table'=>'lade_section'),
			'name' => array('display_key'=>true, 'simple_key'=>true, 'simple'=>'name', 'display'=>'Name', 'edit'=>true, 'add'=>true, 'list'=>true, 'get'=>true),
			'value' => array('simple'=>'value', 'display'=>'Value', 'edit'=>true, 'add'=>true, 'list'=>true, 'input'=>'Textarea'),
			'enabled' => array('simple'=>'enabled', 'display'=>'Enabled', 'edit'=>true, 'add'=>true, 'list'=>true, 'boolean'=>true,
				'default'=>1))
		
	),
	'sections', => array(
		
	)
);*/

		/*$section_table = new LadeTable("ladedgm_section", "section", "Wordlet Sections");
			$section_table->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false));
				$section_name_column = new LadeColumn("name", "name", "Name", true, true, true);
					$section_name_column->SetAllowGet(true);
			$section_table->AddSimpleColumn($section_name_column);
			$section_table->AddDisplayColumn(new LadeColumn("value", "value", "Value", true, true, true));
				$section_sortorder_column = new LadeColumn("sortorder", "sortorder", "Order", true, true, true);
					$section_sortorder_column->SetDefaultValueQuery("select Max(sortorder)+1 from lade_section");
					$section_sortorder_column->SetNumericUpDown(true);
			$section_table->AddColumn($section_sortorder_column);
				$section_table->SetLastAsDefaultOrderByColumn();
					$section_enabled_column = new LadeColumn("enabled", "enabled", "Enabled", true, true, true);
						$section_enabled_column->SetDefaultValue("1");
						$section_enabled_column->SetBoolean(true);
			$section_table->AddColumn($section_enabled_column);
		$lade->AddTable($section_table);

		$wordlet_table = new LadeTable("ladedgm_wordlet", "s_wordlet", "Wordlets");
			$wordlet_table->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false));
				$wordlet_section_id_column = new LadeColumn("section_id", "section_id", "Wordlet Section", false, true, true);
				$wordlet_section_id_column->SetFkTable($section_table);
			$wordlet_table->AddColumn($wordlet_section_id_column);
				$wordlet_name_column = new LadeColumn("name", "name", "Name", false, true, true);
				$wordlet_name_column->SetAllowGet(true);
			$wordlet_table->AddDisplayColumn($wordlet_name_column);
			$wordlet_table->SetLastAsSimpleColumn();
				$wordlet_value_column = new LadeColumn("value", "value", "Value", true, true, true);
				$wordlet_value_column->SetInputType('Textarea');
			$wordlet_table->AddColumn($wordlet_value_column);
				$wordlet_enabled_column = new LadeColumn("enabled", "enabled", "Enabled", true, true, true);
				$wordlet_enabled_column->SetDefaultValue("1");
				$wordlet_enabled_column->SetBoolean(true);
			$wordlet_table->AddColumn($wordlet_enabled_column);
		$lade->AddTable($wordlet_table);

		$asection_table = new LadeTable("ladedgm_asection", "a_section", "Admin Sections");
			$asection_table->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false));
				$asection_name_column = new LadeColumn("name", "name", "Name", true, true, true);
					$asection_name_column->SetAllowGet(true);
			$asection_table->AddSimpleColumn($asection_name_column);
			$asection_table->AddDisplayColumn(new LadeColumn("dname", "dname", "Display Name", true, true, true));
			$asection_table->AddColumn(new LadeColumn("tablename", "tablename", "Table Name", true, true, true));
				$asection_sortorder_column = new LadeColumn("sortorder", "sortorder", "Order", true, true, true);
					$asection_sortorder_column->SetDefaultValueQuery("select Max(sortorder)+1 from lade_asection");
					$asection_sortorder_column->SetNumericUpDown(true);
			$asection_table->AddColumn($asection_sortorder_column);
			$asection_table->SetLastAsDefaultOrderByColumn();
				$admin_enabled_column = new LadeColumn("enabled", "enabled", "Enabled", true, true, true);
					$admin_enabled_column->SetDefaultValue("True");
					$admin_enabled_column->SetBoolean(true);
			$asection_table->AddColumn($admin_enabled_column);
		$lade->AddTable($asection_table);

		$grp_table = new LadeTable("ladedgm_grp", "group", "Groups");
			$grp_table->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false));
			$grp_table->AddSimpleColumn(new LadeColumn("name", "name", "Name", true, true, true));
			$grp_table->AddDisplayColumn(new LadeColumn("dname", "dname", "Display Name", true, true, true));
				$grp_enabled_column = new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true);
					$grp_enabled_column->SetDefaultValue("True");
					$grp_enabled_column->SetBoolean(true);
			$grp_table->AddColumn($grp_enabled_column);
		$lade->AddTable($grp_table);

		$usr_table = new LadeTable('ladedgm_usr', 'user', 'Users');
			$usr_table->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false));
			$usr_table->SetLastAsSimpleColumn();
			$usr_table->AddDisplayColumn(new LadeColumn("email", "email", "Email", true, true, true));
			$usr_table->AddColumn(new LadeColumn("pass", "pass", "Password", true, true, true));
			$usr_grp_column = new LadeColumn("grp_id", "s_grp", "Group", true, true, true);
			$usr_grp_column->SetIsNull(true);
			// TODO: Doesn't look for sub-table privilages here
			$usr_grp_column->SetFkTable($grp_table);
			$usr_table->AddColumn($usr_grp_column);
				$usr_enabled_column = new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true);
					$usr_enabled_column->SetDefaultValue("True");
					$grp_enabled_column->SetBoolean(true);
			$usr_table->AddColumn($grp_enabled_column);
		$lade->AddTable($usr_table);

		$rght_table = new LadeTable("ladedgm_rght", "rights", "Rights");
			$rght_table->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false));
			$rght_table->SetLastAsSimpleColumn();
			$rght_table->SetLastAsDefaultOrderByColumn();
				$rght_asection_id_column = new LadeColumn("asection_id", "section", "Section", true, true, true);
					$rght_asection_id_column->SetFkTable($asection_table);
				$rght_table->AddDisplayColumn($rght_asection_id_column);
				$rght_grp_id_column = new LadeColumn("grp_id", "grp", "Group", false, true, false);
					$rght_grp_id_column->SetFkTable($grp_table);
					$rght_grp_id_column->SetIsNull(true);
					//TODO: SetUseParentPk(true)
				$rght_table->AddColumn($rght_grp_id_column);
				$rght_radd_column = new LadeColumn("radd", "add", "Add", true, true, true);
					$rght_radd_column->SetDefaultValue("True");
					$rght_radd_column->SetBoolean(true);
				$rght_table->AddColumn($rght_radd_column);
				$rght_redit_column = new LadeColumn("redit", "edit", "Edit", true, true, true);
					$rght_redit_column->SetDefaultValue("True");
					$rght_redit_column->SetBoolean(true);
				$rght_table->AddColumn($rght_redit_column);
				$rght_rlist_column = new LadeColumn("rlist", "list", "List", true, true, true);
					$rght_rlist_column->SetDefaultValue("True");
					$rght_rlist_column->SetBoolean(true);
				$rght_table->AddColumn($rght_rlist_column);
				$rght_rdelete_column = new LadeColumn("rdelete", "delete", "Delete", true, true, true);
					$rght_rdelete_column->SetDefaultValue("True");
					$rght_rdelete_column->SetBoolean(true);
				$rght_table->AddColumn($rght_rdelete_column);
				$rght_enabled_column = new LadeColumn("enabled", "enabled", "Enabled", true, true, true);
					$rght_enabled_column->SetDefaultValue("True");
					$rght_enabled_column->SetBoolean(true);
				$rght_table->AddColumn($rght_enabled_column);
		$lade->AddTable($rght_table);

		$news_table = new LadeTable("ladedgm_news", "news", "News");
			$news_id_column = new LadeColumn("id", "id", "ID", false, false, false);
				$news_id_column->SetRevisionColumnName('news_id');
			$news_table->AddPkColumn($news_id_column);
			$news_table->SetLastAsSimpleColumn();
			$news_table->SetLastAsDefaultOrderByColumn();
				$news_usr_id_column = new LadeColumn("usr_id", "usr", "User", true, true, true);
					$news_usr_id_column->SetFkTable($usr_table);
					$news_usr_id_column->SetRevisionColumnName('usr_id');
				$news_table->AddDisplayColumn($news_usr_id_column);

				$news_title_column = new LadeColumn("title", "title", "Title", true, true, false);
					$news_title_column->SetRevisionColumnName('title');
				$news_table->AddColumn($news_title_column);

				$news_body_column = new LadeColumn("body", "body", "Body", true, true, true);
					$news_body_column->SetInputType('textarea');
					$news_body_column->SetRevisionColumnName('body');
				$news_table->AddColumn($news_body_column);

				$news_posted_column = new LadeColumn("posted", "posted", "Posted", true, true, true);
					$news_posted_column->SetDefaultValueQuery("select NOW()");
					$news_posted_column->SetRevisionColumnName('posted');
				$news_table->AddColumn($news_posted_column);

				$news_enabled_column = new LadeColumn("enabled", "enabled", "Enabled", true, true, true);
					$news_enabled_column->SetDefaultValue("True");
					$news_enabled_column->SetBoolean(true);
				$news_table->AddColumn($news_enabled_column);
			$news_table->SetRevisionTableName('ladedgm_newsrev');
		$lade->AddTable($news_table);

		$grp_section = new LadeSection("group", "Groups");
			$grp_section->SetTable($grp_table);
			$grp_grpright_section = new LadeSection("grpright", "Rights");
				$grp_grpright_section->SetTable($rght_table);
		$lade->AddSection($grp_section);
			$grp_section->SetSubSection($grp_grpright_section);

		$usr_section = new LadeSection("user", "Users");
			$usr_section->SetTable($usr_table);
		$lade->AddSection($usr_section);

		$wordlet_section = new LadeSection("wordlets", "Wordlets");
			$wordlet_section->SetTable($wordlet_table);

		$section_section = new LadeSection("section", "Wordlet Sections");
			$section_section->SetTable($section_table);
		$lade->AddSection($section_section);
			$section_section->SetSubSection($wordlet_section);

		$asection_section = new LadeSection("a_section", "Admin Sections");
			$asection_section->SetTable($asection_table);
		$lade->AddSection($asection_section);

		$news_section = new LadeSection("news", "News");
			$news_section->SetTable($news_table);
		$lade->AddSection($news_section);*/


		/*
			->AddTable
			(
				new LadeTable("usr", "user", "Users")
				->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false))
			//->AddSimpleColumn(new LadeColumn("email", "email", "Email", true, true, true))
				->AddColumn
				(
					new LadeColumn("grp_id", "grp", "Group", true, true, true)
					->SetFkTable(self::$_lade->Tables["grp"])
					->SetIsNull(true)
				)
				->AddDisplayColumn(new LadeColumn("email", "email", "Email", true, true, true))
				->AddColumn(new LadeColumn("pass", "password", "Password", true, true, true))
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddTable
			(
				new LadeTable("country", "s_country", "Countries")
				->AddPkColumn(new LadeColumn("id", "s_id", "ID", false, false, false))
				->AddSimpleColumn(new LadeColumn("name", "s_name", "Name", true, true, true))
				->AddDisplayColumn(new LadeColumn("value", "s_value", "Value", true, true, true))
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddTable
			(
				new LadeTable("faqs", "s_faqs", "FAQ's")
				->AddPkColumn(new LadeColumn("id", "s_id", "ID", false, false, false))
				->SetLastAsSimpleColumn()
				->AddDisplayColumn(new LadeColumn("Question", "s_Question", "Q", true, true, true))
				->AddColumn
				(
					new LadeColumn("Answer", "s_Answer", "A", true, true, true)
					->SetInputType(Db->Form->InputType->Textarea)
				)
				->AddColumn
				(
					new LadeColumn("country_id", "s_country_id", "Country", true, true, true)
					->SetFkTable(self::$_lade->Tables["country"])
				)
				->AddColumn
				(
					new LadeColumn("sortorder", "s_sortorder", "Order", true, true, true)
					->SetDefaultValueQuery("select Max(sortorder)+1 from faqs")
					->SetNumericUpDown(true)
				)
				->SetLastAsDefaultOrderByColumn()
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddTable
			(
				new LadeTable("site_section", "s_section", "Site Sections")
				->AddPkColumn(new LadeColumn("id", "s_id", "ID", false, false, false))
				->AddSimpleColumn(
					new LadeColumn("name", "s_name", "Name", true, true, true)
					->SetAllowGet(true)
					)
				->AddDisplayColumn(new LadeColumn("value", "s_value", "Value", true, true, true))
				->AddColumn(
					new LadeColumn("sortorder", "s_sortorder", "Order", true, true, true)
					->SetDefaultValueQuery("select Max(sortorder)+1 from site_section")
					->SetNumericUpDown(true)
				)
				->SetLastAsDefaultOrderByColumn()
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddTable
			(
				new LadeTable("admin_section", "a_section", "Admin Sections")
				->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false))
				->AddSimpleColumn(
					new LadeColumn("name", "s_name", "Name", true, true, true)
					->SetAllowGet(true)
					)
				->AddDisplayColumn(new LadeColumn("dname", "dname", "Display Name", true, true, true))
				->AddColumn(new LadeColumn("tablename", "tablename", "Table Name", true, true, true))
				->AddColumn(
					new LadeColumn("sortorder", "s_sortorder", "Order", true, true, true)
					->SetDefaultValueQuery("select Max(sortorder)+1 from admin_section")
					->SetNumericUpDown(true)
				)
				->SetLastAsDefaultOrderByColumn()
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddTable
			(
				new LadeTable("rght", "rights", "Rights")
				->AddPkColumn(new LadeColumn("id", "id", "ID", false, false, false))
				->SetLastAsSimpleColumn()
				->SetLastAsDefaultOrderByColumn()
				->AddDisplayColumn
				(
					new LadeColumn("admin_section_id", "section", "Section", true, true, true)
					->SetFkTable(self::$_lade->Tables["admin_section"])
				)
				->AddColumn
				(
					new LadeColumn("grp_id", "grp", "Group", false, true, false)
					->SetFkTable(self::$_lade->Tables["grp"])
					->SetIsNull(true)
					//TODO: SetUseParentPk(true)
				)
				->AddColumn
				(
					new LadeColumn("r_create", "create", "Create", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
				->AddColumn
				(
					new LadeColumn("r_update", "update", "Update", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
				->AddColumn
				(
					new LadeColumn("r_read", "read", "Read", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
				->AddColumn
				(
					new LadeColumn("r_delete", "delete", "Delete", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddTable
			(
				new LadeTable("wordlet", "s_wordlet", "Wordlets")
				->AddPkColumn(new LadeColumn("id", "s_id", "ID", false, false, false))
				->AddColumn
				(
					new LadeColumn("site_section_id", "s_section_id", "Site Section", false, true, true)
					->SetFkTable(self::$_lade->Tables["site_section"])
				)
				->AddDisplayColumn(
					new LadeColumn("name", "s_name", "Name", false, true, true)
					->SetAllowGet(true)
				)
				->SetLastAsSimpleColumn()
				->AddColumn(
					new LadeColumn("value", "s_value", "Value", true, true, true)
					->SetInputType(Db->Form->InputType->Textarea)
				)
				->AddColumn
				(
					new LadeColumn("enabled", "s_enabled", "Enabled", true, true, true)
					->SetDefaultValue("True")
					->SetBoolean(true)
				)
			)
			->AddSection
			(
				new Db->Section("grp", "Groups")
				->SetTable(self::$_lade->Tables["grp"])
				->SetSubSection
				(
					new Db->Section("grpright", "Rights")
					->SetTable(self::$_lade->Tables["rght"])
				)
			)
			->AddSection
			(
				new Db->Section("user", "Users")
				->SetTable(self::$_lade->Tables["usr"])
			)
			->AddSection
			(
				new Db->Section("country", "Countries")
				->SetTable(self::$_lade->Tables["country"])
			)
			->AddSection
			(
				new Db->Section("faqs", "FAQ's")
				->SetTable(self::$_lade->Tables["faqs"])
			)
			->AddSection
			(
				new Db->Section("admin_section", "Admin Sections")
				->SetTable(self::$_lade->Tables["admin_section"])
			)
			->AddSection
			(
				new Db->Section("site_section", "Site Sections")
				->SetTable(self::$_lade->Tables["site_section"])
				->SetSubSection
				(
					new Db->Section("wordlets", "Wordlets")
					->SetTable(self::$_lade->Tables["wordlet"])
				)
			);*/
		return self::$_lade;
	}
}

class Anchor {
	public $Href = '';
	public $Text = '';
	public $Current = FALSE;
	public function __construct($params) {
		if (isset($params['Href'])) $this->Href = $params['Href'];
		if (isset($params['Text'])) $this->Text = $params['Text'];
		if (isset($params['Current'])) $this->Current = $params['Current'];
	}
}
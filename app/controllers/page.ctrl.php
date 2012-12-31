<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
// require_once(M::Get('model_directory', NULL, TRUE) . 'indexPage.mdl.php');

// Set model for view to access
class pagePage extends _site {
	public static $page_name;

	public static function Init() {
		self::$page_name = explode('/', @$_SERVER['PATH_INFO']);
		self::$page_name = array_pop(self::$page_name);

		self::SetViewModel(new self());

		self::InitializeSite();

		self::SetWordlets();

		self::SetViewFile('page.view.php');

		self::$view->RenderViewContent();
	}

	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('page_' . self::$page_name);
	}
}

pagePage::Init();

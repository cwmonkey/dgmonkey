<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'loginPage.mdl.php');

// Set model for view to access
loginPage::SetViewModel(new loginPage());

loginPage::InitializeSite();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	loginPage::ProcessForm();
}

loginPage::SetControllerFile('login.ctrl.php');
loginPage::SetViewFile('login.view.php');

loginPage::$view->RenderViewContent();
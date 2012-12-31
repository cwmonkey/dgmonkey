<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'logoutPage.mdl.php');

// Set model for view to access
logoutPage::SetViewModel(new logoutPage());

logoutPage::InitializeSite();
logoutPage::InitializePage();

logoutPage::SetControllerFile('logout.ctrl.php');
logoutPage::SetViewFile('logout.view.php');

logoutPage::$view->RenderViewContent();
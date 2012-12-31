<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'editPage.mdl.php');

// Set model for view to access
editPage::SetViewModel(new editPage());

editPage::InitializeSite();
editPage::InitializePage();

editPage::SetControllerFile('edit.ctrl.php');
editPage::SetViewFile('edit.view.php');

editPage::$view->RenderViewContent();
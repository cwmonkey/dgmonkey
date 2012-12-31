<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'listPage.mdl.php');

// Set model for view to access
listPage::SetViewModel(new listPage());

listPage::InitializeSite();
listPage::InitializePage();

listPage::SetControllerFile('list.ctrl.php');
listPage::SetViewFile('list.view.php');

listPage::$view->RenderViewContent();
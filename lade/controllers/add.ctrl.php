<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'addPage.mdl.php');

// Set model for view to access
addPage::SetViewModel(new addPage());

addPage::InitializeSite();
addPage::InitializePage();

addPage::SetControllerFile('add.ctrl.php');
addPage::SetViewFile('add.view.php');

addPage::$view->RenderViewContent();
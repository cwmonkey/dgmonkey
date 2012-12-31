<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'indexPage.mdl.php');

// Set model for view to access
indexPage::SetViewModel(new indexPage());

indexPage::InitializeSite();

indexPage::SetControllerFile('index.ctrl.php');
indexPage::SetViewFile('index.view.php');

indexPage::$view->RenderViewContent();
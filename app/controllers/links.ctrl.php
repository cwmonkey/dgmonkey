<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'linksPage.mdl.php');

// Set model for view to access
linksPage::SetViewModel(new linksPage());

linksPage::InitializeSite();

linksPage::SetWordlets();

linksPage::SetControllerFile('links.ctrl.php');
linksPage::SetViewFile('links.view.php');

linksPage::$view->RenderViewContent();
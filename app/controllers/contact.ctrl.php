<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'contactPage.mdl.php');

// Set model for view to access
contactPage::SetViewModel(new contactPage());

contactPage::InitializeSite();

contactPage::SetWordlets();

contactPage::SetControllerFile('contact.ctrl.php');
contactPage::SetViewFile('contact.view.php');

contactPage::$view->RenderViewContent();
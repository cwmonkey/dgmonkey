<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'registrationPage.mdl.php');

// Set model for view to access
registrationPage::SetViewModel(new registrationPage());

registrationPage::InitializeSite();

registrationPage::SetWordlets();
registrationPage::SetEvent();

registrationPage::SetControllerFile('registration.ctrl.php');
registrationPage::SetViewFile('registration.view.php');

registrationPage::$view->RenderViewContent();
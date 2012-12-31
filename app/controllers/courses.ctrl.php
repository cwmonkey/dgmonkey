<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'coursesPage.mdl.php');

// Set model for view to access
coursesPage::SetViewModel(new coursesPage());

coursesPage::InitializeSite();

coursesPage::SetWordlets();

coursesPage::SetControllerFile('courses.ctrl.php');
coursesPage::SetViewFile('courses.view.php');

coursesPage::$view->RenderViewContent();
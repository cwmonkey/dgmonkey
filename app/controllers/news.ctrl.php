<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'newsPage.mdl.php');

// Set model for view to access
newsPage::SetViewModel(new newsPage());

newsPage::InitializeSite();

newsPage::SetNews();
newsPage::SetUpcomingEvents();
newsPage::SetWordlets();

newsPage::SetControllerFile('news.ctrl.php');
newsPage::SetViewFile('news.view.php');

newsPage::$view->RenderViewContent();
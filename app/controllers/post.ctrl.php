<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'postPage.mdl.php');

// Set model for view to access
postPage::SetViewModel(new postPage());

postPage::InitializeSite();

postPage::SetNews();
postPage::SetWordlets();

postPage::SetControllerFile('post.ctrl.php');
postPage::SetViewFile('post.view.php');

postPage::$view->RenderViewContent();
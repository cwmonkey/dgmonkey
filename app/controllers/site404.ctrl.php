<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'site404Page.mdl.php');

// Set model for view to access
site404Page::SetViewModel(new site404Page());

site404Page::InitializeSite();

site404Page::SetWordlets();

site404Page::SetControllerFile('site404.ctrl.php');
site404Page::SetViewFile('site404.view.php');

site404Page::$view->RenderViewContent();
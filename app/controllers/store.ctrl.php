<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'storePage.mdl.php');

// Set model for view to access
storePage::SetViewModel(new storePage());

storePage::InitializeSite();

storePage::SetStoreItems();
storePage::SetWordlets();

storePage::SetControllerFile('store.ctrl.php');
storePage::SetViewFile('store.view.php');

storePage::$view->RenderViewContent();
<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'sponsored_playersPage.mdl.php');

// Set model for view to access
sponsored_playersPage::SetViewModel(new sponsored_playersPage());

sponsored_playersPage::InitializeSite();

sponsored_playersPage::SetWordlets();

sponsored_playersPage::SetControllerFile('sponsored_players.ctrl.php');
sponsored_playersPage::SetViewFile('sponsored_players.view.php');

sponsored_playersPage::$view->RenderViewContent();
<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'tour_schedulePage.mdl.php');

// Set model for view to access
tour_schedulePage::SetViewModel(new tour_schedulePage());

tour_schedulePage::InitializeSite();

tour_schedulePage::SetWordlets();
tour_schedulePage::SetUpcomingEvents();
tour_schedulePage::SetPastEvents();

tour_schedulePage::SetControllerFile('tour_schedule.ctrl.php');
tour_schedulePage::SetViewFile('tour_schedule.view.php');

tour_schedulePage::$view->RenderViewContent();
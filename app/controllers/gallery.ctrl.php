<?php

// Include site specific logic
require_once(M::Get('model_directory', NULL, TRUE) . '_site.mdl.php');

// Include page specific logic
require_once(M::Get('model_directory', NULL, TRUE) . 'galleryPage.mdl.php');

// Set model for view to access
galleryPage::SetViewModel(new galleryPage());

galleryPage::InitializeSite();

galleryPage::SetGalleryImages();
galleryPage::SetWordlets();

galleryPage::SetControllerFile('gallery.ctrl.php');
galleryPage::SetViewFile('gallery.view.php');

galleryPage::$view->RenderViewContent();
<?php

// Define base M application directory
$dirname = dirname(__FILE__);

// Main M Model file
require_once($dirname . '/model.php');

// Main M configuration file
require_once($dirname . '/config.php');

// Attempt to load local configuration for overrides, etc
try {
	include_once($dirname . '/local.config.php');
} catch ( Exception $e ) {
	// M::Error($e);
}

// Get page controller name
$page_name = @$_SERVER['PATH_INFO'];

$controller_name = M::GetIntercept($page_name);

if ( $controller_name == NULL ) $controller_name = M::Get('site_404_controller');

// TODO: Run more validation on the controller name?
if ( !$controller_name ) M::Error('Could not locate 404 controller', TRUE);
$controller_file = M::Get('controller_directory', NULL, TRUE) . $controller_name;

// Include page controller file
if ( !include_once($controller_file) ) M::Error('Could not load controller ' . $controller_file, TRUE);
<?php

/* M specific configuration */

$site_404_controller =  'site404';
$fatal_error_message =  'An error has occured whie processing your request.';
$dev = false;

if ( $_SERVER['HTTP_HOST'] == 'dgmonkey.local' ) {
	$monkake_directory = 'e:/wamp/www/sites/dgmonkey/';
	$dev = true;
	error_reporting(E_ALL);
} else {
	$monkake_directory = '/home/monkey/sites/dgmonkey/';
}

$app_directory =        $monkake_directory . 'lade/';
$class_directory =      $monkake_directory . 'app/classes/';
$controller_directory = $app_directory .     'controllers/';
$model_directory =      $app_directory .     'models/';
$view_directory =       $app_directory .     'views/';

$htpasswd_file =        $monkake_directory . '.htpasswd';

/* Site specific configuration */
$site_title = 'LADE CMS';

//$DB_TABLE_PREFIX = 'ladedgm_');

$site_url =  '/lade/';
$media_url = '/media/lade/';

$cache_directory = $monkake_directory . '_cache/';

$routes = array(
	'/lade/@^home.*$@' => array(
		'controller' => 'index',
		'template' => 'index',
	),
	'/lade/@^list.*$@' => array(
		'controller' => 'list',
		'template' => 'list',
	),
	'/lade/@^add.*$@' => array(
		'controller' => 'add',
		'template' => 'add',
	),
	'/lade/@^delete.*$@' => array(
		'controller' => 'delete',
		'template' => 'delete',
	),
	'/lade/@^edit.*$@' => array(
		'controller' => 'edit',
		'template' => 'edit',
	),
	'/lade/@^single_edit.*$@' => array(
		'controller' => 'single_edit',
		'template' => 'single_edit',
	),
	'/lade/@^login.*$@' => array(
		'controller' => 'login',
		'template' => 'login',
	),
	'/lade/@^logout.*$@' => array(
		'controller' => 'logout',
		'template' => 'logout',
	),
);

include('local.config.php');
include($monkake_directory . 'shared/shared.config.php');

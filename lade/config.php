<?php

/* M specific configuration */

M::Set('site_404_controller',  'site404');
M::Set('fatal_error_message',  'An error has occured whie processing your request.');

if ( $_SERVER['HTTP_HOST'] == 'dgmonkey.local' ) {
	M::Set('monkake_directory', 'c:/wamp/www/sites/dgmonkey/');
	error_reporting(E_ALL);
} elseif ( strstr(__DIR__, '/usr/home/monkey/sites/dgmonkey') !== FALSE ) {
	M::Set('monkake_directory', '/home/monkey/sites/dgmonkey/');
} else {
	M::Set('monkake_directory', '/home/monkey/sites/mysmilies/subdomains/dgmonkey/');
}

M::Set('app_directory',        M::Get('monkake_directory') . 'lade/');
M::Set('class_directory',      M::Get('monkake_directory') . 'app/classes/');
M::Set('controller_directory', M::Get('app_directory') .     'controllers/');
M::Set('model_directory',      M::Get('app_directory') .     'models/');
M::Set('view_directory',       M::Get('app_directory') .     'views/');

M::Set('htpasswd_file',        M::Get('monkake_directory') . '.htpasswd');

/* Site specific configuration */
M::Set('site_title', 'LADE CMS');

//M::Set('DB_TABLE_PREFIX', 'ladedgm_');

M::Set('site_url',  '/lade/');
M::Set('media_url', '/media/lade/');

M::Set('cache_directory', M::Get('monkake_directory') . '_cache/');

M::Set('url_intercepts', array(
	'/^$/' => 'index.ctrl.php',
	'/^\/$/' => 'index.ctrl.php',
	'/^\/home$/' => 'index.ctrl.php',
	'/^\/list$/' => 'list.ctrl.php',
	'/^\/add$/' => 'add.ctrl.php',
	'/^\/delete$/' => 'delete.ctrl.php',
	'/^\/edit$/' => 'edit.ctrl.php',
	'/^\/login$/' => 'login.ctrl.php',
	'/^\/logout$/' => 'logout.ctrl.php',
	));

include(M::Get('monkake_directory') . 'shared/shared.config.php');

<?php

/* M specific configuration */

M::Set('site_404_controller',  'site404.ctrl.php');
M::Set('fatal_error_message',  'An error has occured whie processing your request.');

if ( $_SERVER['HTTP_HOST'] == 'dgmonkey.local' ) {
	M::Set('monkake_directory', 'c:/wamp/www/sites/dgmonkey/');
	error_reporting(E_ALL);
} elseif ( strstr(__DIR__, '/usr/home/monkey/sites/dgmonkey') !== FALSE ) {
	M::Set('monkake_directory', '/home/monkey/sites/dgmonkey/');
} else {
	M::Set('monkake_directory', '/home/monkey/sites/mysmilies/subdomains/dgmonkey/');
}

M::Set('docroot_directory',    M::Get('monkake_directory') . 'doc-root');
M::Set('app_directory',        M::Get('monkake_directory') . 'app/');
M::Set('class_directory',      M::Get('app_directory') .     'classes/');
M::Set('controller_directory', M::Get('app_directory') .     'controllers/');
M::Set('model_directory',      M::Get('app_directory') .     'models/');
M::Set('view_directory',       M::Get('app_directory') .     'views/');

M::Set('htpasswd_file',        M::Get('monkake_directory') . '.htpasswd');

/* Site specific configuration */

M::Set('DB_TABLE_PREFIX', 'monkavi_');

M::Set('form_builder_table', M::Get('DB_TABLE_PREFIX') . 'form');

M::Set('media_url', '/media/');
M::Set('site_url',  '/');

M::Set('cache_directory', M::Get('monkake_directory') . '_cache/');

M::Set('url_intercepts', array(
	'/^$/' => 'index.ctrl.php',
	'/^\/$/' => 'index.ctrl.php',
	'/^\/[0-9]+$/' => 'index.ctrl.php',
	'/^\/index/' => 'index.ctrl.php',
	'/^\/links/' => 'links.ctrl.php',
	'/^\/gallery/' => 'gallery.ctrl.php',
	'/^\/storeou812/' => 'store.ctrl.php',
	'/^\/courses/' => 'courses.ctrl.php',
	'/^\/contact/' => 'contact.ctrl.php',
	'/^\/registration\/([0-9]+)/' => 'registration.ctrl.php',
	'/^\/tour_schedule/' => 'tour_schedule.ctrl.php',
	'/^\/sponsored_players/' => 'sponsored_players.ctrl.php',
	'/^\/post/' => 'post.ctrl.php',
	'/^\/news/' => 'news.ctrl.php',
	));

include(M::Get('monkake_directory') . 'shared/shared.config.php');

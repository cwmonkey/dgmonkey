<?php

/* M specific configuration */

$site_404_controller =  'site404';
$fatal_error_message =  'An error has occured whie processing your request.';

if ( $_SERVER['HTTP_HOST'] == 'dgmonkey.local' ) {
	$monkake_directory = 'c:/wamp/www/sites/dgmonkey/';
	error_reporting(E_ALL);
} elseif ( strstr(__DIR__, '/usr/home/monkey/sites/dgmonkey') !== FALSE ) {
	$monkake_directory = '/home/monkey/sites/dgmonkey/';
} else {
	$monkake_directory = '/home/monkey/sites/mysmilies/subdomains/dgmonkey/';
}

$docroot_directory =    $monkake_directory . 'doc-root';
$app_directory =        $monkake_directory . 'app/';
$class_directory =      $app_directory .     'classes/';
$controller_directory = $app_directory .     'controllers/';
$model_directory =      $app_directory .     'models/';
$view_directory =       $app_directory .     'views/';

$css_dir =              $docroot_directory . 'css/';
$css_compressed_dir =   $css_dir . 'compressed/';
$js_dir =               $docroot_directory . 'js/';
$js_compressed_dir =    $js_dir . 'compressed/';

$htpasswd_file =        $monkake_directory . '.htpasswd';

/* Site specific configuration */

$DB_TABLE_PREFIX = 'monkavi_';

$form_builder_table = $DB_TABLE_PREFIX . 'form';

$media_url = '/media/';
$site_url =  '/';

$cache_directory = $monkake_directory . '_cache/';

$url_intercepts = array(
	'/^$/' => 'index',
	'/^\/$/' => 'index',
	'/^\/[0-9]+$/' => 'index',
	'/^\/index/' => 'index',
	'/^\/links/' => 'links',
	'/^\/gallery/' => 'gallery',
	'/^\/storeou812/' => 'store',
	'/^\/courses/' => 'courses',
	'/^\/contact/' => 'contact',
	'/^\/registration\/([0-9]+)/' => 'registration',
	'/^\/tour_schedule/' => 'tour_schedule',
	'/^\/sponsored_players/' => 'sponsored_players',
	'/^\/post/' => 'post',
	'/^\/news/' => 'news',
	);

include($monkake_directory . 'shared/shared.config.php');

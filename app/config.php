<?php

$debug = false;
$dev = false;
$minify_js = true;
$minify_css = true;
$use_cdn = true;

/* M specific configuration */

$site_404_controller =  'site404';
$fatal_error_message =  'An error has occured whie processing your request.';

//} elseif ( strstr(__DIR__, '/usr/home/monkey/sites/dgmonkey') !== FALSE ) {
$monkake_directory = '/home/monkey/sites/dgmonkey/';

if ( $_SERVER['HTTP_HOST'] == 'dgmonkey.local' ) {
	$monkake_directory = 'e:/wamp/www/sites/dgmonkey/';
	error_reporting(E_ALL);
	$debug = true;
	$dev = true;
	$minify_js = false;
	$minify_css = false;
	$use_cdn = false;
} elseif ( $_SERVER['HTTP_HOST'] == 'dgmonkey.mysmilies.com' ) {
	$monkake_directory = '/home/monkey/sites/mysmilies/subdomains/dgmonkey/';
}

$docroot_directory =    $monkake_directory . 'doc-root/';
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

$site404Route = array(
	'controller' => 'site404',
	'template' => 'site404',
	'name' => 'site404',
);

$routes = array(
	'' => array(
		'controller' => 'index',
		'template' => 'index',
		'name' => 'home',
	),
	'/links' => array(
		'controller' => 'links',
		'template' => 'links',
		'name' => 'links',
	),
	'/gallery' => array(
		'controller' => 'gallery',
		'template' => 'gallery',
		'name' => 'gallery',
	),
	'/storeou812' => array(
		'controller' => 'store',
		'template' => 'store',
		'name' => 'store',
	),
	'/courses' => array(
		'controller' => 'courses',
		'template' => 'courses',
		'name' => 'courses',
	),
	'/contact' => array(
		'controller' => 'contact',
		'template' => 'contact',
		'name' => 'contact',
	),
	'/registration/:event_id(/:title)' => array(
		'controller' => 'registration',
		'template' => 'registration',
		'name' => 'registration',
	),
	'/tour_schedule' => array(
		'controller' => 'tour_schedule',
		'template' => 'tour_schedule',
		'name' => 'tour_schedule',
	),
	'/sponsored_players' => array(
		'controller' => 'sponsored_players',
		'template' => 'sponsored_players',
		'name' => 'sponsored_players',
	),
	'/post/:post_id' => array(
		'controller' => 'post',
		'template' => 'post',
		'name' => 'post',
	),
	'/news' => array(
		'controller' => 'news',
		'template' => 'news',
		'name' => 'news',
	),
	'/:page_name@^((baskets)|(store_thanks))$@' => array(
		'controller' => 'page',
		'template' => 'page',
	),
);

include('local.config.php');
include($monkake_directory . 'shared/shared.config.php');

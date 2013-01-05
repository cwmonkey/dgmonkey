<?php

class pageController extends _siteController {
	public static $page_name;

	public static function Init($route) {
		$route['name'] = 'page_' . $route['page_name'];
		parent::Init($route);
	}

	public static function InitializePage($route) {
		self::$view->BodyClass = $route['match0'];
	}
}

<?php

class logoutPage extends _site {
	public $BackLink = '';
	public $ShowNav = FALSE;

	// TODO: allow for overriding variables
	public static function InitializePage() {
		_site::$Secured = FALSE;
		// TODO: Site back link?
		self::$view->BackLink = M::Get('site_url', NULL, TRUE);
		if ( @$_GET['backlink'] ) self::$view->BackLink = $_GET['backlink'];
		session_destroy();
		_site::InitializeSite();
	}
}
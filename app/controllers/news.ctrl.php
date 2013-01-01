<?php

class newsController extends _siteController {
	public $News = array();
	public $NewsAddLink;
	public $Pagination;

	private static $_PageNumber;
	private static $_PostsPerPage = 10;

	public static function InitializePage($route) {
		self::SetNews();
		self::SetUpcomingEvents();
	}

	public static function SetNews() {
		if ( self::$view->EditMode ) {
			$news = self::Lade()->GetList('news', '', 'ladedgm_news.posted', 'DESC');
		} else {
			$news = self::Lade()->GetList('news', 'ladedgm_news.enabled=1 AND ladedgm_news.posted<NOW()', 'ladedgm_news.posted', 'DESC');
		}
		self::$view->News = $news->Values;
		self::$view->NewsAddLink = $news->AddLink;
	}
}

class NewsItem {
	public $PostId;
	public $Title;
	public $Created;
	public $UsrDname;
	public $Body;
}
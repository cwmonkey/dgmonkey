<?php

class indexPage extends _site {
	public $News = array();
	public $NewsAddLink;
	public $Pagination;

	private static $_PageNumber;
	private static $_PostsPerPage = 10;

	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets("home");
	}

	public static function SetNews() {
		if ( self::$view->EditMode ) {
			$news = _site::Lade()->GetList('news', '', 'ladedgm_news.posted', 'DESC');
		} else {
			$news = _site::Lade()->GetList('news', 'ladedgm_news.enabled=1 AND ladedgm_news.posted<NOW()', 'ladedgm_news.posted', 'DESC', "LIMIT 10");
		}
		self::$view->News = array_slice($news->Values, 0, 10);
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
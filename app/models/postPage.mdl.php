<?php

class postPage extends _site {
	public $News = array();
	public $NewsAddLink;
	public $Pagination;

	private static $_PageNumber;
	private static $_PostsPerPage = 10;

	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets("post");
	}

	public static function SetNews() {
		@list($tmp, $page_name, $id) = split('/', @$_SERVER['PATH_INFO']);
		$id = intval($id);
		if ( !$id ) {
			header("Location: /");
			exit;
		}

		if ( self::$view->EditMode ) {
			$news = _site::Lade()->GetList('news', 'ladedgm_news.id=' . $id, 'ladedgm_news.posted', 'DESC');
		} else {
			$news = _site::Lade()->GetList('news', 'ladedgm_news.enabled=1 AND ladedgm_news.posted<NOW() AND ladedgm_news.id=' . $id, 'ladedgm_news.posted', 'DESC');
		}

		if ( !count($news->Values) ) {
			header("Location: /");
			exit;
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
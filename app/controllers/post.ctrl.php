<?php

class postController extends _siteController {
	public $News = array();
	public $NewsAddLink;
	public $Pagination;

	private static $_PageNumber;
	private static $_PostsPerPage = 10;

	public static function InitializePage($route) {
		self::SetNews($route['post_id']);
	}

	public static function SetNews($id) {
		$id = intval($id);
		if ( !$id ) {
			header("Location: /");
			exit;
		}

		if ( self::$view->EditMode ) {
			$news = self::Lade()->GetList('news', 'ladedgm_news.id=' . $id, 'ladedgm_news.posted', 'DESC');
		} else {
			$news = self::Lade()->GetList('news', 'ladedgm_news.enabled=1 AND ladedgm_news.posted<NOW() AND ladedgm_news.id=' . $id, 'ladedgm_news.posted', 'DESC');
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
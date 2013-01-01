<?php

class storeController extends _siteController {
	public $GalleryImages = array();
	public $GalleryImageAddLink;

	public static function InitializePage() {
		self::SetStoreItems();
	}

	public static function SetStoreItems() {
		if ( self::$view->EditMode ) {
			$images = self::Lade()->GetList('store_item', '', 'ladedgm_storeitem.created', 'DESC');
		} else {
			$images = self::Lade()->GetList('store_item', 'ladedgm_storeitem.enabled=1 AND ladedgm_storeitem.created<NOW()', 'ladedgm_storeitem.created', 'DESC');
		}

		foreach ( $images->Values as $key => $val ) {
			$images->Values[$key]['imgtag'] = self::ResizeStoreImage($val['imgpath']);
			$images->Values[$key]['url'] = '/store/' . $val['id'] . '/' . preg_replace('([^a-zA-Z0-9\-]+)', '-', $val['title']);
			//$images->Values[$key]['price'] = add_cents_to_decimal($images->Values[$key]['price'], 200);
		}

		self::$view->GalleryImages = $images->Values;
		self::$view->GalleryImageAddLink = $images->AddLink;
	}
}

function add_cents_to_decimal($dec, $cents) {
	list($ppd, $ppc) = preg_split('/\\./', $dec);
	$pp = intval($ppd) * 100 + intval($ppc) + $cents;
	return number_format($pp/100, 2);
}

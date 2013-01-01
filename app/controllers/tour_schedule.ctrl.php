<?php

class tour_scheduleController extends _siteController {
	public $PastEvents = array();

	public static function InitializePage($route) {
		self::SetUpcomingEvents();
		self::SetPastEvents();
	}

	public static function SetPastEvents() {
		if ( self::$view->EditMode ) {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled<NOW()', 'ladedgm_event.scheduled', 'DESC');
		} else {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled<NOW() && ladedgm_event.scheduled > DATE_SUB(NOW(), INTERVAL 2 YEAR) && ladedgm_event.enabled=1', 'ladedgm_event.scheduled', 'DESC');
		}

		self::$view->PastEvents = $events->Values;
	}
}
<?php

class tour_schedulePage extends _site {
	public $PastEvents = array();

	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('tour_schedule');
	}

	public static function SetPastEvents() {
		if ( self::$view->EditMode ) {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled<NOW()', 'ladedgm_event.scheduled', 'DESC');
		} else {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled<NOW() && ladedgm_event.enabled=1', 'ladedgm_event.scheduled', 'DESC');
		}

		self::$view->PastEvents = $events->Values;
		//self::$view->UpcomingEventsAddLink = $events->AddLink;
	}
}
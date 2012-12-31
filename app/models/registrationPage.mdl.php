<?php

class registrationPage extends _site {
	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('registration');
	}

	public static function SetEvent() {
		/* if ( self::$view->EditMode ) {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled>=NOW()', 'ladedgm_event.scheduled', 'ASC');
		} else {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled>=NOW() && ladedgm_event.enabled=1', 'ladedgm_event.scheduled', 'ASC');
		}*/

//http://www.dgmonkey.com/registration/74

		$path = @$_SERVER['PATH_INFO'];
		if ( !preg_match('/^\/registration\/([0-9]+)/', $path, $matches) || !($id =  intval($matches[1])) || $id <= 0 ) {
			header('Location: /tour_schedule');
			exit;
		}

		$event = self::Lade()->GetList('event', 'ladedgm_event.scheduled>=NOW() && ladedgm_event.enabled=1 && ladedgm_event.id=' . $id, 'ladedgm_event.scheduled', 'ASC');

		if ( !count($event->Values) ) {
			header('Location: /tour_schedule');
			exit;
		}

		self::$view->Event = $event->Values[0];

		if ( !self::$view->Event['signupenabled'] ) {
			header('Location: /tour_schedule');
			exit;
		}


		self::$view->Event['discs'] = trim(self::$view->Event['discs']);
		if ( self::$view->Event['discs'] ) {
			self::$view->Event['discs'] = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", self::$view->Event['discs']);
		}

		self::$view->Event['title'] = htmlspecialchars(self::$view->Event['title']);

		self::$view->Event['CostPdgaPro'] = add_cents_to_decimal(self::$view->Event['pricepro'], 200);
		self::$view->Event['CostPro'] = add_cents_to_decimal(self::$view->Event['pricepro'], 1200);

		self::$view->Event['CostPdgaAdv'] = add_cents_to_decimal(self::$view->Event['priceadv'], 200);
		self::$view->Event['CostAdv'] = add_cents_to_decimal(self::$view->Event['priceadv'], 1200);

		self::$view->Event['CostPdgaRec'] = add_cents_to_decimal(self::$view->Event['pricerec'], 200);
		self::$view->Event['CostRec'] = add_cents_to_decimal(self::$view->Event['pricerec'], 1200);

		self::$view->Event['CostJr'] = add_cents_to_decimal(self::$view->Event['pricejr'], 200);
		//self::$view->UpcomingEventsAddLink = $events->AddLink;
	}
}

function add_cents_to_decimal($dec, $cents) {
	list($ppd, $ppc) = preg_split('/\\./', $dec);
	$pp = intval($ppd) * 100 + intval($ppc) + $cents;
	return number_format($pp/100, 2);
}
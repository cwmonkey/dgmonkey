<?php

class contactPage extends _site {
	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('contact');
	}
}
<?php

class linksPage extends _site {
	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('links');
	}
}
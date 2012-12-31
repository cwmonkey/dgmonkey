<?php

class sponsored_playersPage extends _site {
	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('sponsored_players');
	}
}
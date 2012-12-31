<?php

class site404Page extends _site {
	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets("site404");
	}
}

<?php

class coursesPage extends _site {
	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('courses');
	}
}
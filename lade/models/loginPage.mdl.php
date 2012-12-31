<?php

class loginPage extends _site {
	public $FormFail = FALSE;
	public $FormAction = 'login';
	public $ShowNav = FALSE;

	// TODO: allow for overriding variables
	public static function InitializeSite() {
		if ( isset($_GET['backlink']) ) {
			self::$view->FormAction .= '?backlink=' . $_GET['backlink'];
		}
		_site::$Secured = FALSE;
		_site::InitializeSite();
	}

	public static function ProcessForm() {
		$username = @$_POST['gcmsusername'];
		$password = @$_POST['gcmspassword'];

		$query = "SELECT * FROM
								ladedgm_usr AS usr
							JOIN ladedgm_grp AS grp
								ON usr.grp_id=grp.id AND grp.enabled=1
							JOIN ladedgm_rght AS rght
								ON rght.grp_id=grp.id AND rght.enabled=1
							WHERE usr.email='" . mysql_escape_string($username) . "' AND usr.pass='" . mysql_escape_string($password) . "' AND usr.enabled=1";

		$result = _site::Query($query);

		$valid = mysql_num_rows($result);

		//while ( $row = mysql_fetch_assoc($result) ) {
			//var_dump($row);
			//echo "<br />";
		//}

		if ( $valid ) {
			$_SESSION['gcmsusername'] = $_POST['gcmsusername'];
			$_SESSION['gcmspassword'] = $_POST['gcmspassword'];
			// TODO: go back to url requested
			if ( isset($_GET['backlink']) ) {
				self::Redirect($_GET['backlink']);
			} else {
				self::Redirect(self::$view->Url);
			}
		}

		self::$view->FormFail = TRUE;
	}
}
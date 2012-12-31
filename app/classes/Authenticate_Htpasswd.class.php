<?php

class Authenticate_Htpasswd extends Authenticate {
	private $HtpasswdFile;

	public function __construct($htpasswd_file) {
		if ( !file_exists($htpasswd_file) ) throw new HtpasswdAuthenticateException('Could not load authentication file.');
		$this->HtpasswdFile = $htpasswd_file;
	}

	public function Validate() {
		$this->HtpasswdFile = $this->_LoadHtpasswd();

		if ( isset($_SERVER['PHP_AUTH_USER']) && $this->_TestHtpasswd($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ) {
			return true;
		} else {
			header('WWW-Authenticate: Basic realm="Restricted area"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Access denied. Please enter correct credentials.';
			exit;
		}
	}

	private function _LoadHtpasswd() {
		$res = Array();

		foreach ( file($this->HtpasswdFile) as $l ) {
			$array = explode(':',$l);
			$user = $array[0];
			$pass = chop($array[1]);
			$res[$user] = $pass;
		}

		return $res;
	}

	private function _TestHtpasswd($user, $pass) {
		if ( !isset($this->HtpasswdFile[$user]) ) return False;
		$crypted = $this->HtpasswdFile[$user];

		// Determine the password type
		// TODO: Support for MD5 Passwords
		if ( substr($crypted, 0, 6) == '{SSHA}' ) {
			$ohash = base64_decode(substr($crypted, 6));
			return substr($ohash, 0, 20) == pack('H*', sha1($pass . substr($ohash, 20)));
		} else if ( substr($crypted, 0, 5) == '{SHA}' ) {
			return ($this->_NonSaltedSha1($pass) == $crypted);
		} else {
			return crypt( $pass, substr($crypted, 0, CRYPT_SALT_LENGTH) ) == $crypted;
		}
	}

	private function _NonSaltedSha1( $pass ) {
		return '{SHA}' . base64_encode(pack('H*', sha1($pass)));
	}
}

class HtpasswdAuthenticateException extends Exception { }
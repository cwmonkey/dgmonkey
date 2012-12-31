<?php

class Config {
	private static $_config = array();
	// Loads variables set in php files into self::$_config
	public static function Load($file_name) {
		if ( !include_once($file_name) ) {
			throw new ConfigFileDoesNotExistException('Could not load configuration ' . $file_name);
		} else {
			unset($file_name);
			self::$_config = array_merge(self::$_config, get_defined_vars());
		}
	} // LoadConfig()

	// Get a value from self::$_config, if $required is passed as TRUE and value not defined, death occurs
	public static function Get($var_name, $default = NULL, $required = FALSE) {
		if ( isset(self::$_config[$var_name]) ) {
			return self::$_config[$var_name];
		} elseif ( !$required ) {
			return $default;
		} else {
			throw new RequiredConfigKeyNotSetException('Required configuration missing ' . $var_name);
		}
	} // Get()
}

class ConfigFileDoesNotExistException extends Exception { }

class RequiredConfigKeyNotSetException extends Exception { }
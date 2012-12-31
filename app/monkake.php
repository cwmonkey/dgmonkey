<?php

class M {
	public static function Init($dirname) {
		// TODO: Fix all this garbage

		$_SERVER['PATH_INFO'] = str_replace('/doc-root', '', @$_SERVER['PATH_INFO']);
		error_reporting(0);

		ob_start();

		// Main M configuration file
		self::LoadConfig($dirname . '/config.php');

		// Attempt to load local configuration for overrides, etc
		if ( file_exists($dirname . '/local.config.php') ) {
			self::LoadConfig($dirname . '/local.config.php');
		}

		// Get page controller name
		$page_name = @$_SERVER['PATH_INFO'];

		$controller_name = M::GetIntercept($page_name);

		if ( $controller_name == NULL ) $controller_name = M::Get('site_404_controller');

		// TODO: Run more validation on the controller name?
		if ( !$controller_name ) M::Error('Could not locate 404 controller', TRUE);
		$controller_file = M::Get('controller_directory', NULL, TRUE) . $controller_name . M::Get('controller_file_append');

		// Include page controller file
		if ( !include_once($controller_file) ) M::Error('Could not load controller ' . $controller_file, TRUE);

		// Include main Monkake controller here:
		$html = ob_get_clean();

		echo $html;
	}

	// Only static methods/members in this class, disallow instantiation.
	private function __construct() { }

	// Store errors in self::$_errors, if $fatal == TRUE death occurs
	private static $_Errors = array();
	public static function Error($message, $fatal = false) {
		self::$_Errors[] = array(
			'message' => $message,
			'debug_backtrace' => debug_backtrace(),
			);

		if ( $fatal ) {
			echo self::Get('fatal_error_message', 'An error occured while processing your request.');
			if ( self::Get('debug') ) {
				echo '<pre>';
				var_dump(self::$_Errors);
				echo '</pre>';
			}
			die();
		}
	} // Error()

	// URL intercepts
	public static function GetIntercept($path) {
		$intercepts = self::Get('url_intercepts', array(), FALSE);
		foreach ( $intercepts as $pattern => $controller ) {
			if ( preg_match($pattern, $path) ) {
				return $controller;
			}
		}

		return NULL;
	}

	private static $_Config = array();
	// Get a value from self::$_config, if $required is passed as TRUE and value not defined, death occurs
	public static function Get($var_name, $default = NULL, $required = FALSE) {
		if ( isset(self::$_Config[$var_name]) ) {
			return self::$_Config[$var_name];
		} elseif ( !$required ) {
			return $default;
		} else {
			throw new RequiredConfigKeyNotSetException('Required configuration missing ' . $var_name);
		}
	} // Get()

	public static function Set($var_name, $var_value = NULL, $overwrite = TRUE) {
		if ( is_array($var_name) ) {
			foreach ( $var_name as $key => $val ) {
				self::Set($key, $val);
			}
		} else {
			if ( $overwrite ) {
				self::$_Config[$var_name] = $var_value;
			} else if ( !isset($_config[$var_name]) ) {
				self::$_Config[$var_name] = $var_value;
			}
		}
	} // Set()


	public function LoadConfig($file) {
		if ( file_exists($file) ) {
			require_once($file);
			self::Set(get_defined_vars());
		}
	}

	private static $_loadedfiles = array();
	private static $_loadedinstances = array();
	public static function &GetObject($name) {
		// Load class file
		$override_path = self::Get('class_override_directory') . $name . '.class.php';
		$path = self::Get('class_directory', NULL, TRUE) . $name . '.class.php';
		if ( !isset(self::$_loadedfiles[$override_path]) && !isset(self::$_loadedfiles[$path]) ) {
			if ( (file_exists($override_path)) && (require_once($override_path)) && (class_exists($name)) ) {
				self::$_loadedfiles[$override_path] = TRUE;
			} elseif ( (file_exists($path)) && (require_once($path)) && (class_exists($name)) ) {
				self::$_loadedfiles[$path] = TRUE;
			} else {
				die('an error occured while trying to load the class file "' . $override_path . ', ' . $path . '"');
			}
		}

		// Generate key based on class and parameters passed
		$class_key = '';
		if ( func_num_args() > 1 ) {
			$class_key = $name . serialize(func_get_args());
		} else {
			$class_key = $name;
		}

		// Instantiate object
		$instance = NULL;
		if ( !isset(self::$_loadedinstances[$class_key]) ) {
			if ( func_num_args() > 1 ) {
				$args = array();
				for ( $arg_count = 1; $arg_count < func_num_args(); $arg_count++ ) {
					$args[]  = func_get_arg($arg_count);
				}

				$arg_names = array();
				foreach ( $args as $key => $val ) {
					$arg_names[] = '$args[' . $key . ']';
				}
				eval('self::$_loadedinstances[$class_key] = new ' . $name . '(' . implode(', ', $arg_names) . ');');
			} else {
				eval('self::$_loadedinstances[$class_key] = new ' . $name . '();');
			}

			return self::$_loadedinstances[$class_key];
		} else {
			return self::$_loadedinstances[$class_key];
		}
	}

	public static function autoload($name) {
		$file = self::Get('monkake_dir');
		$loading_file = null;
		$class_file = $file . self::Get('class_directory') . $name . self::Get('class_file_append');
		$loading = null;

		if ( strpos($name, self::Get('controller_append')) === 0 ) {
			$loading = 'controller';
			$name = str_replace(self::Get('controller_append'), '', $name);
		} elseif ( strpos($name, self::Get('model_append')) === 0 ) {
			$loading = 'model';
			$name = str_replace(self::Get('model_append'), '', $name);
		} elseif ( strpos($name, self::Get('view_append')) === 0 ) {
			$loading = 'view';
			$name = str_replace(self::Get('view_append'), '', $name);
		}

		if ( $loading ) {
			$loading_file = $file . self::Get($loading . '_dir') . $name . self::Get($loading . '_file_append');
			if ( !$loading_file || !file_exists($loading_file) || !require_once($loading_file) ) {
				throw new Exception('Unable to load class: ' . $name . ' as ' . $loading_file);
			}
		} else if ( !$class_file || !file_exists($class_file) || !require_once($class_file) ) {
			throw new Exception('Unable to load class: ' . $name . ' as ' . $class_file);
		}
	}
}


function __autoload($name) {
	M::autoload($name);
}

class RequiredConfigKeyNotSetException extends Exception { }

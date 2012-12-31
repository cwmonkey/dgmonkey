<?php

class Cache {
	private $_Ttl;
	private $_ServeCache;
	private $_IsVar;
	private $_CacheFile;
	private $_CacheRoot;
	private $_Buffering = FALSE;
	private $_ModTime;
	public $Error;

	public function __construct($cache_root=NULL, $is_var=FALSE, $serve_cache='auto', $ttl=600) {
		$this->SetOptions($cache_root, $is_var, $serve_cache, $ttl);
	}

	public function SetOptions($cache_root=NULL, $is_var=FALSE, $serve_cache='auto', $ttl=600) {
		$this->_Ttl = $ttl;

		if ( array_key_exists('sCache', $_GET) && $_GET['sCache'] ) {
			$this->_ServeCache = TRUE;
		} elseif ( array_key_exists('sCache', $_GET) && !$_GET['sCache'] ) {
			$this->_ServeCache = FALSE;
		} else {			
			$this->_ServeCache = $serve_cache;
		}

		$this->_IsVar = $is_var;

		if ( !$cache_root ) {
			$this->_CacheRoot = dirname(__FILE__) . '/cache/';
		} else {
			$this->_CacheRoot = $cache_root;
		}

		if ( file_exists($this->_CacheFile) ) {
			$this->_ModTime = filemtime($this->_CacheFile);
		}
	}

	public function SetServeCache($serve_cache) {
		$this->_ServeCache = $serve_cache;
	}

	private function _SetCacheFile($cache_name) {
		$this->_CacheFile = $this->_CacheRoot . $cache_name;
	}

	public function CheckCache($cache_name) {
		//$cache_file = $this->GetCacheFile($cache_name); - TODO: should do something more like this
		$cache_file = $this->_CacheRoot . $cache_name;

		if ( $this->_ServeCache == 'auto' ) {
			if ( file_exists($cache_file) ) {
				if ( time() - filemtime($cache_file) < $this->_Ttl ) {
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		} elseif ( $this->_ServeCache == TRUE ) {
			if ( file_exists($cache_file) ) {
				return TRUE;
			} else {
				return FALSE;
			}
		} elseif ( $this->_ServeCache == FALSE ) {
			return FALSE;
		}
	}

	public function GetCache($cache_name) {
		$this->_SetCacheFile($cache_name);

		if ( $this->CheckCache($cache_name) ) {
			if ( $this->_IsVar ) {
				return unserialize(file_get_contents($this->_CacheFile));
			} else {
				return file_get_contents($this->_CacheFile);
			}
		} else {
			if ( $this->_Buffering ) {
				$this->Errror = 'GetCache called while GetCache already active.';
				ob_end_clean();
			}
			ob_start();
			$this->_Buffering = TRUE;
			return FALSE;
		}
	}

	public function EchoCache($cache_name) {
		$var = $this->GetCache($cache_nam);
		echo $var;
		return $var;
	}

	public function SetCache($var = '') {
		if ( $this->_IsVar ) {
			$var = serialize($var);
		} else if ( $this->_Buffering ) {
			$this->_Buffering = FALSE;
			$var = ob_get_clean();
		}

		// If file does not exist make sure file structure is set up
		if ( !file_exists($this->_CacheFile) ) {
			$pieces = split('/', $this->_CacheFile);
			if ( '' == array_pop($pieces) ) return FALSE;
			$dir = '';
			foreach ( $pieces as $piece ) {
				$dir .= $piece . '/';
				if ( !is_dir($dir) ) {
					if( !mkdir($dir) ) {
						$this->Error = 'Error while creating directory: "' . $dir . '"';
						return FALSE;
					}
					chmod($dir, 0777);
				}
			}
		}
	
		if ( !$handle = fopen($this->_CacheFile, 'w') ) {
			$this->Error = 'Error while fopen(' . $this->_CacheFile . ')';
			return FALSE;
		}

		if ( fwrite($handle, $var) === FALSE ) {
			$this->Error = 'fwrite error';
			return FALSE;
		}

		fclose($handle);

		return $var;
	}
} // Cache{}

?>
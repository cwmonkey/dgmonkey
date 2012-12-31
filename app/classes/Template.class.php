<?php

class Template {
	private $_FileName;
	private $_WrapperFileName;
	private $_PageContent;
	private $_PageContentSet = FALSE;
	//private $_PageDataName = 'page';
	//private $_Variables;

	//public function __construct($variables = array()) {
		//if ( $page_data_name && $page_data_name != $this->_PageDataName ) $this->_PageDataName = $page_data_name;
		//$this->_Variables = $variables;
	//}

	public function SetFileName($file_name) {
		if ( $file_name && file_exists($file_name) ) {
			$this->_FileName = $file_name;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function SetWrapperFileName($wrapper_file_name) {
		if ( $wrapper_file_name && file_exists($wrapper_file_name) ) {
			$this->_WrapperFileName = $wrapper_file_name;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function SetPageContent($variables = array()) {
		if ( isset($variables['variables']) ) {
			extract($variables);
			unset($variables);
		} else {
			extract($variables);
		}

		if ( $this->_FileName ) {
			ob_start();
			include($this->_FileName);
			
			$this->_PageContent = ob_get_clean();
			$this->_PageContentSet = TRUE;
		}
	}

	public function RenderOutput($variables = array()) {
		if ( isset($variables['variables']) ) {
			extract($variables);
			unset($variables);
		} else {
			extract($variables);
		}

		if ( $this->_PageContentSet ) {
			if ( $this->_WrapperFileName ) {
				$content = $this->_PageContent;
	
				include($this->_WrapperFileName);
			} else {
				echo $this->_PageContent;
			}
		} else {
			include($this->_FileName);
		}
	}
} // Template{}

?>

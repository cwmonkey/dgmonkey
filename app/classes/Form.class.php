<?php

// Generic class for holding form information
class Form {
	// Form properties
	public $Action = '';
	public $Enctype = '';
	public $Method = '';

	public $Inputs = array();
	public $HiddenInputs = array();
	private $_InputsByName = array();
	public $Info = array();
	public $Errors = array();

	// Add "visible" input
	public function AddInput($input) {
		$this->_InputsByName[$input->Name] = $input;
		$this->Inputs[] =& $this->_InputsByName[$input->Name];
	}

	public function AddInputToInput($input_subject, $input_to_add) {
		$this->_InputsByName[$input_to_add->Name] = $input_to_add;
		//$this->Inputs[] =& $this->_InputsByName[$input->Name];
		$input_subject->AddInput($input_to_add);
	}

	// Add "hidden" input
	public function AddHiddenInput($input) {
		$this->_InputsByName[$input->Name] = $input;
		$this->HiddenInputs[] =& $this->_InputsByName[$input->Name];
	}

	// Return input by name requested
	public function &GetInput($name) {
		$input =& $this->_InputsByName[$name];
		return $input;
	}
}

// Generic class for holding input information
class FormInput {
	public $Type = '';
	public $Name = '';
	public $Value = '';
	public $LabelText = '';
	public $Maxlength;
	public $Error = '';
	public $Required = false;
	public $Disabled = false;
	public $Selected = false;

	public $Inputs = array();
	private $_InputsByName = array();
	public $Info = array();
	public $Cols = '';
	public $Rows = '';
	public $Id = null;

	public function __construct(
		$type,
		$name = null,
		$value = null,
		$label_text = '',
		$max_length = null,
		$required = false,
		$disabled = false,
		$selected = false,
		$error = '',
		$inputs = array(),
		$info = null,
		$cols = '',
		$rows = '',
		$id = null
		) {

		if ( is_array($type) ) {
			$this->Type = ( isset($type['type']) ) ? $type['type'] : '';
			$this->Name = ( isset($type['name']) ) ? $type['name'] : $name;
			$this->Value = ( isset($type['value']) ) ? $type['value'] : $value;
			$this->LabelText = ( isset($type['label_text']) ) ? $type['label_text'] : $label_text;
			$this->MaxLength = ( isset($type['max_length']) ) ? $type['max_length'] : $max_length;
			$this->Required = ( isset($type['required']) ) ? $type['required'] : $required;
			$this->Disabled = ( isset($type['disabled']) ) ? $type['disabled'] : $disabled;
			$this->Selected = ( isset($type['selected']) ) ? $type['selected'] : $selected;
			$this->Error = ( isset($type['error']) ) ? $type['error'] : $error;
			$this->Inputs = ( isset($type['inputs']) ) ? $type['inputs'] : $inputs;
			$this->Info = ( isset($type['info']) ) ? $type['info'] : $info;
			$this->Cols = ( isset($type['cols']) ) ? $type['cols'] : $cols;
			$this->Rows = ( isset($type['rows']) ) ? $type['rows'] : $rows;
			$this->Id = ( isset($type['id']) ) ? $type['id'] : $id;
		} else {
			$this->Type = $type;
			$this->Name = $name;
			$this->Value = $value;
			$this->LabelText = $label_text;
			$this->MaxLength = $max_length;
			$this->Required = $required;
			$this->Disabled = $disabled;
			$this->Selected = $selected;
			$this->Error = $error;
			$this->Inputs = $inputs;
			$this->Info = $info;
			$this->Cols = $cols;
			$this->Rows = $rows;
			$this->Id = $id;
		}

		if ( $this->Id == null ) {
			$search = array('[',  ']');
			$replace = array('LB', 'RB');
			$this->Id = str_replace($search, $replace, $this->Name);
		}
	}
	public function AddInput($input) {
		$this->_InputsByName[$input->Name] = $input;
		$this->Inputs[] =& $this->_InputsByName[$input->Name];
	}

	public function &GetInput($name) {
		$input =& $this->_InputsByName[$name];
		return $input;
	}
}
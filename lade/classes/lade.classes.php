<?php

class Lade {
	public $_conn;
	public function __construct($addr, $user, $pass, $dbname, $type = 'mysql') {
		$this->_conn = new LadeConn($addr, $user, $pass, $dbname, $type);
	}

	public $DefaultAscDesc = "asc";
	public function InvertAcsDecs($ascdesc)
	{
		if ( $ascdesc != "asc" && $ascdesc != "desc" ) {
			if ( $this->DefaultAscDesc == "asc" ) {
				return "desc";
			} else {
				return "asc";
			}
		} elseif ( $ascdesc == "asc" ) {
			return "desc";
		} else {
			return "asc";
		}
	}

	public $Tables = array();

	public function AddTables($tables) {
		foreach($tables as $table) {
			$this->AddTable($table);
		}
	}

	public function AddTable($table) {
		if ( gettype($table) == 'object' ) {
			$this->Tables[$table->Name] = $table;
		} elseif ( gettype($table) == 'array' ) {
			$ltable = new LadeTable($table['Name'], $table['SimpleName'], $table['DisplayName']);

			foreach($table['Columns'] as $column) {
				$lcolumn = new LadeColumn($column['Name'], $column['SimpleName'], $column['DisplayName'], @$column['Editable'], @$column['Addable'], @$column['Listed']);

				if ( isset($column['IsNull']) ) $lcolumn->SetIsNull($column['IsNull']);
				if ( isset($column['Boolean']) ) $lcolumn->SetBoolean($column['Boolean']);
				if ( isset($column['NumericUpDown']) ) $lcolumn->SetNumericUpDown($column['NumericUpDown']);
				if ( isset($column['AllowGet']) ) $lcolumn->SetAllowGet($column['AllowGet']);
				if ( isset($column['InputType']) ) $lcolumn->SetInputType($column['InputType']);
				if ( isset($column['DefaultAscDesc']) ) $lcolumn->SetDefaultAscDesc($column['DefaultAscDesc']);
				if ( isset($column['DefaultValue']) ) $lcolumn->SetDefaultValue($column['DefaultValue']);
				if ( isset($column['DefaultValueQuery']) ) $lcolumn->SetDefaultValueQuery($column['DefaultValueQuery']);
				if ( isset($column['FkTable']) ) $lcolumn->SetFkTable($this->GetTableByName($column['FkTable']));
				if ( isset($column['TruncateAt']) ) $lcolumn->SetTruncateAt($column['TruncateAt']);
				if ( isset($column['RevisionColumn']) ) $lcolumn->SetRevisionColumnName($column['RevisionColumn']);
				if ( isset($column['ValidationType']) ) $lcolumn->SetValidationType($column['ValidationType']);
				if ( isset($column['UseParentPk']) ) $lcolumn->SetUseParentPk($column['UseParentPk']);
				if ( isset($column['FormNote']) ) $lcolumn->SetFormNote($column['FormNote']);

				if ( isset($column['UploadDirectory']) ) $lcolumn->SetUploadDirectory($column['UploadDirectory']);
				if ( isset($column['UploadVirtualDirectory']) ) $lcolumn->SetUploadVirtualDirectory($column['UploadVirtualDirectory']);
				if ( isset($column['UploadFilename']) ) $lcolumn->SetUploadFilename($column['UploadFilename']);

				if ( isset($column['UploadImageDefaultWidthMax']) ) $lcolumn->SetUploadImageDefaultWidthMax($column['UploadImageDefaultWidthMax']);

				$ltable->AddColumn($lcolumn);

				if ( $column['Name'] == $table['PrimaryKeyColumn'] ) {
					$ltable->SetLastAsPkColumn();
				}

				if ( $column['Name'] == $table['SimpleColumn'] ) {
					$ltable->SetLastAsSimpleColumn();
				}

				if ( $column['Name'] == $table['DisplayColumn'] ) {
					$ltable->SetLastAsDisplayColumn();
				}
			}

			if ( isset($table['RevisionTable']) ) $ltable->SetRevisionTableName($table['RevisionTable']);

			$this->Tables[$ltable->Name] = $ltable;
		}
	}

	public function GetTableByName($name) {
		foreach( $this->Tables as $table ) {
			if ( $table->Name == $name ) return $table;
		}
		return NULL;
	}

	public function GetSectionByName($name) {
		foreach( $this->Sections as $section) {
			if ( $section->Name == $name ) return $section;
		}
		return NULL;
	}

	public $Sections = array();

	public function AddSections($sections) {
		foreach($sections as $section) {
			$this->AddSection($section);
		}
	}

	public function AddSection($section) {
		if ( gettype($section) == 'object' ) {
			// TODO: Set parent object?
			$section->SetConn($this->_conn);
			$this->Sections[$section->Name] = $section;
		} elseif ( gettype($section) == 'array' ) {
			$lsection = new LadeSection($section['Name'], $section['DisplayName']);
			$lsection->SetTable($this->GetTableByName($section['Table']));
			if ( isset($section['SubSection']) && $section['SubSection'] ) {
				$lsection->SetSubSection($this->GetSectionByName($section['SubSection']));
			}

			$lsection->SetConn($this->_conn);
			$this->Sections[$lsection->Name] = $lsection;
		}
	}

	public function GetConn() {
		return $this->_conn;
	}

	public function GetList($section_name, $where = '', $order = '', $ascdesc = '') {
		$lade_list = new LadeList($this, $section_name, $where, $order, $ascdesc);
		return $lade_list;
	}
}

class LadeConn {
	private $_type;
	private $_addr;
	private $_user;
	private $_pass;
	private $_dbname;
	private $_link;

	public function __construct($addr, $user, $pass, $dbname, $type = 'mysql') {
		$this->_type = $type;
		$this->_addr = $addr;
		$this->_user = $user;
		$this->_dbname = $dbname;
		$this->_pass = $pass;
	}

	public function GetResult($query) {
		if ( !$this->_link ) $this->Connect();

		switch ( $this->_type ) {
			case 'mysql':
				$result = mysql_query($query);

				return $result;
				break;
		}
	}

	public function Connect() {
		switch ( $this->_type ) {
			case 'mysql':
				$this->_link = mysql_connect($this->_addr, $this->_user, $this->_pass);
				mysql_query("use " . $this->_dbname);
				break;
		}
	}
}

class LadeTable {
	public $Name;
	public $SimpleName;
	public $DisplayName;
	public $Columns = array();
	public $PkColumn;
	public $DisplayColumn;
	public $SimpleColumn;

	public $RevisionTableName;
	public function SetRevisionTableName($name) {
		$this->RevisionTableName = $name;
	}

	private $_defaultOrderByColumn = NULL;
	public function DefaultOrderByColumn($column = NULL) {
		if ($column != NULL) $this->_defaultOrderByColumn = $column;
		if ( $this->_defaultOrderByColumn == NULL ) return $this->PkColumn;
		return $this->_defaultOrderByColumn;
	}

	public function __construct($name, $simple_name, $display_name) {
		$this->Name = $name;
		$this->SimpleName = $simple_name;
		$this->DisplayName = $display_name;
	}

	public function AddPkColumn($column) {
		$this->PkColumn = $column;
		$this->AddColumn($column);
	}

	public function AddDisplayColumn($column) {
		$this->DisplayColumn = $column;
		$this->AddColumn($column);
	}

	public function AddSimpleColumn($column) {
		$this->SimpleColumn = $column;
		$this->AddColumn($column);
	}

	public function AddColumn($column) {
		$column->Table = $this;
		$this->Columns[$column->Name] = $column;
	}

	public function SetLastAsPkColumn() {
		$keys = array_keys($this->Columns);
		//$this->PkColumn = $this->Columns[count($this->Columns) - 1];
		$this->PkColumn = $this->Columns[$keys[count($keys) - 1]];
	}

	public function SetLastAsDisplayColumn() {
		$keys = array_keys($this->Columns);
		//$this->DisplayColumn = $this->Columns[count($this->Columns) - 1];
		$this->DisplayColumn = $this->Columns[$keys[count($keys) - 1]];
	}

	public function SetLastAsSimpleColumn() {
		$keys = array_keys($this->Columns);
		//$this->SimpleColumn = $this->Columns[count($this->Columns) - 1];
		$this->SimpleColumn = $this->Columns[$keys[count($keys) - 1]];
	}

	public function SetLastAsDefaultOrderByColumn() {
		$keys = array_keys($this->Columns);
		//$this->DefaultOrderByColumn = $this->Columns[count($this->Columns) - 1];
		$this->DefaultOrderByColumn = $this->Columns[$keys[count($keys) - 1]];
	}

	public function GetColumnByColumn($name) {
		foreach ($this->Columns as $column) {
			if ($name == $column->Name) return $column;
		}
		return NULL;
	}

	public function GetColumnBySimpleName($simple_name) {
		foreach ($this->Columns as $column) {
			if ($simple_name == $column->SimpleName) return $column;
		}
		return NULL;
	}
}

class LadeValidationType {
	public $None = NULL;
	public $Date = 'date';
}

class LadeValidation {
	public $RegEx = '';
	public $Description = '';
	public $Error = '';

	public function Validate($value) {
		
	}
}

class LadeValidationDate {
	
}

class LadeColumn {
	public $Name;
	public $SimpleName;
	public $DisplayName;
	public $FkColumn;
	public $Table;
	public $FkTable;
	public $Listed;
	public $Editable;
	public $Addable;
	public $TruncateAt = 0;
	public $Options = array();

	public $UploadFilename;
	public function SetUploadFilename($value) {
		$this->UploadFilename = $value;
	}

	public $UploadImageDefaultWidthMax;
	public function SetUploadImageDefaultWidthMax($value) {
		$this->UploadImageDefaultWidthMax = $value;
	}

	public $UploadDirectory;
	public function SetUploadDirectory($value) {
		$this->UploadDirectory = $value;
	}

	public $UploadVirtualDirectory;
	public function SetUploadVirtualDirectory($value) {
		$this->UploadVirtualDirectory = $value;
	}

	public $FormNote;
	public function SetFormNote($value) {
		$this->FormNote = $value;
	}

	public $RevisionColumnName;
	public function SetRevisionColumnName($value) {
		$this->RevisionColumnName = $value;
	}

	public $UseParentPk = FALSE;
	public function SetUseParentPk($value) {
		$this->UseParentPk = $value;
	}

	public $IsNull = FALSE;
	public function SetIsNull($value) {
		$this->IsNull = $value;
	}

	public $Boolean = FALSE;
	public function SetBoolean($value) {
		$this->Boolean = $value;
	}

	public $NumericUpDown = FALSE;
	public function SetNumericUpDown($value) {
		$this->NumericUpDown = $value;
	}

	public $AllowGet = FALSE;
	public function SetAllowGet($value) {
		$this->AllowGet = $value;
	}


	public $InputType;
	public function SetInputType($input_type) {
		$this->InputType = strtolower($input_type);
	}

	public $DefaultAscDesc = "asc";
	public function SetDefaultAscDesc($ascdesc) {
		$this->DefaultAscDesc = $ascdesc;
	}

	public $DefaultValue = "";
	public function SetDefaultValue($value) {
		$this->DefaultValue = $value;
	}

	public $DefaultValueQuery;
	public function SetDefaultValueQuery($value) {
		$this->DefaultValueQuery = $value;
	}

	public $ValidationType;
	public function SetValidationType($value) {
		$this->ValidationType = $value;
	}

	public function SetFkTable($table) {
		$this->FkTable = $table;
	}

	public function SetTruncateAt($truncateat) {
		$this->TruncateAt = $truncateat;
	}

	public function __construct($name, $simple_name, $display_name, $editable, $addable, $listed) {
		$this->Name = $name;
		$this->SimpleName = $simple_name;
		$this->DisplayName = $display_name;
		$this->Listed = $listed;
		$this->Editable = $editable;
		$this->Addable = $addable;
		$this->Listed = $listed;
	}
}

class LadeSection {
	public $Name;
	public $DisplayName;
	public $Table;
	public $ParentSection;
	public $SubSection;

	public function __construct($name, $display_name) {
		$this->Name = $name;
		$this->DisplayName = $display_name;
	}

	private $_conn;
	public function SetConn($conn) {
		$this->_conn = $conn;
	}

	public function SetTable($table) {
		$this->Table = $table;
	}

	public function SetSubSection($section) {
		$section->ParentSection = $this;
		$section->SetConn($this->_conn);
		$this->SubSection = $section;
	}

	private $_formInfo = NULL;
	public function Form() {
		return $this->_formInfo;
	}

	public function SetForm($type) {
		$this->_formInfo = new LadeForm();
		$revisions = array();

		if ( $this->Table->RevisionTableName && isset($this->Value[$this->Table->PkColumn->Name]) ) {
			$query = "select id, revmodified from " . $this->Table->RevisionTableName . " where " . $this->Table->PkColumn->RevisionColumnName . "=" . $this->Value[$this->Table->PkColumn->Name];

			$result = $this->_conn->GetResult($query);

			if ( $result ) {
				while ( $row = mysql_fetch_assoc($result) ) {
					$revision_link = new LadeRevisionLink();
					$revision_link->Id = $row['id'];
					$revisions[] = $row;
					$revision_link->Date = $row['revmodified'];
					$this->_formInfo->Revisions[] = $revision_link;
				}

				$revision_link = new LadeRevisionLink();
				$this->_formInfo->Revisions[] = $revision_link;
			}
		}

		foreach ( $this->Table->Columns as $column ) {
			if ( !empty($_GET['hide_' . $column->SimpleName]) ) {
				continue;
			}

			if ( ($type == 'edit' && $column->Editable) || ($type == 'add' && $column->Addable) ) {
				$input = new LadeInput($column->SimpleName, $column->DisplayName, '', $column);

				if ( $column->RevisionColumnName ) {
					foreach ( $revisions as $rev ) {
						$revision_link = new LadeRevisionLink();
						$revision_link->Id = $rev['id'];
						$revision_link->Date = $rev['revmodified'];
						$input->Revisions[] = $revision_link;
					}

					$revision_link = new LadeRevisionLink();
					$input->Revisions[] = $revision_link;
				}

				$input->Type = $column->InputType;

				$input->Inputs = array();
				if ( isset($column->FormNote) ) $input->Note = $column->FormNote;

				if ( $this->Value != NULL ) $input->Value = (isset($this->Value[$column->Name]))?$this->Value[$column->Name]:'';

				// TODO: default to parent PK
				if ( $column->FkTable != NULL ) {
					if ( $column->IsNull ) {
						$sub_input = new LadeInput('', '', '', NULL);
						$input->Inputs[] = $sub_input;
					}
					
					$input->Type = 'select';
					// TODO: selected state?
					// TODO: abstract sql
					$sub_inputs = array();

					// TODO: filter, i.e. where Enabled = 1
					$query = "select * from " . $column->FkTable->Name;

					if ( $this->_conn ) {
						$result = $this->_conn->GetResult($query);
					} elseif ( $this->ParentSection->_conn ) {
						$result = $this->ParentSection->_conn->GetResult($query);
					}

					//Convert.ToString(rdr[Column.ColumnName]);

					// 2.  print necessary columns of each record
					while ( $row = mysql_fetch_assoc($result) ) {
						$sub_input = new LadeInput($column->SimpleName, $row[$column->FkTable->DisplayColumn->Name], $row[$column->FkTable->PkColumn->Name], $column);
						//sub_input.Name = ;
						// TODO: have name, value and display value here
						//sub_input.Value = ;
						$input->Inputs[] = $sub_input;
						if ( $this->Value[$column->Name] == $row[$column->FkTable->PkColumn->Name] ) {
							$sub_input->Selected = TRUE;
						} else {
							$sub_input->Selected = FALSE;
						}
					}

					$this->_formInfo->AddInput($input);
				} elseif ( $column->Boolean || !empty($_GET[$input->Name . '_boolean']) ) {
					$input->Type = 'select';

					$sub_inputs = array();

					$input_true = new LadeInput('1', 'True', '1', NULL);
					$input_false = new LadeInput('0', 'False', '0', NULL);

					if ( $this->Value[$column->Name] == '1' ) {
						$input_true->Selected = TRUE;
					} else {
						$input_false->Selected = TRUE;
					}

					$sub_inputs[] = $input_true;
					$sub_inputs[] = $input_false;

					$input->Inputs = $sub_inputs;
					$this->_formInfo->Inputs[] = $input;
				} elseif ( $column->InputType == 'image' ) {
					$input->UploadImageWidthMax = $column->UploadImageDefaultWidthMax;
					$this->_formInfo->Inputs[] = $input;
				} elseif ( !$column->InputType ) {
					$input->Type = 'text';
					$this->_formInfo->Inputs[] = $input;
				} else {
					$this->_formInfo->Inputs[] = $input;
				}
			}
		}
	}

	public function Delete() {
		$query = "delete from " . $this->Table->Name . " where " . $this->Table->PkColumn->Name . "='" . $this->Value[$this->Table->PkColumn->Name] . "'";

		$this->_conn->GetResult($query);

		return true;
	}

	public $Values;
	public function SetValues($where, $orderby, $ascdesc, $friendly_fk_values) {
		if ( $where == NULL ) $where = '';

		$order = '';
		if ( $orderby ) $order = " order by " . $orderby . " " . $ascdesc;

		$col_names = array();
		$table_names = array();
		$table_lefts = array();
		$wheres = array();
		foreach ( $this->Table->Columns as $column ) {
			// TODO: Figure out how to ignore unneeded fk'd columns, I.E joins
			if ( $column->FkTable != NULL && $column->IsNull ) {
				$col_names[] = $column->FkTable->Name . "." . $column->FkTable->DisplayColumn->Name . " as " . $column->Name;
				$table_lefts[] = " left outer join " . $column->FkTable->Name . " on " . $column->FkTable->Name . "." . $column->FkTable->PkColumn->Name . "=" . $column->Table->Name . "." . $column->Name;
			} elseif ( $column->FkTable != NULL ) {
				$col_names[] = $column->FkTable->Name . "." . $column->FkTable->DisplayColumn->Name . " as " . $column->Name;
				$table_names[] = $column->FkTable->Name;
				$qwhere = $column->FkTable->Name . "." . $column->FkTable->PkColumn->Name . "=" . $column->Table->Name . "." . $column->Name;
				$wheres[] = $qwhere;
			} else {
				$col_names[] = $this->Table->Name . "." . $column->Name . " as " . $column->Name;
			}
		}

		$table_names[] = $this->Table->Name;
		if ( $where ) $wheres[] = $where;

		$select_cols = implode(", ", $col_names);
		$select_tables = implode(", ", $table_names);
		$select_table_lefts = implode(", ", $table_lefts);
		$select_wheres = implode(" and ", $wheres);
		if ( $select_wheres != '' ) $select_wheres = " where " . $select_wheres;

		$query = "select " . $select_cols . " from " . $select_tables . $select_table_lefts . $select_wheres . $order;

		if ( $this->_conn ) {
			$result = $this->_conn->GetResult($query);
		} elseif ( $this->ParentSection->_conn ) {
			$result = $this->ParentSection->_conn->GetResult($query);
		}

		$vals = array();
		if ( $result ) {
			while ( $row = mysql_fetch_assoc($result) ) {
				$cols = array();
				foreach ( $this->Table->Columns as $column ) {
					$val = $row[$column->Name];
					if ( $column->TruncateAt > 0 && $val->Length > $column->TruncateAt ) {
						$val = substr($val, 0, $column->TruncateAt) + "&hellip;";
					}
					$cols[$column->Name] = $val;
				}
				$vals[] = $cols;
			}
		}
		$this->Values = $vals;
	}

	public $Value;
	public function SetValue($pk, $Request, $revid = NULL, $revcols = NULL) {
		$this->Value = array();
		if ( $pk != NULL ) {
			$rev_values = NULL;
			if ( ($revid || $revcols) && $this->Table->RevisionTableName ) {
				$rev_values = array();
				//$query = "select * from " . $this->Table->RevisionTableName . " where " . $this->Table->PkColumn->RevisionColumnName . "='" . $pk . "' AND id=" . $revid;
				$query = "select * from " . $this->Table->RevisionTableName . " where " . $this->Table->PkColumn->RevisionColumnName . "='" . $pk . "'";

				$result = $this->_conn->GetResult($query);

				while ( $row = mysql_fetch_assoc($result) ) {
					$rev_values[$row['id']] = $row;
				}
			}

			$query = "select * from " . $this->Table->Name . " where " . $this->Table->PkColumn->Name . "='" . $pk . "'";

			if ( $this->_conn ) {
				$result = $this->_conn->GetResult($query);
			} elseif ( $this->ParentSection->_conn ) {
				$result = $this->ParentSection->_conn->GetResult($query);
			}

			// 2.  print necessary columns of each record
			while ( $row = mysql_fetch_assoc($result) ) {
				foreach ( $this->Table->Columns as $column ) {
					if ( $Request != NULL ) {
						$request_value = NULL;
						if ( $Request == 'POST' ) {
							$request_value = @$_POST[$column->SimpleName];
						} else {
							if ( $column->AllowGet ) $request_value = @$_GET[$column->SimpleName];
						}

						if ( $request_value !== NULL && $column->Editable ) {
							if ( get_magic_quotes_gpc() ) {
								$request_value = stripslashes($request_value);
							}
							$this->Value[$column->Name] = $request_value;
						} else {
							$use_rev_id = NULL;
							if ( $revid ) $use_rev_id = $revid;
							if ( $revcols && isset($revcols[$column->SimpleName]) ) $use_rev_id = $revcols[$column->SimpleName];

							if ( $use_rev_id && $rev_values && $column->RevisionColumnName && isset($rev_values[$use_rev_id]) && isset($rev_values[$use_rev_id][$column->RevisionColumnName]) ) {
								$this->Value[$column->Name] = $rev_values[$use_rev_id][$column->RevisionColumnName];
							} else {
								$this->Value[$column->Name] = $row[$column->Name];
							}
						}
					// TODO: Uh, pretty sure this can't happen o_O
					} else {
						$use_rev_id = NULL;
						if ( $revid ) $use_rev_id = $revid;
						if ( $revcols && isset($revcols[$column->SimpleName]) ) $use_rev_id = $revcols[$column->SimpleName];

						if ( $use_rev_id && $rev_values && $column->RevisionColumnName && isset($rev_values[$use_rev_id]) && isset($rev_values[$use_rev_id][$column->RevisionColumnName]) ) {
							$this->Value[$column->Name] = $rev_values[$use_rev_id][$column->RevisionColumnName];
						} else {
							$this->Value[$column->Name] = $row[$column->Name];
						}
					}
				}
			}
		} else {
			foreach ( $this->Table->Columns as $column ) {
				if ( $column->FkTable && $column->UseParentPk && $this->ParentSection && $this->ParentSection->Value ) {
					$this->Value[$column->Name] = $this->ParentSection->Value[$column->FkTable->PkColumn->Name];
				} elseif ( $Request == 'POST' ) {
					$postvalue = @$_POST[$column->SimpleName];
					if ( isset($_POST[$column->SimpleName]) && $column->Addable ) {
						if ( get_magic_quotes_gpc() ) {
							$postvalue = stripslashes($postvalue);
						}
						$this->Value[$column->Name] = $postvalue;
					}
				} else {
					//TODO: Refactor default value/query and querystring logic?
					$request_value = NULL;
					if ( $column->AllowGet ) $request_value = @$_GET[$column->SimpleName];

					if ( $request_value != NULL ) {
						if ( get_magic_quotes_gpc() ) {
							$request_value = stripslashes($request_value);
						}
						$this->Value[$column->Name] = $request_value;
					} elseif ( $column->DefaultValueQuery ) {
						$result = $this->_conn->GetResult($column->DefaultValueQuery);
						$row = mysql_fetch_array($result);
						$value = $row[0];
						$this->Value[$column->Name] = $value;
					} else {
						$this->Value[$column->Name] = $column->DefaultValue;
					}
				}
			}
		}
	}
	
	// Save/upload from forms
	public function SaveValue() {
		$query_sets = array();
		$query_rev_sets = array();
		$query_rev_selects = array();

		foreach ( $this->Table->Columns as $column) {
			if ( $column != $this->Table->PkColumn ) {
				// TODO Copied to Add, OOGIE!
				if ( $column->InputType == 'image' && ($file = $_FILES[$column->SimpleName . '_file']) && ($image_info = getimagesize($file['tmp_name'])) ) {
					$file['name'] = stripslashes($file['name']);
					$uploaddir = $column->UploadDirectory;
					$filename = str_replace(' ', '_', strtolower(basename($file['name'])));
					$filename_parts = explode('.', $filename);
					$ext = array_pop($filename_parts);
					$filename = implode('.', $filename_parts);
					$value_filename = $column->UploadFilename;
					$value_filename = str_replace(array('{name}', '{datetime}', '{ext}'), array($filename, time(), $ext), $value_filename);
					$uploadfile = $uploaddir . $value_filename;

					if ( $column->UploadImageDefaultWidthMax && $image_info[0] > $column->UploadImageDefaultWidthMax && (($im = ImageCreateFromJPEG ($file['tmp_name'])) || ($im = ImageCreateFromPNG ($file['tmp_name'])) || ($im = ImageCreateFromGIF($file['tmp_name']))) ) {
						$h = intval($column->UploadImageDefaultWidthMax / $image_info[0] * $image_info[1]);
						$rim = ImageCreateTrueColor ($column->UploadImageDefaultWidthMax, $h);
						ImageCopyResampled ($rim, $im, 0, 0, 0, 0, $column->UploadImageDefaultWidthMax, $h, $image_info[0], $image_info[1]);
						ImageJPEG($rim, $uploadfile);
					} else {
						move_uploaded_file($file['tmp_name'], $uploadfile);
					}

					$this->Value[$column->Name] = $column->UploadVirtualDirectory . $value_filename;
				} elseif ( $column->InputType == 'upload' && ($file = $_FILES[$column->SimpleName . '_file']) && $file['name'] ) {
					$file['name'] = stripslashes($file['name']);
					$uploaddir = $column->UploadDirectory;
					$filename = str_replace(' ', '_', strtolower(basename($file['name'])));
					$filename_parts = explode('.', $filename);
					$ext = array_pop($filename_parts);
					$filename = implode('.', $filename_parts);
					$value_filename = $column->UploadFilename;
					$value_filename = str_replace(array('{name}', '{datetime}', '{ext}'), array($filename, time(), $ext), $value_filename);
					$uploadfile = $uploaddir . $value_filename;

					move_uploaded_file($file['tmp_name'], $uploadfile);

					$this->Value[$column->Name] = $column->UploadVirtualDirectory . $value_filename;
				}

				$value = $this->Value[$column->Name];

				if ( $value == NULL ) $value = '';
				if ( $value == '' && $column->IsNull ) {
					$query_sets[] = $column->Name . "=NULL";
				} else {
					$query_sets[] = $column->Name . "=\"" . addslashes($value) . "\"";
				}
			}

			if ( $column->RevisionColumnName ) {
				$query_rev_sets[] = $column->RevisionColumnName;
				$query_rev_selects[] = $this->Table->Name . "." . $column->Name;
			}
		}

		$query = "update " . $this->Table->Name . " set " . implode(", ", $query_sets) . " where " . $this->Table->PkColumn->Name . "='" . $this->Value[$this->Table->PkColumn->Name] . "'";

		if ( $this->_conn ) {
			$result = $this->_conn->GetResult($query);
		} elseif ( $this->ParentSection->_conn ) {
			$result = $this->ParentSection->_conn->GetResult($query);
		}

		if ( $result ) {
			if ( $this->Table->RevisionTableName ) {
				// TODO: Set to currently logged in user
				$query_rev_sets[] = 'revusr_id';
				$query_rev_selects[] = '0';
	
				$query_rev_sets[] = 'revmodified';
				$query_rev_selects[] = 'NOW()';
	
				$query = "insert into " . $this->Table->RevisionTableName . "(" . implode(', ', $query_rev_sets) . ") select " . implode(', ', $query_rev_selects) . " from " . $this->Table->Name . " where " . $this->Table->PkColumn->Name . "='" . $this->Value[$this->Table->PkColumn->Name] . "'";
	
				$this->_conn->GetResult($query);
			}
		}

		return $result;
	}

	public function Add() {
		$Cols = array();
		$Vals = array();
		foreach ( $this->Table->Columns as $column ) {
			// TODO Copied from SaveValue, OOGIE!
			if ( $column->InputType == 'image' && ($file = $_FILES[$column->SimpleName . '_file']) && ($image_info = getimagesize($file['tmp_name'])) ) {
				$file['name'] = stripslashes($file['name']);
				$uploaddir = $column->UploadDirectory;
				$filename = str_replace(' ', '_', strtolower(basename($file['name'])));
				$filename_parts = explode('.', $filename);
				$ext = array_pop($filename_parts);
				$filename = implode('.', $filename_parts);
				$value_filename = $column->UploadFilename;
				$value_filename = str_replace(array('{name}', '{datetime}', '{ext}'), array($filename, time(), $ext), $value_filename);
				$uploadfile = $uploaddir . $value_filename;

				if ( $column->UploadImageDefaultWidthMax && $image_info[0] > $column->UploadImageDefaultWidthMax && (($im = ImageCreateFromJPEG ($file['tmp_name'])) || ($im = ImageCreateFromPNG ($file['tmp_name'])) || ($im = ImageCreateFromGIF($file['tmp_name']))) ) {
					$h = intval($column->UploadImageDefaultWidthMax / $image_info[0] * $image_info[1]);
					$rim = ImageCreateTrueColor ($column->UploadImageDefaultWidthMax, $h);
					ImageCopyResampled ($rim, $im, 0, 0, 0, 0, $column->UploadImageDefaultWidthMax, $h, $image_info[0], $image_info[1]);
					ImageJPEG($rim, $uploadfile);
				} else {
					move_uploaded_file($file['tmp_name'], $uploadfile);
				}

				$this->Value[$column->Name] = $column->UploadVirtualDirectory . $value_filename;
			} elseif ( $column->InputType == 'upload' && ($file = $_FILES[$column->SimpleName . '_file']) && $file['name'] ) {
				$file['name'] = stripslashes($file['name']);
				$uploaddir = $column->UploadDirectory;
				$filename = str_replace(' ', '_', strtolower(basename($file['name'])));
				$filename_parts = explode('.', $filename);
				$ext = array_pop($filename_parts);
				$filename = implode('.', $filename_parts);
				$value_filename = $column->UploadFilename;
				$value_filename = str_replace(array('{name}', '{datetime}', '{ext}'), array($filename, time(), $ext), $value_filename);
				$uploadfile = $uploaddir . $value_filename;

				move_uploaded_file($file['tmp_name'], $uploadfile);

				$this->Value[$column->Name] = $column->UploadVirtualDirectory . $value_filename;
			}


			$value = @$this->Value[$column->Name];
			if ( !$value ) $value = "";

			// TODO checkboxes may come back as null if unchecked
			if ( $value !== NULL && $column != $this->Table->PkColumn ) {
				$Cols[] = $column->Name;
				if ( $column->IsNull && $value == "" ) {
					$Vals[] = "NULL";
				} else {
					$Vals[] = "'" . addslashes($value) . "'";
				}
			}
		}

		$query = "insert into " . $this->Table->Name . " (" . implode(", ", $Cols) . ") values (" . implode(", ", $Vals) . ")";

		if ( $this->_conn ) {
			return $this->_conn->GetResult($query);
		} elseif ( $this->ParentSection->_conn ) {
			return $this->ParentSection->_conn->GetResult($query);
		}
	}
}

class LadeRevisionLink {
	public $Href;
	public $Id;
}

class LadeForm {
	public $Action;
	public $Inputs = array();
	public $Revisions = array();

	public function AddInput($input) {
		$this->Inputs[] = $input;
	}
}

class LadeInput {
	public $Name;
	private $_value;
	public $Index = -1;
	public $Revisions = array();
	public $Column;
	public $UploadImageWidthMax;

	public function Value($value = NULL) {
		if ( $value != NULL ) {
			$this->_value = $value;
			foreach ( $this->Inputs as $input ) {
				if ( $value == $input->Value ) {
					$input->Selected = TRUE;
				} else {
					$input->Selected = FALSE;
				}
			}
		} else {
			return $this->_value;
		}
	}

	public $Type;
	public $DisplayName;
	public $Inputs = array();
	public $Selected;
	public $ValidationType;
	public $Note;

	public function __construct($name, $display_name, $value, $column) {
		$this->Name = $name;
		$this->DisplayName = $display_name;
		if ( $value != NULL ) $value = str_replace('<', '&lt;', str_replace('"', '&quot;', $value));
		$this->Value = $value;
		$this->Column = $column;
		if (isset($column->ValidationType)) $this->ValidationType = $column->ValidationType;
	}
}

class LadeWordlet {
	private $_editMode = FALSE;
	private $_backUrl = "";

	public function __construct($edit_mode, $back_url, $friendly_name) {
		$this->_editMode = $edit_mode;
		$this->_backUrl = $back_url;
		$this->_friendlyName = $friendly_name;
	}

	private $_lastSectionName = "";
	private $_friendlyName = "";

	private $_conn;
	public function SetConn($conn) {
		$this->_conn = $conn;
	}

	private $_privatePk = "";
	private function _pk() {
		if ( $this->_privatePk == "" ) {
			$query = "select id from ladedgm_section where name='" . $this->_lastSectionName . "'";

			$result = $this->_conn->GetResult($query);

			while ( $row = mysql_fetch_assoc($result) ) {
				$this->_privatePk = $row["id"];
			}
		}

		return $this->_privatePk;
	}
	
	private $_values = array();
	
	public $InvalidSections = array();
	
	public function AddWordlets($section_name) {
		$this->_lastSectionName = $section_name;

		$query = "select id from ladedgm_section where name='" . $section_name . "'";
		$result = $this->_conn->GetResult($query);

		if ( !$result || !mysql_num_rows($result) ) {
			$this->InvalidSections[$section_name] = $section_name;
			return FALSE;
		}
	
		$query = "select ladedgm_wordlet.name, ladedgm_wordlet.value, ladedgm_wordlet.id, ladedgm_wordlet.section_id from ladedgm_wordlet, ladedgm_section where ladedgm_section.name='" . $section_name . "' and ladedgm_wordlet.section_id=ladedgm_section.id";

		$result = $this->_conn->GetResult($query);
	
		while ( $row = mysql_fetch_assoc($result) ) {
			if ( isset($this->_values[$row["name"]]) ) {
				$id = "gcms_" . $section_name . "_" . $row["name"];
				$edit_link = "/lade/edit?section=site_section&pk=" . $row["section_id"] . "&subsection=wordlets&subpk=" . $row["id"];
				$edit_link .= "&backlink=" . urlencode($this->_backUrl); //HttpServerUtility.UrlEncode(_backUrl);
				$value = $row["value"];
				//if (_editMode) value = "<span id=\"gcms_" + section_name + "_" + rdr["name"].ToString() + "\" class=\"gcms_" + section_name + "_" + rdr["name"].ToString() + " gcms_wordlet\"><a href=\"" + edit_link + "\" class=\"gcms_edit_link\">Edit</a>" + value + "</span>";
				$item = new LadeWordletItem($section_name, $value, $edit_link, $id);
				$wordlet = $this->_values[$row["name"]];
				$wordlet->Value = $value;
				$wordlet->Section = $section_name;
				$wordlet->EditLink = $edit_link;
				$wordlet->Id = $id;
				$wordlet->Items[] = $item;
			} else {
				$id = "gcms_" . $section_name . "_" . $row["name"];
				$edit_link = "/lade/edit?section=section&pk=" . $row["section_id"] . "&subsection=wordlets&subpk=" . $row["id"];
				$edit_link .= "&backlink=" . urlencode($this->_backUrl);
				$value = $row["value"];
				//if (_editMode) value = "<span id=\"gcms_" + section_name + "_" + rdr["name"].ToString() + "\" class=\"gcms_" + section_name + "_" + rdr["name"].ToString() + " gcms_wordlet\"><a href=\"" + edit_link + "\" class=\"gcms_edit_link\">Edit</a>" + value + "</span>";
				$item = new LadeWordletItem($section_name, $value, $edit_link, $id);
				$this->_values[$row["name"]] = $item;
			}
		}

		return true;
	}

	public function Get($name, $boolean = false) {
		if ( isset($this->_values[$name]) ) {
			$wordlet = $this->_values[$name];
			if ( $this->_editMode ) {
				return "</a><span id=\"" . $wordlet->Id . "\" class=\"" . $wordlet->Id . " gcms_wordlet\"><a href=\"" . $wordlet->EditLink . ( $boolean ? '&value_boolean=1&hide_enabled=1' : '' ) . "\" class=\"gcms_link\">Edit</a>" . $wordlet->Value . "</span>";
			}

			return $wordlet->Value;
		} elseif ( $this->_editMode ) {
			$add_link = "/lade/add?section=section&pk=" . $this->_pk() . "&subsection=wordlets&" . $this->_friendlyName . "=" . $name;
			$value = "</a><span id=\"gcms_" . $this->_lastSectionName . "_" . $name . "\" class=\"gcms_" . $this->_lastSectionName . "_" . $name . " gcms_wordlet\"><a href=\"" . $add_link . "\" class=\"gcms_link\">Add '" . $name . "'</a><em>Add '" . $name . "'</em></span>";
			return $value;
		} else {
			return "";
		}
	}

	public function GetTag($name, $text, $boolean = false) {
		if ( isset($this->_values[$name]) ) {
			$wordlet = $this->_values[$name];
			if ( $this->_editMode ) {
				return "</a><span id=\"" . $wordlet->Id . "\" class=\"" . $wordlet->Id . " gcms_wordlet\"><a href=\"" . $wordlet->EditLink . ( $boolean ? '&value_boolean=1&hide_enabled=1' : '' ) . "\" class=\"gcms_link\">Edit</a>" . $text . "</span>";
			}

			return $wordlet->Value;
		} elseif ( $this->_editMode ) {
			$add_link = "/lade/add?section=section&pk=" . $this->_pk() . "&subsection=wordlets&" . $this->_friendlyName . "=" . $name;
			$value = "</a><span id=\"gcms_" . $this->_lastSectionName . "_" . $name . "\" class=\"gcms_" . $this->_lastSectionName . "_" . $name . " gcms_wordlet\"><a href=\"" . $add_link . "\" class=\"gcms_link\">Add '" . $name . "'</a><em>Add '" . $name . "'</em></span>";
			return $value;
		} else {
			return "";
		}
	}

	public function GetWordlet($name) {
		if ( isset($this->_values[$name]) ) {
			return $this->_values[$name]->Value;
		} else {
			return "";
		}
	}
}
	
class LadeWordletItem {
	public $Section;
	public $Value;
	public $EditLink;
	public $Id;
	public $Items = array();

	public function __construct($section, $value, $edit_link, $id) {
		$this->Section = $section;
		$this->Value = $value;
		$this->EditLink = $edit_link;
		$this->Id = $id;
	}
}

class LadeList {
    public $AddLink = '</a><span class="gcms_list_item"><a href="/lade/add?section=%s" class="gcms_link">Add %s item</a></span>';
    public $EditLink = '</a><span class="gcms_list_item"><a href="/lade/edit?section=%s&pk=%s" class="gcms_link">Edit</a></span>';
    public $SectionName;
	public $Values = array();

	public function __construct($lade, $section_name, $where = '', $order = '', $ascdesc = '') {
		//$this->_addListItems($section_name, $where);
	//}

    //private function _addListItems($section_name, $where) {
		//var_dump(array_keys($lade->Sections));
		$section_info = $lade->Sections[$section_name];
		$this->SectionName = $section_info->DisplayName;
		$this->AddLink =  sprintf($this->AddLink, $section_info->Name, $section_info->DisplayName);
		$this->EditLink = sprintf($this->EditLink, $section_info->Name, "%s");
		$section_info->SetValues($where, $order, $ascdesc, true);
//var_dump($section_info->Values);
		foreach($section_info->Values as $value) {
			$value['edit_link'] = sprintf($this->EditLink, $value['id']);
			$this->Values[] = $value;
			//_addListItem(value);
		}
    }
/*
    private virtual void _addListItem(Hashtable value) {
		
    }
*/
}
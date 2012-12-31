<?php

class addPage extends _site {
    public $QueryExtra;
    public $BackLink = 'list';
    public $DatabaseUpdated = FALSE;
    public $FormAction;
	public $Form;

	public static function InitializePage() {
		$view = self::$view;

		$db_info = self::Lade();

        $section = @$_GET['section'];
        $orderby = @$_GET['order'];
        $ascdesc = @$_GET['ascdesc'];
        $section_info = $db_info->Sections[$section];

        /*if (User->Rights[section] == NULL || !User->Rights[section]->Create)
        {
            return;
            // no access
        }*/

        $pk = @$_GET['pk'];

        $subsection_info = NULL;

		if ( $section_info != NULL ) {
			$subsection = @$_GET['subsection'];
			$suborderby = @$_GET['suborder'];
			$subascdesc = @$_GET['subascdesc'];

			if ( $section_info->SubSection != NULL && $section_info->SubSection->Name == $subsection ) $subsection_info = $section_info->SubSection;
			$view->FormAction = $_SERVER['REQUEST_URI'];

			if ( $subsection_info != NULL ) {
				$view->BackLink .= '?section=' . $section_info->Name . '&pk=' . $pk;
				if ( $orderby ) $view->BackLink .= '&order=' . $orderby . '&ascdesc=' . $ascdesc;
				$view->BackLink .= '&subsection=' . $subsection_info->Name;
				if ( $suborderby ) $view->BackLink .= '&suborder=' . $suborderby . '&subascdesc=' . $subascdesc;

                $section_info->SetValue($pk, NULL);
				$subsection_info->SetValue(NULL, $_SERVER['REQUEST_METHOD']);

				$view->SectionInfo = $subsection_info;
				$view->SectionInfo->ParentSection = $section_info;
			} elseif ( $section_info != NULL ) {
				$view->BackLink .= '?section=' . $section_info->Name;
				if ( $orderby ) $view->BackLink .= '&order=' . $orderby . '&ascdesc=' . $ascdesc;

				$section_info->SetValue(NULL, $_SERVER['REQUEST_METHOD']);
				$view->SectionInfo = $section_info;
			}

			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $view->SectionInfo != NULL ) {
				$view->DatabaseUpdated = $view->SectionInfo->Add();
			} else {
				if ( $subsection_info != NULL ) {
					foreach ( $subsection_info->Table->Columns as $column) {
						if ( $column->FkTable == $section_info->Table ) {
							$subsection_info->Value[$column->Name] = $pk;
						}
					}
				}
			}

			$view->SectionInfo->SetForm('add');
            
            $submit = new LadeInput('submitr', '', 'Submit', '');
            $submit->Type = 'submit';
            $view->SectionInfo->Form()->Inputs[] = $submit;

            $fieldset = new LadeInput('', '', '', '');
            $fieldset->Type = 'fieldset';
            $fieldset->Inputs = $view->SectionInfo->Form()->Inputs;
            $inputs = array();
            $inputs[] = $fieldset;
            $view->SectionInfo->Form()->Inputs = $inputs;

			$view->SectionInfo->Form()->Action = $view->FormAction;
			$view->Form = $view->SectionInfo->Form();
		}
	}
}

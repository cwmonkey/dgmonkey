<?php

class editPage extends _site {
	public $QueryExtra;
	public $BackLink = 'list';
	public $DatabaseUpdated = FALSE;
	public $NothingToUpdateError = FALSE;
	public $FormAction;
	public $Form;

	public static function InitializePage() {
		$view = self::$view;

		$db_info = self::Lade();

		$section = @$_GET['section'];
		$orderby = @$_GET['order'];
		$ascdesc = @$_GET['ascdesc'];
		$revid = @$_GET['revid'];
		$revcols = @$_GET['revcols'];

		$section_info = $db_info->Sections[$section];
		$current_link = '';

		/* if (User->Rights[section] == NULL || !User->Rights[section]->Update) {
			return;
			// no access
		} */

		$pk = @$_GET['pk'];
	
		$subsection_info = NULL;

		if ( $section_info != NULL ) {
			$subsection = @$_GET['subsection'];
			$suborderby = @$_GET['suborder'];
			$subascdesc = @$_GET['subascdesc'];

			if ( $section_info->SubSection != NULL && $section_info->SubSection->Name == $subsection ) $subsection_info = $section_info->SubSection;

			$subpk = @$_GET['subpk'];

			$query_pk;

			$view->FormAction = $_SERVER['REQUEST_URI'];

			if ( $subsection_info != NULL ) {
				if ( @$_GET['backlink'] != NULL ) {
					$view->BackLink = @$_GET['backlink'];
				} else {
					$view->BackLink .= '?section=' . $section_info->Name . '&pk=' . $pk;
					if ( $orderby ) $view->BackLink .= '&order=' . $orderby . '&ascdesc=' . $ascdesc;
					$view->BackLink .= '&subsection=' . $subsection_info->Name;
					if ( $suborderby ) $view->BackLink .= '&suborder=' . $suborderby . '&subascdesc=' . $subascdesc;
				}

				// TODO: fix this link and the link below
				$current_link = str_replace('list', '/lade/edit', $view->BackLink) . '&subpk=' . $subpk;

				$section_info->SetValue($pk, NULL);
				$subsection_info->SetValue($subpk, $_SERVER['REQUEST_METHOD'], $revid, $revcols);

				$query_pk = $subpk;

				$view->SectionInfo = $subsection_info;
				$view->SectionInfo->ParentSection = $section_info;
			} elseif ( $section_info != NULL ) {
				$view->BackLink .= '?section=' . $section_info->Name;
				if ( $orderby ) $view->BackLink .= '&order=' . $orderby . '&ascdesc=' . $ascdesc;

				$current_link = str_replace('list', '/lade/edit', $view->BackLink) . '&pk=' . $pk;

				$query_pk = $pk;

				$section_info->SetValue($pk, $_SERVER['REQUEST_METHOD'], $revid, $revcols);
				$view->SectionInfo = $section_info;
			}

			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $view->SectionInfo != NULL ) {
				$view->DatabaseUpdated = $view->SectionInfo->SaveValue();
			}

			$view->SectionInfo->SetForm('edit');

			$placeholder_link = $current_link;
			//if ( $revid ) $current_link .= '&revid=' . $revid;
			//$placeholder_link .= '&revid=' . $revid;

			foreach ( $view->SectionInfo->Form()->Inputs as $input ) {
				if ( $input->Column->RevisionColumnName ) {
					if ( isset($revcols[$input->Name]) ) {
						$placeholder_link .= '&revcols[' . $input->Name . ']=' . $revcols[$input->Name];
						$current_link .= '&revcols[' . $input->Name . ']=' . $revcols[$input->Name];
					} else {
						$placeholder_link .= '&revcols[' . $input->Name . ']=';
					}
				}
			}

			foreach ( $view->SectionInfo->Form()->Revisions as $rev ) {
				$rev->Href = $placeholder_link;

				if ( $rev->Id ) {
					foreach ( $view->SectionInfo->Form()->Inputs as $input ) {
						if ( $input->Column->RevisionColumnName ) {
							if ( isset($revcols[$input->Name]) ) {
								$rev->Href = str_replace('&revcols[' . $input->Name . ']=' . $revcols[$input->Name], '&revcols[' . $input->Name . ']=' . $rev->Id, $rev->Href);
							} else {
								$rev->Href = str_replace('&revcols[' . $input->Name . ']=', '&revcols[' . $input->Name . ']=' . $rev->Id, $rev->Href);
							}
						}
					}
				} else {
					foreach ( $view->SectionInfo->Form()->Inputs as $input ) {
						if ( $input->Column->RevisionColumnName ) {
							if ( isset($revcols[$input->Name]) ) {
								$rev->Href = str_replace('&revcols[' . $input->Name . ']=' . $revcols[$input->Name], '', $rev->Href);
							} else {
								$rev->Href = str_replace('&revcols[' . $input->Name . ']=', '', $rev->Href);
							}
						}
					}
				}

				if ( $rev->Href == $current_link ) $rev->Href = NULL;
			}

			foreach ( $view->SectionInfo->Form()->Inputs as $input ) {
				foreach ( $input->Revisions as $rev ) {
					if ( isset($revcols[$input->Name]) ) {
						if ( $rev->Id ) {
							$rev->Href = str_replace('&revcols[' . $input->Name . ']=' . $revcols[$input->Name], '&revcols[' . $input->Name . ']=' . $rev->Id, $placeholder_link);
						} else {
							$rev->Href = str_replace('&revcols[' . $input->Name . ']=' . $revcols[$input->Name], '', $placeholder_link);
						}
					} else {
						if ( $rev->Id ) {
							$rev->Href = str_replace('&revcols[' . $input->Name . ']=', '&revcols[' . $input->Name . ']=' . $rev->Id, $placeholder_link);
						} else {
							$rev->Href = str_replace('&revcols[' . $input->Name . ']=', '', $current_link);
						}
					}

					if ( !$revid ) $rev->Href = str_replace('&revid=', '', $rev->Href);
					foreach ( $view->SectionInfo->Form()->Inputs as $input2 ) {
						if ( $input2->Column->RevisionColumnName && $input->Name != $input2->Name ) {
							if ( !isset($revcols[$input2->Name]) ) {
								$rev->Href = str_replace('&revcols[' . $input2->Name . ']=', '', $rev->Href);
							}
						}
					}

					if ( $rev->Href == $current_link ) $rev->Href = NULL;
				}
			}

			$submit = new LadeInput('submitr', '', 'Submit', NULL);
			$submit->Type = 'submit';
			$view->SectionInfo->Form()->Inputs[] = $submit;

			$fieldset = new LadeInput('', '', '', NULL);
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

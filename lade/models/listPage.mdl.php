<?php

class listPage extends _site {
	public $QueryExtra;
	public $SingleEditLink = "single_edit";
	public $DeleteLink = "delete";
	public $BackLink = "";
	public $AddLink = "add";
	public $SubSectionLink = "";
	public $QueryOrderBy = "";
	public $QueryAscDesc = "";
	public $RemoveSortLink = "";
	
	public $EditBaseLink = "edit";
	public $OrderByLinks = array();
	
	public $EditLinks = array();
	public $DeleteLinks = array();

	public static function InitializePage() {
		$view = self::$view;

		$db_info = self::Lade();
	
		$section = @$_GET["section"];
		$orderby = @$_GET["order"];
		$ascdesc = @$_GET["ascdesc"];
		$section_info = @$db_info->Sections[$section];
	
		/* if ( self::$User->Rights[$section] == NULL || !self::$User->Rights[$section]->Read ) {
			return;
			// no access
		} */
	
		$pk = @$_GET["pk"];
	
		$subsection_info = NULL;
	
		if ( $section_info != NULL ) {
			$subsection = @$_GET["subsection"];
			$suborderby = @$_GET["suborder"];
			$subascdesc = @$_GET["subascdesc"];
	
			if ( $section_info->SubSection != NULL && $section_info->SubSection->Name == $subsection ) $subsection_info = $section_info->SubSection;
	
			if ( $subsection_info != NULL ) {
				$view->EditBaseLink .= "?section=" . $section_info->Name . "&pk=" . $pk;
				if ( $orderby ) $view->EditBaseLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->EditBaseLink .= "&subsection=" . $subsection_info->Name;
				if ( $suborderby ) $view->EditBaseLink .= "&suborder=" . $suborderby . "&subascdesc=" . $subascdesc;
				$view->EditBaseLink .= "&subpk=";
	
				$view->SingleEditLink .= "?section=" . $section_info->Name . "&pk=" . $pk;
				if ( $orderby ) $view->SingleEditLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->SingleEditLink .= "&subsection=" . $subsection_info->Name;
				if ( $suborderby ) $view->SingleEditLink .= "&suborder=" . $suborderby . "&subascdesc=" . $subascdesc;
				$view->SingleEditLink .= "&subpk=";
	
				$view->DeleteLink .= "?section=" . $section_info->Name . "&pk=" . $pk;
				if ( $orderby ) $view->DeleteLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->DeleteLink .= "&subsection=" . $subsection_info->Name;
				if ( $suborderby ) $view->DeleteLink .= "&suborder=" . $suborderby . "&subascdesc=" . $subascdesc;
				$view->DeleteLink .= "&subpk=";
	
				$view->AddLink .= "?section=" . $section_info->Name . "&pk=" . $pk;
				if ( $orderby ) $view->AddLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->AddLink .= "&subsection=" . $subsection_info->Name;
				if ( $suborderby ) $view->AddLink .= "&suborder=" . $suborderby . "&subascdesc=" . $subascdesc;
	
				if ( $subsection_info->Table->DefaultOrderByColumn() != NULL && ($suborderby != $subsection_info->Table->DefaultOrderByColumn()->SimpleName || $subascdesc != $subsection_info->Table->DefaultOrderByColumn()->DefaultAscDesc) ) {
					$view->RemoveSortLink .= "list?section=" . $section_info->Name . "&pk=" . $pk;
					if ( $orderby ) $view->RemoveSortLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
					$view->RemoveSortLink .= "&subsection=" . $subsection_info->Name;
	
					$view->QueryOrderBy = $suborderby;
					$view->QueryAscDesc = $subascdesc;
				}
	
				$view->BackLink .= "list?section=" . $section_info->Name;
				if ( $orderby ) $view->BackLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
	
				$view->OrderByLinkBase = "list?section=" . $section_info->Name . "&pk=" . $pk;
				if ( $orderby ) $orderbyLinkBase .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->OrderByLinkBase .= "&subsection=" . $subsection_info->Name;
	
				foreach ( $subsection_info->Table->Columns as $column ) {
					if ( $column->Listed ) {
						$view->OrderByLinks[$column->Name] = $view->OrderByLinkBase . "&suborderby=" . $column->SimpleName . "&subascdesc=" . (($suborderby == $column->SimpleName && $subascdesc == "asc") ? "desc" : "asc");
					}
				}
	
				$where = "";
				foreach ( $subsection_info->Table->Columns as $column ) {
					if ( $column->FkTable != NULL && $column->FkTable->Name == $section_info->Table->Name) $where = $column->Name . "=" . $pk;
				}

				$subsection_info->SetValues($where, $suborderby, $subascdesc, true);

				foreach ( $subsection_info->Values as $value ) {
					$view->EditLinks[] = $view->EditBaseLink . $value[$subsection_info->Table->PkColumn->Name];
					$view->DeleteLinks[] = $view->DeleteLink . $value[$subsection_info->Table->PkColumn->Name];
				}
	
				$section_info->SetValue($pk, null);
	
				$view->SectionInfo = $subsection_info;
				$view->SectionInfo->ParentSection = $section_info;
			} else {
				$view->EditBaseLink .= "?section=" . $section_info->Name;
				if ( $orderby ) $view->EditBaseLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->EditBaseLink .= "&pk=";
	
				$view->SingleEditLink .= "?section=" . $section_info->Name;
				if ( $orderby ) $view->SingleEditLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->SingleEditLink .= "&pk=";

				$view->DeleteLink .= "?section=" . $section_info->Name;
				if ( $orderby ) $view->DeleteLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;
				$view->DeleteLink .= "&pk=";

				$view->AddLink .= "?section=" . $section_info->Name;
				if ( $orderby ) $view->AddLink .= "&order=" . $orderby . "&ascdesc=" . $ascdesc;

				if ( $orderby == NULL ) {
					$orderby = $section_info->Table->DefaultOrderByColumn()->SimpleName;
					$ascdesc = $section_info->Table->DefaultOrderByColumn()->DefaultAscDesc;
				}
	
				if ( $orderby != $section_info->Table->DefaultOrderByColumn()->SimpleName || ($ascdesc != $section_info->Table->DefaultOrderByColumn()->DefaultAscDesc && $ascdesc != "asc") ) {
					$view->RemoveSortLink .= "list?section=" . $section_info->Name;
	
					$view->QueryOrderBy = $section_info->Table->Columns->GetColumnBySimpleName($orderby)->DisplayName;
					$view->QueryAscDesc = $ascdesc;
				}
	
				$view->BackLink = "/lade";
	
				$view->SubSectionLink = "list?section=" . $section_info->Name;
	
				$view->OrderByLinkBase = "list?section=" . $section_info->Name;
	
				foreach ( $section_info->Table->Columns as $column ) {
					if ( $column->Listed ) {
						// TODO: change column name to name
						$view->OrderByLinks[$column->Name] = $view->OrderByLinkBase . "&orderby=" . $column->SimpleName . "&ascdesc=" . (($orderby == $column->SimpleName && $ascdesc == "asc") ? "desc" : "asc");
					}
				}

				$section_info->SetValues("", $section_info->Table->GetColumnBySimpleName($orderby)->Name, $ascdesc, true);

				foreach ( $section_info->Values as $value ) {
					//$view->EditLinks[$value] = $view->EditBaseLink . $value[$section_info->Table->PkColumn->Name];
					//$view->DeleteLinks[$value] = $view->DeleteLink . $value[$section_info->Table->PkColumn->Name];
					$view->EditLinks[] = $view->EditBaseLink . $value[$section_info->Table->PkColumn->Name];
					$view->DeleteLinks[] = $view->DeleteLink . $value[$section_info->Table->PkColumn->Name];
				}
	
				$view->SectionInfo = $section_info;
			}
		}
	}
}

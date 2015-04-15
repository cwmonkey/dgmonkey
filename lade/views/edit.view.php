<?
	$page->Title = 'Edit';
	$page->BodyId = 'edit';
	
	$page->SetWrapperFile('_wrapper.view.php');
?>

<? if ( $page->SectionInfo != NULL && $page->SectionInfo->Value != NULL ) { ?>
	<? if ( !$page->Ajax ) { ?>
		<p><a href="<?=$page->BackLink?>">Back</a></p>
	<? } ?>
	<h3>
		Edit
		[ <?=( $page->SectionInfo->ParentSection != NULL ) ? $page->SectionInfo->ParentSection->DisplayName . " : " . htmlspecialchars($page->SectionInfo->ParentSection->Value[$page->SectionInfo->ParentSection->Table->DisplayColumn->Name]) . " - " : ""?>
		<?=$page->SectionInfo->DisplayName?> ]
		<?=htmlspecialchars($page->SectionInfo->Value[$page->SectionInfo->Table->DisplayColumn->Name])?>
	</h3>
	<? if ( $page->DatabaseUpdated ) { ?>
		<p id="gcms_success">Database Updated Successfully</p>
	<? } ?>

	<? if ( $page->NothingToUpdateError ) { ?>
		<p id="gcms_error">Nothing to update</p>
	<? } ?>

	<? if (($page->DatabaseUpdated && !$page->Ajax) || (!$page->DatabaseUpdated && !$page->Ajax) || (!$page->DatabaseUpdated && $page->Ajax)) { ?>
		<? $page->RenderView('modules/form.view.php', array('form' => $page->Form, 'page' => $page)) ?>
	<? } ?>

	<? if ( !$page->Ajax ) { ?>
		<p><a href="<?=$page->BackLink?>">Back</a></p>
	<? } ?>
<? } else { ?>
	<p id="error">Nothing here.</p>
<? } ?>
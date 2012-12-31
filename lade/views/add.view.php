<?
	$page->Title = 'Add';
	$page->BodyId = 'add';
	
	$page->SetWrapperFile('_wrapper.view.php');
?>

<? if ( !$page->Ajax ) { ?>
	<p><a href="<?=$page->BackLink?>">Back</a></p>
<? } ?>
<h3>
	Add
	[ <?=($page->SectionInfo->ParentSection) ? $page->SectionInfo->ParentSection->DisplayName . " : " . $page->SectionInfo->ParentSection->Value[$page->SectionInfo->ParentSection->Table->DisplayColumn->Name] . " - " : ""?>
	<?=$page->SectionInfo->DisplayName?> ]
	Item
</h3>

<? if ( $page->DatabaseUpdated ) { ?>
	<p id="gcms_success">Item Added</p>
<? } elseif ( $page->SectionInfo != NULL ) { ?>
	<? $page->RenderView('modules/form.view.php', array('form' => $page->Form, 'page' => $page)) ?>
<? } else { ?>
	<p id="error">Nothing here.</p>
<? } ?>

<? if ( !$page->Ajax ) { ?>
	<p><a href="<?=$page->BackLink?>">Back</a></p>
<? } ?>
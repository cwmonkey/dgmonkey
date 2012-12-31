<fieldset id="<?=$fieldset->Name?>">
	<? foreach ( $fieldset->Inputs as $input ) { ?>
		<? if ( $input->Type == 'group' ) { ?>
			<? $page->RenderView('modules/formGroup.view.php', array('group' => $input, 'page' => $page)) ?>
		<? } else { ?>
			<? $page->RenderView('modules/formInput.view.php', array('input' => $input, 'page' => $page)) ?>
		<? } ?>
	<? } ?>
</fieldset>
<div id="form_<?=$input->Name?>_wrapper" class="input_wrapper type_<?=$input->Type?><?=( $input->Index==0 )?" first_child":""?><?=( $input->Index==Count-1 )?" last_child":""?>">
	<div class="description">
		<p>
			<label for="<?=$input->Inputs[0]->Name?>"><?=$input->DisplayName?></label>
		</p>
	</div>
	<div class="data">
		<? foreach ( $input->Inputs as $input ) { ?>
			<? $page->RenderView('modules/formInput.view.php', array('input' => $input, 'page' => $page)) ?>
		<? } ?>
	</div>
</div>
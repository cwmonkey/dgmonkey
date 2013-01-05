<form enctype="multipart/form-data" action="<?=$form->Action?>" method="post" class="<?=$page->SectionInfo->Name?>">
	<div class="header">
		<? if ( $form->Revisions ): ?>
			<div class="revisions">
				<h4>Revisions:</h4>
				<ul>
					<? foreach ( $form->Revisions as $rev ): ?>
						<? if ( $rev->Href ): ?>
							<li><a href="<?=$rev->Href?>"><?=$rev->Id?$rev->Id:'Current'?>: <?=isset($rev->Date)?$rev->Date:''?></a></li>
						<? else: ?>
							<li class="current"><span><?=$rev->Id?$rev->Id:'Current'?>: <?=isset($rev->Date)?$rev->Date:''?></span></li>
						<? endif ?>
					<? endforeach ?>
				</ul>
			</div>
		<? endif ?>
	</div>
	<? foreach ( $form->Inputs as $input ): ?>
		<? if ( $input->Type == 'fieldset' ): ?>
			<? $page->RenderView('modules/formFieldset.view.php', array('fieldset' => $input, 'page' => $page)) ?>
		<? elseif ( $input->Type == 'group' ): ?>
			<? $page->RenderView('modules/formGroup.view.php', array('group' => $input, 'page' => $page)) ?>
		<? else: ?>
			<? $page->RenderView('modules/formInput.view.php', array('input' => $input, 'page' => $page)) ?>
		<? endif ?>
	<? endforeach ?>
</form>
<div id="gcms_<?=$input->Name?>_wrapper" class="gcms_wrapper <?=($input->ValidationType)?'gcms_val_'.$input->ValidationType:''?> type_<?=$input->Type?><? /*=($input->Index==0)?" first_child":""?><?=($input->Index==Count-1)?" last_child":"" */ ?>">
	<? if ( $input->DisplayName ) { ?>
		<div class="description">
			<p>
				<label for="<?=$input->Name?>"><?=$input->DisplayName?></label>
			</p>
		</div>
	<? } ?>
	<div class="data <?=$input->Type?>">
		<p>
			<? if ( $input->Type == 'select' ) { ?>
				<select id="<?=$input->Name?>" name="<?=$input->Name?>">
					<? foreach ( $input->Inputs as $subinput ) { ?>
						<option value="<?=$subinput->Value?>" <?=$subinput->Selected?" selected=\"selected\"":""?>><?=$subinput->DisplayName?></option>
					<? } ?>
				</select>
			<? } elseif ( $input->Type == 'textarea' ) { ?>
				<textarea id="<?=$input->Name?>" name="<?=$input->Name?>"><?=$input->Value?></textarea>
			<? } elseif ( $input->Type == 'submit' ) { ?>
				<input type="submit" id="<?=$input->Name?>" name="<?=$input->Name?>" value="<?=$input->Value?>" />
			<? } elseif ( $input->Type == 'image' ) { ?>
				<label for="<?=$input->Name?>_url" class="value">URL:</label>
				<input type="text" name="<?=$input->Name?>" id="<?=$input->Name?>" value="<?=$input->Value?>" class="value" /><br />
				<em>Or</em><br />
				<label for="<?=$input->Name?>_file" class="file">File:</label>
				<input type="file" name="<?=$input->Name?>_file" id="<?=$input->Name?>_file" class="file" /><br />
				<? if ( $input->UploadImageWidthMax ) { ?>
					<label for="<?=$input->Name?>_width" class="width">Max Width:</label>
					<input type="text" name="<?=$input->Name?>_width" id="<?=$input->Name?>_width" value="<?=$input->UploadImageWidthMax?>" class="width" />
				<? } ?>
			<? } elseif ( $input->Type == 'upload' ) { ?>
				<label for="<?=$input->Name?>_url" class="value">URL:</label>
				<input type="text" name="<?=$input->Name?>" id="<?=$input->Name?>" value="<?=$input->Value?>" class="value" /><br />
				<em>Or</em><br />
				<label for="<?=$input->Name?>_file" class="file">File:</label>
				<input type="file" name="<?=$input->Name?>_file" id="<?=$input->Name?>_file" class="file" />
			<? } elseif ( $input->Type == 'none' ) { ?>
				<?=$input->Value?>
			<? } else { /* text */ ?>
				<input type="<?=$input->Type?>" id="<?=$input->Name?>" name="<?=$input->Name?>" value="<?=$input->Value?>" />
			<? } ?>
		</p>
	</div>
	<? if ( $input->Note ) { ?>
		<div class="note">
			<h4>Note:</h4>
			<p>
				<?=$input->Note?>
			</p>
		</div>
	<? } ?>
	<? if ( $input->Revisions ) { ?>
		<div class="revisions">
			<h4>Input Revisions:</h4>
			<ul>
				<? foreach ( $input->Revisions as $rev ) { ?>
					<? if ( $rev->Href ) { ?>
						<li><a href="<?=$rev->Href?>"><?=$rev->Id?$rev->Id:'Current'?>: <?=isset($rev->Date)?$rev->Date:''?></a></li>
					<? } else { ?>
						<li class="current"><span><?=$rev->Id?$rev->Id:'Current'?>: <?=isset($rev->Date)?$rev->Date:''?></span></li>
					<? } ?>
				<? } ?>
			</ul>
		</div>
	<? } ?>
</div>
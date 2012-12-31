<?
	$page->Title = 'Login';
	$page->BodyId = 'login';
	
	$page->SetWrapperFile('_wrapper.view.php');
?>

<? if ( $page->FormFail ) { ?>
	<p id="gcmserror">Unable to validate your information.</p>
<? } ?>

<form action="<?=$page->FormAction?>" id="gcms_login_form" method="post">
	<fieldset>
		<p id="gcms_gcmsusername">
			<label for="gcmsusername">Username:</label>
			<input type="text" id="gcmsusername" name="gcmsusername" />
		</p>
		<p id="gcms_gcmspassword">
			<label for="gcmspassword">Password:</label>
			<input type="password" id="gcmspassword" name="gcmspassword" />
		</p>
		<p id="gcms_submit">
			<input type="submit" value="Log In" />
		</p>
	</fieldset>
</form>

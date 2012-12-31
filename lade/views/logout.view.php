<?
	$page->Title = 'Logout';
	$page->BodyId = 'logout';

	$page->SetWrapperFile('_wrapper.view.php');
?>

<p id="gcms_message">Logged out.</p>
<p>
	<a href="<?=$page->BackLink?>">Back</a>
</p>
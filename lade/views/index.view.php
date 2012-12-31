<?
	$page->Title = 'Home';
	$page->BodyId = 'index';
	
	$page->SetWrapperFile('_wrapper.view.php');
?>

<? if ( $page->BackLink ) { ?>
	<a href="<?=$page->BackLink?>">Back to Website</a>
<? } else { ?>
	<p>Not a lot here.</p>
<? } ?>
<?
	$page->Title = 'Single Edit';
	$page->BodyId = 'single';
?>

<? if ( $page->ErrorMessage != NULL ) { ?>
	<p id="error"><?=$page->ErrorMessage?></p>
<? } ?>
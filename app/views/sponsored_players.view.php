<?php
$page->BodyId = 'sponsored_players';
$page->BodyClass = '';
$page->Section = 'sponsored_players';
$page->Title = $page->Wordlets->GetWordlet("page_title");

$page->SetWrapperFile('_wrapper.view.php');
?>

<h2><?=$page->Wordlets->Get('page_title')?></h2>

<div class="cms">
	<?=$page->Wordlets->Get('body')?>
</div>
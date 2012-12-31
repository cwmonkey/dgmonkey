<?php
$page->BodyId = 'courses';
$page->BodyClass = '';
$page->Section = 'courses';
$page->Title = $page->Wordlets->GetWordlet("page_title");

$page->SetWrapperFile('_wrapper.view.php');
?>

<h2><?=$page->Wordlets->Get('page_title')?></h2>

<div class="cms">
	<?=$page->Wordlets->Get('body')?>
	<?=$page->Wordlets->Get('sucka')?>
</div>
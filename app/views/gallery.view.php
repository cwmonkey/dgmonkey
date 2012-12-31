<?php
$page->BodyId = 'gallery';
$page->BodyClass = '';
$page->Section = 'gallery';
$page->Title = $page->Wordlets->GetWordlet('page_title');

$page->SetWrapperFile('_wrapper.view.php');
?>

<h2><?=$page->Wordlets->Get('page_title')?></h2>

<? if ( $page->EditMode ): ?>
	<p>
		<?=$page->GalleryImageAddLink?>
	</p>
<? endif; ?>

<ul>
	<? foreach ( $page->GalleryImages as $gallery_item ): ?> 
		<li class="<?=$page->EditMode?' gcms_list_item':''?>">
			<p class="image">
				<a href="<?=$gallery_item['imgpath']?>" title="<?=htmlspecialchars($gallery_item['title'])?>"><?=$gallery_item['imgtag']?></a>
			</p>
			<? if ($page->EditMode): ?>
				<p class="name">
					<a href="<?=$gallery_item['imgpath']?>"></a>
				</p>
				<p>Enabled: <?=$gallery_item['enabled']?'True':'False'?></p>
				<p>Date: <?=date('M jS, o', strtotime($gallery_item['created']))?></p>
				<p>
					<?=$gallery_item['edit_link']?>
				</p>
			<? endif; ?>
		</li>
	<? endforeach; ?>
</ul>
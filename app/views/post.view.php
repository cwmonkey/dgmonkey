<?php
$page->BodyId = 'post';
$page->BodyClass = '';
$page->Section = 'post';
$page->Title = $page->Wordlets->GetWordlet("page_title");

$page->SetWrapperFile('_wrapper.view.php');
?>

<? if ( $page->EditMode ): ?>
	<p>
		<?=$page->NewsAddLink?>
	</p>
<? endif; ?>

<? foreach ( $page->News as $news_item ): ?> 
	<div class="news_item">
		<div class="post<?=$page->EditMode?' gcms_list_item':''?>">
			<h2><?=$news_item['title']?> - <?=date('M jS, o', strtotime($news_item['posted']))?></h2>
			<div class="cms">
				<?
				// TODO: Move to model
				if ( $news_item['plaintext'] ) {
					$b = trim($news_item['body']);
	
					$b = str_replace("\n\n", '</p><p>', $b);
					$b = str_replace("\n", '<br/>', $b);
					$b = str_replace('&', '&amp;', $b);
					$b = preg_replace('/([^\s]+\@[^\s]+\.[^\s]+)/', '<a href="mailto:$1">$1</a>', $b);
					$b = '<p>' . $b . '</p>';
	
					$news_item['body'] = $b;
				}
				echo $news_item['body'];
				?>
			</div>
			<? if ($page->EditMode): ?>
				<p>Enabled: <?=$news_item['enabled']?'True':'False'?></p>
				<p>Home Page: <?=$news_item['homepage']?'True':'False'?></p>
				<p>Plain Text: <?=$news_item['plaintext']?'True':'False'?></p>
				<p>
					<?=$news_item['edit_link']?>
				</p>
			<? endif; ?>
		</div>
	</div>
<? endforeach; ?>

<? /*
<div class="pagination_wrapper">
	<? $page->RenderView('modules/pagination.view.php', array('pagination' => $page->Pagination)) ?>
</div>
*/ ?>
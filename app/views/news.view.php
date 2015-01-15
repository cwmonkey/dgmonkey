<?php
$page->BodyId = 'news';
$page->BodyClass = '';
$page->Section = 'news';
$page->Title = $page->Wordlets->GetWordlet("page_title");

$page->SetWrapperFile('_wrapper.view.php');
?>

<? if ( $page->EditMode ): ?>
	<p>
		<?=$page->NewsAddLink?>
	</p>
<? endif; ?>

<? /*
<? if ( count($page->UpcomingEvents) || $page->EditMode ): ?>
<div id="tour_bar">
	<? if ($page->EditMode): ?>
		<p>
			<?=$page->UpcomingEventsAddLink?>
		</p>
	<? endif; ?>

	<h2>Tournament Schedule</h2>
	<dl class="schedule">
		<? foreach ( $page->UpcomingEvents as $event_item ): ?> 
			<dt>
				<? if ( $event_item['month'] ): ?>
					<?=date('M', strtotime($event_item['scheduled']))?>
				<? else: ?>
					<?=date('M jS, o', strtotime($event_item['scheduled']))?>
				<? endif; ?>

				<? if ( $event_item['scheduleend'] ): ?>
					-
					<? if ( $event_item['month'] ): ?>
						<?=date('M', strtotime($event_item['scheduleend']))?>
					<? else: ?>
						<?=date('M jS, o', strtotime($event_item['scheduleend']))?>
					<? endif; ?>
				<? endif; ?>
			</dt>
			<dd>
				<? if ( $event_item['link'] ): ?>
					<p><a href="<?=$event_item['link']?>"><?=$event_item['title']?> - <?=$event_item['location']?></a></p>
				<? elseif ( $event_item['flyerimg'] ): ?>
					<p><a href="<?=$event_item['flyerimg']?>"><?=$event_item['title']?> - <?=$event_item['location']?></a></p>
				<? elseif ( $event_item['flyerpdf'] ): ?>
					<p><a href="<?=$event_item['flyerpdf']?>"><?=$event_item['title']?> - <?=$event_item['location']?></a></p>
				<? else: ?>
					<p><?=$event_item['title']?> - <?=$event_item['location']?></p>
				<? endif; ?>
	
				<? if ( $event_item['flyerimg'] ): ?>
					<p><a href="<?=$event_item['flyerimg']?>">Click here to see the flyer</a></p>
				<? endif; ?>
	
				<? if ( $event_item['flyerpdf'] ): ?>
					<p><a href="<?=$event_item['flyerpdf']?>">Click here to see the PDF flyer</a></p>
				<? endif; ?>
	
				<? if ( $event_item['signup'] ): ?>
					<p><a href="<?=$event_item['signup']?>">Click here to sign up!</a></p>
				<? endif; ?>
				<? if ($page->EditMode) { ?>
					<p>
						<?=$event_item['edit_link']?>
					</p>
				<? } ?>
			</dd>
		<? endforeach; ?>
	</dl>

	<? if ($page->EditMode): ?>
		<p>
			<?=$page->UpcomingEventsAddLink?>
		</p>
	<? endif; ?>
</div>
<? endif; ?>
*/ ?>

<? foreach ( $page->News as $news_item ): ?> 
	<div class="news_item">
		<div class="post<?=$page->EditMode?' gcms_list_item':''?>">
			<h2><a href="/post/<?=$news_item['id']?>"><?=$news_item['title']?> - <?=date('M jS, o', strtotime($news_item['posted']))?></a></h2>
			<? if ( intval(date('Y')) - intval($news_item['posted']) < 2 ): ?>
				<div class="cms">
					<?
					// TODO: Move to model
					if ( $news_item['plaintext'] ) {
						$b = trim($news_item['body']);
		
						$b = str_replace("\302\240", ' ', $b);
						$b = preg_replace('/([\s])(http:\/\/[^\s]+)/', '$1<a href="$2" target="_blank">$2</a>', $b);
						$b = str_replace("\n\n", '</p><p>', $b);
						$b = str_replace("\n", '<br/>', $b);
						$b = str_replace('&', '&amp;', $b);
						$b = preg_replace('/([^\s]+\@[^\s]+\.[^\s]+)/', '<a href="mailto:$1">$1</a>', $b);
						$b = preg_replace('/([\s])([a-zA-Z0-9\-\.]+\.com[a-zA-Z0-9\-\.\/]*)/', '$1<a href="http://$2" target="_blank">$2</a>', $b);
						$b = '<p>' . $b . '</p>';
		
						$news_item['body'] = $b;
					}
					echo $news_item['body'];
					?>
				</div>
			<? endif ?>
			<? if ($page->EditMode): ?>
				<p>Enabled: <?=$news_item['enabled']?'True':'False'?>z</p>
				<p>Home Page: <?=$news_item['homepage']?'True':'False'?></p>
				<p>Plain Text: <?=$news_item['plaintext']?'True':'False'?></p>
				<p>
					<?=$news_item['edit_link']?>
				</p>
			<? endif ?>
		</div>
	</div>
<? endforeach ?>

<? /*
<div class="pagination_wrapper">
	<? $page->RenderView('modules/pagination.view.php', array('pagination' => $page->Pagination)) ?>
</div>
*/ ?>
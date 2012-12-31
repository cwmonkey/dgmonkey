<?php
$page->BodyId = 'tour';
$page->BodyClass = '';
$page->Section = 'tour_schedule';
$page->Title = $page->Wordlets->GetWordlet("page_title");

$page->SetWrapperFile('_wrapper.view.php');
?>

<h2><?=$page->Wordlets->Get('page_title')?></h2>

<div class="cms">
	<?=$page->Format($page->Wordlets->Get('body'))?>
</div>

<? /*=$page->Wordlets->Get('schedule')*/ ?>

<h3>Upcoming Events &amp; Event Information</h3>
<? if ($page->EditMode): ?>
	<p>
		<?=$page->UpcomingEventsAddLink?>
	</p>
<? endif; ?>

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

			<? if ( $event_item['directions'] ): ?>
				<p><a href="<?=$event_item['directions']?>">Directions</a></p>
			<? endif; ?>

			<? if ( $event_item['flyerimg'] ): ?>
				<p><a href="<?=$event_item['flyerimg']?>">Flyer</a></p>
			<? endif; ?>

			<? if ( $event_item['flyerpdf'] ): ?>
				<p><a href="<?=$event_item['flyerpdf']?>">PDF Flyer</a></p>
			<? endif; ?>

			<? if ( !$event_item['signupenabled'] ): ?>
				<p>Registration closed!</p>
			<? elseif ( $event_item['signup'] ): ?>
				<p><a href="<?=$event_item['signup']?>">Click here to register!</a></p>
			<? else: ?>
				<p>
					<a href="/registration/<?=$event_item['id']?>/<?=urlencode($event_item['title'])?>">Click here to register!</a>
				</p>
			<? endif; ?>
			<? if ($page->EditMode) { ?>
				<p>
					<?=$event_item['edit_link']?>
				</p>
			<? } ?>
        </dd>
    <? endforeach; ?>
</dl>

<div class="cms"></div>

<h3>Past Event Results</h3>

<dl class="schedule">
	<? foreach ( $page->PastEvents as $event_item ): ?> 
		<dt>
			<? if ( $event_item['month'] ): ?>
				<?=date('M', strtotime($event_item['scheduled']))?>
			<? else: ?>
				<?=date('M jS, o', strtotime($event_item['scheduled']))?>
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

			<? if ( $event_item['directions'] ): ?>
				<p><a href="<?=$event_item['directions']?>">Directions</a></p>
			<? endif; ?>

			<? if ( $event_item['flyerimg'] ): ?>
				<p><a href="<?=$event_item['flyerimg']?>">Flyer</a></p>
			<? endif; ?>

			<? if ( $event_item['flyerpdf'] ): ?>
				<p><a href="<?=$event_item['flyerpdf']?>">PDF flyer</a></p>
			<? endif; ?>

			<? if ( $event_item['results'] ): ?>
				<p><a href="<?=$event_item['results']?>">Results</a></p>
			<? endif; ?>
			<? if ($page->EditMode) { ?>
				<p>
					<?=$event_item['edit_link']?>
				</p>
			<? } ?>
        </dd>
    <? endforeach; ?>
</dl>

<div class="clear"></div>
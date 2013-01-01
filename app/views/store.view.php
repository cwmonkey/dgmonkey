<?php
$page->BodyId = 'store';
$page->BodyClass = '';
$page->Section = 'store';
$page->Title = $page->Wordlets->GetWordlet('page_title');

$page->SetWrapperFile('_wrapper.view.php');
?>

<h2><?=$page->Wordlets->Get('page_title')?></h2>

<!-- PayPal Logo -->
<div id="paypal_nav">
	<p id="paypal_logo">
		<a href="#" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');"><img  src="https://www.paypal.com/en_US/i/bnr/bnr_paymentsBy_150x40.gif" border="0" alt="PayPal Logo"></a>
	</p>
	<form name="_xclick" target="paypal" action="https://www.paypal.com/us/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_cart">
		<input type="hidden" name="business" value="discgolfer8@yahoo.com">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/view_cart_new.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
		<input type="hidden" name="display" value="1">
	</form>
</div>
<!-- /PayPal Logo -->

<? if ( $page->EditMode ): ?>
	<p>
		<?=$page->GalleryImageAddLink?>
	</p>
<? endif; ?>

<ul>
	<? foreach ( $page->GalleryImages as $gallery_item ): ?> 
		<li class="<?=$page->EditMode?' gcms_list_item':''?> storeitem cms">
			<form name="_xclick" target="paypal" action="https://www.paypal.com/us/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_cart">

				<input type="hidden" name="item_number" value="storeitem<?=$gallery_item['id']?>">
				<input type="hidden" name="shipping" value="<?=$gallery_item['shipping']?>">
				<input type="hidden" name="shipping2" value="<?=$gallery_item['shipping2']?>">
				<input type="hidden" name="handling" value="0.00">

				<input type="hidden" name="business" value="discgolfer8@yahoo.com">
				<input type="hidden" name="currency_code" value="USD">

				<p class="image">
					<a href="<?=$gallery_item['imgpath']?>" title="<?=htmlspecialchars($gallery_item['title'])?>"><?=$gallery_item['imgtag']?></a>
				</p>

				<div class="description cms">
					<p>
						<input type="hidden" name="item_name" value="<?=htmlspecialchars($gallery_item['title'])?>">
						<a href="<?=$gallery_item['url']?>"><?=htmlspecialchars($gallery_item['title'])?></a>
					</p>

					<? if ( $gallery_item['description'] ): ?>
						<p class="text"><?=$gallery_item['description']?></p>
					<? endif ?>
					<p class="cost">
						Cost: <input type="hidden" name="amount" value="<?=$gallery_item['price']?>"> $<?=$gallery_item['price']?>
					</p>

					<div class="add_to_cart">
						<p><input type="image" src="http://www.paypal.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"></p>
					</div>
				</div>

				<input type="hidden" name="add" value="1">
			</form>

			<? /* <h5><?=$gallery_item['title']?></h5>
			<p class="image">
				<a href="<?=$gallery_item['imgpath']?>" title="<?=htmlspecialchars($gallery_item['title'])?>"><?=$gallery_item['imgtag']?></a>
			</p>
			<? if ( $gallery_item['description'] ): ?>
				<p class="description"><?=$gallery_item['description']?></p>
			<? endif ?>
			<p class="pice">Price: <?=$gallery_item['price']?></p>
			<div class="purchase">
				<?=$gallery_item['paypal']?>
			</div>*/ ?>

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
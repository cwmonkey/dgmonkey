<?php
$page->BodyId = 'registration';
$page->BodyClass = '';
$page->Section = 'registration';
$page->Title = $page->Wordlets->GetWordlet("page_title");

$page->SetWrapperFile('_wrapper.view.php');

?>

<? function signup_paypal_form($page, $title, $cost, $pdga = false) { ?>
<form name="_xclick" target="paypal" action="https://www.paypal.com/us/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_cart">
	<input type="hidden" name="business" value="discgolfer8@yahoo.com">
	<input type="hidden" name="currency_code" value="USD">
	<h4>
		<input type="hidden" name="item_name" value="<?=$title?>"><?=$title?><br/>
		Cost: <input type="hidden" name="amount" value="<?=$cost?>"> $<?=$cost?>
	</h4>
	<input type="hidden" name="on0" value="Tournament Name">
	<input type="hidden" name="os0" value="<?=$page->Event['title']?>">
	<div class="add_to_cart">
		<? if ( $pdga ): ?>
			<p><input type="hidden" name="on1" value="Full Name">Full Name: <input type="text" name="os1" maxlength="200"></p>
			<p><input type="hidden" name="on2" value="PDGA#">PDGA#: <input type="text" name="os2" maxlength="200"></p>
			<p><input type="hidden" name="on3" value="Disc">Disc:
				<? if ( !$page->Event['discs'] ): ?>
					<input type="text" name="os3" maxlength="200">
				<? else: ?>
					<select name="os3">
						<option value="">-Select a disc-</option>
						<? foreach ( $page->Event['discs'] as $disc ): ?>
							<option><?=$disc?></option>
						<? endforeach ?>
					</select>
				<?  endif ?>
			</p>
			<p><input type="hidden" name="on4" value="Shirt Size">Shirt Size:
				<select name="os4">
					<option value="">-Select a size-</option>
					<option>S</option>
					<option>M</option>
					<option>L</option>
					<option>XL</option>
					<option>2XL</option>
					<option>3XL</option>
				</select>
			</p>
		<? else: ?>
			<p><input type="hidden" name="on1" value="Full Name">Full Name: <input type="text" name="os1" maxlength="200"></p>
			<p><input type="hidden" name="on2" value="Disc">Disc:
				<? if ( !$page->Event['discs'] ): ?>
					<input type="text" name="os2" maxlength="200">
				<?  else: ?>
					<select name="os2">
						<option value="">-Select a disc-</option>
						<? foreach ( $page->Event['discs'] as $disc ): ?>
							<option><?=$disc?></option>
						<? endforeach ?>
					</select>
				<? endif ?>
			</p>
			<p><input type="hidden" name="on3" value="Shirt Size">Shirt Size:
				<select name="os3">
					<option value="">-Select a size-</option>
					<option>S</option>
					<option>M</option>
					<option>L</option>
					<option>XL</option>
					<option>2XL</option>
					<option>3XL</option>
				</select>
			</p>
		<? endif ?>
		<p><input type="image" src="http://www.paypal.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"></p>
	</div>
	<input type="hidden" name="add" value="1">
</form>
<? } ?>

<? if ( $page->Event['signup'] ): ?>
	<p>Eek, we're full!</p>
<? else: ?>
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
	
	<? if ($page->EditMode) { ?>
		<p>
			<?=$page->Event['edit_link']?>
		</p>
	<? } ?>
	
	<h2><?=$page->Wordlets->Get('page_title')?> - <?=$page->Event['title']?></h2>
	
	<div class="cms">
		<?=$page->Format($page->Wordlets->Get('body'))?>
	
		<? if ( $page->Event['ispdga'] ): ?>
			<h3>Non-PDGA Members:</h3>
		
			<? signup_paypal_form($page, 'Professional Division (Non-PDGA member)', $page->Event['CostPro']); ?>
			<? signup_paypal_form($page, 'Advanced Division (Non-PDGA member)', $page->Event['CostAdv']); ?>
			<? signup_paypal_form($page, 'Intermediate Division (Non-PDGA member)', $page->Event['CostRec']); ?>
			<? signup_paypal_form($page, 'Recreational Division (Non-PDGA member)', $page->Event['CostRec']); ?>
			<? signup_paypal_form($page, 'Junior Division', $page->Event['CostJr']); ?>
		
			<h3>PDGA Members:</h3>
			
			<? signup_paypal_form($page, 'Professional Division (PDGA member)', $page->Event['CostPdgaPro'], true); ?>
			<? signup_paypal_form($page, 'Advanced Division (PDGA member)', $page->Event['CostPdgaAdv'], true); ?>
			<? signup_paypal_form($page, 'Intermediate Division (PDGA member)', $page->Event['CostPdgaRec'], true); ?>
			<? signup_paypal_form($page, 'Recreational Division (PDGA member)', $page->Event['CostPdgaRec'], true); ?>
		<? else: ?>
			<? signup_paypal_form($page, 'Professional Division', $page->Event['CostPdgaPro']); ?>
			<? signup_paypal_form($page, 'Advanced Division', $page->Event['CostPdgaAdv']); ?>
			<? signup_paypal_form($page, 'Intermediate Division', $page->Event['CostPdgaRec']); ?>
			<? signup_paypal_form($page, 'Recreational Division', $page->Event['CostPdgaRec']); ?>
			<? signup_paypal_form($page, 'Junior Division', $page->Event['CostJr']); ?>
		<? endif ?>
	</div>

<? endif ?>

<? if ($page->EditMode) { ?>
	<p>
		<?=$page->Event['edit_link']?>
	</p>
<? } ?>
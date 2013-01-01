<!doctype html>
<!--[if lt IE 7]> <html lang="en-us" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html lang="en-us" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html lang="en-us" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en-us" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<title><?=$page->RootTitle?><?=( $page->Title ) ? ' - ' . $page->Title : ''?></title>

	<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<script>window.html5 || document.write('<? $view->RenderJsFiles('lib/html5.js', true, false) ?>')</script>
	<![endif]-->

	<!--[if !IE 6]><!-->
	<? $view->RenderCssFiles(array(
		'reset.css',
		// 'normalize_cms.css',
		'global.css'
	)) ?>
	<!--<![endif]-->

	<? if ( $page->EditMode || $page->CmsAccess ) { ?>
		<link rel="stylesheet" type="text/css" href="/css/gcms.css" />
		<link rel="stylesheet" type="text/css" href="/css/datetime.css" />
	<? } ?>

	<link rel="icon" type="image/png" href="/favicon.ico"/>
</head>
<body id="<?=$page->BodyId ?>" class="<?=$page->BodyClass?>">

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div id="main">
	<div id="main_w"><div id="main_e">
		<!-- GCMS -->
			<? if ( $page->CmsAccess ) { ?>
				<div id="gcms_bar">
					<ul>
						<li><a href="<?=$page->GcmsEditModeLink?>">Edit This Page</a></li>
						<li><a href="<?=$page->GcmsLink?>">LADE CMS</a></li>
						<li><a href="<?=$page->GcmsLogoutLink?>">Logout</a></li>
					</ul>
				</div>
			<? } elseif ( $page->EditMode ) { ?>
				<div id="gcms_bar">
					<ul>
						<li><a href="<?=$page->GcmsEditModeOffLink?>">Leave Edit Mode</a></li>
						<li><a href="<?=$page->GcmsLink?>">LADE CMS</a></li>
						<li><a href="<?=$page->GcmsLogoutLink?>">Logout</a></li>
					</ul>
					<div id="gcms_vars">
						<? foreach( $page->Wordlets->InvalidSections as $isection ) { ?>
							<p>
								<strong>Warning: The section '<?=$isection?>' does not exist.</strong>
								<span class="gcms_list_item">
									<a class="gcms_link" href="/lade/add?section=section&name=<?=$isection?>">Add '<?=$isection?>'</a>
								</span>
							</p>
						<? } ?>
						<p>
							Site Title: <?=$page->Wordlets->Get('root_title')?>
						</p>
						<p>
							Page Title: <?=$page->Wordlets->Get('page_title')?>
						</p>
					</div>
				</div>
			<? } ?>
		<!-- /GCMS -->
		<header id="header">
			<h1><a href="/"><img src="<?=$view->MediaUrl('/images/global_header.png')?>" width="727" height="128" alt="Disc Golf Monkey" /></a></h1>
			<nav id="nav"><div id="nav_w">
				<ul>
					<li id="home_nav"><a href="/"><?=$page->Wordlets->Get('nav_home')?></a></li>
					<li id="tour_nav"><a href="/tour_schedule"><?=$page->Wordlets->Get('nav_tour')?></a></li>
					<li id="contact_nav"><a href="/contact"><?=$page->Wordlets->Get('nav_contact')?></a></li>
					<li id="sponsored_nav"><a href="/sponsored_players"><?=$page->Wordlets->Get('nav_sponsored')?></a></li>
					<li id="courses_nav"><a href="/courses"><?=$page->Wordlets->Get('nav_courses')?></a></li>
					<li id="gallery_nav"><a href="/gallery"><?=$page->Wordlets->Get('nav_gallery')?></a></li>
					<li id="links_nav"><a href="/links"><?=$page->Wordlets->Get('nav_links')?></a></li>
				</ul>
			</div></nav>
			<? if ( $page->EditMode ): ?>
				<hr /><br />
				<a href="/">>home</a>
				| <a href="/tour_schedule">>tour</a>
				| <a href="/contact">>contact</a>
				| <a href="/sponsored_players">>sponsor</a>
				| <a href="/courses">>courses</a>
				| <a href="/gallery">>gallery</a>
				| <a href="/links">>links</a>
				| <a href="/storeou812">>store</a>
			<? endif ?>
		</header>
		<hr />
		<div id="content">
			<?=$content?>
		</div>
	</div>
	
	<div id="extras">
		<ul id="personal_sponsors">
			<li><a href="http://www.golfdisc.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_millennium_golf_disc.png')?>" alt="Millennium Golf Disc" width="190" height="38"></a></li>
			<li><a href="http://www.creatordesigns.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_creator_designs.png')?>" alt="Creator Designs" width="190" height="40"></a></li>
			<li><a href="http://www.innovadiscs.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_innovadiscs.png')?>" alt="Innova Discs" width="133" height="40"></a></li>
			<li><a href="http://www.gorilla-boy.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_gbds.png')?>" alt="Gorila Boy" width="94" height="40"></a></li>
			<li><a href="http://www.nnwainc.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_nnwa.png')?>" alt="NNWA, Inc." width="122" height="45"></a></li>
		</ul>

		<div id="ad">
			<h4>Monkey Traps - Disc golf baskets hand-made and custom colored</h4>
			<p>
				<a href="/baskets"><img src="<?=$view->MediaUrl('/images/home_ad_monkey_traps.png')?>" alt="Monkey Traps - Disc golf baskets hand-made and custom colored" width="240" height="564" /></a>
			</p>
		</div>
	</div>
	<hr />
	<footer id="footer">
		<div id="sponsors">
			<h4>Sponsors:</h4>
			<ul>
				<? /* <li><img src="/images/global_sponsors_coors_light.png" alt="Coors Light" width="94" height="68"></li>
				<li><img src="/images/global_sponsors_hooters.png" alt="Hooters" width="115" height="58"></li> */ ?>
				<li><a href="http://www.vibram.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_vibram.png')?>" alt="Vibram Disc Golf" width="67" height="40"></a></li>
				<li><a href="http://www.qtsign.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_quality_trim.png')?>" alt="Quality Trim &amp; Design" width="114" height="40"></a></li>
				<li><a href="http://discsunlimited.net"><img src="<?=$view->MediaUrl('/images/global_sponsors_discsunlimited.png')?>" alt="Discs Unlimited" width="105" height="40"></a></li>
				<li><a href="http://www.keenfootwear.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_keen.png')?>" alt="Keen" width="122" height="40"></a></li>
				<li><a href="http://www.mauijim.com/"><img src="<?=$view->MediaUrl('/images/global_sponsors_mauijim.png')?>" alt="Maui Jim" width="85" height="40"></a></li>
				<? //<li><a href="http://mrdiscgolf.com/"><img src="/images/global_sponsors_mrdiscgolf.png" alt="Mr. DiscGolf" width="85" height="102"></a></li> ?>
				<? /* <li><a href="http://gdstour.com/"><img src="/images/global_sponsors_gateway.png" alt="Gateway" width="125" height="40"></a></li> */ ?>
			</ul>
		</div>
		<p>&copy; <?=date('Y')?> <?=$page->Wordlets->Get('copyright_by')?></p>
	</footer>
	
	</div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<? $view->RenderJsFiles('lib/jquery-1.8.2.js', true) ?>')</script>

<? if ( $page->EditMode ): ?>
	<? $view->RenderJsFiles(array(
		'modal.jquery.js',
		'dropdown.jquery.js',

		'autosize.jquery.js',
		'datetime.jquery.js',
		'jquery.form.js',
		'gcms.js'
	)) ?>
<? elseif ( $page->CmsAccess ): ?>
	<? $view->RenderJsFiles(array(
		'modal.jquery.js',
		'dropdown.jquery.js',

		'global.js',

		'autosize.jquery.js',
		'datetime.jquery.js',
		'jquery.form.js',
		'gcms.js'
	)) ?>
<? else: ?>
	<? $view->RenderJsFiles(array(
		'modal.jquery.js',
		'dropdown.jquery.js',

		'global.js'
	)) ?>
<? endif ?>

<script type="text/javascript">
	// Google Analytics
    var _gaq=[
    	['_setAccount','UA-7244346-1'],
    	['_setDomainName','www.discgolfmonkey.com'],
    	['_trackPageview']
    ];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
</script>

</body>
</html>
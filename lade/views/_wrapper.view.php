<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<? if ( !$page->Ajax ) { ?>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
	<title>LADE CMS<?=( $page->Title ) ? ' - ' . $page->Title : ''?></title>
	<link type="text/css" rel="stylesheet" href="/css/lade.css" />
	<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="/js/lade.js"></script>
</head>
<? } ?>
<body id="<?=$page->BodyId?>" class="<?=$page->BodyClass?>">
	<? if ( !$page->Ajax ) { ?>
		<h1>LADE CMS</h1>
	<? } ?>

	<? if ( !$page->Ajax && $page->ShowNav ) { ?>
		<ul id="nav">
			<li><a href="/">Site</a></li>
			<li><a href="/lade/home">Home</a></li>
			<li><a href="/lade/logout">Logout</a></li>
			<li>
				<a href="#">Sections</a>
				<ul>
					<? foreach ( $page->NavLinks as $link ) { ?>
						<li>
							<a href="<?=$link->Href?>" class="<?=($link->Current)?'current ':''?>"><?=$link->Text?></a>
						</li>
					<? } ?>
				</ul>
			</li>
		</ul>
	<? } ?>

	<? if ( !$page->Ajax ) { ?>
		<div id="page_content">
	<? } ?>

	<?=$content ?>

	<? if ( !$page->Ajax ) { ?>
		</div>
	<? } ?>
</body>
</html>
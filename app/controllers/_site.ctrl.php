<?php

// requires M object

class _siteController {
	// Instantiated version of this object here
	public static $view;

	// View accessible variables/methods
	public $BodyId;
	public $BodyClass;
	public $Section;
	public $Title;
	public $PageHeading;
	public $ArchiveDisplay = 'genre';
	public $ArchiveType;
	public $ArchiveDname;
	public $ControllerFile;
	public $SelfLink = '';
	public $Url;
	public $MediaUrl;
	public $UpcomingEvents;
	public $UpcomingEventsAddLink;
	public $Sponsors;
	public $FeaturedSponsors;
	public $SponsorsAddLink;

	public static function SetViewModel($object) {
		self::$view = $object;
	}

	public static function SetControllerFile($controller_name) {
		if ( self::$view ) self::$view->Controller = $controller_name;
	}

	public $EditMode;
	public $GcmsEditModeOffLink;
	public $CmsAccess;
	public $GcmsEditModeLink;
	public $Wordlets;
	public $RootTitle;
	public $GcmsLogoutLink;
	public $GcmsLink;

	public static function Init($route) {
		$controller_class = $route['controller'] . M::Get('controller_append');
		$controller_class::InitializeSite($route);
		$controller_class::InitializePage($route);

		if ( isset($route['template']) ) $controller_class::SetViewFile($route['template'] . M::Get('view_file_append'));
		$controller_class::$view->RenderViewContent();
	}

	/* Initialize site */
	public static function InitializeSite($route) {
		session_start();

		$controller_class = $route['controller'] . M::Get('controller_append');
		self::SetViewModel(new $controller_class());

		$view = self::$view;
		$view->Url = M::Get('site_url', NULL, TRUE);
		$view->MediaUrl = M::Get('media_url', NULL, TRUE);

		//if ( self::$SetPreferencesEnabled ) self::_SetPreferences();
		//if ( self::$SetArchivesEnabled ) self::_SetArchives();

/*-- GCMS --*/
		$cookie_username = @$_SESSION["gcmsusername"];
		$cookie_password = @$_SESSION["gcmspassword"];

		if ( $cookie_username && $cookie_password /*$cookie_username != null && $cookie_password != null && Global.ValidUser(cookie_username.Value, cookie_password.Value, true)*/ ) {
			$view->GcmsLink = '/lade/home';
			if ( @$_GET["EditMode"] ) {
				$view->EditMode = true;
				//(Page as BasePage).EditMode = true;

				$view->GcmsEditModeOffLink = str_replace('EditMode=1', '', str_replace('&EditMode=1', '', str_replace('?EditMode=1', '', $_SERVER['REQUEST_URI'])));
				$view->GcmsLogoutLink = '/lade/logout?backlink=' . str_replace('EditMode=1', '', str_replace('&EditMode=1', '', str_replace('?EditMode=1', '', $_SERVER['REQUEST_URI'])));
			} else {
				$view->CmsAccess = true;
				$view->GcmsEditModeLink = $_SERVER['REQUEST_URI'];

				if ( strstr($view->GcmsEditModeLink, "?") > -1 ) {
					if ( $_SERVER['QUERY_STRING'] != "") $view->GcmsEditModeLink += "&";
					$view ->GcmsEditModeLink .= "EditMode=1";
				} else {
					$view->GcmsEditModeLink .= "?EditMode=1";
				}
				$view->GcmsLogoutLink = "/lade/logout?backlink=" . str_replace("?EditMode=1", "", str_replace("&EditMode=1", "", str_replace("EditMode=1", "", $_SERVER['REQUEST_URI'])));
			}
		} elseif ( @$_GET["EditMode"] ) {
			header("Location: /lade/login?backlink=" . urlencode($_SERVER['REQUEST_URI']));
		}

		if ( $view->EditMode ) {
			error_reporting(E_ALL);
		}

		$db_info = self::Lade();

		$view->Wordlets = new LadeWordlet($view->EditMode, $_SERVER['REQUEST_URI'], $db_info->Tables["ladedgm_wordlet"]->Columns["name"]->SimpleName);
		$view->Wordlets->SetConn($db_info->GetConn());
		$view->Wordlets->AddWordlets("site");
		$view->RootTitle = $view->Wordlets->GetWordlet('root_title');

// Update timezone for new server
$now = new DateTime();
$mins = $now->getOffset() / 60;

$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;

$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

$query = "SET time_zone='$offset';";

$db_info->_conn->GetResult($query);

		if ( isset($route['name']) ) $view->Wordlets->AddWordlets($route['name']);
/*-- /GCMS --*/

		self::SetSponsors();
	}

	public static function InitializePage($route) {}

	public static function SetSponsors() {
		if ( self::$view->EditMode ) {
			$sponsors = self::Lade()->GetList('sponsor', 'featured=0', 'ladedgm_sponsor.pos', 'ASC');
			$featured_sponsors = self::Lade()->GetList('sponsor', 'featured=1', 'ladedgm_sponsor.pos', 'ASC');
		} else {
			$sponsors = self::Lade()->GetList('sponsor', 'featured=0 AND enabled=1', 'ladedgm_sponsor.pos', 'ASC');
			$featured_sponsors = self::Lade()->GetList('sponsor', 'featured=1 AND enabled=1', 'ladedgm_sponsor.pos', 'ASC');
		}

		self::$view->Sponsors = $sponsors->Values;
		self::$view->FeaturedSponsors = $featured_sponsors->Values;
		self::$view->SponsorsAddLink = $sponsors->AddLink;
	}

	public static function SetUpcomingEvents() {
		if ( self::$view->EditMode ) {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled>=NOW()', 'ladedgm_event.scheduled', 'ASC');
		} else {
			$events = self::Lade()->GetList('event', 'ladedgm_event.scheduled>=NOW() && ladedgm_event.enabled=1', 'ladedgm_event.scheduled', 'ASC');
		}
		self::$view->UpcomingEvents = $events->Values;
		self::$view->UpcomingEventsAddLink = $events->AddLink;
	}

/*-- GCMS -- */
	public static $_lade;
	public static function Lade() {
		if ( self::$_lade ) return self::$_lade;

		include(M::Get('monkake_directory') . '/lade/classes/lade.classes.php');

		self::$_lade = new Lade(M::Get('DB_ADDR'), M::Get('DB_USER'), M::Get('DB_PASS'), M::Get('DB_NAME'));

		self::$_lade->AddTables(M::Get('ladedgm_tables'));
		self::$_lade->AddSections(M::Get('ladedgm_sections'));

		return self::$_lade;
	}

	/* Template members/methods */
	public $WrapperFile;
	public $ViewFile;
	public function SetWrapperFile($name) {
		$this->WrapperFile = self::GetViewFile($name);
	}

	public static function ResizeStoreImage($src, $alt = '', $parameters = '') {
		return self::ResizeImage(M::Get('docroot_directory') . $src, M::Get('docroot_directory') . '/upload/store/resized/', $alt, 115, 115, $parameters);
	}

	public static function ResizeImage($src, $resize_path, $alt = '', $width = '', $height = '', $parameters = '') {
		// TODO: Put the image path on the server in a config somewhere
		// TODO: Handle sub directories
		// TODO: Sniff for image type
		//$image_path = '/home/dgmonkey/public_html/';
		//$resize_path = M::Get('gallery_images_directory') . '/resized/';
		$compression = 75;
		$resize_url_path = '/upload/store/resized/';
		$width = intval($width);
		$height = intval($height);

		if ( !$src ) {
			return false;
		}

		// alt is added to the img tag even if it is null to prevent browsers from outputting
		// the image filename as default
		//if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height)) ) {
			if ($image_size = @getimagesize($src)) {
				$owidth = $image_size[0];
				$oheight = $image_size[1];

				if ( !$width && $height ) {
					$ratio = $height / $image_size[1];
					$width = intval($image_size[0] * $ratio);
				} elseif ( $width && !$height ) {
					$ratio = $width / $image_size[0];
					$height = intval($image_size[1] * $ratio);
				} elseif ( !$width && !$height ) {
					$width = $image_size[0];
					$height = $image_size[1];
				} else {
					$w_ratio = $width / $image_size[0];
					$h_ratio = $height / $image_size[1];
					$w_to_w = intval($w_ratio * $image_size[0]);
					$w_to_h = intval($w_ratio * $image_size[1]);
					$h_to_w = intval($h_ratio * $image_size[0]);
					$h_to_h = intval($h_ratio * $image_size[1]);
					if ( $w_to_w > $width || $w_to_h > $height ) {
						$width = $h_to_w;
						$height = $h_to_h;
					} else {
						$width = $w_to_w;
						$height = $w_to_h;
					}
				}

				if ( ($owidth != $width || $oheight != $height) &&  $image_size[2] == 2 ) {
					$rextension = substr($src, strrpos($src, '.'));
					$rfilename = basename($src, $rextension);
					$rsrc = $resize_path . $rfilename . '_' . $width . 'x' . $height . 'at' . $compression . $rextension;
					if ( !file_exists($rsrc) ) {
						$rimage = imagecreatetruecolor($width, $height);
						$oimage = imagecreatefromjpeg($src);
						imagecopyresampled($rimage, $oimage, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
						imagedestroy($oimage);
						imagejpeg($rimage, $rsrc, $compression);
						imagedestroy($rimage);
						chmod($rsrc, 0777);
					}
					$src = $rsrc;
					$src = $resize_url_path . $rfilename . '_' . $width . 'x' . $height . 'at' . $compression . $rextension;
				}
			} else {
				return false;
			}
		//}

		$image = '<img src="' . $src . '" alt="' . htmlspecialchars($alt) . '"';

		if ( $width && $height ) {
			$image .= ' width="' . $width . '" height="' . $height . '"';
		}
		
		if ( $parameters ) $image .= ' ' . $parameters;
		
		$image .= ' />';
		return $image;
	}

	public static function SetViewFile($name) {
		self::$view->ViewFile = self::GetViewFile($name);
	}

	public static function GetViewFile($name) {
		$view_file = M::Get('view_directory', NULL, TRUE) . $name;
		if ( !file_exists($view_file) ) return FALSE;
		return $view_file;
	}

	public static function RenderViewContent() {
		require_once(M::Get('class_directory', TRUE, TRUE) . 'Template.class.php');
		$template = new Template();
		if ( !$template->SetFileName(self::$view->ViewFile) ) M::Error('Could not render template:' . self::$view->ViewFile, TRUE);
		$template->SetPageContent(array('page' => self::$view, 'view' => self::$view));
		if ( self::$view->WrapperFile ) $template->SetWrapperFileName(self::$view->WrapperFile);
		$template->RenderOutput(array('page' => self::$view, 'view' => self::$view));
	}

	public function RenderView($file_name, $variables) {
		$template = new Template();
		$file_name = self::GetViewFile($file_name);
		if ( !$template->SetFileName($file_name) ) M::Error('Could not render template:' . $file_name);
		$template->RenderOutput($variables);
	}

	public function Format($b) {
		$b = trim($b);

		//$b = preg_replace('/([\s])(http:\/\/[^\s]+)/', '$1<a href="$2" target="_blank">$2</a>', $b);
		$b = str_replace("\r", '', $b);
		$b = str_replace("\n\n", '</p><p>', $b);
		$b = str_replace("\n", '<br/>', $b);
		//$b = str_replace('&', '&amp;', $b);
		//$b = preg_replace('/([^\s]+\@[^\s]+\.[^\s]+)/', '<a href="mailto:$1">$1</a>', $b);
		$b = '<p>' . $b . '</p>';

		return $b;
	}

	public static $ForceCacheUpdate = FALSE;
	public static $Cache;
	public static function EchoCache($cache_name) {
		// set up cache
		self::$Cache = M::GetObject('Cache', $cache_name, Monkavi::Get('cache_path', TRUE));

		$check_paths = array(
			M::Get('controller_path', NULL, TRUE) . Monkavi::Get('controller', TRUE) . '.controller.php',
			M::Get('template_path', NULL, TRUE) . $this->TemplateName . '.tpl.php',
			M::Get('template_path', NULL, TRUE) . $this->TemplateWrapper . '.tpl.php',
		);

		foreach ( $check_paths as $check_path ) {
			if ( filemtime($check_path) > $this->Cache->ModTime ) {
				$this->ForceCacheUpdate = TRUE;
				$this->Cache->SetServeCache(FALSE);
				return;
			}
		}

		return $this->Cache->EchoCache();
	}

	public function MediaUrl($path) {
		if ( !M::Get('use_cdn') ) return $path;
		$letters = str_split($path);
		$total = 0;
		$servers = array(
			'http://discgolfmonkey.com',
			/* 'http://dgmi.mysmilies.com',
			'http://dgmi.mysmilies.com',
			'http://dgmi.mysmilies.com',
			'http://dgmi.mysmilies.com', */
		);
		foreach ( $letters as $letter ) {
			$total += ord($letter) - 32;
		}
		$entry = $total % count($servers);

		return $servers[$entry] . trim($path);
	}

	public function RenderJsFiles($js_files, $add_slashes = false, $defer = true) {
		$slash = '';

		if ( $add_slashes ) $slash = '\\';
		if ( !is_array($js_files) ) $js_files = array($js_files);

		if ( !M::Get('minify_js') ) {
			$jsextra = ( M::Get('debug') ) ? '?' . time() : '';
			for ( $i=0; $i < count($js_files); $i++ ) {
				$file = $js_files[$i];
				echo '<script type="text/javascript" src="' . $this->MediaUrl('/js/' . $file) . $jsextra . '" ' . (($defer)?'defer="defer"':'') . '><' . $slash . '/script>';
			}
			return;
		}

		$output = '';
		$lastmod = 0;
		$files = array();

		foreach ( $js_files as $file ) {
			$path = M::Get('js_dir') . $file;
			if ( ($mtime = filemtime($path)) ) {
				if ( $lastmod < $mtime ) $lastmod = $mtime;
				$files[] = urlencode($file);
			}
		}

		if ( count($files) ) {
			$filename = M::Get('js_compressed_dir') . md5(implode(',', $files)) . '.js';

			if ( intval(@filemtime($filename)) < $lastmod ) {
				foreach ( $files as $file ) {
					$path = M::Get('js_dir') . urldecode($file);
					$output .= "\n" . file_get_contents($path);
				}

				$fp = fopen($filename, 'w+');
				$output = $this->GetCompressedJs($output);
				fwrite($fp, $output);
				fclose($fp);
				chmod($filename, 0777);
			}
			echo '<script type="text/javascript" src="' . $this->MediaUrl('/js/' . $lastmod . '/' . md5(implode(',', $files)) . '.js') . '" ' . (($defer)?'defer="defer"':'') . '><' . $slash . '/script>';
		}
	}

	public function GetCompressedJs($output) {
		$jsp = new JavaScriptPacker($output, 0);
		$output = $jsp->pack();
		return $output;
	}

	public function RenderCssFiles($css_files) {
		if ( !is_array($css_files) ) $css_files = array($css_files);

		if ( !M::Get('minify_css') ) {
			$cssextra = ( M::Get('debug') ) ? '?' . time() : '';
			foreach ( $css_files as $file ) {
				echo '<link rel="stylesheet" type="text/css" href="' . $this->MediaUrl('/css/' . $file . $cssextra) . '" />';
			}
			return;
		}

		$output = '';
		$lastmod = 0;
		$files = array();

		foreach ( $css_files as $file ) {
			$path = M::Get('css_dir') . $file;
			if ( ($mtime = filemtime($path)) ) {
				if ( $lastmod < $mtime ) $lastmod = $mtime;
				$files[] = $file;
			}
		}

		if ( count($files) ) {
			$filename = M::Get('css_compressed_dir') . md5(implode(',', $files)) . '.css';

			if ( intval(@filemtime($filename)) < $lastmod ) {
				foreach ( $files as $file ) {
					$path = M::Get('css_dir') . $file;
					$output .= "\n" . file_get_contents($path);
				}

				$fp = fopen($filename, 'w+');
				fwrite($fp, Minify_CSS_Compressor::process($output));
				fclose($fp);
				chmod($filename, 0777);
			}
			echo '<link rel="stylesheet" type="text/css" href="' . $this->MediaUrl('/css/' . $lastmod . '/' . md5(implode(',', $files)) . '.css') . '" />';
		}
	}
}

class Event {
	public $Title;
	public $Link;
	public $FlyerImage;
	public $FlyerPdf;
	public $Results;
	public $Scheduled;
	public $Month;

	public function __construct($params) {
		$this->Title = @$params['title'];
		$this->Link = @$params['link'];
		$this->FlyerImage = @$params['flyerimg'];
		$this->FlyerPdf = @$params['flyerpdf'];
		$this->Results = @$params['results'];
		$this->Scheduled = @$params['scheduled'];
		$this->Month = @$params['month'];
	}
}
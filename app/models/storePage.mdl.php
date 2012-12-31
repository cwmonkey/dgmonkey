<?php

class storePage extends _site {
	public $GalleryImages = array();
	public $GalleryImageAddLink;

	public static function SetWordlets() {
		self::$view->Wordlets->AddWordlets('store');
	}

	public static function SetStoreItems() {
		if ( self::$view->EditMode ) {
			$images = _site::Lade()->GetList('store_item', '', 'ladedgm_storeitem.created', 'DESC');
		} else {
			$images = _site::Lade()->GetList('store_item', 'ladedgm_storeitem.enabled=1 AND ladedgm_storeitem.created<NOW()', 'ladedgm_storeitem.created', 'DESC');
		}

		foreach ( $images->Values as $key => $val ) {
			$images->Values[$key]['imgtag'] = tep_image(M::Get('docroot_directory') . $val['imgpath'], M::Get('docroot_directory') . '/upload/store/resized/', '', 115, 115);
			//$images->Values[$key]['price'] = add_cents_to_decimal($images->Values[$key]['price'], 200);
		}

		self::$view->GalleryImages = $images->Values;
		self::$view->GalleryImageAddLink = $images->AddLink;
	}
}

function add_cents_to_decimal($dec, $cents) {
	list($ppd, $ppc) = preg_split('/\\./', $dec);
	$pp = intval($ppd) * 100 + intval($ppc) + $cents;
	return number_format($pp/100, 2);
}

function tep_image($src, $resize_path, $alt = '', $width = '', $height = '', $parameters = '') {
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

	if ( $alt ) {
		$image .= ' title=" ' . htmlspecialchars($alt) . ' "';
	}

	if ( $width && $height ) {
		$image .= ' width="' . $width . '" height="' . $height . '"';
	}
	
	if ( $parameters ) $image .= ' ' . $parameters;
	
	$image .= ' />';
	return $image;
}

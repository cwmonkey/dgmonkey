<?php

class galleryController extends _siteController {
	public $GalleryImages = array();
	public $GalleryImageAddLink;

	public static function InitializePage($route) {
		self::SetGalleryImages();
	}

	public static function SetGalleryImages() {
		if ( self::$view->EditMode ) {
			$images = self::Lade()->GetList('gimage', '', 'ladedgm_gimage.created', 'DESC');
		} else {
			$images = self::Lade()->GetList('gimage', 'ladedgm_gimage.enabled=1 AND ladedgm_gimage.created<NOW()', 'ladedgm_gimage.created', 'DESC');
		}

		foreach ( $images->Values as $key => $val ) {
			$images->Values[$key]['imgtag'] = tep_image(M::Get('docroot_directory') . $val['imgpath'], M::Get('docroot_directory') . '/upload/gallery/resized/', '', 100, 100);
		}

		self::$view->GalleryImages = $images->Values;
		self::$view->GalleryImageAddLink = $images->AddLink;
	}
}

function tep_image($src, $resize_path, $alt = '', $width = '', $height = '', $parameters = '') {
	// TODO: Put the image path on the server in a config somewhere
	// TODO: Handle sub directories
	// TODO: Sniff for image type
	//$image_path = '/home/dgmonkey/public_html/';
	//$resize_path = M::Get('gallery_images_directory') . '/resized/';
	$compression = 75;
	$resize_url_path = '/upload/gallery/resized/';
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

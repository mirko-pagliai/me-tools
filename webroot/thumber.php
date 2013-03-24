<?php

// --------------------------------------------------------------------------
// define paths
// --------------------------------------------------------------------------
define('PATH_TO_THUMBS', 'files/thumbs/');
define('PATH_TO_LOGS', '');

// --------------------------------------------------------------------------
// activate error handling
// --------------------------------------------------------------------------
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 0);
ini_set('error_log', 'thumber_errors.log');
ini_set('log_errors', 1);

function myErrorHandler($errno, $errstr, $errfile, $errline) {
	Thumber::error($errstr);
	return true;
}

set_error_handler('myErrorHandler');

// --------------------------------------------------------------------------
// set memory limit if necessary
// --------------------------------------------------------------------------
ini_set('memory_limit', '50M');

// if the above doesn’t work:
// add/ modify the .htaccess file (in the same directory as this file):
// php_value memory_limit 50M
// --------------------------------------------------------------------------
// instantiate the Thumber class
// --------------------------------------------------------------------------
$thumber = new Thumber();

/**
 * Thumber
 *
 * please drop me a note if you like it, have comments/suggestions/wishes,
 * found a bug, or just to say hello.
 *
 * @copyright	Copyright (c) 2008, 2009, 2010, 2011 Peter Chylewski
 *               released under the gnu license v3 <http://www.gnu.org/licenses/gpl.html>
 * @author	    Peter Chylewski <peter@boring.ch>
 * @version	    0.5.6
 *
 * history:
 *
 * 0.5.4
 * 	- much faster image output via fpasstru instead of a redirect
 *
 * 0.5.5
 * 	- parameters 'w' and 'h' - if both set - define a 'box' - the output of distorted images is no longer possible
 *   - substituted an '_' with an 'x' in the thumb filename that makes more sense,
 *     e.g. 'cross_red_10x10.png' instead of 'cross_red_10_10.png'
 *   - added alpha channel support for pngs and gifs
 *
 * 0.5.6
 * - cleaned up the code, improved comments
 * - force the creation of a new thumbnail if the creation date of the cached one is older
 *   than the orginal’s modification date
 * - better error handling
 *
 * to to:
 * - cache purging
 * - implement / finalize proper error handling
 * - auto detect presence of an alpha channel in the image
 *
 * nice to have (maybe)
 *
 * - 'hot linking' of original files (through CURL or so)
 */
class Thumber {
	private $pathToImage, $pathToThumb;
	private $imageType;
	private $imageWidth, $imageHeight;
	private $thumbArea;
	private $thumbWidth, $thumbHeight;

	function __construct() {
		$this->_logic();
	}

	private function _logic() {
		// --------------------------------------------------------------------------
		// what this program is supposed to do
		// --------------------------------------------------------------------------
		$this->pathToImage = isset($_GET['img']) ? base64_decode($_GET['img']) : '';
		if(!file_exists($this->pathToImage))
			trigger_error('input image not found  at ' . $this->pathToImage, E_USER_ERROR);

		$this->thumbArea = isset($_GET['a']) ? $_GET['a'] : null;
		$this->thumbWidth = isset($_GET['w']) ? $_GET['w'] : null;
		$this->thumbHeight = isset($_GET['h']) ? $_GET['h'] : null;

		$this->_gatherInfo();
		$this->_calculateThumbDimensions();
		$this->_serveThumb();
	}

	private function _gatherInfo() {
		// --------------------------------------------------------------------------
		// determine the file type and the dimensions of the original image
		// --------------------------------------------------------------------------
		// right now, only 'gif', 'jpg' and 'png' files work as input,
		// but future versions of the GD library might understand more formats
		$types = array(
			1 => 'gif',
			2 => 'jpg',
			3 => 'png',
			4 => 'swf',
			5 => 'psd',
			6 => 'bmp',
			7 => 'tiff(intel byte order)',
			8 => 'tiff(motorola byte order)',
			9 => 'jpc',
			10 => 'jp2',
			11 => 'jpx',
			12 => 'jb2',
			13 => 'swc',
			14 => 'iff',
			15 => 'wbmp',
			16 => 'xbm'
		);

		$info = getimagesize($this->pathToImage);
		$this->imageWidth = $info[0];
		$this->imageHeight = $info[1];
		$this->imageType = $types[$info[2]];
	}

	private function _calculateThumbDimensions() {
		if(isset($this->thumbArea)) {
			// --------------------------------------------------------------------------
			// if the 'a' (for area) parameter has been set, calculate the thumb
			// dimensions so that their product will approximate the required area
			// (given in square pixels)
			// --------------------------------------------------------------------------
			$imageArea = $this->imageWidth * $this->imageHeight;
			$sizeRatio = $this->thumbArea / $imageArea;

			$this->thumbWidth = ceil($this->thumbArea / $this->imageHeight);
			$this->thumbHeight = ceil($this->thumbArea / $this->imageWidth);
		}
		else if(isset($this->thumbWidth) && isset($this->thumbHeight)) {
			// --------------------------------------------------------------------------
			// if both the width and the height have been given, calculate a bounding box
			// --------------------------------------------------------------------------
			if($this->imageWidth < $this->imageHeight)
				$sizeRatio = $this->imageHeight / $this->thumbHeight;
			else
				$sizeRatio = $this->imageWidth / $this->thumbWidth;

			$this->thumbWidth = ceil($this->imageWidth / $sizeRatio);
			$this->thumbHeight = ceil($this->imageHeight / $sizeRatio);
		}
		else {
			// --------------------------------------------------------------------------
			// if the width has not been given, calculate it from the height
			// if the height has not been given, calculate it from the width
			// --------------------------------------------------------------------------
			if(!isset($this->thumbWidth)) {
				$sizeRatio = $this->imageHeight / $this->thumbHeight;
				$this->thumbWidth = ceil($this->imageWidth / $sizeRatio);
			}
			else if(!isset($this->thumbHeight)) {
				$sizeRatio = $this->imageWidth / $this->thumbWidth;
				$this->thumbHeight = ceil($this->imageHeight / $sizeRatio);
			}
		}

		// --------------------------------------------------------------------------
		// make sure the thumbnail isn’t bigger than the original image (disputable)
		// --------------------------------------------------------------------------
		if($this->thumbWidth > $this->imageWidth || $this->thumbHeight > $this->imageHeight) {
			$this->thumbWidth = $this->imageWidth;
			$this->thumbHeight = $this->imageHeight;
		}
		// --------------------------------------------------------------------------
		// now that we know the definitive dimensions of our thumbnail (as integers),
		// why not use those to label the file properly?
		// --------------------------------------------------------------------------
		$pathParts = pathinfo($this->pathToImage);

		$this->pathToThumb = PATH_TO_THUMBS
				. $pathParts['filename']
				. '_' . $this->thumbWidth
				. 'x' . $this->thumbHeight
				. '.' . $pathParts['extension'];
	}

	private function _serveThumb() {
		// --------------------------------------------------------------------------
		// if the thumbnail image already exists, serve it;
		// otherwise generate one
		// --------------------------------------------------------------------------
		#$this->_generateThumb(); return; // force the generation of a new thumbnail (for testing)
		if(file_exists($this->pathToThumb)) {
			#self::error(filemtime($this->pathToImage) . '->' . filemtime($this->pathToThumb));
			// force the creation of a new thumbnail if the modification date of the cached one is older than the orginal’s
			if(filemtime($this->pathToImage) > filemtime($this->pathToThumb)) {
				$this->_generateThumb();
				return;
			}

			#$pathToThumb = ltrim($this->pathToThumb);
			// open the file in binary mode
			$fp = fopen($this->pathToThumb, 'rb');

			// send the right headers
			header('Content-Type: image/' . ($this->imageType == 'jpg' ? 'jpeg' : $this->imageType));
			header('Content-Disposition: inline; filename=' . urlencode(basename($this->pathToThumb)) . '');
			header('Content-Length: ' . filesize($this->pathToThumb));

			// stream it through
			fpassthru($fp);
			fclose($fp);
			exit;
		}
		elseif(file_exists($this->pathToImage))
			$this->_generateThumb();
	}

	private function _generateThumb() {
		// --------------------------------------------------------------------------
		// create an image from the input image file
		// --------------------------------------------------------------------------
		switch($this->imageType) {
			case 'jpg':
				$image = @imagecreatefromjpeg($this->pathToImage);
				break;
			case 'gif':
				$image = @imagecreatefromgif($this->pathToImage);
				break;
			case 'png':
				$image = @imagecreatefrompng($this->pathToImage);
				break;
		}

		if($image === false) {
			trigger_error('image could not be created', 1024);
			print 'nöööööö';
			exit;
		}

		// --------------------------------------------------------------------------
		// create the thumbnail image and paste the original into it in its new
		// dimensions
		// --------------------------------------------------------------------------
		$thumbImage = @ImageCreateTrueColor($this->thumbWidth, $this->thumbHeight);

		if($this->imageType == 'png' || $this->imageType == 'gif')
			imagealphablending($thumbImage, false);

		// copy image and paste it into the thumb image
		ImageCopyResampled($thumbImage, $image, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $this->imageWidth, $this->imageHeight);

		if($this->imageType == 'png' || $this->imageType == 'gif') {
			ImageSaveAlpha($thumbImage, true);
			// we don’t sharpen thumbs that might contain alpha channels, because it produces nasty borders
			// to do: detect alpha channel in the original image
		}
		else {
			// --------------------------------------------------------------------------
			// sharpen it a little
			// --------------------------------------------------------------------------
			if(function_exists('imageconvolution')) {
				$sharpen = array(array(-1, -1, -1),
					array(-1, 34, -1),
					array(-1, -1, -1)
				);
				$divisor = array_sum(array_map('array_sum', $sharpen));
				imageconvolution($thumbImage, $sharpen, $divisor, 0);
			}
		}

		// --------------------------------------------------------------------------
		// spit it out
		// --------------------------------------------------------------------------
		switch($this->imageType) {
			case 'jpg':
				imagejpeg($thumbImage, $this->pathToThumb, 80);
				header('Content-type: image/jpeg');
				imagejpeg($thumbImage, NULL, 80);
				break;
			case 'gif':
				imagegif($thumbImage, $this->pathToThumb);
				header('Content-type: image/gif');
				imagegif($thumbImage, NULL);
				break;
			case 'png':
				imagepng($thumbImage, $this->pathToThumb);
				header('Content-type: image/png');
				imagepng($thumbImage, NULL);
				break;
		}

		imagedestroy($image);
		imagedestroy($thumbImage);

		exit;
	}

	public static function error($msg) {
		ob_end_clean();
		#header('HTTP/1.1 500 Internal Server Error');
		echo $msg;
		die();
	}
}
?>
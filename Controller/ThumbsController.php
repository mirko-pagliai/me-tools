<?php
/**
 * ThumbsController
 *
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Controller
 */
App::uses('MeToolsAppController', 'MeTools.Controller');
App::uses('Folder', 'Utility');

/**
 * Creates and displays image thumbs.
 * 
 * The `thumb()` action takes the maximum width and/or the maximum height as query string (`w` and `h` parameters).
 * It can also create square thumbs. In this case, it's sufficient to indicate square side in the query string (`s` parameter).
 * With square thumbs, the initial image will be cut off if it is rectangular.
 * 
 * `ThumbsController` doesn't just show the thumb, but creates a real thumb in the temporary directory (`app/tmp/thumbs`), 
 * which can be used later when the same thumbs will be required (as if it were a cache).
 * 
 * To display a thumb or get the thumb url, you hav to use `thumb()` or `thumbUrl()` methods provided by the `MeHtml` helper.
 * The `thumb()` method, using this controller, creates the thumb and returns the HTML code to show it.
 * The `thumbUrl()` method creates the thumbs and returns its url.
 * @see MeHtmlHelper::thumb(), MeHtmlHelper::thumbUrl()
 */
class ThumbsController extends MeToolsAppController {
	/**
	 * Current image path
	 * @var string Image path
	 */
	protected $file = FALSE;
	
	/**
	 * Info about the current image.
	 * It will contain the initial, the max and the final sizes (width and height) and the the mimetype.
	 * @var array Array of info
	 */
	protected $info = array();
	
	/**
	 * Thumb path.
	 * @var string Thumb path
	 */
	protected $thumb = FALSE;
	
	/**
	 * Creates the thumb.
	 * @uses file to get the current image path
	 * @uses info to get info about the current image
	 * @uses thumb to get the thumb path
	 * @throws NotFoundException
	 */
	protected function __createThumb() {		
		switch($this->info['mime']) {
			case 'image/jpeg':
				$src = imagecreatefromjpeg($this->file);
				break;
			case 'image/png':
				$src = imagecreatefrompng($this->file);
				break;
			case 'image/gif':
				$src = imagecreatefromgif($this->file);
				break;
			default:
				throw new InternalErrorException(__d('me_tools', 'Invalid mimetype'));
				break;
		}
		
		$thumb = imagecreatetruecolor($this->info['finalWidth'], $this->info['finalHeight']);
		
		//Transparency for png images
		if($this->info['mime']==='image/png') {
			imagealphablending($thumb, FALSE);
			imagesavealpha($thumb, TRUE);
		}
		
		imagecopyresampled($thumb, $src, 0, 0, $this->info['x'], $this->info['y'], $this->info['finalWidth'], $this->info['finalHeight'], $this->info['width'], $this->info['height']); 

		$target = is_writable(dirname($this->thumb)) ? $this->thumb : NULL;
		
		switch($this->info['mime']) {
			case 'image/jpeg':
				imagejpeg($thumb, $target, 100);
				break;
			case 'image/png':
				imagepng($thumb, $target, 0);
				break;
			case 'image/gif':
				imagegif($thumb, $target);
				break;
			default:
				throw new InternalErrorException(__d('me_tools', 'Invalid mimetype'));
				break;
		}
		
		imagedestroy($src);
		imagedestroy($thumb);
	}
	
	/**
	 * Sets info (max sizes, final sizes, mimetype) about the current image and the thumb path.
	 * @uses file to get the current image path
	 * @uses info to set info about the current image
	 * @uses thumb to set the thumb path
	 */
	protected function __setInfo() {
		$imageSize = getimagesize($this->file);
		
		$this->info = array(
			'mime'			=> $imageSize['mime'],
			'filename'		=> pathinfo($this->file, PATHINFO_FILENAME),
			'extension'		=> pathinfo($this->file, PATHINFO_EXTENSION),
			'width'			=> $imageSize[0],
			'height'		=> $imageSize[1],
			'maxWidth'		=> (int)$this->request->query('w'),
			'maxHeight'		=> (int)$this->request->query('h'),
			'side'			=> (int)$this->request->query('s')
		);
		
		//Sets empty values. These values will be set later, depending on the size and on the request
		$this->info['x'] = $this->info['y'] = $this->info['finalWidth'] = $this->info['finalHeight'] = $finalWidth = $finalHeight = 0;
		
		//If the side (for square thumbs) is defined
		if($finalWidth = $finalHeight = $this->info['side']) {
			if($this->info['width'] < $this->info['height'])
				$this->info['y'] = floor(($this->info['height'] - ($this->info['height'] = $this->info['width'])) / 2);
			else
				$this->info['x'] = floor(($this->info['width'] - ($this->info['width'] = $this->info['height'])) / 2);
		}
		//Else, if the maximum width and the maximum height are defined
		elseif($this->info['maxWidth'] && $this->info['maxHeight']) {
			//Tries to get final sizes from the width
			$finalWidth = $this->info['width'] * $this->info['maxHeight'] / $this->info['height'];
			
			//If the final width is greater than the maximum width, get final sizes from the final height
			if($finalWidth > $this->info['maxWidth'])
				$finalHeight = $this->info['height'] * ($finalWidth = $this->info['maxWidth']) / $this->info['width'];
			//Else, the final height is the maximum height
			else
				$finalHeight = $this->info['maxHeight'];
		}
		//Else, if only the maximum width is defined
		elseif($this->info['maxWidth'])
			$finalHeight = $this->info['height'] * ($finalWidth = $this->info['maxWidth']) / $this->info['width'];
		//Else, if only the maximum height is defined
		elseif($this->info['maxHeight']) 
			$finalWidth = $this->info['width'] * ($finalHeight = $this->info['maxHeight']) / $this->info['height'];
		
		//If final sizes are defined and are lowen than initial sizes
		if($finalWidth && $finalHeight && ($finalWidth < $this->info['width'] || $finalHeight < $this->info['height'])) {
			$this->info['finalWidth'] = (int)floor($finalWidth);
			$this->info['finalHeight'] = (int)floor($finalHeight);
			$this->thumb = TMP.'thumbs'.DS.md5($this->file).'_'.$this->info['finalWidth'].'x'.$this->info['finalHeight'].'.'.$this->info['extension'];
		}
	}


	/**
	 * Shows (and creates) a thumb for an image, if it's necessary to create a thumb.
	 * 
	 * Please, refer to the class description for more information.
	 * It's convenient to use `thumb()` or `thumbUrl()` method provided by the `MeHtml` helper.
	 * @param string $file Encoded file path
	 * @throws InternalErrorException
	 * @throws NotFoundException
	 * @see MeHtmlHelper::thumb(), MeHtmlHelper::thumbUrl()
	 * @uses file to set the current image path
	 * @uses thumb to get the thumb path
	 * @uses __createThumb() to create the thumb
	 * @uses __setInfo() to set info about the current image
	 */
	public function thumb($file = FALSE) {
		if(!function_exists('gd_info'))
			throw new InternalErrorException(__d('me_tools', 'GD libraries are missing'));
		
		//Checks if a path has been passed
		if(empty($file))
			throw new InternalErrorException(__d('me_tools', 'The file has not been specified'));
		
		//Decodes the path
		$this->file = urldecode(base64_decode($file));
		
		//If the path is relative, then is relative to the webroot
		$this->file = !Folder::isAbsolute($this->file) ? WWW_ROOT.$this->file : $this->file;
		
		if(!file_exists($this->file))
			throw new NotFoundException(__d('me_tools', 'This image does not exist'));
				
		//Sets info about the current image
		$this->__setInfo();
		
		//If we need a thumb and it doesn't exist
		if($this->thumb && !file_exists($this->thumb)) {
			//Creates the thumb dir, if this doesn't exist
			if(!file_exists($dir = dirname($this->thumb))) {
				$folder = new Folder();
				if(!@$folder->create($dir))
					throw new InternalErrorException(__d('me_tools', 'The thumb directory cannot be created'));
			}

			//Creates the thumb
			$this->__createThumb();
		}
		
		header("Content-type: ".$this->info['mime']);
		readfile(empty($this->thumb) ? $this->file : $this->thumb);
		$this->autoRender = FALSE;
		
		exit;
	}
}
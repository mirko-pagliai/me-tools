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
 * Creates and displays thumbnails from images.
 * 
 * Images must be located in the webroot (`app/webroot`).
 * 
 * The `thumb()` action takes the image path (relative to the webroot) as a list of arguments. For example:
 * <pre>/me_tools/thumbs/thumb/img/my_pic.jpg</pre>
 * will refer to `app/webroot/img/my_pic.jpg`.
 * 
 * It takes the maximum width and/or the maximum height as query string (`w` and `h` parameters). 
 * For example:
 * <pre>/me_tools/thumbs/thumb/img/my_pic.jpg?w=150</pre>
 * This will create a thumb with a maximum width of 150px.
 * <pre>/me_tools/thumbs/thumb/img/my_pic.jpg?w=150&h=100</pre>
 * This will create a thumb with a maximum width of 150px and a maximux height of 100px.
 * 
 * You can also create square thumbs. In this case, it's sufficient to indicate square side in the query string (`s` parameter).
 * For example:
 * <pre>/me_tools/thumbs/thumb/img/my_pic.jpg?s=150</pre>
 * This will create a square thumb with a side (width and height) of 150px.
 * 
 * With square thumbs, the initial image will be cut off if it is rectangular.
 * 
 * `ThumbsController` doesn't just show the thumb, but creates a real thumb in the filesystem, which can be used later 
 * when the same thumbs will be required (as if it were a cache). If the directory in which the image is located 
 * is writable, it creates the thumb inside the sub-directory `.thumbs` (which is also created, if not already existing). 
 * 
 * For example:
 * <pre>/me_tools/thumbs/thumb/img/my_pic.jpg?w=150</pre>
 * If the thumb will be 150x100, this will create the file `app/webroot/img/.thumbs/my_pic_150x100.jpg`.
 * 
 * If you use MeTools routes, then it will also be possible to use the simplified url:
 * <pre>/thumb/img/my_pic.jpg?w=150</pre>
 * 
 * In any case, it's better to use `thumb()` and `thumbUrl()` methods provided by the `MeHtml` helper.
 * The `thumb()` method, using this controller, creates the thumb and returns the HTML code to show it.
 * The `thumbUrl()` method creates the thumbs and returns its url.
 * @see MeHtmlHelper::thumb(), MeHtmlHelper::thumbUrl()
 */
class ThumbsController extends MeToolsAppController {
	/**
	 * Path of the initial image.
	 * @var string Image path
	 */
	protected $file = NULL;
	
	/**
	 * Informations about the image.
	 * It will contain the initial, the max and the final sizes (width and height) and the the mimetype.
	 * @var array Array of informations
	 */
	protected $info = array();
	
	/**
	 * Path of the final thumb, if a thumb was created.
	 * @var string Thumb path
	 */
	protected $thumb = NULL;
	
	/**
	 * Creates the thumb.
	 * @throws NotFoundException
	 */
	protected function __createThumb() {
		//Tries to create the directory for thumbs
		if(!fileExistsinPath($dir = dirname($this->thumb)) && is_writable(dirname($this->file)))
			new Folder($dir, true, 0755);
		
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
				throw new NotFoundException(__d('me_tools', 'Invalid mimetype'));
				break;
		}
		
		$thumb = imagecreatetruecolor($this->info['finalWidth'], $this->info['finalHeight']);
		
		//Transparency for png images
		if($this->info['mime']==='image/png') {
			imagealphablending($thumb, false);
			imagesavealpha($thumb, true);
		}
		
		imagecopyresampled($thumb, $src, 0, 0, $this->info['x'], $this->info['y'], $this->info['finalWidth'], $this->info['finalHeight'], $this->info['width'], $this->info['height']); 

		$target = is_writable(dirname($this->thumb)) ? $this->thumb : null;
		
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
				throw new NotFoundException(__d('me_tools', 'Invalid mimetype'));
				break;
		}
		
		imagedestroy($src);
		imagedestroy($thumb);
	}
	
	/**
	 * Sets informations about the current image (max sizes, final sizes, mimetype)
	 * @uses info to set informations about the current image
	 * @uses thumb for the path of the final thumb
	 */
	protected function __setInfo() {
		$info = getimagesize($this->file);
		
		$this->info = array(
			'mime'			=> $info['mime'],
			'extension'		=> pathinfo($this->file, PATHINFO_EXTENSION),
			'width'			=> $info[0],
			'height'		=> $info[1],
			'x'				=> 0,
			'y'				=> 0,
			'maxWidth'		=> (int)$this->request->query('w'),
			'maxHeight'		=> (int)$this->request->query('h'),
			'side'			=> (int)$this->request->query('s'),
			'finalWidth'	=> 0,
			'finalHeight'	=> 0
		);
		
		$finalWidth = $finalHeight = 0;
		
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
			$this->thumb = dirname($this->file).DS.'.thumbs'.DS.pathinfo($this->file, PATHINFO_FILENAME).'_'.$this->info['finalWidth'].'x'.$this->info['finalHeight'].'.'.$this->info['extension'];
		}
	}
	
	/**
	 * Shows (and creates) a thumb for an image, if it's necessary to create a thumb.
	 * 
	 * Please, refer to the class description for more information.
	 * It's convenient to use the `thumb()` method provided by the `MeHtml` helper.
	 * @throws NotFoundException
	 * @uses file for the path of the initial image
	 * @uses __createThumb() to create the thumb
	 * @uses __setInfo() to set informations about the current image
	 */
	public function thumb() {
		$this->autoRender = false;
		
		$this->file = WWW_ROOT.implode('/', func_get_args());
		
		if(!fileExistsInPath($this->file))
			throw new NotFoundException(__d('me_tools', 'Invalid image'));
		
		$this->__setInfo();
		
		header("Content-type: ".$this->info['mime']);
		
		if($this->info['finalWidth'] && $this->info['finalHeight'] && function_exists('gd_info')) {
			if(!fileExistsinPath($this->thumb))
				$this->__createThumb();
			readfile($this->thumb);
		}
		else
			readfile($this->file);
	}
}
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Controller
 */

App::uses('Controller', 'Controller');
App::uses('Folder', 'Utility');
App::uses('System', 'MeTools.Utility');

/**
 * Creates and displays thumbnails for image and video files.
 * 
 * The `thumb()` action takes the maximum width and/or the maximum height as query string parameters (`w` and `h` parameters).
 * It can also create square thumbs. In this case, it's sufficient to indicate the maximum side in the query string (`s` parameter).
 * With square thumbs, the initial image will be cut off if it is rectangular.
 * 
 * You can set the maximum height only for image files.
 * Instead, the video thumbnails can be created using the maximum width or maximum side.
 * 
 * `ThumbsController` doesn't just show thumbnails, but creates real thumbnails in the temporary directory (`app/tmp/thumbs`), 
 * which can be used later when the same thumbs will be required (as if it were a cache).
 * 
 * To display a thumbnail or get the url for a thumbnail, you have to use `thumb()` or `thumbUrl()` methods provided by the `MeHtml` helper.
 * The `thumb()` method, using this controller, creates the thumbnail and returns the HTML code to show it.
 * The `thumbUrl()` method creates the thumbs and returns its url.
 * @see MeHtmlHelper::thumb(), MeHtmlHelper::thumbUrl()
 */
class ThumbsController extends Controller {
	/**
	 * Max height of the thumb.
	 * It will be set by `beforeFilter()`.
	 * @var int
	 */
	protected $maxHeight;
	
	/**
	 * Max side of the thumb.
	 * It will be set by `beforeFilter()`.
	 * @var int
	 */
	protected $maxSide;
	
	/**
	 * Max width of the thumb.
	 * It will be set by `beforeFilter()`.
	 * @var int
	 */
	protected $maxWidth;
	
	/**
	 * File object
	 * @var object
	 */
	protected $object;
	
	/**
	 * Thumb path
	 * @var string 
	 */
	protected $thumb;

	/**
	 * Creates a thumbnail of an image.
	 * @return void
	 * @throws InternalErrorException
	 * @uses maxHeight
	 * @uses maxSide
	 * @uses maxWidth
	 * @uses thumb
	 */
	protected function _imageThumb() {
		//Checks for Imagick
        if(!extension_loaded('imagick'))
            throw new InternalErrorException(__d('me_tools', 'The %s library is missing', 'Imagick'));
				
		//Creates the Imagick object
		$imagick = new Imagick($this->object->path);
		
		//If the max side is defined (then has been requested a square thumb)
		if($this->maxSide) {
			//If the maximum side is larger than the width and height, then the maximum side is equal to the smallest size
			if($this->maxSide > $imagick->getImageWidth() || $this->maxSide > $imagick->getImageHeight())
				$this->maxSide = $imagick->getImageWidth() > $imagick->getImageHeight() ? $imagick->getImageHeight() : $imagick->getImageWidth();
						
			//Creates the thumbnail
			$imagick->cropThumbnailImage($finalWidth = $finalHeight = $this->maxSide, $this->maxSide);
		}
		//Else, if the maximum width and the maximum height are defined
		elseif($this->maxWidth && $this->maxHeight) {
            //Tries to get the final sizes from the width
            $finalWidth = floor($imagick->getImageWidth() * $this->maxHeight / $imagick->getImageHeight());

            //If the final width is greater than the maximum width, it gets the final sizes from the final height
            if($finalWidth > $this->maxWidth)
                $finalHeight = floor($imagick->getImageHeight() * ($finalWidth = $this->maxWidth) / $imagick->getImageWidth());
            //Else, the final height is the maximum height
            else
                $finalHeight = $this->maxHeight;
		
			//Creates the thumbnail
			$imagick->thumbnailImage($finalWidth, $finalHeight);
		}
        //Else, if only the maximum width is defined
		elseif($this->maxWidth) {
			//If the maximum width is greater than the actual width, then it's not necessary
			//to create a thumbnail and the thumbnail will be the original image
			if(($finalWidth = $this->maxWidth) >= $imagick->getImageWidth()) {
				$this->thumb = $this->object->path;
				return;
			}
			
			//Gets the final height and creates the thumbnail
			$finalHeight = floor($finalWidth * $imagick->getImageHeight() / $imagick->getImageWidth());
			$imagick->thumbnailImage($finalWidth, 0);
			
		}
        //Else, if only the maximum height is defined
		else {
			//If the maximum height is greater than the actual height, then it's not necessary
			//to create a thumbnail and the thumbnail will be the original image
			if(($finalHeight = $this->maxHeight) >= $imagick->getImageHeight()) {
				$this->thumb = $this->object->path;
				return;
			}
				
			//Gets the final width and creates the thumbnail
			$finalWidth = floor($finalHeight * $imagick->getImageWidth() / $imagick->getImageHeight());
			$imagick->thumbnailImage(0, $finalHeight);
		}
		
		//Writes the image to the output directory and destroys the Imagick object
		$imagick->writeImage($this->thumb);
		$imagick->destroy();
		
		//Checks if the thumbnail has been created
		if(!is_readable($this->thumb))
            throw new InternalErrorException(__d('me_tools', 'The thumbnail %s has not been created', $this->thumb));
	}
	
	/**
	 * Creates a thumbnail of a video.
	 * @throws InternalErrorException
	 * @uses maxSide
	 * @uses maxWidth
	 * @uses thumb
	 */
	protected function _videoThumb() {
		//Checks for ffmpegthumbnailer
		if(!System::which('ffmpegthumbnailer'))
            throw new InternalErrorException(__d('me_tools', '%s is not avalaible', 'ffmpegthumbnailer'));
		
		//Creates the thumbnail
		shell_exec(sprintf('ffmpegthumbnailer -s %s -q 10 -f -i \'%s\' -o \'%s\'', empty($this->maxSide) ? $this->maxWidth : $this->maxSide, $this->object>path, $this->thumb));
		
		//Checks if the thumbnail has been created
		if(!is_readable($this->thumb))
            throw new InternalErrorException(__d('me_tools', 'The thumbnail %s has not been created', $this->thumb));
		
		//If the max side is defined (then has been requested a square thumb)
		if($this->maxSide) {
			//Creates the Imagick object
			$imagick = new Imagick($this->thumb);
			
			//If the height of the thumbnail is larger than the width
			if($imagick->getImageHeight() < $imagick->getImageWidth()) {
				//Adds a border
				$imagick->borderImage('#000000', 0, ($imagick->getImageWidth() - $imagick->getImageHeight()) / 2);
				
				//Writes the image to output directory and destroys the Imagick object
				$imagick->writeImage($this->thumb);
				$imagick->destroy();
			}
		}
	}
	
	/**
	 * Called before the controller action. 
	 * It's used to perform logic before each controller action.
	 * @uses maxHeight
	 * @uses maxSide
	 * @uses maxWidth
	 */
	public function beforeFilter() {
		//Sets the maximum sizes
		$this->maxWidth = (int) $this->request->query('w');
        $this->maxHeight = (int) $this->request->query('h');
        $this->maxSide = (int) $this->request->query('s');
	}
	
	/**
	 * Creates and shows thumbnails for images and video.
     * 
     * Please, refer to the class description for more information.
     * It's convenient to use `thumb()` or `thumbUrl()` method provided by the `MeHtml` helper.
	 * @param string $file File path, encoded by `base64_encode()`
	 * @throws InternalErrorException
	 * @throws NotFoundException
     * @see MeHtmlHelper::thumb(), MeHtmlHelper::thumbUrl()
	 * @uses maxHeight
	 * @uses maxSide
	 * @uses maxWidth
	 * @uses thumb
	 * @uses _imageThumb()
	 * @uses _videoThumb()
	 */
    public function thumb($file) {
		//Removes the fake file extension from the path
		$file = pathinfo($file, PATHINFO_FILENAME);
		
        //Decodes the path
        $file = urldecode(base64_decode($file));
		
		//If the file is remote
		if(filter_var($file, FILTER_VALIDATE_URL)) {
			//Downloads the file into /tmp, if not already done
			if(!is_readable($tmp = DS.'tmp'.DS.md5($file).pathinfo($file, PATHINFO_EXTENSION)))
				file_put_contents($tmp, file_get_contents($file));
			
			//The file is the temporary file
			$file = $tmp;
		}
		//Else, if the file is local and its path is relative, then the path will be relative to the webroot
		elseif(!Folder::isAbsolute($file))
			$file = WWW_ROOT.$file;
		
		//Checks if the file is readable
		if(!is_readable($file))
			throw new NotFoundException(__d('me_tools', 'The file %s doesn\'t exist or is not readable', $file));

		//File object
		$this->object = new File($file);
		
		//If the file is an image
		if(preg_match('/image\/\S+/', $mime = $this->object->mime())) {
			//If no maximum size is specified, it's not necessary to create a thumbnail
			if(!$this->maxSide && !$this->maxWidth && !$this->maxHeight) {
				//Renders the original image
				header(sprintf('Content-type: %s', $mime));
				readfile($this->object->path);

				$this->autoRender = FALSE;
				exit;
			}
			
			//Sets the thumb path
			$this->thumb = TMP.'thumbs'.DS.'photos'.DS.md5($this->object->path);
			
			if($this->maxSide)
				$this->thumb = sprintf('%s_s_%s', $this->thumb, $this->maxSide);
			else {
				if($this->maxWidth)
					$this->thumb = sprintf('%s_w_%s', $this->thumb, $this->maxWidth);
				if($this->maxHeight)
					$this->thumb = sprintf('%s_h_%s', $this->thumb, $this->maxHeight);
			}
		}
		//Else, if the file is a video
		elseif(preg_match('/video\/\S+/', $mime) || $mime == 'application/ogg') {
			if(!$this->maxSide && !$this->maxWidth)
				$this->maxSide = 270;
			
			//Sets the thumb path
			$this->thumb = TMP.'thumbs'.DS.'videos'.DS.md5($this->object->path);
			
			if($this->maxSide)
				$this->thumb = sprintf('%s_s_%s', $this->thumb, $this->maxSide);
			elseif($this->maxWidth)
				$this->thumb = sprintf('%s_w_%s', $this->thumb, $this->maxWidth);
		}
		//Else, if the mime type is not known
		else
			throw new InternalErrorException(__d('me_tools', 'The mime type %s is not supported', $mime));
		
		$this->thumb = sprintf('%s.jpg', $this->thumb);
			
		//Now the thumbnail path has been set
		
		//If the thumbnail does not yet exist
		if(!is_readable($this->thumb)) {
			//Checks if the target directory is writable
			if(!is_writable(dirname($this->thumb)))
				throw new InternalErrorException(__d('me_tools', 'The target directory %s is not writable', dirname($this->thumb)));
			
			//Creates the thumbnail
			if(preg_match('/image\/\S+/', $mime))
				$this->_imageThumb();
			else
				$this->_videoThumb();
		}
		
		//Renders the thumbnail
		header('Content-type: image/jpeg');
        readfile($this->thumb);
		
        $this->autoRender = FALSE;
        exit;
    }
}
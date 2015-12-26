<?php
/**
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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Controller;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\InternalErrorException;
use MeTools\Controller\AppController;
use MeTools\Utility\Php;
use MeTools\Utility\Thumbs;
use MeTools\Utility\Unix;

/**
 * Creates and displays thumbnails for image and video files.
 * 
 * The `thumb()` action takes the maximum width and/or the maximum height as query string parameters (`width` and `height` parameters).
 * It can also create square thumbs. In this case, it's sufficient to indicate the maximum side in the query string (`side` parameter).
 * With square thumbs, the initial image will be cut off if it is rectangular.
 * 
 * You can set the maximum height only for image files.
 * Instead, the video thumbnails can be created using the maximum width or maximum side.
 * 
 * `ThumbsController` doesn't just show thumbnails, but creates real thumbnails in the temporary directory, 
 * which can be used later when the same thumbs will be required (as if it were a cache).
 * 
 * To display a thumbnail or get the url for a thumbnail, you have to use `image()` and `url()` methods provided by `ThumbHelper`.
 * The `ThumbHelper::image()` method, using this controller, creates the thumbnail and returns the HTML code to show it.
 * The `ThumbHelper::url()` method creates the thumbs and returns its url.
 * @see MeTools\View\Helper\ThumbHelper::image(), MeTools\View\Helper\ThumbHelper::url()
 */
class ThumbsController extends AppController {	
	/**
	 * File object
	 * @var object
	 */
	protected $file;
	
	/**
	 * Thumb sizes
	 * @var array 
	 */
	protected $sizes = [];
	
	/**
	 * Thumb path
	 * @var string 
	 */
	protected $thumb;

	/**
	 * Creates a thumbnail from an image file
	 * @return void
	 * @throws InternalErrorException
	 * @uses MeTools\Utility\Php::extension()
	 * @uses file
	 * @uses sizes
	 * @uses thumb
	 */
	protected function _imageThumb() {
		//Checks for Imagick extension
        if(!Php::extension('imagick'))
            throw new InternalErrorException(__d('me_tools', '{0} is not available', 'Imagick'));
				
		//Creates the Imagick object
		$imagick = new \Imagick($this->file->path);
		
		//If the max side is defined (then has been requested a square thumb)
		if($this->sizes['side']) {
			//If the maximum side is larger than the width and height, then the maximum side is equal to the smallest size
			if($this->sizes['side'] > $imagick->getImageWidth() || $this->sizes['side'] > $imagick->getImageHeight())
				$this->sizes['side'] = $imagick->getImageWidth() > $imagick->getImageHeight() ? $imagick->getImageHeight() : $imagick->getImageWidth();
						
			//Creates the thumbnail
			$imagick->cropThumbnailImage($finalWidth = $finalHeight = $this->sizes['side'], $this->sizes['side']);
		}
		//Else, if the maximum width and the maximum height are defined
		elseif($this->sizes['width'] && $this->sizes['height']) {
            //Tries to get the final sizes from the width
            $finalWidth = floor($imagick->getImageWidth() * $this->sizes['height'] / $imagick->getImageHeight());

            //If the final width is greater than the maximum width, it gets the final sizes from the final height
            if($finalWidth > $this->sizes['width'])
                $finalHeight = floor($imagick->getImageHeight() * ($finalWidth = $this->sizes['width']) / $imagick->getImageWidth());
            //Else, the final height is the maximum height
            else
                $finalHeight = $this->sizes['height'];
		
			//Creates the thumbnail
			$imagick->thumbnailImage($finalWidth, $finalHeight);
		}
        //Else, if only the maximum width is defined
		elseif($this->sizes['width']) {
			//If the maximum width is greater than the actual width, then it's not necessary
			//to create a thumbnail and the thumbnail will be the original image
			if(($finalWidth = $this->sizes['width']) >= $imagick->getImageWidth()) {
				$this->thumb = $this->file->path;
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
			if(($finalHeight = $this->sizes['height']) >= $imagick->getImageHeight()) {
				$this->thumb = $this->file->path;
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
            throw new InternalErrorException(__d('me_tools', 'The thumbnail {0} has not been created', $this->thumb));
	}
	
	/**
	 * Creates a thumbnail from a video file
	 * @return void
	 * @throws InternalErrorException
	 * @uses MeTools\Utility\Unix::which()
	 * @uses sizes
	 * @uses thumb
	 */
	protected function _videoThumb() {
		//Checks for ffmpegthumbnailer
		if(!Unix::which('ffmpegthumbnailer'))
            throw new InternalErrorException(__d('me_tools', '{0} is not available', 'ffmpegthumbnailer'));
		
		//Creates the thumbnail
		shell_exec(sprintf('ffmpegthumbnailer -s %s -q 10 -f -i \'%s\' -o \'%s\'', empty($this->sizes['side']) ? $this->sizes['width'] : $this->sizes['side'], $this->file>path, $this->thumb));
		
		//Checks if the thumbnail has been created
		if(!is_readable($this->thumb))
            throw new InternalErrorException(__d('me_tools', 'The thumbnail {0} has not been created', $this->thumb));
		
		//If the max side is defined (then has been requested a square thumb)
		if($this->sizes['side']) {
			//Creates the Imagick object
			$imagick = new \Imagick($this->thumb);
			
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
	 * Creates and shows thumbnails for image and video files.
     * 
     * Please, refer to the class description for more information.
     * It's convenient to use `image()` or `url()` methods provided by `ThumbHelper`.
	 * @param string $file File path, encoded by `base64_encode()`
     * @see MeTools\View\Helper\ThumbHelper::image(), MeTools\View\Helper\ThumbHelper::url()
	 * @throws InternalErrorException
	 * @uses MeTools\Utility\Thumbs::photo()
	 * @uses MeTools\Utility\Thumbs::remote()
	 * @uses MeTools\Utility\Thumbs::video()
	 * @uses _imageThumb()
	 * @uses _videoThumb()
	 * @uses file
	 * @uses sizes
	 * @uses thumb
	 */
	public function thumb($file) {
		//Removes the fake file extension from the path
		$file = pathinfo($file, PATHINFO_FILENAME);
		
        //Decodes the path
        $file = urldecode(base64_decode($file));
		
		//If the file is remote
		if(is_url($file, FILTER_VALIDATE_URL)) {			
			//Downloads the file, if not already done
			if(!is_readable($tmp = Thumbs::remote(md5($file).'.'.pathinfo($file, PATHINFO_EXTENSION)))) {
				//Checks if the target directory is writable
				if(!is_writable(dirname($tmp)))
					throw new InternalErrorException(__d('me_tools', 'File or directory `{0}` not writeable', dirname($tmp)));
					
				//Downloads the file
				file_put_contents($tmp, fopen($file, 'r'));
			}
			//The file is now the temporary file
			$file = $tmp;
		}
		//Else, if the file is local and its path is relative, then the path will be relative to the webroot
		elseif(!Folder::isAbsolute($file))
			$file = WWW_ROOT.$file;

		//Checks if the file is readable
		if(!is_readable($file))
			throw new InternalErrorException(__d('me_tools', 'File or directory `{0}` not readable', $file));
		
		//Creates the File object
		$this->file = new File($file);
		
		//Sets the maximum sizes		
		foreach(['side', 'width', 'height'] as $v)
			$this->sizes[$v] = (int) $this->request->query($v);
		
		//If the file is an image
		if(preg_match('/image\/\S+/', $mime = $this->file->mime())) {
			//If no maximum size is specified, it's not necessary to create a thumbnail
			if(!array_filter($this->sizes)) {
				//Renders the original image
				header(sprintf('Content-type: %s', $mime));
				readfile($this->file->path);

				$this->autoRender = FALSE;
				exit;
			}
			
			//Sets the initial thumb path
			$this->thumb = Thumbs::photo(md5($this->file->path));
			
			//Updates the thumb path with the maximum sizes
			if($this->sizes['side'])
				$this->thumb = sprintf('%s_s_%s', $this->thumb, $this->sizes['side']);
			else {
				if($this->sizes['width'])
					$this->thumb = sprintf('%s_w_%s', $this->thumb, $this->sizes['width']);
				if($this->sizes['height'])
					$this->thumb = sprintf('%s_h_%s', $this->thumb, $this->sizes['height']);
			}
			
			//Updates the thumb path with the file extensions
			$this->thumb = sprintf('%s.%s', $this->thumb, $this->file->ext());
		}
		//Else, if the file is a video
		elseif(preg_match('/video\/\S+/', $mime) || $mime == 'application/ogg') {
			if(!$this->sizes['side'] && !$this->sizes['width'])
				$this->sizes['side'] = 270;
			
			//Sets the initial thumb path
			$this->thumb = Thumbs::video(md5($this->file->path));
			
			//Updates the thumb path with the maximum sizes
			if($this->sizes['side'])
				$this->thumb = sprintf('%s_s_%s', $this->thumb, $this->sizes['side']);
			elseif($this->sizes['width'])
				$this->thumb = sprintf('%s_w_%s', $this->thumb, $this->sizes['width']);
		
			//Updates the thumb path with the file extensions
			$this->thumb = sprintf('%s.jpg', $this->thumb);
		}
		//Else, if the file type is unknown
		else
			throw new InternalErrorException(__d('me_tools', 'The file type {0} is unknown', $mime));
		
		//If the thumbnail doesn't yet exist
		if(!is_readable($this->thumb)) {
			//Checks if the target directory is writable
			if(!is_writable(dirname($this->thumb)))
				throw new InternalErrorException(__d('me_tools', 'File or directory `{0}` not writeable', dirname($this->thumb)));
			
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
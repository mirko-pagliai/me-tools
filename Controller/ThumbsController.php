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
App::uses('MeToolsAppController', 'MeTools.Controller');
App::uses('Folder', 'Utility');

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
class ThumbsController extends MeToolsAppController {
	/**
	 * Creates a thumbnail of an image.
	 * @param object $file File object
	 * @return string Thumbnail path, if a thumbnail has been created
	 * @throws InternalErrorException
	 */
	private function _imageThumb($file) {
		//Checks for Imagick
        if(!extension_loaded('imagick'))
            throw new InternalErrorException(__d('me_tools', 'The %s library is missing', 'Imagick'));
		
		//Gets the maximum sizes
		$maxWidth = (int) $this->request->query('w');
        $maxHeight = (int) $this->request->query('h');
        $maxSide = (int) $this->request->query('s');
		
		//If no size is specified, then it's not necessary to create a thumbnail
		if(!$maxWidth && !$maxHeight && !$maxSide)
			return $file->path;
		
		//Creates the Imagick object
		$image = new Imagick($file->path);
		
		//If the max side is defined (then has been requested a square thumb)
		if($maxSide) {
			//If the maximum side is larger than the width and height, then the maximum side is equal to the smallest size
			if($maxSide > $image->getImageWidth() || $maxSide > $image->getImageHeight())
				$maxSide = $image->getImageWidth() > $image->getImageHeight() ? $image->getImageHeight() : $image->getImageWidth();
						
			//Creates the thumbnail
			$image->cropThumbnailImage($finalWidth = $finalHeight = $maxSide, $maxSide);
		}
		//Else, if the maximum width and the maximum height are defined
		elseif($maxWidth && $maxHeight) {
            //Tries to get the final sizes from the width
            $finalWidth = floor($image->getImageWidth() * $maxHeight / $image->getImageHeight());

            //If the final width is greater than the maximum width, it gets the final sizes from the final height
            if($finalWidth > $maxWidth)
                $finalHeight = floor($image->getImageHeight() * ($finalWidth = $maxWidth) / $image->getImageWidth());
            //Else, the final height is the maximum height
            else
                $finalHeight = $maxHeight;
		
			//Creates the thumbnail
			$image->thumbnailImage($finalWidth, $finalHeight);
		}
        //Else, if only the maximum width is defined
		elseif($maxWidth) {
			//If the maximum width is greater than the actual width, then it's not necessary to create a thumbnail
			if(($finalWidth = $maxWidth) >= $image->getImageWidth())
				return $file->path;
			
			//Gets the final height and creates the thumbnail
			$finalHeight = floor($finalWidth * $image->getImageHeight() / $image->getImageWidth());
			$image->thumbnailImage($finalWidth, 0);
			
		}
        //Else, if only the maximum height is defined
		else {
			//If the maximum height is greater than the actual height, then it's not necessary to create a thumbnail
			if(($finalHeight = $maxHeight) >= $image->getImageHeight())
				return $file->path;
			
			//Gets the final width and creates the thumbnail
			$finalWidth = floor($finalHeight * $image->getImageWidth() / $image->getImageHeight());
			$image->thumbnailImage(0, $finalHeight);
		}
		
		//Gets the thumbnail path
		$thumb = TMP.'thumbs'.DS.'photos'.DS.md5($file->path).'_'.$finalWidth.'x'.$finalHeight.'.jpg';
		
		//Checks if the thumbnail already exists
		if(is_readable($thumb))
			return $thumb;
		
		//Checks if the target directory is writable
		if(!is_writable(dirname($thumb)))
            throw new InternalErrorException(__d('me_tools', 'The target directory %s is not writable', dirname($thumb)));
		
		//Writes the image to the output directory and destroys the Imagick object
		$image->writeImage($thumb);
		$image->destroy();
		
		//Checks if the thumbnail has been created
		if(!is_readable($thumb))
            throw new InternalErrorException(__d('me_tools', 'The thumbnail %s has not been created', $thumb));
		
		return $thumb;
	}
	
	/**
	 * Creates a thumbnail of a video.
	 * @param object $file File object
	 * @return string Thumbnail path, if a thumbnail has been created
	 * @throws InternalErrorException
	 */
	private function _videoThumb($file) {
		//Gets the maximum sizes
		$maxWidth = (int) $this->request->query('w');
        $maxSide = (int) $this->request->query('s');
		
		//If no size is specified, it sets a maximum width
		if(!$maxWidth && !$maxSide)
			$maxWidth = 270;
		
		//Gets the thumbnail path
		if($maxSide)
			$thumb = TMP.'thumbs'.DS.'videos'.DS.md5($file->path).'_s'.($maxWidth = $maxSide).'.jpg';
		else
			$thumb = TMP.'thumbs'.DS.'videos'.DS.md5($file->path).'_w'.$maxWidth.'.jpg';
				
		//Checks if the thumbnail already exists
		if(is_readable($thumb))
			return $thumb;
		
		//Checks for ffmpegthumbnailer
		if(empty(shell_exec('which ffmpegthumbnailer')))
            throw new InternalErrorException(__d('me_tools', '%s is not avalaible', 'ffmpegthumbnailer'));
		
		//Checks if the target directory is writable
		if(!is_writable(dirname($thumb)))
            throw new InternalErrorException(__d('me_tools', 'The target directory %s is not writable', dirname($thumb)));
		
		//Creates the thumbnail
		shell_exec($cmd = sprintf('ffmpegthumbnailer -s %s -q 10 -f -i \'%s\' -o \'%s\'', $maxWidth, $file->path, $thumb));
		
		//Checks if the thumbnail has been created
		if(!is_readable($thumb))
            throw new InternalErrorException(__d('me_tools', 'The thumbnail %s has not been created', $thumb));
		
		//If the max side is defined (then has been requested a square thumb)
		if($maxSide) {
			//Creates the Imagick object
			$image = new Imagick($thumb);
			
			//If the height of the thumbnail is larger than the width
			if($image->getImageHeight() < $image->getImageWidth()) {
				//Adds a border
				$border = ($image->getImageWidth() - $image->getImageHeight()) / 2;
				$image->borderImage('#000000', 0, $border);
				
				//Writes the image to output directory and destroys the Imagick object
				$image->writeImage($thumb);
				$image->destroy();
			}
		}
		
		return $thumb;
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
	 * @uses _imageThumb() to create a thumbnail of an image
	 * @uses _videoThumb() to create a thumbnail of a video
	 */
    public function thumb($file = FALSE) {
		//Checks the file path
        if(empty($file))
            throw new InternalErrorException(__d('me_tools', 'The file has not been specified'));

        //Decodes the path. If the path is relative, then it's relative to the webroot
        $file = urldecode(base64_decode($file));
        $file = !Folder::isAbsolute($file) ? WWW_ROOT.$file : $file;

		//Checks if the file is readable
        if(!is_readable($file))
            throw new NotFoundException(__d('me_tools', 'The file %s doesn\'t exist or is not readable', $file));
		
		//Now `$file` is the File object
		$file = new File($file);
		$mime = $file->mime();

		//If the file is an image
		if(preg_match('/image\/\S+/', $mime))
			$thumb = $this->_imageThumb($file);
		//Else, if the file is a video
		elseif(preg_match('/video\/\S+/', $mime) || $mime == 'application/ogg')
			$thumb = $this->_videoThumb($file);
		//Else, if the mime type is not known
		else
			throw new InternalErrorException(__d('me_tools', 'The mime type %s is not supported', $mime));
		
		//Renders the thumbnail
		header("Content-type: image/jpeg");
        readfile($thumb);
		
        $this->autoRender = FALSE;
        exit;
    }
}
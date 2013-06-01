<?php
App::uses('AppHelper', 'View/Helper');

/**
 * ThumbHelper allows you to generate thumbnails of images, using {@link https://code.google.com/p/phpthumbmaker Thumber}.
 *
 * Before using ThumbHelper, the file <i>Plugin/MeTools/webroot/thumber.php</i> must be copied into <i>webroot/</i> (the webroot of your app).
 *
 * This helper just returns the url of the thumb, then <i>thumber.php</i> will really create the thumb.
 *
 * The thumbnails will be created in <i>webroot/files/thumbs/</i> (the webroot of your app), which must be writable.
 *
 * Example, in your view:
 * <code>
 * echo $this->Html->image($this->Thumb->getUrl('/path/to/image.jpg', 200));
 * </code>
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
 * @package		MeTools.View.Helper
 */
class ThumbHelper extends AppHelper {
	/**
	 * Get the url of a photo thumbnail
	 * @param string $path Image path
	 * @param string|int $width Thumb width
	 * @param string|int $height Thumb height
	 * @return string Thumb url
	 */
	public function getUrl($path, $width=null, $height=null) {
		//Initial url
		$url = '/thumber.php?img='.urlencode(base64_encode($path));

		//If requested to resize according to the width and to the height
		if(!empty($width) && !empty($height)) {
			//Get image sizes
			$originalSizes = getimagesize($path);

			//If width is larger or equal than height, append width to the url
			if($originalSizes['0'] >= $originalSizes['1'])
				$url .= '&w='.$width;
			//Else, if height is larger than height, append height to the url
			else
				$url .= '&h='.$height;
		}
		//If requested to resize according to the width, append width to the url
		elseif(!empty($width))
			$url .= '&w='.$width;
		//If requested to resize according to the height, append height to the url
		elseif(!empty($height))
			$url .= '&h='.$height;

		return $url;
	}
}
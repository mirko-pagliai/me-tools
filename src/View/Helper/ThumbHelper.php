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
namespace MeTools\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Thumb helper
 */
class ThumbHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.MeHtml'], 'Url'];
	
	/**
	 * Creates and returns the url for a thumbnail of an image or a video.
     * 
     * To get a thumbnail, you have to use `width` and/or `height` options. 
     * To get a square thumbnail, you have to use the `side` option.
	 * 
	 * You can use the `height` option only for image files.
     * 
     * Note that to directly display a thumbnail, you should use the `thumb()` method. 
	 * This method only returns the url of the thumbnail.
     * @param string $path Image path (absolute or relative to the webroot)
	 * @param array $options Array of options and HTML attributes
     * @return string Url
     * @see image()
	 * @uses UrlHelper::build()
	 */
	public function url($path, array $options = []) {
		$sizes = [];
		
		foreach(['side', 'width', 'height'] as $v)
			$sizes[$v] = !empty($options[$v]) && is_numeric($options[$v]) ? $options[$v] : NULL;
		
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$path = base64_encode($path);
		$url = ['controller' => 'Thumbs', 'action' => 'thumb', 'plugin' => 'MeTools', 'admin' => FALSE, 'ext' => $ext];
		
		return $this->Url->build(array_merge($url, ['?' => $sizes], [$path]), TRUE);
	}
	
	/**
	 * Creates and returns a thumbnail of an image or a video.
     * 
     * To get a thumbnail, you have to use `width` and/or `height` options. 
     * To get a square thumbnail, you have to use the `side` option.
	 * 
	 * You can use the `height` option only for image files.
     * @param string $path Image path (absolute or relative to the webroot)
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses MeHtmlHelper::_addValue()
	 * @uses MeHtmlHelper::img()
	 * @uses url()
	 */
	public function image($path, array $options = []) {
        $path = self::url($path, $options);
        unset($options['side'], $options['width'], $options['height']);
		
		$options = $this->Html->_addValue('class', 'thumb', $options);

        return $this->Html->img($path, $options);
	}
}
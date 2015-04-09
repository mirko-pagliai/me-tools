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
 * Layout Helper.
 * 
 * This helper allows you to gerate the layouts.
 */
class LayoutHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.MeHtml']];
	
	/**
	 * Adds the `viewport` meta tag as required by Bootstrap.
	 * @param bool $zooming Enabled or disabled zooming capabilities on mobile devices
     * @return string Html code
     * @see http://getbootstrap.com/css/#overview-mobile Bootstrap documentation
     * @uses MeTools\View\Helper\MeHtmlHelper::meta()
	 */
	public function viewport($zooming = FALSE) {
		$content = 'width=device-width, initial-scale=1';
		
		if(!$zooming)
			$content = sprintf('%s, %s', $content, 'maximum-scale=1, user-scalable=no');
			
		return $this->Html->meta(am(['name' => 'viewport'], compact('content')));
	}
}
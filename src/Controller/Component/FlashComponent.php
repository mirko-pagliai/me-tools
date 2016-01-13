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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://api.cakephp.org/3.1/class-Cake.Controller.Component.FlashComponent.html FlashComponent
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component\FlashComponent as CakeFlashComponent;
use MeTools\Core\Plugin;

/**
 * Provides a way to persist client data between page requests. It acts as a wrapper for the 
 * `$_SESSION` as well as providing convenience methods for several `$_SESSION` related functions.
 * 
 * Rewrites {@link http://api.cakephp.org/3.1/class-Cake.Controller.Component.FlashComponent.html FlashComponent}.
 */
class FlashComponent extends CakeFlashComponent {
	/**
	 * Magic method for verbose flash methods based on element names.
	 * @param string $name Element name to use
	 * @param array $args Parameters to pass
	 * @uses MeTools\Core\Plugin::path()
	 */
	public function __call($name, $args) {
		if(!isset($args[1]['plugin']) && is_readable(Plugin::path('MeTools', 'src'.DS.'Template'.DS.'Element'.DS.'Flash'.DS.\Cake\Utility\Inflector::underscore($name).'.ctp')))
			$args[1]['plugin'] = 'MeTools';
		
		return parent::__call($name, $args);
	}
}
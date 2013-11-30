<?php
/**
 * CleanerUrlComponent
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
 * @package		MeTools\Controller\Component
 */

/**
 * A component to clean the current url, removing the query string.
 *
 * For example:
 * <pre>/admin/users?var1=a&var2=b</pre>
 * will become:
 * <pre>/admin/users/index/var1:a/var2:b</pre>
 *
 * To use the `CleanerUrl` component, you need to add it to the components list in your controller:
 * <code>
 * public $components = array('MeTools.CleanerUrl');
 * </code>
 */
class CleanerUrlComponent extends Component {
	/**
	 * Called before the controller's beforeFilter method. Execute `cleanUrl`.
	 * @param Controller $controller
	 * @see http://api.cakephp.org/2.4/class-Component.html#_initialize CakePHP Api
	 */
	public function initialize(Controller $controller) {
		$this->cleanUrl($controller);
	}

	/**
	 * Cleans the current url, turning query arguments in named arguments, and executes a redirect.
	 * @param Controller $controller
	 */
	protected function cleanUrl(Controller $controller) {
		if(!empty($controller->request->query)) {
			//Merge named arguments with query arguments (note: query arguments will overwrite named arguments)
			$named = array_merge($controller->request->params['named'], $controller->request->query);

			//Merge controller, action, plugin, passed arguments (only values) and named values
			$url = array_merge(array(
				'controller'	=> $controller->request->params['controller'],
				'action'		=> $controller->request->params['action'],
				'plugin'		=> $controller->request->params['plugin']
			), array_values($controller->request->params['pass']), $named);

			$controller->redirect($url);
		}
	}
}
<?php

/**
 * A component that clean the current url, removing the query string.
 *
 * For example:
 * <code>
 * /admin/users?var1=a&var2=b
 * </code>
 * will become:
 * <code>
 * /admin/users/index/var1:a/var2:b
 * </code>
 *
 * To use the <i>CleanerUrl</i> component, add it to the list of components in your controller (for example, the <i>AppController</i>):
 * <code>
 * public $components = array('MeTools.CleanerUrl');
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
 * @package		MeTools.Controller.Component
 */
class CleanerUrlComponent extends Component {
	/**
	 * Called before the controller's beforeFilter method. Execute cleanUrl
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-Component.html#_initialize CakePHP Api}
	 * @param Controller $controller
	 */
	public function initialize(Controller $controller) {
		//If there's a query string
		if(!empty($_SERVER["QUERY_STRING"]))
			$this->cleanUrl($controller);
	}

	/**
	 * Clean the current url, removing the query string, then executes redirect
	 * @param Controller $controller
	 */
	protected function cleanUrl(Controller $controller) {
		//Take the query string and traslate it in the $queryString array
		parse_str($_SERVER["QUERY_STRING"], $queryString);
		//Delete all empty values, then recreate the query string (now without empty values)
		$queryString = http_build_query(array_filter($queryString));
		//Change "&" with "/" and "=" with ":" from the query string
		$queryString = str_replace(array('&', '='), array('/', ':'), $queryString);

		/**
		 * Perform the redirect
		 *
		 * $controller->request->here contains the current url, to which is appended the cleaned query string
		 */
		$controller->redirect($controller->request->here.'/'.$queryString);
	}
}
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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Controller
 */

App::uses('Controller', 'Controller');

/**
 * Application level controller.
 */
class MeToolsAppController extends Controller {
	/**
	 * Checks if the specified action is the current one. The action can be a string or an array of actions.
	 * 
	 * Optionally, it can also check the controller.
	 * 
	 * Example:
	 * <code>
	 * $this->isAction('delete');
	 * </code>
	 * It returns TRUE if the current action is `delete`, otherwise FALSE.
	 * 
	 * Example:
	 * <code>
	 * $this->isAction(array('edit', 'delete'), 'users');
	 * </code>
	 * It returns TRUE if the current action is `edit` or `delete` and if the controller is `users`, otherwise FALSE.
	 * @param string|array $action Action name
	 * @param string $controller Controller name
	 * @return bool TRUE if it's the current one, otherwise FALSE
	 * @uses isController()
	 */
	public function isAction($action, $controller = NULL) {
		if(is_array($action))
			$action = in_array($this->request->params['action'], $action);
		else
			$action = $this->request->params['action'] === $action;
		
		if(empty($controller))
			return $action;
		
		return $action && $this->isController($controller);
	}
	
	/**
	 * Checks if this is an admin request
	 * @return boolean TRUE if is an admin request, otherwise FALSE
	 */
	public function isAdminRequest() {
		return !empty($this->request->params['admin']);
	}
	
	/**
	 * Checks if the specified controller is the current one
	 * @param string $controller Controller name
	 * @return bool TRUE if it's the current one, otherwise FALSE
	 */
	public function isController($controller) {
		return $this->request->params['controller'] === $controller;
	}
	
	/**
	 * Checks if the current action is a "request action"
	 * @return bool TRUE if it's a "request action", otherwise FALSE
	 */
	public function isRequestAction() {
		return !empty($this->request->params['requested']);
	}
}
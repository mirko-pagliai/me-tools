<?php

/**
 * MeToolsAppController
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

/**
 * Application level controller.
 */
class MeToolsAppController extends AppController {
	/**
	 * Called before the controller action. 
	 * It's used to perform logic before each controller action.
	 */
	public function beforeFilter() {
		//Sets the element that will be used for flash auth errors
		//http://stackoverflow.com/a/20545037/1480263
		if(!empty($this->Auth))
			$this->Auth->flash['element'] = 'MeTools.error';
	}
	
	/**
	 * Called after the controller action is run, but before the view is rendered. 
	 * It's used to perform logic or set view variables that are required on every request.
	 */
	public function beforeRender() {
		//Sets the user authentication data
		if(!empty($this->Auth))
			$this->set('auth', $this->Auth->user());
	}
	
	/**
	 * Checks if the user is logged in
	 * @return boolean TRUE if the user is logged in, otherwise FALSE
	 */
	protected function isLogged() {
		return !empty($this->Auth->user('id'));
	}
}
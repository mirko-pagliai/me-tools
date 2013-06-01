<?php
App::uses('FormAuthenticate', 'Controller/Component/Auth');
App::uses('FileArray', 'MeTools.Utility');

/**
 * An authentication adapter for AuthComponent. Provides the ability to authenticate using POST
 * data, using an array of user data in the file <i>APP/user.txt</i> and generated through the <i>FileArray</i> utility. 
 * 
 * Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate setting.
 * 
 * {{{
 * $this->Auth->authenticate = array(
 *		'Form' => array(
 *			'scope' => array('User.active' => 1)
 *		)
 * )
 * }}}
 * 
 * When configuring FormAuthenticate you can pass in settings to which fields, model and additional conditions
 * are used. See FormAuthenticate::$settings for more information.
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
 * @package		MeTools.Controller.Component.Auth
 */
class FileArrayAuthenticate extends FormAuthenticate {
	/**
	 * Authenticates the identity contained in a request. Will use the `settings.userModel`, and `settings.fields`
	 * to find POST data that is used to find a matching record in the `settings.userModel`. Will return false if
	 * there is no post data, either username or password is missing, of if the scope conditions have not been met.
	 * 
	 * @param CakeRequest $request The request that contains login information.
	 * @param CakeResponse $response Unused response object.
	 * @return mixed An array of user data or FALSE on login failure.
	 */
    public function authenticate(CakeRequest $request, CakeResponse $response) {
		//Get model
		$userModel = $this->settings['userModel'];
        list(, $model) = pluginSplit($userModel);
		
		//Get fields
		$fields = $this->settings['fields'];
		
		//Checks the fields to ensure they are supplied
		if(!$this->_checkFields($request, $model, $fields))
			return false;
		
		//Search the user in the fileArray
		$this->fileArray = new FileArray(APP.'users.txt');
		$user = $this->fileArray->getFirst(array(
			'username' => $fields['username'], 
			'password' => md5($fields['password'])
		));
		
		//Return user data if the user exists, else FALSE
		return !empty($user) ? $user : false;
    }
}
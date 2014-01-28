<?php
/**
 * FileArrayAuthenticate
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
 * @author	Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license	http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link	http://git.novatlantis.it Nova Atlantis Ltd
 * @package	MeTools\Controller\Component\Auth
 */
App::uses('FormAuthenticate', 'Controller/Component/Auth');
App::uses('FileArray', 'MeTools.Utility');

/**
 * An authentication adapter for `AuthComponent`. Provides the ability to authenticate using POST
 * data with an array located in `APP/users.txt` and generated through the `FileArray` utility.
 */
class FileArrayAuthenticate extends FormAuthenticate {
	/**
	 * Provides the ability to authenticate using POSTdata with an array located in `APP/users.txt` and 
	 * generated through the `FileArray` utility.
	 * @param CakeRequest $request The request that contains login information
	 * @param CakeResponse $response Unused response object
	 * @return mixed An array of user data or FALSE on login failure
	 */
    public function authenticate(CakeRequest $request, CakeResponse $response) {
		//Gets model and fields
		$userModel = $this->settings['userModel'];
        list(, $model) = pluginSplit($userModel);
		$fields = $this->settings['fields'];
		
		//Checks the fields to ensure they are supplied
		if(!$this->_checkFields($request, $model, $fields))
			return FALSE;
		
		//Searches the user in the fileArray
		$this->fileArray = new FileArray(APP.'users.txt');
		$user = $this->fileArray->getFirst(array(
			'username' => $fields['username'], 
			'password' => md5($fields['password'])
		));
		
		//Returns user data if user exists, else FALSE
		return !empty($user) ? $user : FALSE;
    }
}
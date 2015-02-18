<?php
/**
 * TokenComponent
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
 * @package		MeTools\Controller\Component
 */
App::uses('CakeTime', 'Utility');

/**
 * A component to handle tokens.
 */
class TokenComponent extends Component {
	/**
	 * Construct. 
	 * 
	 * It loads the `Token` model.
	 * @param ComponentCollection $collection
	 * @param array $settings Array of configuration settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		$this->Token = ClassRegistry::init('MeTools.Token');
	}
	
	/**
	 * Checks if a token exists.
	 * @param string $token Token
	 * @param array $options Options (`data`, `expiry`, `type` and `user_id`)
	 * @return bool
	 */
	public function check($token, $options = array()) {
		$token = $this->Token->find('active', array('conditions' => am(compact('token'), $options)));
		
		return (bool) $token;
	}
	
	/**
	 * Creates and saves a token.
	 * 
	 * If the salt is empty, it will use the current timestamp.
	 * @param string $salt Salt to use to generate the token
	 * @param array $options Options (`data`, `expiry`, `type` and `user_id`)
	 * @return mixed The token value on success, otherwise FALSE
	 * @see http://php.net/strftime strftime documentation
	 */
	public function create($salt = NULL, $options = array()) {
		//Saves the token
		$save = $this->Token->save(am(array('token' => empty($salt) ? time() : $salt), $options));
		
		return $save['Token']['token'] ? $save['Token']['token'] : FALSE;
	}
	
	/**
	 * Deletes a token.
	 * @param string $token Token
	 * @return boolean
	 */
	public function delete($token) {
		return $this->Token->deleteAll(compact('token'));
	}
}
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
	 * 
	 * If the user ID is empty or 0, it will check for a token that is not related to a user.
	 * @param string $token The token
	 * @param string $type Type of the token
	 * @param int $user_id User ID, otherwise 0 if the token is not related to a user
	 * @return mixed The token ID if the token exists, otherwise FALSE
	 */
	public function check($token, $type, $user_id = 0) {
		$conditions = array(
			'expiration >'	=> CakeTime::format(time(), '%Y-%m-%d %H:%M:%S'),
			'type'			=> $type,
			'value'			=> $token
		);
		
		if(!empty($user_id))
			$conditions['user_id'] = $user_id;
		
		$token = $this->Token->find('first', array('conditions' => $conditions, 'fields' => 'id'));
		
		return empty($token['Token']['id']) ? FALSE : $token['Token']['id'];
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
	 * @param int $id Token ID
	 * @return boolean
	 */
	public function delete($id) {
		return $this->Token->delete($id);
	}
}
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
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
	 * Construct. It loads the Token model.
	 * @param ComponentCollection $collection
	 * @param array $settings Array of configuration settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->Token = ClassRegistry::init('MeTools.Token');
	}
	
	/**
	 * Creates a token. Internal function.
	 * 
	 * If the salt is empty, it will use the current timestamp.
	 * @param string $salt Salt to use to generate the token
	 * @param int $maxLenght Maximum length of the token
	 * @param string $method Method to use (sha1/sha256/md5/blowfish)
	 * @return string Token
	 */
	private function __createToken($salt = NULL, $maxLenght = 25) {
		$token = Security::hash(empty($salt) ? time() : $salt, 'sha1', TRUE);
		
		//Truncates the token, if it's longer than the maximum length
		$token = strlen($token) > $maxLenght ? substr($token, 0, $maxLenght) : $token;
		
		return $token;
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
	 * If the user ID is empty or 0, it will create a token that is not related to a user.
	 * If the expiration is empty, will be set to 12 hours.
	 * @param string $salt Salt to use to generate the token
	 * @param string $type Type of the token
	 * @param int $user_id User ID, otherwise 0 if the token is not related to a user
	 * @param string $expiration Expiration, strftime compatible formatting
	 * @return mixed The token value on success, otherwise FALSE
	 * @see http://php.net/strftime strftime documentation
	 * @uses __createToken() to create the token
	 */
	public function create($salt = NULL, $type, $user_id = 0, $expiration = NULL) {		
		$this->Token->create();
		$save = $this->Token->save(array(
			'expiration'	=> CakeTime::format(empty($expiration) ? '+12 hours' : $expiration, '%Y-%m-%d %H:%M:%S'),
			'type'			=> $type,
			'user_id'		=> $user_id,
			'value'			=> $token = self::__createToken($salt)
		));
		
		return empty($save) ? FALSE : $token;
	}
	
	/**
	 * Deletes a token
	 * @param int $id Token id
	 * @return boolean TRUE on success
	 */
	public function delete($id) {
		return $this->Token->delete($id);
	}
}
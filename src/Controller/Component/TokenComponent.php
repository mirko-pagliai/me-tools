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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component;

/**
 * A component to handle tokens
 */
class TokenComponent extends Component {
	/**
	 * `Token` table
	 * @var object
	 */
	protected $Tokens;

	/**
	 * Constructor
	 * @param \Cake\Controller\ComponentRegistry $registry A ComponentRegistry this component can use to lazy load its components
	 * @param array $config Array of configuration settings
	 * @uses $Tokens;
	 */
	public function __construct(\Cake\Controller\ComponentRegistry $registry, array $config = []) {
		parent::__construct($registry, $config);
		
		$this->Tokens = \Cake\ORM\TableRegistry::get('MeTools.Tokens');
	}
	
	/**
	 * Internal method to find a token
	 * @param string $token Token
	 * @param array $options Options (`data`, `expiry`, `type` and `user_id`)
	 * @return object Tokens entity
	 */
	protected function _find($token, array $options = []) {
		return $this->Tokens->find('active')
			->where(am([
				'type'		=> empty($options['type']) ? NULL : $options['type'],
				'user_id'	=> empty($options['user_id']) ? NULL : $options['user_id'],
			], compact('token')))
			->first();
	}
	
	/**
	 * Checks if a token exists.
	 * @param string $token Token
	 * @param array $options Options (`data`, `expiry`, `type` and `user_id`)
	 * @return bool
	 * @uses _find()
	 */
	public function check($token, array $options = []) {
		return !empty($this->_find($token, $options));
	}
	
	/**
	 * Creates and saves a token.
	 * 
	 * Note that if the salt is empty, it will use the current timestamp.
	 * @param string $salt Salt to use to generate the token
	 * @param array $options Options (`data`, `expiry`, `type` and `user_id`)
	 * @return mixed The token value on success, otherwise FALSE
	 */
	public function create($salt = NULL, array $options = []) {
		return $this->Tokens->save($entity = $this->Tokens->newEntity(af([
			'data'		=> empty($options['data']) ? NULL : $options['data'],
			'expiry'	=> empty($options['expiry']) ? NULL : $options['expiry'],
			'type'		=> empty($options['type']) ? NULL : $options['type'],
			'token'		=> $salt,
			'user_id'	=> empty($options['user_id']) ? NULL : $options['user_id']
		]))) ? $entity->token : FALSE;
	}
	
	/**
	 * Deletes a token
	 * @param string $token Token
	 * @return bool
	 */
	public function delete($token) {
		$this->Tokens->deleteAll(compact('token'));
	}
}
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
namespace MeTools\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use MeTools\Model\Entity\Token;

/**
 * Tokens model
 */
class TokensTable extends Table {
	/**
	 * Called before request data is converted into entities
	 * @param \Cake\Event\Event $event Event object
	 * @param \ArrayObject $data Data
	 * @param \ArrayObject $options Options
	 * @uses Cake\I18n\Time::i18nFormat()
	 * @uses Cake\Utility\Security::hash()
	 */
	public function beforeMarshal(\Cake\Event\Event $event, \ArrayObject $data, \ArrayObject $options) {
		if(empty($data['token']))
			$data['token'] = time();
		
		$data['token'] = substr(\Cake\Utility\Security::hash($data['token'], 'sha1', TRUE), 0, 25);
		
		if(empty($data['expiry']))
			$data['expiry'] = (new Time('+12 hours'))->i18nFormat(FORMAT_FOR_MYSQL);
	}
	
	/**
	 * Called before each entity is saved.
	 * Stopping this event will abort the save operation
	 * @param \Cake\Event\Event $event Event object
	 * @param \Cake\ORM\Entity $entity Entity object
	 * @param \ArrayObject $options Options
	 * @return bool
	 * @uses deleteExpired()
	 */
	public function beforeSave(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options) {
		$this->deleteExpired($entity);
		
		return TRUE;
	}

	/**
	 * Deletes all expired tokens, based on the expiry and (optional) the user ID and the token value
	 * @param MeTools\Model\Entity\Token $entity Token entity or NULL
	 * @uses Cake\I18n\Time::i18nFormat()
	 */
	public function deleteExpired(\Cake\ORM\Entity $entity = NULL) {
		//Deletes all expired tokens
		$conditions = ['expiry <=' => (new Time())->i18nFormat(FORMAT_FOR_MYSQL)];
				
		if(!empty($entity->token))
			$or[] = ['token' => $entity->token];
		
		if(!empty($entity->user_id))
			$or[] = ['user_id' => $entity->user_id];
		
		if(!empty($or))
			$conditions = ['OR' => am($conditions, $or)];
		
		$this->deleteAll($conditions);
	}
	
	/**
	 * "Active" find method
	 * @param Query $query Query object
	 * @param array $options Options
	 * @return Query Query object
	 * @uses Cake\I18n\Time::i18nFormat()
	 */
	public function findActive(Query $query, array $options) {
        $query->where([sprintf('%s.expiry >', $this->alias()) => (new Time())->i18nFormat(FORMAT_FOR_MYSQL)]);
		
        return $query;
    }

	/**
     * Initialize method
     * @param array $config The table configuration
     */
    public function initialize(array $config) {
        $this->table('tokens');
        $this->displayField('id');
        $this->primaryKey('id');
    }
	
    /**
     * Default validation rules
     * @param \Cake\Validation\Validator $validator Validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
		//ID
		$validator->add('id', 'valid', ['rule' => 'naturalNumber'])
			->allowEmpty('id', 'create');
		
		//User id
		$validator->add('user_id', 'valid', ['rule' => 'naturalNumber'])
			->allowEmpty('user_id');
		
		//Type
		$validator->add('type', 'lengthBetween', ['rule' => ['lengthBetween', 3, 100]])
			->allowEmpty('type');
		
		//Token
		$validator->requirePresence('token', 'create')
			->add('token', 'lengthBetween', ['rule' => ['lengthBetween', 25, 25]]);
		
		//Data
		$validator->allowEmpty('data');
			
		//Expiry
		$validator->add('expiry', 'datetime', ['rule' => 'datetime'])
            ->allowEmpty('expiry');

        return $validator;
    }
}
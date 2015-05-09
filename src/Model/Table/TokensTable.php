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
			->add('type', 'lengthBetween', ['rule' => ['lengthBetween', 25, 25]]);
		
		//Data
		$validator->allowEmpty('data');
			
		//Expiry
		$validator->add('expiry', 'datetime', ['rule' => 'datetime'])
            ->allowEmpty('expiry');

        return $validator;
    }
}
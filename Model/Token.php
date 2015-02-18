<?php
/**
 * Token model
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
 * @package		MeTools\Model
 */

App::uses('MeToolsAppModel', 'MeTools.Model');

/**
 * Token Model
 * @property User $User
 */
class Token extends MeToolsAppModel {
	/**
	 * Validation rules
	 * @var array
	 */
	public $validate = array(
		'id' => array(
			'blankOnCreate' => array(
				'message'	=> 'Can not be changed',
				'on'		=> 'create',
				'rule'		=> 'blank'
			)
		),
		'user_id' => array(
			'allowEmpty'	=> TRUE,
			'message'		=> 'You have to select an option',
			'rule'			=> array('naturalNumber')
		),
		'type' => array(
			'allowEmpty'	=> TRUE,
			'message'		=> 'Must be at most %d chars',
			'rule'			=> array('maxLength', 255)
		),
		'token' => array(
			'message'	=> 'Must be at most %d chars',
			'rule'		=> array('maxLength', 25)
		),
		'expiry' => array(
			'message'	=> 'Must be a valid datetime',
			'rule'		=> array('datetime')
		),
	);
	
	/**
	 * Called after every deletion operation.
	 */
	public function afterDelete() {
		//Deletes all expired tokens
		$this->deleteAll(array('expiry <=' => CakeTime::format(time(), '%Y-%m-%d %H:%M:%S')), FALSE);
	}
	
	/**
	 * Executes after model validation and before the data is saved.
	 * @param array $options Options
	 * @return boolean TRUE
	 */
	public function beforeSave($options = array()) {
		parent::beforeSave($options);
		
		//Token hash
		if(!empty($this->data[$this->alias]['token'])) {
			$this->data[$this->alias]['token'] = Security::hash($this->data[$this->alias]['token'], 'sha1', TRUE);
			$this->data[$this->alias]['token'] = substr($this->data[$this->alias]['token'], 0, 25);
		}
		
		//Expiry, default 12 hours
		if(empty($this->data[$this->alias]['expiry']))
			$this->data[$this->alias]['expiry'] = CakeTime::format('+12 hours', '%Y-%m-%d %H:%M:%S');
		
		//Deletes all expired tokens
		$conditions = array('expiry <=' => CakeTime::format(time(), '%Y-%m-%d %H:%M:%S'));
		
		//Deletes all the tokens of the same user
		if(!empty($this->data[$this->alias]['user_id']))
			$conditions = array('OR' => am(array('user_id' => $this->data[$this->alias]['user_id']), $conditions));
		
		$this->deleteAll($conditions, FALSE);
		
		return TRUE;
	}
}
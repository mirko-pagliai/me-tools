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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Model
 */

/**
 * Token model
 */
class Token extends MeToolsAppModel {
	/**
	 * Called before every deletion operation
	 * @param boolean $cascade If TRUE records that depend on this record will also be deleted
	 * @return boolean TRUE if the operation should continue, FALSE if it should abort
	 */
	public function beforeDelete($cascade = TRUE) {
		//Instead of delete only the current token, it also deletes all expired tokens
		if(!empty($this->id)) {
			$this->deleteAll(array('OR' => array('id' => $this->id, 'expiration <=' => CakeTime::format(time(), '%Y-%m-%d %H:%M:%S'))), FALSE);

			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Executes after model validation and before the data is saved
	 * @param array $options Options
	 * @return boolean TRUE if the save operation should continue
	 */
	public function beforeSave($options = array()) {
		//Deletes all expired tokens
		$conditions = array('expiration <=' => CakeTime::format(time(), '%Y-%m-%d %H:%M:%S'));
		
		//Deletes all the tokens of the same user
		if(!empty($this->data[$this->alias]['user_id']))
			$conditions = array('OR' => am(array('user_id' => $this->data[$this->alias]['user_id']), $conditions));
		
		$this->deleteAll($conditions, FALSE);
		
		return TRUE;
	}
}
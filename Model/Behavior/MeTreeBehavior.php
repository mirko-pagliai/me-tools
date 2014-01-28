<?php

/**
 * MeTreeBehavior
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
 * @package	MeTools\Model\Behavior
 * @see		http://api.cakephp.org/2.4/source-class-TreeBehavior.html CakePHP Api
 */
App::uses('TreeBehavior', 'Model/Behavior');

/**
 * Tree behavior class. Enables a model object to act as a node-based tree.
 * 
 * Rewrites {@link http://api.cakephp.org/2.4/source-class-TreeBehavior.html TreeBehavior}.
 * 
 * This class is only useful to rewrite the `generateTreeList()` method provided by the `TreeBehavior` behavior.
 */
class MeTreeBehavior extends TreeBehavior {
    /**
     * A convenience method for returning a hierarchical array used for HTML select boxes.
     * @param Model $Model Model instance
     * @param string|array $conditions SQL conditions as a string or as an array('field' =>'value',...)
     * @param string $keyPath A string path to the key, i.e. "{n}.Post.id"
     * @param string $valuePath A string path to the value, i.e. "{n}.Post.title"
     * @param string $spacer The character or characters which will be repeated
     * @param integer $recursive The number of levels deep to fetch associated records
     * @return array An associative array of records, where the id is the key, and the display field is the value
     */
    public function generateTreeList(Model $Model, $conditions = NULL, $keyPath = NULL, $valuePath = NULL, $spacer = '—', $recursive = NULL) {
        return parent::generateTreeList($Model, $conditions, $keyPath, $valuePath, $spacer, $recursive);
    }
}
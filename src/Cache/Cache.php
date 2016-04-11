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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://api.cakephp.org/3.2/class-Cake.Cache.Cache.html Cache
 */
namespace MeTools\Cache;

use Cake\Cache\Cache as CakeCache;

/**
 * Cache provides a consistent interface to Caching in your application.
 * 
 * Rewrites {@link http://api.cakephp.org/3.2/class-Cake.Cache.Cache.html Cache}.
 */
class Cache extends CakeCache {
	/**
	 * Deletes all cache keys, from all configurations and all groups
     * @return array Status code. For each configuration, it reports the status of the operation
	 */
	public static function clearAll($check = false) {
        $status = [];
        
        foreach(self::configured() as $config)
            $status[$config] = self::clear($check, $config);
        
        return $status;
	}
}

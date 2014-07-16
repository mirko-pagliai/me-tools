<?php

/**
 * MeFileLog
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
 * @package		MeTools\Lib\Log\Engine
 * @see			http://api.cakephp.org/2.4/class-FileLog.html CakePHP Api
 */
App::uses('FileLog', 'Log/Engine');

/**
 * File Storage stream for logging. Writes log files.
 * 
 * Rewrites {@link http://api.cakephp.org/2.4/class-FileLog.html FileLog}.
 * 
 * This class is only useful to rewrite the `write()` method provided 
 * by the `FileLog` engine, so that the client IP address is recorded in logs.
 */
class MeFileLog extends FileLog {
    /**
     * Implements writing to log files.
     * @param string $type The type of log you are making.
     * @param string $message The message you want to log.
     * @return boolean success of write.
     */
    public function write($type, $message) {
        //Get the client ip
        $request = new CakeRequest(NULL, FALSE);
        $ip = $request->clientIp(FALSE);

        $message = empty($ip) ? $message : sprintf('%s (%s)', $message, $ip);

        return parent::write($type, lcfirst($message));
    }
}
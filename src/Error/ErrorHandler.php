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
 * @see			http://api.cakephp.org/3.2/source-class-Cake.Error.ErrorHandler.html ErrorHandler
 */
namespace MeTools\Error;

use Cake\Error\Debugger;
use Cake\Error\ErrorHandler as CakeErrorHandler;
use Cake\Log\Log;
use Cake\Routing\Router;

/**
 * Error Handler provides basic error and exception handling for your application. It captures and
 * handles all unhandled exceptions and errors. Displays helpful framework errors when debug > 1.
 * 
 * Rewrites {@link http://api.cakephp.org/3.2/source-class-Cake.Error.ErrorHandler.html ErrorHandler}.
 * This allows to track the "request URL" also for errors and not only for exceptions.
 */
class ErrorHandler extends CakeErrorHandler {
	/**
	 * Log an error
	 * @param string $level The level name of the log.
	 * @param array $data Array of error data.
	 * @return bool
	 */
	protected function _logError($level, $data) {
		$message = sprintf(
			'%s (%s): %s in [%s, line %s]',
			$data['error'],
			$data['code'],
			$data['description'],
			$data['file'],
			$data['line']
        );
		
		//Adds the request URL
		 if((PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg')) {
			$request = Router::getRequest();
			if($request)
				$message .= "\nRequest URL: ".$request->here();
		}

		if(!empty($this->_options['trace'])) {
			$trace = Debugger::trace([
				'start' => 1,
				'format' => 'log'
			]);
			$message .= "\nTrace:\n".$trace."\n";
		}
		
		$message .= "\n\n";
		
		return Log::write($level, $message);
	}
}
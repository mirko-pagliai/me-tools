<?php
/**
 * Exception Renderer.
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
 * @package		MeTools\Lib\Error
 * @see			http://api.cakephp.org/2.0/class-ExceptionRenderer.html
 */

/**
 * This class is used only in order to use the error views from the MeTools plugin.
 * 
 * In the `app/Config/core.php` file, you have to change the configuration of the exceptions 
 * as follows:
 * <code>
 * Configure::write('Exception', array(
 *     'handler' => 'ErrorHandler::handleException',
 *     'renderer' => 'MeTools.MeExceptionRenderer',
 *     'log' => true
 * ));
 * </code>
 * 
 * Rewrites {@link http://api.cakephp.org/2.0/class-ExceptionRenderer.html ExceptionRenderer}
 */
class MeExceptionRenderer extends ExceptionRenderer {
	/**
	 * This method is used only in order to use the error views from the MeTools plugin.
	 * @param Exception $exception The exception to get a controller for.
	 * @return Controller
	 */
	protected function _getController($exception) {
		$controller = parent::_getController($exception);
		$controller->plugin = 'MeTools';
		return $controller;
	}
}
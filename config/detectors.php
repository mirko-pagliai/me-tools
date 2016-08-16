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
 */

use Cake\Routing\Router;
use Cake\Network\Request;

/**
 * Adds `is('action')` detector.
 * 
 * It checks if the specified action is the current action.
 * The action name can be passed as string or array.
 * 
 * Optionally, you can also check if the specified controller is the 
 * current controller.
 * 
 * The controller name can be passed as string or array.
 * 
 * Example:
 * <code>
 * $this->request->is('action', 'delete');
 * </code>
 * returns `TRUE` if the current action is `delete`, otherwise `FALSE`.
 * 
 * Example:
 * <code>
 * $this->request->is('action', ['edit', 'delete'], 'Pages');
 * </code>
 * returns `TRUE` if the current action is `edit` or `delete` and if the 
 * current controller is `Pages`, otherwise `FALSE`.
 */
Request::addDetector('action', function($request, $action, $controller = NULL) {
    $action = in_array($request->param('action'), (array) $action);
    
    //Checks only action
    if(empty($controller)) {
        return $action;
    }

    //Checks action and controller
    return $action && $request->is('controller', $controller);
});

/**
 * Adds `is('controller')` detector.
 * 
 * It checks if the specified controller is the current controller.
 * The controller name can be passed as string or array.
 * 
 * Example:
 * <code>
 * $this->request->is('controller', 'Pages');
 * </code>
 * returns `TRUE` if the current controller is `Pages`, otherwise `FALSE`.
 */
Request::addDetector('controller', function($request, $controller) {
    return in_array($request->param('controller'), (array) $controller);
});

/**
 * Adds `is('url')` detector.
 * 
 * It checks if the specified url is the current url.
 * 
 * Example:
 * <code>
 * $this->request->is('url', ['_name' => 'posts']);
 * </code>
 */
Request::addDetector('url', function($request, $url) {
    return rtrim(Router::url($url), '/') === rtrim($request->here, '/');
});

/**
 * Adds `is('prefix')` detector.
 * 
 * It checks if the specified prefix is the current prefix.
 * The prefix name can be passed as string or array.
 * 
 * Example:
 * <code>
 * $this->request->is('prefix', ['admin', 'manager']);
 * </code>
 */
Request::addDetector('prefix', function($request, $prefix) {
    return in_array($request->param('prefix'), (array) $prefix);
});
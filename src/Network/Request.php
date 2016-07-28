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
 * @see			http://api.cakephp.org/3.2/class-Cake.Network.Http.Request.html
 */
namespace MeTools\Network;

use Cake\Network\Request as CakeRequest;
use Cake\Routing\Router;

/**
 * Implements methods for HTTP requests.
 * 
 * Used by Cake\Http\Client to contain request information for making 
 *  requests.
 * 
 * Rewrites {@link http://api.cakephp.org/3.2/class-Cake.Network.Http.Request.html Request}.
 */
class Request extends CakeRequest {
	/**
	 * Checks if the specified action is the current action.
	 * The action name can be passed as string or array.
	 * 
	 * Optionally, you can also check if the specified controller is the 
     *  current controller.
	 * The controller name can be passed as string or array.
	 * 
	 * Example:
	 * <code>
	 * $this->request->isAction('delete');
	 * </code>
	 * returns TRUE if the current action is `delete`, otherwise FALSE.
	 * 
	 * Example:
	 * <code>
	 * $this->request->isAction(['edit', 'delete'], 'Pages');
	 * </code>
	 * returns TRUE if the current action is `edit` or `delete` and if the 
     *  current controller is `Pages`, otherwise FALSE.
	 * @param string|array $action Action name
	 * @param string|array $controller Controller name
	 * @return bool
	 * @uses isController()
	 */
	public function isAction($action, $controller = NULL) {
		$action = in_array($this->param('action'), (array) $action);
		
		return empty($controller) ? $action : $action && $this->isController($controller);
	}
	
	/**
	 * Checks if the specified controller is the current controller.
	 * The controller name can be passed as string or array.
	 * 
	 * Example:
	 * <code>
	 * $this->request->isController('Pages');
	 * </code>
	 * returns TRUE if the current controller is `Pages`, otherwise FALSE.
	 * @param string|array $controller Controller name
	 * @return bool
	 */
	public function isController($controller) {
		return in_array($this->param('controller'), (array) $controller);
	}
	
	/**
	 * Checks if the specified url is the current url
	 * @param string|array|null $url An array specifying any of the following: 
     *  'controller', 'action', 'plugin' additionally, 
	 * you can provide routed elements or query string parameters. If string 
     *  it can be name any valid url string
	 * @return bool
	 */
	public function isHere($url) {
        return Router::url($url) === $this->here;
	}
	
	/**
	 * Checks if the specified prefix is the current prefix.
	 * The prefix name can be passed as string or array.
	 * @param string|array $prefix Prefix name
	 * @return bool
	 */
	public function isPrefix($prefix) {
		return in_array($this->param('prefix'), (array) $prefix);
	}
}
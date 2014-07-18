<?php

/**
 * MeSecurityComponent
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
 * @package		MeTools\Controller\Component
 * @see			http://api.cakephp.org/2.5/class-SecurityComponent.html SecurityComponent
 */
App::uses('SecurityComponent', 'Controller/Component');

/**
 * Provides an easy way to integrate tighter security in the application.
 * 
 * Rewrites {@link http://api.cakephp.org/2.5/class-SecurityComponent.html SecurityComponent}.
 * 
 * You should use this component as an alias, for example:
 * <code>
 * public $component = array('Security' => array('className' => 'MeTools.MeSecurityComponent'));
 * </code>
 */
class MeSecurityComponent extends SecurityComponent {
    /**
     * Called before the controller's beforeFilter method.
     * @param Controller $controller
     * @see http://api.cakephp.org/2.5/class-Component.html#_initialize CakePHP Api
     */	
	public function initialize(Controller $controller) {
		$this->request = $controller->request;
	}

	/**
	 * Checks if the user's IP address is part of a list of allowed IP addresses.
	 * Note that access to localhost will always be admitted.
	 * 
	 * Addresses can be separated by a comma, a comma and a space or a pipe.
	 * The asterisk can be used as a wildcard.
	 * @param string $allowed_ip allowed IP addresses
	 * @return boolean TRUE if the user's IP address is part of allowed addresses, otherwise FALSE.
	 */
	public function allowIp($allowed_ip = NULL) {
		if(!is_object($this->request))
			return FALSE;
		
		$ip = $this->request->clientIp(TRUE);
		
        //Skips if it's localhost or if the control has already happened and was successful
        if($ip == '127.0.0.1' || $ip == '::1' || $this->Session->read('allowed_ip'))
            return TRUE;
		
		if(empty($allowed_ip))
			return FALSE;
		
		//For addresses that end with a zero, it changes the zero with an asterisk
		$allowed_ip = preg_replace('/((([0-9]{1,3}|\*)\.){3})(0)/', '$1*', $allowed_ip);
		
		//Changes whitespace character, commas and pipes with pipes. It also changes the asterisks
		$allowed_ip = preg_replace(array('/[\s,|]+/', '/\\\\\*/'), array('|', '[0-9]{1,3}'), preg_quote($allowed_ip));
		
		//If the IP doesn't match with any of allowed IPs, exit with error
        if(!preg_match('/^('.$allowed_ip.')$/', $ip))
            return FALSE;
		
        //In any other case, saves the result in the session
        $this->Session->write('allowed_ip', TRUE);
        return TRUE;
	}
}

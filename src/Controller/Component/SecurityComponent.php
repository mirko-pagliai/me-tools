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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://api.cakephp.org/3.0/class-Cake.Controller.Component.SecurityComponent.html SecurityComponent
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component\SecurityComponent as CakeSecurityComponent;

/**
 * The Security Component creates an easy way to integrate tighter security in your application.
 * 
 * Rewrites {@link http://api.cakephp.org/3.0/class-Cake.Controller.Component.SecurityComponent.html SecurityComponent}.
 */
class SecurityComponent extends CakeSecurityComponent {
	/**
	 * Checks if the user's IP address is part of an array of banned IP addresses.
	 * Note that access to localhost will always be admitted.
	 * 
	 * The asterisk can be used as a wildcard.
	 * @param array $bannedIp List of banned IP addresses
	 * @return bool
	 */
	public function isBanned(array $bannedIp = []) {
        //Skips if it's localhost or if the control has already happened and was successful
		 if(empty($bannedIp) || is_localhost() || $this->request->session()->check('allowed_ip'))
            return FALSE;
		
		//For addresses that end with a zero, it replaces the zero with an asterisk
		$bannedIp = preg_replace('/^((([0-9]{1,3}|\*)\.){3})(0)$/', '${1}*', $bannedIp);
		
		//Replaces asteriskes
		$bannedIp = preg_replace('/\\\\\*/', '[0-9]{1,3}', array_map('preg_quote', $bannedIp));
				
        if(preg_match(sprintf('/^(%s)$/', implode('|', $bannedIp)), $this->request->clientIp()))
			return TRUE;
		
        //In any other case, saves the result in the session
        $this->Session->write('allowed_ip', TRUE);
        return FALSE;
	}
}
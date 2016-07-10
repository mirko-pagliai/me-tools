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
 * @see			https://www.google.com/recaptcha reCAPTCHA site
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Http\Client;

/**
 * A component to use reCAPTCHA
 */
class RecaptchaComponent extends Component {
	/**
	 * Error
	 * @var string
	 */
	protected $error;
	
	/**
	 * Checks for reCAPTCHA
	 * @return boolean
	 * @see https://developers.google.com/recaptcha/docs/verify
	 * @throws \Cake\Network\Exception\InternalErrorException
	 */
	public function check() {		
		//Loads the configuration file and gets the form keys
		Configure::load('recaptcha');
		$keys = Configure::read('Recaptcha.Form');
		
		//Checks for form keys
		if(empty($keys['public']) || empty($keys['private'])) {
            throw new InternalErrorException(__d('me_tools', 'Form keys are not configured'));
        }
        
		$controller = $this->_registry->getController();
		$response = $controller->request->data('g-recaptcha-response');
		
		if(empty($response)) {
			$this->error = __d('me_tools', 'You have not filled out the {0} control', 'reCAPTCHA');	
			return FALSE;
		}
		 		
		$results = (new Client())->post('https://www.google.com/recaptcha/api/siteverify', am([
			'remoteip' => $controller->request->clientIp(),
			'secret' => $keys['private'],
		], compact('response')));
				
		if(empty($results) || empty($results->json['success'])) {
			$this->error = __d('me_tools', 'It was not possible to verify the {0} control', 'reCAPTCHA');
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Gets the last error
	 * @return string Error
	 * @uses error
	 */
	public function getError() {
		return $this->error;
	}
}
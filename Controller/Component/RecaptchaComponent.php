<?php
/**
 * RecaptchaComponent
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
 * @package		MeTools\Controller\Component
 * @see			https://www.google.com/recaptcha reCAPTCHA site
 */

App::uses('HttpSocket', 'Network/Http');

/**
 * A component to check for reCAPTCHA.
 *
 * Before using this comoonent, you have to configure keys in `app/Config/recaptcha.php`.
 * You can use as example the file `app/Plugin/MeTools/Config/recaptcha.default.php`.
 */
class RecaptchaComponent extends Component {
	/**
	 * Controller
	 * @var object
	 */
	protected $controller;
	
	/**
	 * Error
	 * @var string 
	 */
	public $error;
	
	/**
	 * reCAPTCHA form keys
	 * @var array Keys
	 */
	private $keys = array();
	
	/**
	 * Constructor.
	 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
	 * @param array $settings Array of configuration settings.
	 * @uses error
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		//Sets the error message
		$this->error = __d('me_tools', 'You have not filled out the %s control', 'reCAPTCHA');
	}
	
	/**
	 * Is called after the controller's beforeFilter method but before the controller executes the current action handler.
	 * @param Controller $controller
	 * @uses controller
	 * @uses keys
	 */
	public function startup(Controller $controller) {
		//Loads the configuration file
		Configure::load('recaptcha');
		
		//Gets form keys
		$this->keys = Configure::read('Recaptcha.Form');
		
		//Checks for form keys
		if(empty($this->keys['public']) || empty($this->keys['private']))
            throw new InternalErrorException(__d('me_tools', 'Form keys are not configured'));
		
		$this->controller = $controller;
	}
	
	/**
	 * Checks for reCAPTCHA.
	 * @return bool TRUE on success, otherwise FALSE
	 * @see https://developers.google.com/recaptcha/docs/verify
	 * @uses controller
	 * @uses keys
	 * @uses HttpSocket::post()
	 */
	public function check() {
		$response = $this->controller->request->data['g-recaptcha-response'];
		
		if(empty($response))
			return FALSE;
		
		//Post request
		$http = new HttpSocket();
		$results = $http->post('https://www.google.com/recaptcha/api/siteverify', am(array(
			'remoteip'	=> $this->controller->request->clientIp(TRUE),
			'secret'	=> $this->keys['private']
		), compact('response')));  

		if(empty($results))
			return FALSE;
		
		$results = json_decode($results, TRUE);
		
		return empty($results['success']) ? FALSE: $results['success'];
	}
}
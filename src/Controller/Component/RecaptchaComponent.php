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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 * @see         https://www.google.com/recaptcha reCAPTCHA site
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Network\Exception\InternalErrorException;

/**
 * A component to use reCAPTCHA
 */
class RecaptchaComponent extends Component
{
    /**
     * Last error
     * @var string
     */
    protected $error;

    /**
     * Construct
     * @param \Cake\Controller\ComponentRegistry $registry A ComponentRegistry
     *  this component can use to lazy load its components
     * @param array $config Array of configuration settings
     */
    public function __construct(\Cake\Controller\ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        //Loads the configuration file
        Configure::load('recaptcha');
    }

    /**
     * Gets results from Recaptcha
     * @param string $remoteIp Remote IP
     * @param string $privateKey Private key
     * @param string $response Response
     * @return mixed
     */
    protected function _getResult($remoteIp, $privateKey, $response)
    {
        return (new Client())->post('https://www.google.com/recaptcha/api/siteverify', [
            'remoteip' => $remoteIp,
            'secret' => $privateKey,
            'response' => $response,
        ]);
    }

    /**
     * Checks for reCAPTCHA
     * @return bool
     * @see https://developers.google.com/recaptcha/docs/verify
     * @uses _getResult()
     * @throws \Cake\Network\Exception\InternalErrorException
     */
    public function check()
    {
        //Gets the form keys
        $keys = Configure::read('Recaptcha.Form');

        //Checks for form keys
        if (empty($keys['public']) || empty($keys['private'])) {
            throw new InternalErrorException(__d('me_tools', 'Form keys are not configured'));
        }

        $controller = $this->getController();
        $response = $controller->request->getData('g-recaptcha-response');

        if (empty($response)) {
            $this->error = __d('me_tools', 'You have not filled out the {0} control', 'reCAPTCHA');

            return false;
        }

        $results = $this->_getResult($controller->request->clientIp(), $keys['private'], $response);

        if (empty($results) || empty($results->json['success'])) {
            $this->error = __d('me_tools', 'It was not possible to verify the {0} control', 'reCAPTCHA');

            return false;
        }

        return true;
    }

    /**
     * Gets the last error
     * @return string Error
     * @uses error
     */
    public function getError()
    {
        if (!isset($this->error)) {
            return false;
        }

        return $this->error;
    }
}

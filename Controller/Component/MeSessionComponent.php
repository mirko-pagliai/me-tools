<?php

/**
 * MeSessionComponent
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Controller\Component
 * @see			http://api.cakephp.org/2.4/class-SessionComponent.html SessionComponent
 */
App::uses('SessionComponent', 'Controller/Component');

/**
 * Provides a way to persist client data between page requests. It acts as a wrapper for the 
 * `$_SESSION` as well as providing convenience methods for several `$_SESSION` related functions.
 * 
 * Rewrites {@link http://api.cakephp.org/2.4/class-SessionComponent.html SessionComponent}.
 * 
 * You should use this component as an alias, for example:
 * <code>
 * public $component = array('Session' => array('className' => 'MeTools.MeSession'));
 * </code>
 */
class MeSessionComponent extends SessionComponent {
    /**
     * Alias for `setFlash()` method
     * @see setFlash()
     */
    public function flash() {
        return call_user_func_array(array('MeSessionComponent', 'setFlash'), func_get_args());
    }

    /**
     * Used to set a session variable that can be used to output messages in the view.
     * @param string $message Flash message
     * @param string $element Element to wrap flash message in
     * @param array $params Parameters to be sent to layout as view variables
     * @param string $key Message key, default is 'flash'
     */
    public function setFlash($message, $element = 'default', $params = array(), $key = 'flash') {
        //Checks if the element exists into MeTools. If so, it uses the MeTools element
        if(file_exists(App::pluginPath('MeTools').'View'.DS.'Elements'.DS.$element.'.ctp'))
            $element = 'MeTools.'.$element;

        parent::setFlash($message, $element, $params, $key);
    }
}
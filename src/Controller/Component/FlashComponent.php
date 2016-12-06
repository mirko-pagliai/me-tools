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
 * @see         http://api.cakephp.org/3.3/class-Cake.Controller.Component.FlashComponent.html FlashComponent
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component\FlashComponent as CakeFlashComponent;

/**
 * Provides a way to set one-time notification messages to be displayed after
 * processing a form or acknowledging data.
 *
 * This class allows the `alert()`, `error()`, `notice()` and `success()`
 * methods are automatically handled by the plugin and rendered dynamically
 * using the `src/Template/Element/Flash/flash.ctp` template.
 *
 * Rewrites the `FlashComponent` class provided by CakePHP.
 */
class FlashComponent extends CakeFlashComponent
{
    /**
     * Magic method for verbose flash methods based on element names.
     * @param string $name Element name to use
     * @param array $args Parameters to pass
     * @return void
     */
    public function __call($name, $args)
    {
        if (!isset($args[1]['plugin']) &&
            in_array($name, ['alert', 'error', 'notice', 'success'])
        ) {
            if (!isset($args[1]['params']['class'])) {
                if ($name === 'alert') {
                    $args[1]['params']['class'] = 'alert-warning';
                } elseif ($name === 'error') {
                    $args[1]['params']['class'] = 'alert-danger';
                } elseif ($name === 'notice') {
                    $args[1]['params']['class'] = 'alert-info';
                } else {
                    $args[1]['params']['class'] = sprintf('alert-%s', $name);
                }
            }

            $name = 'flash';

            $args[1]['plugin'] = METOOLS;
        }

        parent::__call($name, $args);
    }
}

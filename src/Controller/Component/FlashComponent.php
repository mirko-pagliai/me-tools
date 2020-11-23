<?php
declare(strict_types=1);

/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
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
 * @method void alert(string $message, array $options = []) Set a message an "alert" message
 * @method void error(string $message, array $options = []) Set a message an "error" message
 * @method void notice(string $message, array $options = []) Set a message a "notice" message
 * @method void success(string $message, array $options = []) Set a message a "success" message
 */
class FlashComponent extends CakeFlashComponent
{
    /**
     * Magic method for verbose flash methods based on element names.
     * @param string $name Element name to use
     * @param array $args Parameters to pass
     * @return void
     */
    public function __call(string $name, array $args): void
    {
        if (!isset($args[1]['plugin']) && in_array($name, ['alert', 'error', 'notice', 'success'])) {
            if (!isset($args[1]['params']['class'])) {
                switch ($name) {
                    case 'alert':
                        $class = 'alert-warning';
                        break;
                    case 'error':
                        $class = 'alert-danger';
                        break;
                    case 'notice':
                        $class = 'alert-info';
                        break;
                    default:
                        $class = 'alert-' . $name;
                        break;
                }

                $args[1]['params']['class'] = $class;
            }

            $name = 'flash';
            $args[1]['plugin'] = 'MeTools';
        }

        parent::__call($name, $args);
    }
}

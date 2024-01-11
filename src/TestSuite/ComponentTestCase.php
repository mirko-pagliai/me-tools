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
 * @since       2.17.5
 */
namespace MeTools\TestSuite;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;

/**
 * Abstract class for test components
 * @property \Cake\Controller\Component $Component The component instance for which a test is being performed
 */
abstract class ComponentTestCase extends TestCase
{
    /**
     * Get magic method.
     *
     * It provides access to the cached properties of the test.
     * @param string $name Property name
     * @return \Cake\Controller\Component|string
     * @throws \ReflectionException
     */
    public function __get(string $name): Component|string
    {
        if ($name === 'Component') {
            if (empty($this->_cache['Component'])) {
                /** @var \Cake\Controller\Component $Component */
                $Component = new $this->originClassName(new ComponentRegistry(new Controller(new ServerRequest())));
                $Component->initialize([]);
                $this->_cache['Component'] = $Component;
            }

            return $this->_cache['Component'];
        }

        return parent::__get($name);
    }
}

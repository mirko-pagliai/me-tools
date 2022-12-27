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

/**
 * Abstract class for test components
 */
abstract class ComponentTestCase extends TestCase
{
    /**
     * @var \Cake\Controller\Component
     */
    private Component $_Component;

    /**
     * Magic method
     * @param string $name Property name
     * @return \Cake\Controller\Component|void
     * @noinspection PhpRedundantVariableDocTypeInspection
     */
    public function __get(string $name)
    {
        if ($name === 'Component') {
            if (empty($this->_Component)) {
                /** @var class-string<\Cake\Controller\Component> $className */
                $className = $this->getOriginClassNameOrFail($this);
                $this->_Component = new $className(new ComponentRegistry(new Controller()));

                if (method_exists($this->_Component, 'initialize')) {
                    $this->_Component->initialize([]);
                }
            }

            return $this->_Component;
        }
    }
}

<?php
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

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\View\View;

/**
 * A mock trait
 */
trait MockTrait
{
    /**
     * Gets the alias for a controller
     * @param string $className Controller class name
     * @return string
     */
    protected function getControllerAlias($className)
    {
        $parts = explode('\\', $className);

        return substr(array_pop($parts), 0, -10);
    }

    /**
     * Mocks a component
     * @param string $className Component class name
     * @param array|null $methods The list of methods to mock
     * @return \Cake\Controller\Component|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForComponent($className, $methods = [])
    {
        return $this->getMockBuilder($className)
            ->setConstructorArgs([new ComponentRegistry(new Controller)])
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks a controller
     * @param string $className Controller class name
     * @param array|null $methods The list of methods to mock
     * @param string $alias Controller alias
     * @return \Cake\Controller\Controller|\PHPUnit_Framework_MockObject_MockObject
     * @uses getControllerAlias()
     */
    protected function getMockForController($className, $methods = [], $alias = null)
    {
        class_exists($className) ?: $this->fail('Class `' . $className . '` does not exist');
        $alias = $alias ?: $this->getControllerAlias($className);

        return $this->getMockBuilder($className)
            ->setConstructorArgs([null, null, $alias])
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks an helper
     * @param string $className Helper class name
     * @param array|null $methods The list of methods to mock
     * @return \Cake\View\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForHelper($className, $methods = [])
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs([new View])
            ->getMock();
    }

    /**
     * Gets the classname for which a test is being performed, starting from the
     *  test class name.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`
     *  will return the string `MyPlugin\Controller\PagesController`.
     * @param object|string $testClass A test class as object or string
     * @return string The class name for which a test is being performed
     * @since 2.18.0
     */
    public function getOriginClassName($testClass)
    {
        $testClass = is_string($testClass) ? $testClass : get_class($testClass);
        $parts = explode('\\', $testClass);
        array_splice($parts, 1, 2, []);
        $parts[] = substr(array_pop($parts), 0, -4);

        $className = implode('\\', $parts);

        if (!class_exists($className)) {
            $this->fail(sprintf('The original class for the `%s` test class can not be found', $testClass));
        }

        return $className;
    }
}

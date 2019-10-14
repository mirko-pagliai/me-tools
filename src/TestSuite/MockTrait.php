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
     * @throws \ReflectionException
     */
    protected function getControllerAlias(string $className): string
    {
        return substr(get_class_short_name($className), 0, -10);
    }

    /**
     * Mocks a component
     * @param string $className Component class name
     * @param array|null $methods The list of methods to mock
     * @return \Cake\Controller\Component|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForComponent(string $className, ?array $methods = []): object
    {
        return $this->getMockBuilder($className)
            ->setConstructorArgs([new ComponentRegistry(new Controller())])
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks a controller
     * @param string $className Controller class name
     * @param array|null $methods The list of methods to mock
     * @param string|null $alias Controller alias
     * @return \Cake\Controller\Controller|\PHPUnit_Framework_MockObject_MockObject
     * @uses getControllerAlias()
     */
    protected function getMockForController(string $className, ?array $methods = [], ?string $alias = null): object
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
    protected function getMockForHelper(string $className, ?array $methods = []): object
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs([new View()])
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
    public function getOriginClassName($testClass): string
    {
        $testClass = is_string($testClass) ? $testClass : get_class($testClass);

        return preg_replace('/^(.+)Test\\\\TestCase\\\\(.+)Test$/', '\1\2', $testClass);
    }
}

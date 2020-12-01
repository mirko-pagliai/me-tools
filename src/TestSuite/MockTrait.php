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
use PHPUnit\Framework\TestCase;

/**
 * A mock trait
 */
trait MockTrait
{
    /**
     * Internal method to set off a test failure if a class does not exist
     * @param string $className Class name
     * @return void
     * @throw \PHPUnit\Framework\AssertionFailedError
     */
    protected function _classExistsOrFail(string $className): void
    {
        class_exists($className) ?: $this->fail('Class `' . $className . '` does not exist');
    }

    /**
     * Gets the alias name for which a test is being performed, starting from a
     *  class or a `TestCase` class.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`
     *  will return the string `Pages`.
     * @param string|object $class Class name as string or object
     * @return string
     * @since 2.19.9
     * @throw \PHPUnit\Framework\AssertionFailedError If the class does not
     *  exist or if it is impossible to get its alias
     */
    protected function getAlias($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        $this->_classExistsOrFail($class);

        $alias = preg_replace('/^(\w+)(Cell|Controller|Table|Validator)(Test)?$/', '$1', get_class_short_name($class), -1, $count);
        $count ?: $this->fail('Unable to get the alias for the `' . $class . '` class');

        return $alias;
    }

    /**
     * Gets the alias for a controller
     * @param string $className Controller class name
     * @return string
     * @deprecated Use instead `getAlias()`
     */
    protected function getControllerAlias(string $className): string
    {
        deprecationWarning('Deprecated. Use instead `getAlias()`');

        return $this->getAlias($className);
    }

    /**
     * Mocks a component
     * @param string $className Component class name
     * @param array|null $methods The list of methods to mock
     * @return \Cake\Controller\Component|\PHPUnit\Framework\MockObject\MockObject
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
     * @return \Cake\Controller\Controller|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForController(string $className, ?array $methods = [], ?string $alias = null): object
    {
        $alias = $alias ?: $this->getAlias($className);

        return $this->getMockBuilder($className)
            ->setConstructorArgs([null, null, $alias])
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks an helper
     * @param string $className Helper class name
     * @param array|null $methods The list of methods to mock
     * @return \Cake\View\Helper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForHelper(string $className, ?array $methods = []): object
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs([new View()])
            ->getMock();
    }

    /**
     * Gets the class name for which a test is being performed, starting from a
     *  `TestCase` class.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`
     *  will return the string `MyPlugin\Controller\PagesController`.
     * @param \PHPUnit\Framework\TestCase $testClass A `TestCase` class
     * @return string|null The class name for which a test is being performed or
     *  `null` on failure
     * @since 2.18.0
     */
    protected function getOriginClassName(TestCase $testClass): ?string
    {
        $className = preg_replace('/^([\w\\\\]+)Test\\\\TestCase\\\\([\w\\\\]+)Test$/', '$1$2', get_class($testClass), -1, $count);

        return $count ? $className : null;
    }

    /**
     * Gets the class name for which a test is being performed, starting from a
     *  `TestCase` class.
     *
     * It fails if the class cannot be determined or it does not exist.
     * @param \PHPUnit\Framework\TestCase $testClass A `TestCase` class
     * @return string The class name for which a test is being performed
     * @since 2.19.2
     * @throw \PHPUnit\Framework\AssertionFailedError If the class does not
     *  exist or if it is impossible to get its origin class name
     */
    protected function getOriginClassNameOrFail(TestCase $testClass): string
    {
        $className = $this->getOriginClassName($testClass);
        $className ?: $this->fail('Unable to get the classname for the `' . get_class($testClass) . '` class');
        $this->_classExistsOrFail($className);

        return $className;
    }

    /**
     * Gets the classname for which a test is being performed, starting from a
     *  `TestCase` class.
     *
     * Example: class `MyPlugin\MySubNamespace\Test\TestCase\MyExampleTest`
     *  will return the string `MyPlugin/MySubNamespace`.
     * @param \PHPUnit\Framework\TestCase $testClass A `TestCase` class
     * @return string
     * @since 2.19.9
     */
    protected function getPluginName(TestCase $testClass): string
    {
        $className = get_class($testClass);

        return str_replace('\\', '/', substr($className, 0, strpos($className, '\\Test\\TestCase')));
    }
}

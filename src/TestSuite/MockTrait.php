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

use Cake\View\Helper;
use Cake\View\View;
use PHPUnit\Framework\TestCase;

/**
 * A mock trait
 */
trait MockTrait
{
    /**
     * Internal method to set off a test failure if a class does not exist
     * @param class-string|string $className Class name
     * @return class-string
     * @throw \PHPUnit\Framework\AssertionFailedError
     */
    protected function _classExistsOrFail(string $className): string
    {
        if (!class_exists($className)) {
            $this->fail('Class `' . $className . '` does not exist');
        }

        return $className;
    }

    /**
     * Gets the alias name for which a test is being performed, starting from a class or a `TestCase` class.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`  will return `Pages`.
     * @param class-string|object $class Class name or object
     * @return string
     * @throws \PHPUnit\Framework\AssertionFailedError|\ReflectionException
     * @since 2.19.9
     */
    protected function getAlias($class): string
    {
        $class = is_object($class) ? get_class($class) : $this->_classExistsOrFail($class);
        $alias = preg_replace('/^(\w+)(Cell|Controller|Table|Validator)(Test)?$/', '$1', get_class_short_name($class), -1, $count);

        if (!$alias || !$count) {
            $this->fail('Unable to get the alias for the `' . $class . '` class');
        }

        return $alias;
    }

    /**
     * Mocks an helper
     * @param class-string<\Cake\View\Helper> $className Helper class name
     * @param array<string> $methods The list of methods to mock
     * @param \Cake\View\View|null $View A `View` instance
     * @return \Cake\View\Helper&\PHPUnit\Framework\MockObject\MockObject
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    protected function getMockForHelper(string $className, array $methods = [], ?View $View = null): Helper
    {
        return $this->getMockBuilder($className)
            ->onlyMethods($methods)
            ->setConstructorArgs([$View ?: new View()])
            ->getMock();
    }

    /**
     * Gets the class name for which a test is being performed, starting from a `TestCase` class.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest` will return `MyPlugin\Controller\PagesController`.
     * @param \PHPUnit\Framework\TestCase $className A `TestCase` class
     * @return class-string<\PHPUnit\Framework\TestCase>|string The class name for which a test is being performed or
     *  empty string on failure
     * @since 2.18.0
     */
    protected function getOriginClassName(TestCase $className): string
    {
        $className = preg_replace('/^([\w\\\\]+)Test\\\\TestCase\\\\([\w\\\\]+)Test$/', '$1$2', get_class($className), -1, $count);

        return $className && $count ? $className : '';
    }

    /**
     * Gets the class name for which a test is being performed, starting from a `TestCase` class.
     *
     * It fails if the class cannot be determined or does not exist.
     * @param \PHPUnit\Framework\TestCase $className A `TestCase` class
     * @return class-string The class name for which a test is being performed
     * @since 2.19.2
     * @throw \PHPUnit\Framework\AssertionFailedError
     */
    protected function getOriginClassNameOrFail(TestCase $className): string
    {
        return $this->_classExistsOrFail($this->getOriginClassName($className));
    }

    /**
     * Gets the table class name from an alias
     * @param string $alias Alias name
     * @param string $plugin Plugin name. If left blank, it will be self-determined
     * @return string
     * @since 2.19.9
     */
    protected function getTableClassNameFromAlias(string $alias, string $plugin = ''): string
    {
        $plugin = str_replace('/', '\\', $plugin ?: $this->getPluginName($this));

        return $plugin . '\\Model\\Table\\' . $alias . 'Table';
    }

    /**
     * Gets the classname for which a test is being performed, starting from a `TestCase` class.
     *
     * Example: class `MyPlugin\MySubNamespace\Test\TestCase\MyExampleTest` will return `MyPlugin/MySubNamespace`.
     * @param \PHPUnit\Framework\TestCase $testClass A `TestCase` class
     * @return string
     * @since 2.19.9
     */
    protected function getPluginName(TestCase $testClass): string
    {
        $className = get_class($testClass);

        return str_replace('\\', '/', substr($className, 0, strpos($className, '\\Test\\TestCase') ?: 0));
    }
}

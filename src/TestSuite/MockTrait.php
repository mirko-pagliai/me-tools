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

/**
 * A mock trait
 */
trait MockTrait
{
    /**
     * Gets the alias name for which a test is being performed, starting from a `TestCase` instance.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`  will return `Pages`.
     * @param \MeTools\TestSuite\TestCase $class `TestCase` instance
     * @return string The alias name for which a test is being performed
     * @throws \PHPUnit\Framework\AssertionFailedError
     * @throws \ReflectionException
     * @since 2.19.9
     */
    protected function getAlias(TestCase $class): string
    {
        $alias = preg_replace('/^(\w+)(Cell|Controller|Helper|Table|Validator|View)Test$/', '$1', get_class_short_name($class), -1, $count);
        if (!$alias || !$count) {
            $this->fail('Unable to get the alias for `' . get_class($class) . '`');
        }

        return $alias;
    }

    /**
     * Mocks an helper
     * @param class-string<\Cake\View\Helper> $className Helper class name
     * @param array<int, non-empty-string> $methods The list of methods to mock
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
     * @param \MeTools\TestSuite\TestCase $className A `TestCase` instance
     * @return class-string The class name for which a test is being performed
     * @since 2.19.2
     * @throw \PHPUnit\Framework\AssertionFailedError
     */
    protected function getOriginClassName(TestCase $className): string
    {
        $originClassName = preg_replace('/^([\w\\\\]+)Test\\\\TestCase\\\\([\w\\\\]+)Test$/', '$1$2', get_class($className), -1, $count);

        if (!$originClassName || !$count) {
            $this->fail('Unable to determine the origin class for `' . get_class($className) . '`');
        } elseif (!class_exists($originClassName)) {
            $this->fail('Class `' . $originClassName . '` does not exist');
        }

        return $originClassName;
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
     * Gets the plugin name for which a test is being performed, starting from a `TestCase` class.
     *
     * Example: class `MyPlugin\MySubNamespace\Test\TestCase\MyExampleTest` will return `MyPlugin/MySubNamespace`.
     * @param \MeTools\TestSuite\TestCase $testClass A `TestCase` instance
     * @return string The plugin name for which a test is being performed
     * @since 2.19.9
     */
    protected function getPluginName(TestCase $testClass): string
    {
        $className = get_class($testClass);

        return str_replace('\\', '/', substr($className, 0, strpos($className, '\\Test\\TestCase') ?: 0));
    }
}

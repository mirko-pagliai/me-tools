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
}

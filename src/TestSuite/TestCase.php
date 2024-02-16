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
 * @since       2.14.0
 */
namespace MeTools\TestSuite;

use Cake\TestSuite\TestCase as CakeTestCase;
use Tools\TestSuite\TestTrait;

/**
 * TestCase class
 */
abstract class TestCase extends CakeTestCase
{
    use TestTrait;

    /**
     * The alias name for which a test is being performed
     * @var string
     */
    protected string $alias;

    /**
     * The class name for which a test is being performed
     * @var class-string<\MeTools\TestSuite\TestCase>
     */
    protected string $originClassName;

    /**
     * Asserts log file contents
     * @param string $expectedContent The expected contents
     * @param string $filename Log filename
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public function assertLogContains(string $expectedContent, string $filename, string $message = ''): void
    {
        $this->assertFileIsReadable($filename);
        $this->assertStringContainsString($expectedContent, file_get_contents($filename) ?: '', $message);
    }

    /**
     * Gets the alias name for which a test is being performed, starting from a `TestCase` instance.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`  will return `Pages`.
     * @return string The alias name for which a test is being performed
     * @throws \PHPUnit\Framework\AssertionFailedError
     * @throws \ReflectionException
     * @since 2.19.9
     */
    protected function getAlias(): string
    {
        if (empty($this->alias)) {
            $this->alias = preg_replace('/^(\w+)(Cell|Controller|Helper|Table|Validator|View)Test$/', '$1', get_class_short_name($this), -1, $count) ?: '';
            if (!$this->alias || !$count) {
                $this->fail('Unable to get the alias for `' . get_class($this) . '`');
            }
        }

        return $this->alias;
    }

    /**
     * Gets the class name for which a test is being performed, starting from a `TestCase` class.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest` will return `MyPlugin\Controller\PagesController`.
     * @return class-string The class name for which a test is being performed
     * @since 2.19.2
     * @throw \PHPUnit\Framework\AssertionFailedError
     */
    protected function getOriginClassName(): string
    {
        if (empty($this->originClassName)) {
            /** @var class-string<\MeTools\TestSuite\TestCase> $originClassName */
            $originClassName = preg_replace('/^([\w\\\\]+)Test\\\\TestCase\\\\([\w\\\\]+)Test$/', '$1$2', get_class($this), -1, $count) ?: '';

            if (!$originClassName || !$count) {
                $this->fail('Unable to determine the origin class for `' . get_class($this) . '`');
            } elseif (!class_exists($originClassName)) {
                $this->fail('Class `' . $originClassName . '` does not exist');
            }

            $this->originClassName = $originClassName;
        }

        return $this->originClassName;
    }
}

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
 * @since       2.14.0
 */
namespace MeTools\TestSuite\Traits;

use Traversable;

/**
 * This trait provides some useful methods for `TestCase` and
 *  `IntegrationTestCase` classes
 */
trait TestCaseTrait
{
    /**
     * Asserts that the array keys are equal to `$expected`
     * @param array $expected Expected keys
     * @param array $array Array to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertArrayKeysEqual($expected, $array, $message = '')
    {
        $this->assertIsArray($array);
        $this->assertEquals($expected, array_keys($array), $message);
    }

    /**
     * Asserts that a filename exists
     * @param string|array|Traversable $filename Filename or array/Traversable
     *  of filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileExists($filename, $message = '')
    {
        if (is_array($filename) || $filename instanceof Traversable) {
            foreach ($filename as $var) {
                parent::assertFileExists($var, $message);
            }

            return;
        }

        parent::assertFileExists($filename, $message);
    }

    /**
     * Asserts that a filename not exists
     * @param string|array|Traversable $filename Filename or array/Traversable
     *  of filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileNotExists($filename, $message = '')
    {
        if (is_array($filename) || $filename instanceof Traversable) {
            foreach ($filename as $var) {
                parent::assertFileNotExists($var, $message);
            }

            return;
        }

        parent::assertFileNotExists($filename, $message);
    }

    /**
     * Asserts that `$actual` is an instance of `$expected`
     * @param string $expected Expected namespace
     * @param array|Traversable|mixed $actual Instance or array/Traversable of
     *  instances
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertInstanceOf($expected, $actual, $message = '')
    {
        if ((is_array($actual) || $actual instanceof Traversable) &&
            !$actual instanceof \Cake\Validation\Validator
        ) {
            foreach ($actual as $var) {
                parent::assertInstanceOf($expected, $var, $message);
            }

            return;
        }

        parent::assertInstanceOf($expected, $actual, $message);
    }

    /**
     * Asserts that a variable is an array
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertIsArray($var, $message = '')
    {
        $this->assertTrue(is_array($var), $message);
    }

    /**
     * Asserts that a variable is an object
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertIsObject($var, $message = '')
    {
        $this->assertTrue(is_object($var), $message);
    }

    /**
     * Asserts that a variable is a string
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertIsString($var, $message = '')
    {
        $this->assertTrue(is_string($var), $message);
    }

    /**
     * Asserts log file contents
     * @param string $expected The expected contents
     * @param string $name Log name
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertLogContains($expected, $name, $message = '')
    {
        $file = LOGS . $name . '.log';

        if (!is_readable($file)) {
            $this->fail('Log file ' . $file . ' not readable');
        }

        $content = trim(file_get_contents($file));

        $this->assertContains($expected, $content, $message);
    }

    /**
     * Asserts that the object properties are equal to `$expected`
     * @param array $expected Expected keys
     * @param array $object Ojbect to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertObjectPropertiesEqual($expected, $object, $message = '')
    {
        $this->assertIsObject($object);
        $this->assertEquals($expected, array_keys((array)$object), $message);
    }

    /**
     * Deletes all logs file
     * @return void
     */
    public function deleteAllLogs()
    {
        foreach (glob(LOGS . '*') as $file) {
            //@codingStandardsIgnoreLine
            @unlink($file);
        }
    }

    /**
     * Deletes a log file
     * @param string $name Log name
     * @return void
     */
    public function deleteLog($name)
    {
        //@codingStandardsIgnoreLine
        @unlink(LOGS . $name . '.log');
    }
}

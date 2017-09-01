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
 */
namespace MeTools\Test\TestCase\TestSuite;

use MeTools\TestSuite\TestCase;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    /**
     * Tests for `assertArrayKeysEqual` method
     * @test
     */
    public function testAssertArrayKeysEqual()
    {
        $array = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertArrayKeysEqual(['key1', 'key2'], $array);
    }

    /**
     * Tests for `assertFileExists` method
     * @test
     */
    public function testAssertFileExists()
    {
        $files = [tempnam(TMP, 'foo'), tempnam(TMP, 'foo')];
        $this->assertFileExists($files[0]);
        $this->assertFileExists($files);

        //@codingStandardsIgnoreStart
        @unlink($files[0]);
        @unlink($files[1]);
        //@codingStandardsIgnoreEnd
    }

    /**
     * Tests for `assertFileNotExists` method
     * @test
     */
    public function testAssertFileNotExists()
    {
        $files = [TMP . 'noExisting1', TMP . 'noExisting2'];
        $this->assertFileNotExists($files[0]);
        $this->assertFileNotExists($files);
    }

    /**
     * Tests for `assertInstanceOf` method
     * @test
     */
    public function testAssertInstanceOf()
    {
        $this->assertInstanceOf('stdClass', new \stdClass);
        $this->assertInstanceOf('stdClass', [new \stdClass]);
    }

    /**
     * Tests for `assertIsArray` method
     * @test
     */
    public function testAssertIsArray()
    {
        $this->assertIsArray([]);
        $this->assertIsArray([true]);
    }

    /**
     * Tests for `assertIsObject` method
     * @test
     */
    public function testAssertIsObject()
    {
        $this->assertIsObject(new \stdClass);
        $this->assertIsObject((object)[]);
    }

    /**
     * Tests for `assertIsString` method
     * @test
     */
    public function testAssertIsString()
    {
        $this->assertIsString('string');
    }

    /**
     * Tests for `assertLogContains` method
     * @test
     */
    public function testAssertLogContains()
    {
        $string = 'cat dog bird';
        $file = LOGS . 'debug.log';
        file_put_contents($file, $string);

        foreach (explode(' ', $string) as $word) {
            $this->assertLogContains($word, 'debug');
        }

        //@codingStandardsIgnoreLine
        @unlink($file);
    }

    /**
     * Tests for `assertLogContains` method, with a no existing log
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @expectedExceptionMessage Log file /tmp/me_tools/cakephp_log/noExisting.log not readable
     * @test
     */
    public function testAssertLogContainsNoExistingLog()
    {
        $this->assertLogContains('content', 'noExisting');
    }

    /**
     * Tests for `assertObjectPropertiesEqual` method
     * @test
     */
    public function testAssertObjectPropertiesEqual()
    {
        $object = new \stdClass;
        $object->first = 'first value';
        $object->second = 'second value';
        $this->assertObjectPropertiesEqual(['first', 'second'], $object);

        $array = ['first' => 'one', 'second' => 'two'];
        $this->assertObjectPropertiesEqual(['first', 'second'], (object)$array);
    }

    /**
     * Tests for `deleteAllLogs` method
     * @test
     */
    public function testDeleteAllLogs()
    {
        file_put_contents(LOGS . 'first.log', null);
        file_put_contents(LOGS . 'first.log', null);

        $this->deleteAllLogs();
        $this->assertFileNotExists(LOGS . 'first.log');
        $this->assertFileNotExists(LOGS . 'first.log');
    }

    /**
     * Tests for `deleteLog` method
     * @test
     */
    public function testDeleteLog()
    {
        file_put_contents(LOGS . 'first.log', null);
        file_put_contents(LOGS . 'first.log', null);

        $this->deleteLog('first');
        $this->deleteLog('second');
        $this->assertFileNotExists(LOGS . 'first.log');
        $this->assertFileNotExists(LOGS . 'first.log');
    }
}

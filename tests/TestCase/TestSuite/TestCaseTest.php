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
use PHPUnit\Framework\AssertionFailedError;
use Tools\ReflectionTrait;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests for `getLogFullPath()` method
     * @test
     */
    public function testGetLogFullPath()
    {
        $expected = LOGS . 'debug.log';

        foreach ([
            'debug',
            'debug.log',
            LOGS . 'debug',
            $expected,
        ] as $filename) {
            $this->assertEquals($expected, $this->invokeMethod($this, 'getLogFullPath', [$filename]));
        }
    }

    /**
     * Tests for `assertLogContains()` method
     * @test
     */
    public function testAssertLogContains()
    {
        $string = 'cat dog bird';
        $file = LOGS . 'debug.log';
        safe_create_file($file, $string);

        foreach (explode(' ', $string) as $word) {
            $this->assertLogContains($word, $file);
        }

        //With a no existing log
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('File or directory `' . $this->getLogFullPath('noExisting') . '` is not readable');
        $this->assertLogContains('content', 'noExisting');
    }

    /**
     * Tests for `deleteLog()` method
     * @test
     */
    public function testDeleteLog()
    {
        safe_create_file(LOGS . 'first.log');
        safe_create_file(LOGS . 'second.log');
        $this->deleteLog('first');
        $this->deleteLog('second');
        $this->assertFileNotExists(LOGS . 'first.log');
        $this->assertFileNotExists(LOGS . 'second.log');
    }
}

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

        safe_unlink($file);
    }

    /**
     * Tests for `assertLogContains` method, with a no existing log
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @expectedExceptionMessageRegExp /^File or directory `[\w\d_\/\\:]+noExisting.log` is not readable$/
     * @test
     */
    public function testAssertLogContainsNoExistingLog()
    {
        $this->assertLogContains('content', 'noExisting');
    }

    /**
     * Tests for `deleteLog` method
     * @test
     */
    public function testDeleteLog()
    {
        file_put_contents(LOGS . 'first.log', null);
        file_put_contents(LOGS . 'second.log', null);

        $this->deleteLog('first');
        $this->deleteLog('second');
        $this->assertFileNotExists(LOGS . 'first.log');
        $this->assertFileNotExists(LOGS . 'second.log');
    }
}

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
namespace MeTools\Test\TestCase\TestSuite\Traits;

use Cake\TestSuite\TestCase;
use MeTools\TestSuite\Traits\LogsMethodsTrait;

/**
 * LogsMethodsTraitTest class
 */
class LogsMethodsTraitTest extends TestCase
{
    use LogsMethodsTrait;

    /**
     * Test for `assertLogContains()` method
     * @test
     */
    public function testAssertLogContains()
    {
        $log = LOGS . 'debug.log';
        $expected = 'a simple log';

        file_put_contents($log, $expected);
        $this->assertLogContains($expected, 'debug');

        //@codingStandardsIgnoreLine
        @unlink($log);
    }

    /**
     * Test for `assertLogContains()` method on failure
     * @expectedException \PHPUnit\Framework\AssertionFailedError
     * @expectedExceptionMessage Log file /tmp/cakephp_log/noExisting.log not readable
     * @test
     */
    public function testAssertLogContainsFailure()
    {
        $this->assertLogContains('value', 'noExisting');
    }

    /**
     * Test for `deleteAllLogs()` method
     * @test
     */
    public function testDeleteAllLogs()
    {
        $logs = [LOGS . 'debug.log', LOGS . 'error.log'];

        foreach ($logs as $log) {
            file_put_contents($log, null);
        }

        $this->deleteAllLogs();

        foreach ($logs as $log) {
            $this->assertFileNotExists($log);
        }
    }

    /**
     * Test for `deleteLog()` method
     * @test
     */
    public function testDeleteLog()
    {
        $log = LOGS . 'debug.log';

        file_put_contents($log, null);

        $this->deleteLog('debug');

        $this->assertFileNotExists($log);
    }
}

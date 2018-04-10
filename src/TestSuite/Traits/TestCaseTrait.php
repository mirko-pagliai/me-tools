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

use Tools\TestSuite\TestCaseTrait as ToolsTestCaseTrait;

/**
 * This trait provides some useful methods for `TestCase` and
 *  `IntegrationTestCase` classes
 */
trait TestCaseTrait
{
    use ToolsTestCaseTrait;

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

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

use Cake\Filesystem\Folder;
use Exception;
use Tools\ReflectionTrait;
use Tools\TestSuite\TestCaseTrait as ToolsTestCaseTrait;

/**
 * This trait provides some useful methods for `TestCase` and
 *  `IntegrationTestCase` classes
 */
trait TestCaseTrait
{
    use ReflectionTrait;
    use ToolsTestCaseTrait;

    /**
     * Asserts log file contents
     * @param string $expectedContent The expected contents
     * @param string $file Log name
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertLogContains($expectedContent, $file, $message = '')
    {
        if (!pathinfo($file, PATHINFO_EXTENSION)) {
            $file .= '.log';
        }

        if (!Folder::isAbsolute($file)) {
            $file = LOGS . $file;
        }

        try {
            is_readable_or_fail($file);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertContains($expectedContent, file_get_contents($file), $message);
    }

    /**
     * Deletes a log file
     * @param string $logName Log name
     * @return void
     * @todo `$logName` could be an absolute path or have an extension
     */
    public function deleteLog($logName)
    {
        safe_unlink(LOGS . $logName . '.log');
    }
}

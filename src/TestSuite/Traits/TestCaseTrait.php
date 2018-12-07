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

use Cake\Core\Plugin;
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
     * Internal method to get a log full path
     * @param string $filename Log filename
     * @return string
     * @since 2.16.10
     */
    protected function getLogFullPath($filename)
    {
        if (!pathinfo($filename, PATHINFO_EXTENSION)) {
            $filename .= '.log';
        }

        return Folder::isAbsolute($filename) ? $filename : LOGS . $filename;
    }

    /**
     * Asserts log file contents
     * @param string $expectedContent The expected contents
     * @param string $filename Log filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @uses getLogFullPath()
     */
    public function assertLogContains($expectedContent, $filename, $message = '')
    {
        $filename = $this->getLogFullPath($filename);

        try {
            is_readable_or_fail($filename);
            $content = file_get_contents($filename);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertContains($expectedContent, $content, $message);
    }

    /**
     * Deletes a log file
     * @param string $filename Log filename
     * @return void
     * @uses getLogFullPath()
     */
    public function deleteLog($filename)
    {
        safe_unlink($this->getLogFullPath($filename));
    }

    /**
     * Checks if there's a plugin in the global plugin collaction
     * @param string $plugin Plugin you want to check
     * @return bool
     * @since 2.18.0
     */
    public function hasPlugin($plugin)
    {
        return Plugin::getCollection()->has($plugin);
    }
}

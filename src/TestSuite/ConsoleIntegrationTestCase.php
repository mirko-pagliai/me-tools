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
 * @since       2.15.0
 */
namespace MeTools\TestSuite;

use Cake\Console\Shell as CakeShell;
use Cake\TestSuite\ConsoleIntegrationTestCase as CakeConsoleIntegrationTestCase;
use MeTools\Console\Shell;
use MeTools\TestSuite\Traits\MockTrait;
use MeTools\TestSuite\Traits\TestCaseTrait;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * ConsoleIntegrationTestCase class
 */
abstract class ConsoleIntegrationTestCase extends CakeConsoleIntegrationTestCase
{
    use MockTrait;
    use TestCaseTrait;

    /**
     * Shell instance
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Shell;

    /**
     * If `true`, a mock instance of the shell will be created
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * Called before every test method
     * @return void
     * @uses $Shell
     * @uses $autoInitializeClass
     */
    public function setUp()
    {
        parent::setUp();

        if (!$this->Shell && $this->autoInitializeClass) {
            $parts = explode('\\', get_class($this));
            array_splice($parts, 1, 2, []);
            $parts[] = substr(array_pop($parts), 0, -4);
            $className = implode('\\', $parts);

            $this->Shell = $this->getMockForShell($className);
        }
    }

    /**
     * Gets all shell methods.
     *
     * It excludes the `main` method.
     * @param array $exclude Other methods you want to exclude
     * @return array
     * @uses $Shell
     */
    protected function getShellMethods(array $exclude = [])
    {
        !empty($this->Shell) ?: $this->fail('The property `$this->Shell` has not been set');

        $class = $this->Shell instanceof MockObject ? get_parent_class($this->Shell) : $this->Shell;
        $parentClass = get_parent_class($class);
        $methods = get_child_methods($class);

        if (!in_array($parentClass, [CakeShell::class, Shell::class])) {
            $methods = array_merge($methods, get_child_methods($parentClass));
        }

        $methods = array_diff($methods, array_merge(['main'], $exclude));
        sort($methods);

        return $methods;
    }

    /**
     * Gets a table from output
     * @return array Headers and rows
     * @uses $_out
     */
    protected function getTableFromOutput()
    {
        $regexRowDivider = '[\+\-]+';
        $regexHeader = '(\|(\s+<info>[^<]+<\/info>\s+\|)+)';
        $regexRow = '\|(\s+[^\|]+\s+\|)+';
        $regexRows = '((' . $regexRow . '\v)+)';
        $regexTable = $regexRowDivider . '\v' . $regexHeader . '\v' . $regexRowDivider . '\v' . $regexRows . $regexRowDivider;
        $output = implode(PHP_EOL, $this->_out->messages());

        preg_match('/' . $regexTable . '/', $output, $matches) ?: $this->fail('Unable to retrieve a table output');

        $regexColumnDivider = '\s*\|\s*';
        $headers = array_values(array_map(function ($header) {
            return preg_replace('/<info>([^<]+)<\/info>/', '$1', $header);
        }, array_filter(preg_split('/' . $regexColumnDivider . '/', $matches[1]))));
        $rows = array_values(array_map(function ($row) use ($regexColumnDivider) {
            $row = preg_split('/' . $regexColumnDivider . '/', $row);
            $row = array_filter($row, function ($row) {
                return in_array($row, [0, '0'], true) || !empty($row);
            });

            return array_values($row);
        }, array_filter(explode(PHP_EOL, $matches[3]))));

        return compact('headers', 'rows');
    }

    /**
     * Asserts shell exited with the error code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithError($message = '')
    {
        $this->assertExitCode(Shell::CODE_ERROR, $message);
    }

    /**
     * Asserts shell exited with the success code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithSuccess($message = '')
    {
        $this->assertExitCode(Shell::CODE_SUCCESS, $message);
    }

    /**
     * Asserts that a table has headers
     * @param array $expected Expected headers values
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     * @uses getTableFromOutput()
     */
    public function assertTableHeadersEquals($expected, $message = '')
    {
        list($headers) = array_values($this->getTableFromOutput());

        $this->assertEquals($expected, $headers, $message);
    }

    /**
     * Asserts that a table has rows
     * @param array $expected Expected rows values
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     * @uses getTableFromOutput()
     */
    public function assertTableRowsEquals($expected, $message = '')
    {
        list(, $rows) = array_values($this->getTableFromOutput());

        $this->assertEquals($expected, $rows, $message);
    }
}

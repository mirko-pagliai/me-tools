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

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase as CakeTestCase;
use Throwable;
use Tools\Exceptionist;
use Tools\Filesystem;
use Tools\TestSuite\TestTrait;

/**
 * TestCase class
 * @property string $alias The alias name for which a test is being performed
 * @property class-string $originClassName The class name for which a test is being performed
 */
abstract class TestCase extends CakeTestCase
{
    use MockTrait;
    use TestTrait;

    /**
     * @var array
     */
    protected array $_cache = [];

    /**
     * Get magic method.
     *
     * It provides access to the cached properties of the test.
     * @param string $name Property name
     * @return mixed
     * @since 2.23.0
     * @throws \ReflectionException
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'alias':
                if (empty($this->_cache['alias'])) {
                    $this->_cache['alias'] = $this->getAlias($this);
                }

                return $this->_cache['alias'];
            case 'originClassName':
                if (empty($this->_cache['originClassName'])) {
                    $this->_cache['originClassName'] = $this->getOriginClassName($this);
                }

                return $this->_cache['originClassName'];
        }

        $this->fail('Property `' . $name . '` does not exist');
    }

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadPlugins(Configure::read('pluginsToLoad', ['MeTools' => []]));
    }

    /**
     * This method is called after the last test of this test class is run
     * @return void
     * @codeCoverageIgnore
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        Filesystem::unlinkRecursive(LOGS, '.gitkeep', true);
    }

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
     * Asserts a sql query string ends not with `$suffix`
     * @param string $suffix Suffix
     * @param string $sql Sql query string
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     * @since 2.20.7
     */
    protected function assertSqlEndsNotWith(string $suffix, string $sql, string $message = ''): void
    {
        $this->assertStringEndsNotWith(str_replace('`', '', $suffix), str_replace('`', '', $sql), $message);
    }

    /**
     * Asserts a sql query string ends with `$suffix`
     * @param string $suffix Suffix
     * @param string $sql Sql query string
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     * @since 2.20.7
     */
    protected function assertSqlEndsWith(string $suffix, string $sql, string $message = ''): void
    {
        $this->assertStringEndsWith(str_replace('`', '', $suffix), str_replace('`', '', $sql), $message);
    }

    /**
     * Deletes a log file
     * @param string $filename Log filename
     * @return void
     * @deprecated 2.24.1
     * @codeCoverageIgnore
     */
    public function deleteLog(string $filename): void
    {
        deprecationWarning('`TestCase::deleteLog()` is deprecated and will be removed in a later release');

        unlink($this->getLogFullPath($filename));
    }

    /**
     * Internal method to get a log full path
     * @param string $filename Log filename
     * @return string
     * @since 2.16.10
     * @deprecated 2.24.1
     * @codeCoverageIgnore
     */
    protected function getLogFullPath(string $filename): string
    {
        deprecationWarning('`TestCase::getLogFullPath()` is deprecated and will be removed in a later release');

        return Filesystem::makePathAbsolute($filename . Filesystem::getExtension($filename) ? '' : '.log', LOGS);
    }

    /**
     * Get a table instance from the registry
     * @param string $alias The alias name you want to get
     * @param array $options The options you want to build the table with
     * @return \Cake\ORM\Table
     * @since 2.18.11
     */
    protected function getTable(string $alias, array $options = []): Table
    {
        $this->getTableLocator()->clear();

        return $this->getTableLocator()->get($alias, $options);
    }
}

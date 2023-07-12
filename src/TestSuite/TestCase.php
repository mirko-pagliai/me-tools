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

use Cake\ORM\Table;
use Cake\TestSuite\TestCase as CakeTestCase;
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

        if (method_exists($this, 'enableCsrfToken')) {
            $this->enableCsrfToken();
        }
        if (method_exists($this, 'enableRetainFlashMessages')) {
            $this->enableRetainFlashMessages();
        }
    }

    /**
     * Asserts log file contents
     * @param string $expectedContent The expected contents
     * @param string $filename Log filename
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     * @todo Can be deprecated?
     */
    public function assertLogContains(string $expectedContent, string $filename, string $message = ''): void
    {
        $this->assertFileIsReadable($filename);
        $this->assertStringContainsString($expectedContent, file_get_contents($filename) ?: '', $message);
    }

    /**
     * Get a table instance from the registry
     * @param string $alias The alias name you want to get
     * @param array $options The options you want to build the table with
     * @return \Cake\ORM\Table
     * @since 2.18.11
     * @deprecated 2.25.0 will be removed in a later release
     * @codeCoverageIgnore
     */
    protected function getTable(string $alias, array $options = []): Table
    {
        deprecationWarning();

        $this->getTableLocator()->clear();

        return $this->getTableLocator()->get($alias, $options);
    }
}

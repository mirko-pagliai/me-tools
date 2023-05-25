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
 * @since       2.23.0
 */
namespace MeTools\TestSuite;

use Cake\Command\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Console\TestSuite\StubConsoleOutput;
use PHPUnit\Framework\MockObject\MockObject;
use Tools\Filesystem;

/**
 * Abstract class for test commands
 * @property \MeTools\Command\Command $Command The command instance for which a test is being performed
 * @property class-string<\MeTools\Command\Command> $originClassName The class name for which a test is being performed
 */
abstract class CommandTestCase extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Get magic method.
     *
     * It provides access to the cached properties of the test.
     * @param string $name Property name
     * @return \MeTools\Command\Command|void
     * @throws \ReflectionException
     */
    public function __get(string $name)
    {
        if ($name === 'Command') {
            if (empty($this->_cache['Command'])) {
                /** @var \MeTools\Command\Command $Command */
                $Command = new $this->originClassName();
                $Command->initialize();
                $this->_cache['Command'] = $Command;
            }

            return $this->_cache['Command'];
        }

        return parent::__get($name);
    }

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->useCommandRunner();
    }
}

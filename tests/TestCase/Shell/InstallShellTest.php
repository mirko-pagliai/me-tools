<?php
namespace MeTools\Test\TestCase\Shell;

use Cake\TestSuite\TestCase;
use MeTools\Shell\InstallShell;

/**
 * MeTools\Shell\InstallShell Test Case
 */
class InstallShellTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMock('Cake\Console\ConsoleIo');
        $this->Install = new InstallShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Install);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

<?php
/** @noinspection PhpUnhandledExceptionInspection */
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
 */
namespace MeTools\Test\TestCase\Command\Install;

use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\StubConsoleOutput;
use MeTools\Command\Install\CreateVendorsLinksCommand;
use MeTools\Core\Configure;
use MeTools\TestSuite\CommandTestCase;
use Tools\Filesystem;

/**
 * CreateVendorsLinksCommandTest class
 */
class CreateVendorsLinksCommandTest extends CommandTestCase
{
    /**
     * Internal method to run the `CreateVendorsLinksCommand`.
     *
     * Resets the `ConsoleOutput` object for stdout and stderr and the exit code.
     * @return void
     */
    protected function runCommand(): void
    {
        $this->_out = new StubConsoleOutput();
        $this->_err = new StubConsoleOutput();
        $Command = new CreateVendorsLinksCommand();
        $this->_exitCode = $Command->run(['-v'], new ConsoleIo($this->_out, $this->_err));
    }

    /**
     * @test
     * @uses \MeTools\Command\Install\CreateVendorsLinksCommand::execute()
     */
    public function testExecute(): void
    {
        $Filesystem = new Filesystem();

        /**
         * Runs with an origin file that doesn't exist
         */
        Configure::write('MeTools.VendorLinks', ['subDir/noExisting' => 'cakephp']);
        $this->runCommand();
        $this->assertExitSuccess();
        $this->assertOutputContains('File or directory `' . rtr(VENDOR . 'subDir' . DS . 'noExisting') . '` does not exist');
        $this->assertErrorEmpty();

        /**
         * Runs and creates a link.
         *
         * We cannot use the `exec()` method, but we have to create an instance of the command to set a different
         * configuration value for `MeTools.VendorLinks`
         */
        $expectedOrigin = VENDOR . 'cakephp' . DS . 'cakephp';
        $expectedTarget = WWW_VENDOR . 'cakephp';
        $this->assertFileExists($expectedOrigin);
        $this->assertFileDoesNotExist($expectedTarget);
        Configure::write('MeTools.VendorLinks', ['cakephp/cakephp' => 'cakephp']);
        $this->runCommand();
        $this->assertExitSuccess();
        $this->assertOutputContains('Link to `' . rtr($expectedTarget) . '` has been created');
        $this->assertErrorEmpty();
        $this->assertFileExists($expectedTarget);
        $this->assertSame($expectedOrigin, readlink($expectedTarget));

        /**
         * Runs again.
         * Link already exists.
         */
        $this->runCommand();
        $this->assertExitSuccess();
        $this->assertOutputContains('Link to `' . rtr($expectedTarget) . '` already exists');
        $this->assertErrorEmpty();
        $Filesystem->remove($expectedTarget);

        /**
         * Runs again.
         * Link already exists, but with a different (BAD) target. Then the link will be recreated.
         */
        $Filesystem->symlink($Filesystem->createTmpFile(), $expectedTarget);
        $this->assertFileExists($expectedTarget);
        $this->assertNotSame($expectedOrigin, readlink($expectedTarget));
        $this->runCommand();
        $this->assertExitSuccess();
        $this->assertOutputContains('Link to `' . rtr($expectedTarget) . '` has been created');
        $this->assertErrorEmpty();
        $this->assertFileExists($expectedTarget);
        $this->assertSame($expectedOrigin, readlink($expectedTarget));
        $Filesystem->remove($expectedTarget);

        /**
         * `WWW_VENDOR` directory does not exist
         */
        $wwwVendorBkp = dirname(WWW_VENDOR) . DS . 'vendor.bkp';
        $Filesystem->rename(WWW_VENDOR, $wwwVendorBkp);
        $this->runCommand();
        $Filesystem->rename($wwwVendorBkp, WWW_VENDOR);
        $this->assertExitError();
        $this->assertErrorContains('File or directory `' . rtr(WWW_VENDOR) . '` is not writable');
    }
}

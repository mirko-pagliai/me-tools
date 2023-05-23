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
     * @requires OS Linux
     * @test
     * @uses \MeTools\Command\Install\CreateVendorsLinksCommand::execute()
     */
    public function testExecute(): void
    {
        $Filesystem = new Filesystem();
        /** @var array<string, string> $vendorLinks */
        $vendorLinks = Configure::read('MeTools.VendorLinks');

        //`WWW_VENDOR` directory does not exist
        $Filesystem->rmdirRecursive(WWW_VENDOR);
        $this->exec('me_tools.create_vendors_links -v');
        $this->assertExitError();
        $this->assertErrorContains('File or directory `' . rtr(WWW_VENDOR) . '` is not writable');
        $Filesystem->createFile(WWW_VENDOR . '.gitkeep');

        //For now, origin files don't exist
        $this->exec('me_tools.create_vendors_links -v');
        foreach (array_keys($vendorLinks) as $expectedOrigin) {
            $this->assertErrorContains('File or directory `' . rtr(VENDOR . $Filesystem->normalizePath($expectedOrigin)) . '` does not exist');
        }

        //Tries to create a link
        $originTest = VENDOR . 'cakephp' . DS . 'cakephp';
        $targetTest = WWW_VENDOR . 'cakephp';
        $this->assertFileExists($originTest);
        Configure::write('MeTools.VendorLinks', ['cakephp/cakephp' => 'cakephp']);
        $Command = new CreateVendorsLinksCommand();
        $this->_out = new StubConsoleOutput();
        $Command->run(['-v'], new ConsoleIo($this->_out));
        $this->assertOutputContains('Link from `' . rtr($originTest) . '` to `' . rtr($targetTest) . '` has been created');

        //Runs again. The link already exists
        $this->_out = new StubConsoleOutput();
        $Command->run(['-v'], new ConsoleIo($this->_out));
        $this->assertOutputContains('Link to `' . rtr($targetTest) . '` already exists');
        $Filesystem->rmdirRecursive($targetTest);

        //Links already exists, with a different (BAD) target. Then the link will be recreated
        $Filesystem->symlink($Filesystem->createTmpFile(), $targetTest);
        $this->assertNotSame($originTest, readlink($targetTest));
        $this->_out = new StubConsoleOutput();
        $Command->run(['-v'], new ConsoleIo($this->_out));
        $this->assertOutputContains('Link from `' . rtr($originTest) . '` to `' . rtr($targetTest) . '` has been created');
        $this->assertSame($originTest, readlink($targetTest));
        $Filesystem->rmdirRecursive($targetTest);
    }
}

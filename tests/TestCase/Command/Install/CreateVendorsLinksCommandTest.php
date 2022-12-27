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
 */
namespace MeTools\Test\TestCase\Command\Install;

use Cake\Core\Configure;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;
use Tools\Filesystem;

/**
 * CreateVendorsLinksCommandTest class
 */
class CreateVendorsLinksCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @uses \MeTools\Command\Install\CreateVendorsLinksCommand::execute()
     * @requires OS Linux
     * @test
     */
    public function testExecute(): void
    {
        $Filesystem = new Filesystem();

        /** @var array<string, string> $expectedVendorLinks */
        $expectedVendorLinks = Configure::readOrFail('VENDOR_LINKS');

        $originFiles = array_map(fn(string $origin): string => ROOT . 'vendor' . DS . $origin, array_keys($expectedVendorLinks));
        $targetFiles = array_map(fn(string $target): string => $Filesystem->rtr(WWW_ROOT . 'vendor' . DS . $target), $expectedVendorLinks);
        array_map(fn(string $file) => file_exists($file) || $Filesystem->createFile($file), $originFiles);

        $this->exec('me_tools.create_vendors_links -v');
        $this->assertExitSuccess();
        foreach ($targetFiles as $targetFile) {
            $this->assertOutputContains('Link `' . $targetFile . '` has been created');
        }

        array_map('unlink', $originFiles);
        $Filesystem->unlinkRecursive(WWW_ROOT . 'vendor', '.gitkeep');
    }
}

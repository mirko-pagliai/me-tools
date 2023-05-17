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
        $vendorLinks = Configure::readFromPlugins('VendorLinks');

        $expectedTargetFiles = array_map(fn(string $target): string => rtr(WWW_VENDOR . $target), $vendorLinks);
        $originFiles = array_map(fn(string $file): string => VENDOR . Filesystem::normalizePath($file), array_keys($vendorLinks));
        $originFiles = array_map(fn(string $file): string => file_exists($file) ? $file : Filesystem::createFile($file), $originFiles);

        $this->exec('me_tools.create_vendors_links -v');
        $this->assertExitSuccess();
        foreach ($expectedTargetFiles as $expectedTargetFile) {
            $this->assertOutputContains('Link `' . $expectedTargetFile . '` has been created');
        }

        array_map('unlink', $originFiles);
        Filesystem::unlinkRecursive(WWW_VENDOR, '.gitkeep');
    }
}

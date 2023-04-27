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
     * @test
     * @uses \MeTools\Command\Install\CreateVendorsLinksCommand::execute()
     */
    public function testExecute(): void
    {
        $Filesystem = new Filesystem();

        $expectedLinks = array_merge(...array_values(Configure::readFromPlugins('VendorLinks')));
        $originFiles = array_map(function (string $file) use ($Filesystem): string {
            $file = $Filesystem->concatenate(ROOT, 'vendor', $Filesystem->normalizePath($file));
            if (!file_exists($file)) {
                $Filesystem->createFile($file);
            }

            return $file;
        }, array_keys($expectedLinks));
        $targetFiles = array_map(fn(string $target): string => $Filesystem->rtr($Filesystem->concatenate(WWW_ROOT, 'vendor', $target)), $expectedLinks);

        $this->exec('me_tools.create_vendors_links -v');
        $this->assertExitSuccess();
        foreach ($targetFiles as $targetFile) {
            $this->assertOutputContains('Link `' . $targetFile . '` has been created');
        }

        array_map('unlink', $originFiles);
        $Filesystem->unlinkRecursive(WWW_ROOT . 'vendor', '.gitkeep');
    }
}

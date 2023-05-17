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

use MeTools\TestSuite\CommandTestCase;
use Tools\Filesystem;

/**
 * FixComposerJsonCommandTest class
 */
class FixComposerJsonCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\FixComposerJsonCommand::execute()
     */
    public function testExecute(): void
    {
        $command = 'me_tools.fix_composer_json -v';

        $file = APP . 'composer.json';
        Filesystem::createFile($file, json_encode([
            'name' => 'example',
            'description' => 'example of composer.json',
            'type' => 'project',
            'require' => ['php' => '>=5.5.9'],
            'autoload' => ['psr-4' => ['App' => 'src']],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->exec($command . ' -p ' . $file);
        $this->assertExitSuccess();
        $this->assertOutputContains('File `' . rtr($file) . '` has been fixed');
        $this->assertErrorEmpty();

        //With an already fixed file
        $this->exec($command . ' -p ' . $file);
        $this->assertExitSuccess();
        $this->assertOutputContains('File `' . rtr($file) . "` doesn't need to be fixed");
        $this->assertErrorEmpty();
        unlink($file);

        //With an invalid file
        $file = TMP . 'invalid.json';
        Filesystem::createFile($file);
        $this->exec($command . ' -p ' . $file);
        $this->assertExitError();
        $this->assertErrorContains('File `' . $file . '` does not seem a valid composer.json file');
        unlink($file);

        //With a no existing file
        $this->exec($command . ' -p noExisting');
        $this->assertExitError();
        $this->assertErrorContains('File or directory `noExisting` is not writable');
    }
}

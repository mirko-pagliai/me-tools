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

use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;
use Tools\Filesystem;

/**
 * FixComposerJsonCommandTest class
 */
class FixComposerJsonCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var string
     */
    protected $command = 'me_tools.fix_composer_json -v';

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute(): void
    {
        $file = APP . 'composer.json';
        Filesystem::instance()->createFile($file, json_encode([
            'name' => 'example',
            'description' => 'example of composer.json',
            'type' => 'project',
            'require' => ['php' => '>=5.5.9'],
            'autoload' => ['psr-4' => ['App' => 'src']],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->exec($this->command . ' -p ' . $file);
        $this->assertExitWithSuccess();
        $this->assertOutputContains('File `' . Filesystem::instance()->rtr($file) . '` has been fixed');
        $this->assertErrorEmpty();
    }

    /**
     * Tests for `execute()` method, with an already fixed file
     * @test
     */
    public function testExecuteAlreadyFixedFile(): void
    {
        $file = APP . 'composer.json';
        $this->exec($this->command . ' -p ' . $file);
        $this->assertExitWithSuccess();
        $this->assertOutputContains('File `' . Filesystem::instance()->rtr($file) . '` doesn\'t need to be fixed');
        $this->assertErrorEmpty();
        unlink(APP . 'composer.json');
    }

    /**
     * Tests for `execute()` method, with an invalid file
     * @test
     */
    public function testExecuteInvalidFile(): void
    {
        $file = TMP . 'invalid.json';
        Filesystem::instance()->createFile($file);
        $this->exec($this->command . ' -p ' . $file);
        $this->assertExitWithError();
        $this->assertErrorContains('File `' . $file . '` does not seem a valid composer.json file');
    }

    /**
     * Tests for `execute()` method, with a no existing file
     * @test
     */
    public function testExecuteNoExistingFile(): void
    {
        $this->exec($this->command . ' -p noExisting');
        $this->assertExitWithError();
        $this->assertErrorContains('File or directory `noExisting` does not exist');
    }
}

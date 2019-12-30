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

/**
 * FixComposerJsonCommandTest class
 */
class FixComposerJsonCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $command = 'me_tools.fix_composer_json -v';

        //Tries to fix a no existing file
        $this->exec($command . ' -p noExisting');
        $this->assertExitWithError();
        $this->assertErrorContains('File or directory `noExisting` is not writable');

        //Tries to fix an invalid composer.json file
        $file = TMP . 'invalid.json';
        create_file($file, 'String');
        $this->exec($command . ' -p ' . $file);
        $this->assertExitWithError();
        $this->assertErrorContains('The file ' . $file . ' does not seem a valid composer.json file');

        //Fixes a valid composer.json file
        $file = APP . 'composer.json';
        create_file($file, json_encode([
            'name' => 'example',
            'description' => 'example of composer.json',
            'type' => 'project',
            'require' => ['php' => '>=5.5.9'],
            'autoload' => ['psr-4' => ['App' => 'src']],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->exec($command . ' -p ' . $file);
        $this->assertExitWithSuccess();
        $this->assertOutputContains('The file ' . rtr($file) . ' has been fixed');
        $this->assertErrorEmpty();

        //The file no longer needs to be fixed
        $this->exec($command . ' -p ' . $file);
        $this->assertExitWithSuccess();
        $this->assertOutputContains('The file ' . rtr($file) . ' doesn\'t need to be fixed');
        $this->assertErrorEmpty();

        unlink(APP . 'composer.json');
    }
}

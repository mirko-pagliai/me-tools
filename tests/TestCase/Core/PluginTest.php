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
namespace MeTools\Test\TestCase\Core;

use Cake\Core\Exception\MissingPluginException;
use MeTools\Core\Plugin;
use MeTools\TestSuite\TestCase;

/**
 * PluginTest class.
 */
class PluginTest extends TestCase
{
    /**
     * Tests for `all()` method
     * @test
     */
    public function testAll(): void
    {
        $this->removePlugins(['TestPlugin']);

        $expected = ['MeTools'];
        if (Plugin::getCollection()->has('Assets')) {
            $expected[] = 'Assets';
        }
        $this->assertEquals($expected, Plugin::all());

        $this->loadPlugins(['TestPlugin' => []]);
        $this->assertEquals(array_merge($expected, ['TestPlugin']), Plugin::all());
        $this->assertEquals($expected, Plugin::all(['exclude' => 'TestPlugin']));
        $this->assertEquals($expected, Plugin::all(['exclude' => ['TestPlugin', 'noExistingPlugin']]));

        $expected = ['MeTools', 'AnotherTestPlugin'];
        if (Plugin::getCollection()->has('Assets')) {
            $expected[] = 'Assets';
        }
        $expected[] = 'TestPlugin';
        $this->loadPlugins(['AnotherTestPlugin' => []]);
        $this->assertEquals($expected, Plugin::all());

        sort($expected);
        $this->assertEquals($expected, Plugin::all(['order' => false]));
    }

    /**
     * Tests for `path()` method
     * @test
     */
    public function testPath(): void
    {
        $this->assertSame(ROOT, Plugin::path('MeTools'));

        $file = 'src' . DS . 'Console' . DS . 'Command.php';

        $this->assertEquals(ROOT . $file, Plugin::path('MeTools', $file));
        $this->assertEquals(ROOT . $file, Plugin::path('MeTools', $file, true));

        //No existing file
        $this->expectException(MissingPluginException::class);
        $this->expectExceptionMessage('File or directory `no_existing.php` does not exist');
        Plugin::path('MeTools', 'no_existing.php', true);
    }
}

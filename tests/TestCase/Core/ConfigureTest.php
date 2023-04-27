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

use MeTools\Core\Configure;
use MeTools\TestSuite\TestCase;

/**
 * ConfigureTest class
 */
class ConfigureTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\Core\Configure::readFromPlugins()
     */
    public function testReadFromPlugins(): void
    {
        Configure::write('MeTools.myConfig', ['a', 'b', 'c']);
        $expected = ['MeTools' => Configure::read('MeTools.myConfig')];
        $this->assertSame($expected, Configure::readFromPlugins('myConfig'));

        $expected += ['TestPlugin' => 'd'];
        $this->loadPlugins(['TestPlugin' => []]);
        Configure::write('TestPlugin.myConfig', 'd');
        $this->assertSame($expected, Configure::readFromPlugins('myConfig'));

        $this->assertEmpty(Configure::readFromPlugins('noExistingConfig'));
    }
}

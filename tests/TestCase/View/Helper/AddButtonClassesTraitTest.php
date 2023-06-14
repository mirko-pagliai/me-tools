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

namespace MeTools\Test\TestCase\View\Helper;

use Cake\View\Helper;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\AddButtonClassesTrait;
use MeTools\View\View;

/**
 * AddButtonClassesTraitTest class
 */
class AddButtonClassesTraitTest extends TestCase
{
    /**
     * @var \Cake\View\Helper
     */
    protected Helper $Helper;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Helper = new class (new View()) extends Helper {
            use AddButtonClassesTrait {
                addButtonClasses as public;
            }
        };
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\AddButtonClassesTrait::addButtonClasses()
     */
    public function testAddButtonClasses(): void
    {
        $expected = ['class' => 'btn btn-primary'];
        $this->assertEquals($expected, $this->Helper->addButtonClasses([], 'primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses([], 'btn-primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn'], 'primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn'], 'btn-primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn-primary'], 'primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn-primary'], 'btn-primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn btn-primary'], 'primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn btn-primary'], 'btn-primary'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn btn-primary'], 'success'));
        $this->assertEquals($expected, $this->Helper->addButtonClasses(['class' => 'btn btn-primary'], 'success'));
    }

    /**
     * With an invalid button class
     * @test
     * @uses \MeTools\View\Helper\AddButtonClassesTrait::addButtonClasses()
     */
    public function testAddButtonClassesWithInvalidClass(): void
    {
        $this->expectExceptionMessage('Invalid `invalidClass` class');
        $this->Helper->addButtonClasses([], 'invalidClass');
    }
}

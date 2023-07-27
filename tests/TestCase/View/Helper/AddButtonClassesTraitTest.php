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
use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\AddButtonClassesTrait;

/**
 * AddButtonClassesTraitTest class
 */
class AddButtonClassesTraitTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\View\Helper\AddButtonClassesTrait::addButtonClasses()
     */
    public function testAddButtonClasses(): void
    {
        $Helper = new class (new View()) extends Helper {
            use AddButtonClassesTrait {
                addButtonClasses as public;
            }
        };

        $expected = ['class' => 'btn btn-primary'];
        $this->assertSame($expected, $Helper->addButtonClasses([], 'btn-primary'));
        $this->assertSame($expected, $Helper->addButtonClasses(['class' => 'btn-primary'], 'btn-primary'));
        $this->assertSame($expected, $Helper->addButtonClasses(['class' => 'btn-primary'], 'btn-danger'));
        $this->assertSame($expected, $Helper->addButtonClasses(['class' => 'btn btn-primary'], 'btn-danger'));

        $this->expectExceptionMessage('Invalid `invalidClass` button class');
        $Helper->addButtonClasses([], 'invalidClass');
    }
}

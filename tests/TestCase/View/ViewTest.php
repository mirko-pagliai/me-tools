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

namespace MeTools\Test\TestCase\View;

use MeTools\TestSuite\TestCase;
use MeTools\View\View;

/**
 * ViewTest
 * @uses \MeTools\View\View
 */
class ViewTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\View\View::initialize()
     */
    public function testInitialize(): void
    {
        //Expects default helpers
        $expected = [
            'Icon' => 'MeTools\View\Helper\IconHelper',
            'Html' => 'MeTools\View\Helper\HtmlHelper',
            'Form' => 'MeTools\View\Helper\FormHelper',
            'Paginator' => 'MeTools\View\Helper\PaginatorHelper',
            'Dropdown' => 'MeTools\View\Helper\DropdownHelper',
        ];
        $View = new View();
        $this->assertEquals($expected, array_map('get_class', iterator_to_array($View->helpers())));

        /**
         * A custom View classes.
         *
         * They load the `Paginator` helper from CakePHP, before calling the parent `initialize()` method.
         */
        $expected['Paginator'] = 'Cake\View\Helper\PaginatorHelper';
        $CustomView = new class extends View {
            public function initialize(): void
            {
                $this->loadHelper('Paginator', ['className' => 'Cake\View\Helper\PaginatorHelper']);

                parent::initialize();
            }
        };
        $this->assertEquals($expected, array_map('get_class', iterator_to_array($CustomView->helpers())));

        $CustomView = new class extends View {
              protected $helpers = ['Paginator'];
        };
        $CustomView->loadHelpers();
        $this->assertEquals($expected, array_map('get_class', iterator_to_array($CustomView->helpers())));

        $CustomView = new class extends View {
            protected $helpers = ['Paginator' => ['className' => 'Cake\View\Helper\PaginatorHelper']];
        };
        $CustomView->loadHelpers();
        $this->assertEquals($expected, array_map('get_class', iterator_to_array($CustomView->helpers())));
    }
}

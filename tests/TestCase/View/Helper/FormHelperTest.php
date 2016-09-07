<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use MeTools\View\Helper\FormHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * FormHelperTest class
 */
class FormHelperTest extends TestCase
{
    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = new View();
        $this->Form = new FormHelper($this->View);
        $this->Html = new HtmlHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Form, $this->Html, $this->View);
    }

    /**
     * Tests for `button()` method
     * @return void
     * @test
     */
    public function testButton()
    {
        $title = 'My button';

        $result = $this->Form->button($title);
        $expected = [
            'button' => [
                'type' => 'button',
                'class' => 'btn btn-default',
            ],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['icon' => 'home']);
        $expected = [
            'button' => [
                'type' => 'button',
                'class' => 'btn btn-default',
            ],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['type' => 'reset']);
        $expected = [
            'button' => [
                'type' => 'reset',
                'class' => 'btn btn-default',
            ],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['type' => 'submit']);
        $expected = [
            'button' => [
                'type' => 'submit',
                'class' => 'btn btn-success',
            ],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['class' => 'btn-danger']);
        $expected = [
            'button' => [
                'type' => 'button',
                'class' => 'btn-danger btn',
            ],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `checkbox()` method
     * @return void
     * @test
     */
    public function testCheckbox()
    {
        $field = 'my-field';

        $result = $this->Form->checkbox($field);
        $expected = [
            ['input' => [
                'type' => 'hidden',
                'name' => $field,
                'value' => '0',
            ]],
            ['input' => [
                'type' => 'checkbox',
                'name' => $field,
                'value' => '1',
            ]],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `ckeditor()` method
     * @return void
     * @test
     */
    public function testCkeditor()
    {
        $field = 'my-field';

        $result = $this->Form->ckeditor($field);
        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'textarea' => [
                'name' => $field,
                'class' => 'ckeditor editor form-control',
                'id' => $field,
            ],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `createInline()` and `isInline()` methods
     * @return void
     * @test
     */
    public function testCreateInlineAndIsInline()
    {
        $expected = [
            'form' => [
              'method' => 'post',
              'accept-charset' => 'utf-8',
              'class' => 'form-inline',
              'action' => '/',
            ],
            'div' => ['style' => 'display:none;'],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/div',
            '/form',
        ];

        $inline = $this->Form->isInline();
        $this->assertFalse($inline);

        $result = $this->Form->createInline(null);
        $inline = $this->Form->isInline();
        $result .= $this->Form->end();
        $this->assertTrue($inline);
        $this->assertHtml($expected, $result);

        $inline = $this->Form->isInline();
        $this->assertFalse($inline);

        // `create()` method with `inline` option
        $result = $this->Form->create(null, ['inline' => true]);
        $inline = $this->Form->isInline();
        $result .= $this->Form->end();
        $this->assertTrue($inline);
        $this->assertHtml($expected, $result);

        $inline = $this->Form->isInline();
        $this->assertFalse($inline);

        // `create()` method with `form-inline` class
        $result = $this->Form->create(null, ['class' => 'form-inline']);
        $inline = $this->Form->isInline();
        $result .= $this->Form->end();
        $this->assertTrue($inline);
        $this->assertHtml($expected, $result);

        $inline = $this->Form->isInline();
        $this->assertFalse($inline);
    }

    /**
     * Tests for `datepicker()`, `datetimepicker()` and `timepicker()` methods
     * @return void
     * @test
     */
    public function testDatetimepicker()
    {
        $field = 'my-field';

        $result = $this->Form->datepicker($field);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'datepicker form-control',
                'data-date-format' => 'YYYY-MM-DD',
                'id' => $field,
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->datetimepicker($field);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'datetimepicker form-control',
                'data-date-format' => 'YYYY-MM-DD HH:mm',
                'id' => $field,
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->timepicker($field);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'timepicker form-control',
                'data-date-format' => 'HH:mm',
                'id' => $field,
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `input()` method
     * @return void
     * @test
     */
    public function testInput()
    {
        $field = 'my-field';

        $result = $this->Form->input($field);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input($field, ['help' => 'My tip']);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            'p' => ['class' => 'help-block'],
            'My tip',
            '/p',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input($field, [
            'help' => ['Tip first line', 'Tip second line'],
        ]);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            ['p' => ['class' => 'help-block']],
            'Tip first line',
            '/p',
            ['p' => ['class' => 'help-block']],
            'Tip second line',
            '/p',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input($field, [
            'button' => $this->Html->button('My button'),
        ]);
        $expected = [
            ['div' => ['class' => 'form-group input text']],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            'span' => ['class' => 'input-group-btn'],
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => 'My button',
            ],
            'My button',
            '/button',
            '/span',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `input()` method with checkboxes
     * @return void
     * @test
     */
    public function testInputCheckbox()
    {
        $field = 'my-field';

        $result = $this->Form->input($field, ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'input checkbox'],
            'label' => ['for' => $field],
            ['input' => [
                'type' => 'hidden',
                'name' => $field,
                'value' => '0',
            ]],
            ['input' => [
                'type' => 'checkbox',
                'name' => $field,
                'value' => '1',
                'id' => $field,
            ]],
            ' My Field',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `input()` method with password inputs
     * @return void
     * @test
     */
    public function testInputPassword()
    {
        //Auto-detect `password` type
        $result = $this->Form->input('old-password');
        $expected = [
            'div' => ['class' => 'form-group input password'],
            'label' => ['for' => 'old-password'],
            'Old Password',
            '/label',
            'input' => [
                'type' => 'password',
                'name' => 'old-password',
                'class' => 'form-control',
                'id' => 'old-password',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `input()` method with selects
     * @return void
     * @test
     */
    public function testInputSelect()
    {
        $field = 'my-field';
        $options = ['1' => 'First value', '2' => 'Second value'];

        $result = $this->Form->input($field, [
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => [
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2']],
            $options['2'],
            '/option',
            '/select',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //With default value
        $result = $this->Form->input($field, [
            'default' => '2',
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => [
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2', 'selected' => 'selected']],
            $options['2'],
            '/option',
            '/select',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //With selected value
        $result = $this->Form->input($field, [
            'options' => $options,
            'type' => 'select',
            'value' => '2',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => [
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2', 'selected' => 'selected']],
            $options['2'],
            '/option',
            '/select',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Custom `empty` value
        $result = $this->Form->input($field, [
            'empty' => '(choose one)',
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => [
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            ['option' => ['value' => '']],
            '(choose one)',
            '/option',
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2']],
            $options['2'],
            '/option',
            '/select',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        // `empty` disabled
        $result = $this->Form->input($field, [
            'empty' => false,
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => [
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2']],
            $options['2'],
            '/option',
            '/select',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `input()` method with textareas
     * @return void
     * @test
     */
    public function testInputTextarea()
    {
        $field = 'my-field';

        $result = $this->Form->input($field, ['type' => 'textarea']);
        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'textarea' => [
                'name' => $field,
                'class' => 'form-control',
                'id' => $field,
            ],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `label()` method
     * @return void
     * @test
     */
    public function testLabel()
    {
        $fieldname = 'my-fieldname';
        $title = 'My label';

        $result = $this->Form->label($fieldname, $title);
        $expected = ['label' => ['for' => 'my-fieldname'], $title, '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label($fieldname, $title, ['icon' => 'home']);
        $expected = [
            'label' => ['for' => 'my-fieldname'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/label',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label($fieldname, 'Single escape \'');
        $expected = [
            'label' => ['for' => 'my-fieldname'],
            'Single escape \'',
            '/label',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label($fieldname, 'Double escape "');
        $expected = [
            'label' => ['for' => 'my-fieldname'],
            'Double escape "',
            '/label',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `postButton()` method
     * @return void
     * @test
     */
    public function testPostButton()
    {
        $title = 'My title';
        $url = 'http://link';

        $result = $this->Form->postButton($title, $url);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => $title,
                'onclick',
            ],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postButton($title, $url, ['icon' => 'home']);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => $title,
                'onclick',
            ],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postButton($title, $url, [
            'class' => 'btn-danger',
        ]);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn-danger btn',
                'title' => $title,
                'onclick',
            ],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `postLink()` method
     * @return void
     * @test
     */
    public function testPostLink()
    {
        $title = 'My title';
        $url = 'http://link';

        $result = $this->Form->postLink($title, $url);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'title' => $title,
                'onclick',
            ],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink($title, $url, ['icon' => 'home']);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'title' => $title,
                'onclick',
            ],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink($title, $url, [
            'tooltip' => 'My tooltip',
        ]);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'title' => 'My tooltip',
                'data-toggle' => 'tooltip',
                'onclick',
            ],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink('Single quote \'', $url);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'title' => 'Single quote &amp;#039;',
                'onclick',
            ],
            'Single quote \'',
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink('Double quote "', $url);
        $expected = [
            'form' => [
                'name' => 'preg:/post_[a-z0-9]+/',
                'style' => 'display:none;',
                'method' => 'post',
                'action' => $url,
            ],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST',
            ],
            '/form',
            'a' => [
                'href' => '#',
                'title' => 'Double quote &amp;quot;',
                'onclick',
            ],
            'Double quote "',
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `select()` method
     * @return void
     * @test
     */
    public function testSelect()
    {
        $field = 'my-field';
        $options = ['1' => 'First value', '2' => 'Second value'];

        $result = $this->Form->select($field, $options);
        $expected = [
            'select' => ['name' => $field],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2']],
            $options['2'],
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        //With default value
        $result = $this->Form->select($field, $options, ['default' => '2']);
        $expected = [
            'select' => ['name' => $field],
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2', 'selected' => 'selected']],
            $options['2'],
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        //With selected value
        $result = $this->Form->select($field, $options, ['value' => '2']);
        $expected = [
            'select' => ['name' => $field],
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2', 'selected' => 'selected']],
            $options['2'],
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        //Custom `empty` value
        $result = $this->Form->select($field, $options, [
            'empty' => '(choose one)',
        ]);
        $expected = [
            'select' => ['name' => $field],
            ['option' => ['value' => '']],
            '(choose one)',
            '/option',
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2']],
            $options['2'],
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        // `empty` disabled
        $result = $this->Form->select($field, $options, ['empty' => false]);
        $expected = [
            'select' => ['name' => $field],
            ['option' => ['value' => '1']],
            $options['1'],
            '/option',
            ['option' => ['value' => '2']],
            $options['2'],
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `submit()` method
     * @return void
     * @test
     */
    public function testSubmit()
    {
        $title = 'My title';

        $expected = [
            'button' => ['type' => 'submit', 'class' => 'btn btn-success'],
            $title,
            '/button',
        ];

        $result = $this->Form->submit($title);
        $this->assertHtml($expected, $result);

        //The `type` option will be overwritten
        $result = $this->Form->submit($title, ['type' => 'reset']);
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit($title, ['class' => 'btn-danger']);
        $expected = [
            'button' => ['type' => 'submit', 'class' => 'btn-danger btn'],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit($title, ['icon' => 'home']);
        $expected = [
            'button' => ['type' => 'submit', 'class' => 'btn btn-success'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `textarea()` method
     * @return void
     * @test
     */
    public function testTextarea()
    {
        $field = 'my-field';

        $result = $this->Form->textarea($field);
        $expected = [
            'textarea' => ['name' => $field],
            '/textarea',
        ];
        $this->assertHtml($expected, $result);
    }
}

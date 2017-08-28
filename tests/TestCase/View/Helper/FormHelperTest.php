<?php
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

use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\FormHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * FormHelperTest class
 */
class FormHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\FormHelper
     */
    protected $Form;

    /**
     * @var \MeTools\View\Helper\HtmlHelper
     */
    protected $Html;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $view = new View;

        $this->Form = new FormHelper($view);
        $this->Html = new HtmlHelper($view);
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
            'button' => ['type' => 'button', 'class' => 'btn btn-default'],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['icon' => 'home']);
        $expected = [
            'button' => ['type' => 'button', 'class' => 'btn btn-default'],
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
            'button' => ['type' => 'reset', 'class' => 'btn btn-default'],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['type' => 'submit']);
        $expected = [
            'button' => ['type' => 'submit', 'class' => 'btn btn-success'],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['class' => 'btn btn-danger']);
        $expected = [
            'button' => ['type' => 'button', 'class' => 'btn btn-danger'],
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button($title, ['class' => 'btn-danger']);
        $expected = [
            'button' => ['type' => 'button', 'class' => 'btn-danger btn'],
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
            ['input' => ['type' => 'hidden', 'name' => $field, 'value' => '0']],
            ['input' => ['type' => 'checkbox', 'name' => $field, 'value' => '1']],
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

        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'textarea' => ['name' => $field, 'class' => 'form-control wysiwyg editor', 'id' => $field],
            '/textarea',
            '/div',
        ];

        $result = $this->Form->ckeditor($field);
        $this->assertHtml($expected, $result);

        $result = $this->Form->ckeditor($field, ['label' => false]);
        $this->assertHtml($expected, $result);

        $result = $this->Form->ckeditor($field, ['label' => 'my label']);
        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'label' => ['for' => $field],
            'my label',
            '/label',
            'textarea' => ['name' => $field, 'class' => 'form-control wysiwyg editor', 'id' => $field],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method
     * @return void
     * @test
     */
    public function testControl()
    {
        $field = 'my-field';

        $result = $this->Form->control($field);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control($field, ['help' => 'My tip']);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            'p' => ['class' => 'form-text text-muted'],
            'My tip',
            '/p',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control($field, ['help' => ['Tip first line', 'Tip second line']]);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            ['p' => ['class' => 'form-text text-muted']],
            'Tip first line',
            '/p',
            ['p' => ['class' => 'form-text text-muted']],
            'Tip second line',
            '/p',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control($field, ['button' => $this->Html->button('My button')]);
        $expected = [
            ['div' => ['class' => 'form-group input text']],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            'span' => ['class' => 'input-group-addon'],
            'button' => ['role' => 'button', 'class' => 'btn btn-default', 'title' => 'My button'],
            'My button',
            '/button',
            '/span',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method with checkboxes
     * @return void
     * @test
     */
    public function testControlCheckbox()
    {
        $field = 'my-field';

        $result = $this->Form->control($field, ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-check input checkbox'],
            'label' => ['for' => $field],
            ['input' => ['type' => 'hidden', 'name' => $field, 'value' => '0']],
            ['input' => ['type' => 'checkbox', 'name' => $field, 'value' => '1', 'id' => $field]],
            ' My Field',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method with password inputs
     * @return void
     * @test
     */
    public function testControlPassword()
    {
        //Auto-detect `password` type
        $result = $this->Form->control('old-password');
        $expected = [
            'div' => ['class' => 'form-group input password'],
            'label' => ['for' => 'old-password'],
            'Old Password',
            '/label',
            'input' => ['type' => 'password', 'name' => 'old-password', 'class' => 'form-control', 'id' => 'old-password'],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method with selects
     * @return void
     * @test
     */
    public function testControlSelect()
    {
        $field = 'my-field';
        $options = ['1' => 'First value', '2' => 'Second value'];

        $result = $this->Form->control($field, ['options' => $options, 'type' => 'select']);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
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
        $result = $this->Form->control($field, [
            'default' => '2',
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
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
        $result = $this->Form->control($field, [
            'options' => $options,
            'type' => 'select',
            'value' => '2',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
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
        $result = $this->Form->control($field, [
            'empty' => '(choose one)',
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
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
        $result = $this->Form->control($field, [
            'empty' => false,
            'options' => $options,
            'type' => 'select',
        ]);
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'select' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
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
     * Tests for `control()` method with textareas
     * @return void
     * @test
     */
    public function testControlTextarea()
    {
        $field = 'my-field';

        $result = $this->Form->control($field, ['type' => 'textarea']);
        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'label' => ['for' => $field],
            'My Field',
            '/label',
            'textarea' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method, into an inline form
     * @return void
     * @test
     */
    public function testControlInline()
    {
        $field = 'my-field';

        $this->Form->createInline();
        $result = $this->Form->control($field);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'sr-only', 'for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Tries with a checkbox
        $result = $this->Form->control($field, ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-check input checkbox'],
            'label' => ['for' => $field],
            ['input' => ['type' => 'hidden', 'name' => $field, 'value' => '0']],
            ['input' => ['type' => 'checkbox', 'name' => $field, 'value' => '1', 'id' => $field]],
            ' My Field',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //Using `label` option
        $result = $this->Form->control($field, ['label' => 'My label']);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'sr-only', 'for' => $field],
            'My label',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        //`label` option `false`
        $result = $this->Form->control($field, ['label' => false]);
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `create()` method
     * @return void
     * @test
     */
    public function testCreate()
    {
        $result = $this->Form->create(null);
        $result .= $this->Form->end();
        $expected = [
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'action' => '/'],
            'div' => ['style' => 'display:none;'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/div',
            '/form',
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
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'form-inline', 'action' => '/'],
            'div' => ['style' => 'display:none;'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
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
                'class' => 'form-control datepicker',
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
                'class' => 'form-control datetimepicker',
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
                'class' => 'form-control timepicker',
                'data-date-format' => 'HH:mm',
                'id' => $field,
            ],
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
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'role' => 'button', 'class' => 'btn btn-default', 'title' => $title, 'onclick'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postButton($title, $url, ['icon' => 'home']);
        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'role' => 'button', 'class' => 'btn btn-default', 'title' => $title, 'onclick'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postButton($title, $url, ['class' => 'btn-danger']);
        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'role' => 'button', 'class' => 'btn-danger btn', 'title' => $title, 'onclick'],
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
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => $title, 'onclick'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink($title, $url, ['icon' => 'home']);
        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => $title, 'onclick'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink($title, $url, ['tooltip' => 'My tooltip']);
        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => 'My tooltip', 'data-toggle' => 'tooltip', 'onclick'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink('Single quote \'', $url);
        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => 'Single quote &amp;#039;', 'onclick'],
            'Single quote \'',
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postLink('Double quote "', $url);
        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => 'Double quote &amp;quot;', 'onclick'],
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
            'select' => ['name' => $field, 'class' => 'form-control'],
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
            'select' => ['name' => $field, 'class' => 'form-control'],
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
            'select' => ['name' => $field, 'class' => 'form-control'],
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
        $result = $this->Form->select($field, $options, ['empty' => '(choose one)']);
        $expected = [
            'select' => ['name' => $field, 'class' => 'form-control'],
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
            'select' => ['name' => $field, 'class' => 'form-control'],
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
            'textarea' => ['name' => $field, 'class' => 'form-control'],
            '/textarea',
        ];
        $this->assertHtml($expected, $result);
    }
}

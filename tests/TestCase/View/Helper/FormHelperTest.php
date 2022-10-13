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

use MeTools\TestSuite\HelperTestCase;
use MeTools\View\Helper\FormHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * FormHelperTest class
 * @property \MeTools\View\Helper\FormHelper $Helper
 */
class FormHelperTest extends HelperTestCase
{
    /**
     * @var int
     */
    protected static int $current;

    /**
     * @var \MeTools\View\Helper\HtmlHelper
     */
    protected HtmlHelper $Html;

    /**
     * This method is called before the first test of this test class is run
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
    }

    /**
     * This method is called after the last test of this test class is run
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        error_reporting(self::$current);
    }

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->Html ??= new HtmlHelper($this->Helper->getView());
    }

    /**
     * Tests the entire class is deprecated
     * @return void
     */
    public function testClassIsDeprecated(): void
    {
        error_reporting(self::$current);
        $this->assertDeprecated(fn() => new FormHelper($this->Helper->getView()), '`FormHelper` is deprecated. Use instead `BootstrapFormHelper`');
        self::$current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
    }

    /**
     * Tests for `button()` method
     * @test
     */
    public function testButton(): void
    {
        $title = 'My button';

        $expected = [
            'button' => ['type' => 'button', 'class' => 'btn btn-primary'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $this->Helper->button($title, ['icon' => 'home']));

        $expected = ['button' => ['type' => 'reset', 'class' => 'btn btn-primary'], $title, '/button'];
        $this->assertHtml($expected, $this->Helper->button($title, ['type' => 'reset']));

        $expected = ['button' => ['type' => 'submit', 'class' => 'btn btn-success'], $title, '/button'];
        $this->assertHtml($expected, $this->Helper->button($title, ['type' => 'submit']));

        $expected = ['button' => ['type' => 'button', 'class' => 'btn btn-danger'], $title, '/button'];
        $this->assertHtml($expected, $this->Helper->button($title, ['class' => 'btn-danger']));

        $this->assertSame('<button class="btn btn-primary" type="button"></button>', $this->Helper->button());
    }

    /**
     * Tests for `checkbox()` method
     * @test
     */
    public function testCheckbox(): void
    {
        $expected = [
            ['input' => ['type' => 'hidden', 'name' => 'my-field', 'value' => '0']],
            ['input' => ['class' => 'form-check-input', 'type' => 'checkbox', 'name' => 'my-field', 'value' => '1']],
        ];
        $this->assertHtml($expected, array_value_first((array)$this->Helper->checkbox('my-field')));
    }

    /**
     * Tests for `ckeditor()` method
     * @test
     */
    public function testCkeditor(): void
    {
        $field = 'my-field';

        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'textarea' => ['name' => $field, 'class' => 'form-control wysiwyg editor', 'id' => $field],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->ckeditor($field));
        $this->assertHtml($expected, $this->Helper->ckeditor($field, ['label' => false]));

        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'label' => ['class' => 'form-label', 'for' => $field],
            'my label',
            '/label',
            'textarea' => ['name' => $field, 'class' => 'form-control wysiwyg editor', 'id' => $field],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->ckeditor($field, ['label' => 'my label']));
    }

    /**
     * Tests for `control()` method
     * @test
     */
    public function testControl(): void
    {
        $field = 'my-field';

        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'form-label', 'for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field));

        $expected = [
            ['div' => ['class' => 'form-group input text']],
            'label' => ['class' => 'form-label', 'for' => $field],
            'My Field',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            ['div' => ['class' => 'input-group-append']],
            'button' => ['role' => 'button', 'class' => 'btn btn-light', 'title' => 'My button'],
            'My button',
            '/button',
            '/div',
            '/div',
        ];
        $result = $this->Helper->control($field, ['button' => $this->Html->button('My button')]);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method, with `help` option
     * @test
     */
    public function testControlWithHelp(): void
    {
        $field = 'my-field';

        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'form-label', 'for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            ['div' => ['class' => 'form-text text-muted']],
            'My tip',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field, ['help' => 'My tip']));

        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'form-label', 'for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            ['div' => ['class' => 'form-text text-muted']],
            'Tip first line',
            '/div',
            ['div' => ['class' => 'form-text text-muted']],
            'Tip second line',
            '/div',
            '/div',
        ];
        $result = $this->Helper->control($field, ['help' => ['Tip first line', 'Tip second line']]);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method with checkboxes
     * @test
     */
    public function testControlCheckbox(): void
    {
        $field = 'my-field';
        $expected = [
            'div' => ['class' => 'form-check input checkbox'],
            'label' => ['class' => 'form-label', 'for' => $field],
            ['input' => ['type' => 'hidden', 'name' => $field, 'value' => '0']],
            ['input' => ['class' => 'form-check-input', 'type' => 'checkbox', 'name' => $field, 'value' => '1', 'id' => $field]],
            ' My Field',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field, ['type' => 'checkbox']));
    }

    /**
     * Tests for `control()` method with password inputs
     * @test
     */
    public function testControlPassword(): void
    {
        $expected = [
            'div' => ['class' => 'form-group input password'],
            'label' => ['class' => 'form-label', 'for' => 'old-password'],
            'Old Password',
            '/label',
            'input' => ['type' => 'password', 'name' => 'old-password', 'class' => 'form-control', 'id' => 'old-password'],
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control('old-password'));
    }

    /**
     * Tests for `control()` method with selects
     * @test
     */
    public function testControlSelect(): void
    {
        $field = 'my-field';
        $options = ['1' => 'First value', '2' => 'Second value'];

        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['class' => 'form-label', 'for' => $field],
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
        $result = $this->Helper->control($field, ['options' => $options, 'type' => 'select']);
        $this->assertHtml($expected, $result);

        //With `default` and `value`
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['class' => 'form-label', 'for' => $field],
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
        $result = $this->Helper->control($field, ['default' => '2', 'options' => $options,'type' => 'select']);
        $this->assertHtml($expected, $result);
        $result = $this->Helper->control($field, ['options' => $options, 'type' => 'select', 'value' => '2']);
        $this->assertHtml($expected, $result);

        //Custom `empty` value
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['class' => 'form-label', 'for' => $field],
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
        $result = $this->Helper->control($field, [
            'empty' => '(choose one)',
            'options' => $options,
            'type' => 'select',
        ]);
        $this->assertHtml($expected, $result);

        // `empty` disabled
        $expected = [
            'div' => ['class' => 'form-group input select'],
            'label' => ['class' => 'form-label', 'for' => $field],
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
        $result = $this->Helper->control($field, compact('options') + ['empty' => false, 'type' => 'select']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `control()` method with textarea
     * @test
     */
    public function testControlTextarea(): void
    {
        $field = 'my-field';
        $expected = [
            'div' => ['class' => 'form-group input textarea'],
            'label' => ['class' => 'form-label', 'for' => $field],
            'My Field',
            '/label',
            'textarea' => ['name' => $field, 'class' => 'form-control', 'id' => $field],
            '/textarea',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field, ['type' => 'textarea']));
    }

    /**
     * Tests for `control()` method, into an inline form
     * @test
     */
    public function testControlInline(): void
    {
        $field = 'my-field';

        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'form-label sr-only', 'for' => $field],
            'My Field',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->Helper->createInline();
        $this->assertHtml($expected, $this->Helper->control($field));

        //Tries with a checkbox
        $expected = [
            'div' => ['class' => 'form-check input checkbox'],
            'label' => ['class' => 'form-label sr-only', 'for' => $field],
            ['input' => ['type' => 'hidden', 'name' => $field, 'value' => '0']],
            ['input' => ['class' => 'form-check-input', 'type' => 'checkbox', 'name' => $field, 'value' => '1', 'id' => $field]],
            ' My Field',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field, ['type' => 'checkbox']));

        //Using `label` option
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'label' => ['class' => 'form-label sr-only', 'for' => $field],
            'My label',
            '/label',
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field, ['label' => 'My label']));

        //`label` option `false`
        $expected = [
            'div' => ['class' => 'form-group input text'],
            'input' => ['type' => 'text', 'name' => $field, 'class' => 'form-control', 'id' => $field],
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->control($field, ['label' => false]));
    }

    /**
     * Tests for `create()` method
     * @test
     */
    public function testCreate(): void
    {
        $expected = [
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'action' => '/'],
            '/form',
        ];
        $this->assertHtml($expected, $this->Helper->create() . $this->Helper->end());
    }

    /**
     * Tests for `createInline()` and `isInline()` methods
     * @test
     */
    public function testCreateInlineAndIsInline(): void
    {
        $expected = [
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'form-inline', 'action' => '/'],
            '/form',
        ];

        $this->assertFalse($this->Helper->isInline());
        $result = $this->Helper->createInline();
        $inline = $this->Helper->isInline();
        $result .= $this->Helper->end();
        $this->assertTrue($inline);
        $this->assertHtml($expected, $result);
        $this->assertFalse($this->Helper->isInline());

        //Tests `create()` method, with `inline` option
        $result = $this->Helper->create(null, ['inline' => true]);
        $inline = $this->Helper->isInline();
        $result .= $this->Helper->end();
        $this->assertTrue($inline);
        $this->assertHtml($expected, $result);
        $this->assertFalse($this->Helper->isInline());

        //Tests `create()` method, with `form-inline` class
        $result = $this->Helper->create(null, ['class' => 'form-inline']);
        $inline = $this->Helper->isInline();
        $result .= $this->Helper->end();
        $this->assertTrue($inline);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `label()` method
     * @test
     */
    public function testLabel(): void
    {
        $expected = [
            'label' => ['class' => 'form-label', 'for' => 'my-fieldname'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'My label',
            '/label',
        ];
        $this->assertHtml($expected, $this->Helper->label('my-fieldname', 'My label', ['icon' => 'home']));

        $expected = ['label' => ['class' => 'form-label', 'for' => 'my-fieldname'], '" \'', '/label'];
        $this->assertHtml($expected, $this->Helper->label('my-fieldname', '" \''));
    }

    /**
     * Tests for `postButton()` method
     * @test
     */
    public function testPostButton(): void
    {
        $title = 'My title';
        $url = 'https://link';

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'role' => 'button', 'class' => 'btn btn-light', 'title' => $title, 'onclick'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postButton($title, $url, ['icon' => 'home']));

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'class' => 'btn btn-danger', 'onclick', 'role' => 'button', 'title' => $title],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postButton($title, $url, ['class' => 'btn-danger']));

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => '/'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'class' => 'btn btn-light', 'onclick', 'role' => 'button', 'title' => ''],
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postButton());
    }

    /**
     * Tests for `postLink()` method
     * @test
     */
    public function testPostLink(): void
    {
        $title = 'My title';
        $url = 'https://link';

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => $title, 'onclick'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postLink($title, $url, ['icon' => 'home']));

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => 'My tooltip', 'data-toggle' => 'tooltip', 'onclick'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postLink($title, $url, ['tooltip' => 'My tooltip']));

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => '&amp;quot; &amp;#039;', 'onclick'],
            '" \'',
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postLink('" \'', $url));

        $expected = [
            'form' => ['name', 'style' => 'display:none;', 'method' => 'post', 'action' => '/'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'title' => '', 'onclick'],
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->postLink());
    }

    /**
     * Tests for `select()` method
     * @test
     */
    public function testSelect(): void
    {
        $field = 'my-field';
        $options = ['1' => 'First value', '2' => 'Second value'];

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
        $this->assertHtml($expected, $this->Helper->select($field, $options));

        //With `default` and `value`
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
        $this->assertHtml($expected, $this->Helper->select($field, $options, ['default' => '2']));
        $this->assertHtml($expected, $this->Helper->select($field, $options, ['value' => '2']));

        //Custom `empty` value
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
        $this->assertHtml($expected, $this->Helper->select($field, $options, ['empty' => '(choose one)']));

        //With `empty` disabled
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
        $this->assertHtml($expected, $this->Helper->select($field, $options, ['empty' => false]));
    }

    /**
     * Tests for `submit()` method
     * @test
     */
    public function testSubmit(): void
    {
        $title = 'My title';

        //The `type` option will be overwritten
        $expected = ['button' => ['type' => 'submit', 'class' => 'btn btn-success'], $title, '/button'];
        $this->assertHtml($expected, $this->Helper->submit($title, ['type' => 'reset']));

        $expected = [
            'button' => ['type' => 'submit', 'class' => 'btn btn-danger'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/button',
        ];
        $this->assertHtml($expected, $this->Helper->submit($title, ['class' => 'btn-danger', 'icon' => 'home']));
    }

    /**
     * Tests for `textarea()` method
     * @test
     */
    public function testTextarea(): void
    {
        $expected = ['textarea' => ['name' => 'my-field', 'class' => 'form-control'], '/textarea'];
        $this->assertHtml($expected, $this->Helper->textarea('my-field'));
    }
}

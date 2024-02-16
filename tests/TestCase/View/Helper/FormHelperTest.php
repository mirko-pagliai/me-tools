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

use Cake\Http\ServerRequest;
use Cake\View\Form\NullContext;
use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\FormHelper;

/**
 * FormHelperTest class
 */
class FormHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\FormHelper
     */
    protected FormHelper $Helper;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Helper ??= new FormHelper(new View());
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::_getLabel()
     */
    public function testGetLabel(): void
    {
        //Checkboxes and radios
        $expected = '<label class="form-label form-check-label"';
        foreach (['checkbox', 'radio'] as $type) {
            $result = $this->Helper->control('my-checkbox', compact('type'));
            $this->assertStringContainsString($expected, $result);
        }

        //Other inputs
        $expected = '<label class="form-label" for="my-control">My Control</label>';
        foreach (['text', 'textarea'] as $type) {
            $result = $this->Helper->control('my-control', compact('type'));
            $this->assertStringContainsString($expected, $result);
        }

        //With inline form
        $this->Helper->createInline();
        $this->assertStringContainsString('<label class="visually-hidden" for="my-control">', $this->Helper->control('my-control'));
        $this->assertStringContainsString('<label class="form-label form-check-label" for="my-checkbox">', $this->Helper->control('my-checkbox', ['type' => 'checkbox']));
        $this->Helper->end();

        //With `false` label or empty array
        $this->assertStringNotContainsString('label', $this->Helper->control('my-control', ['label' => false]));

        //With a string as label
        $expected = '<label class="form-label" for="my-control">My custom title</label>';
        $result = $this->Helper->control('my-control', ['label' => 'My custom title']);
        $this->assertStringContainsString($expected, $result);

        //With an array as label
        $expected = '<label class="my-custom-label form-label" for="my-control">My Control</label>';
        $result = $this->Helper->control('my-control', ['label' => ['class' => 'my-custom-label']]);
        $this->assertStringContainsString($expected, $result);

        //With an array as label and `text` option
        $expected = '<label class="form-label" for="my-control">My custom title</label>';
        $result = $this->Helper->control('my-control', ['label' => ['text' => 'My custom title']]);
        $this->assertStringContainsString($expected, $result);

        //With an icon
        $expected = '<label class="form-label" for="my-control"><i class="fa fa-home"> </i>My Control</label>';
        $result = $this->Helper->control('my-control', ['label' => ['icon' => 'home']]);
        $this->assertStringContainsString($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::_inputType()
     */
    public function testInputType(): void
    {
        foreach (['my-password', 'my-pwd'] as $fieldName) {
            $result = $this->Helper->control($fieldName);
            $this->assertStringContainsString('<input type="password"', $result);
        }

        $result = $this->Helper->control('save-my-password', ['type' => 'checkbox']);
        $this->assertStringNotContainsString('<input type="password"', $result);
    }

    /**
     * Tests all the validation functionalities
     * @test
     */
    public function testValidation(): void
    {
        /** @var \Cake\Http\ServerRequest&\PHPUnit\Framework\MockObject\MockObject $Request */
        $Request = $this->createConfiguredMock(ServerRequest::class, ['is' => true]);
        $Form = new FormHelper(new View($Request));

        //Input is valid (nothing different should happen)
        $expected = '<div class="mb-3 text">' .
            '<label class="form-label" for="my-field">My Field</label>' .
            '<input type="text" name="My field" class="form-control" id="my-field">' .
            '</div>';
        $Context = $this->createConfiguredMock(NullContext::class, ['hasError' => false]);
        $Form->context($Context);
        $this->assertSame($expected, $Form->control('My field'));

        $Context = $this->createConfiguredMock(NullContext::class, ['hasError' => true, 'error' => ['Error message!']]);

        //Input is invalid, but the `validation` option to `false`
        $Form->create($Context, ['validation' => false]);
        $result = $Form->control('My field');
        $Form->end();
        $this->assertStringNotContainsString('is-invalid', $result);

        //Input is invalid and has an error message
        $expected = '<div class="mb-3 text error">' .
            '<label class="form-label" for="my-field">My Field</label>' .
            '<input type="text" name="My field" class="form-control is-invalid" id="my-field" aria-invalid="true" aria-describedby="my-field-error">' .
            '<div class="invalid-feedback" id="my-field-error">Error message!</div>' .
            '</div>';
        $Form->create($Context);
        $result = $Form->control('My field');
        $Form->end();
        $this->assertSame($expected, $result);

        $Context = $this->createConfiguredMock(NullContext::class, ['hasError' => true, 'error' => ['First error!', 'Second error!']]);

        //Input is invalid and has multiple error messages
        $expected = '<div class="invalid-feedback" id="my-field-error"><ul class="ps-3"><li>First error!</li><li>Second error!</li></ul></div>';
        $Form->create($Context);
        $result = $Form->control('My field');
        $Form->end();
        $this->assertStringContainsString($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::button()
     */
    public function testButton(): void
    {
        $expected = '<button type="button" class="btn btn-primary">My button</button>';
        $result = $this->Helper->button('My button');
        $this->assertSame($expected, $result);

        //With `type` option
        $result = $this->Helper->button('My button', ['type' => 'submit']);
        $this->assertStringContainsString('type="submit"', $result);

        //With `class` option
        $result = $this->Helper->button('My button', ['class' => 'btn-success my-custom-class']);
        $this->assertStringContainsString('class="btn btn-success my-custom-class"', $result);

        //With `icon` option
        $result = $this->Helper->button('My button', ['icon' => 'check']);
        $this->assertStringContainsString('<i class="fa fa-check"> </i> My button</button>', $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::ckeditor()
     */
    public function testCkeditor(): void
    {
        $expected = '<textarea name="Text" class="editor wysiwyg" rows="5"></textarea>';
        $result = $this->Helper->ckeditor('Text');
        $this->assertSame($expected, $result);
    }

    /**
     * There is no `checkbox()` method, so this test checks that the parent method is not affected by other code
     * @test
     */
    public function testCheckbox(): void
    {
        $expected = '<input type="hidden" name="My checkbox" value="0">' .
            '<input type="checkbox" name="My checkbox" value="yes">';
        $result = $this->Helper->checkbox('My checkbox', ['value' => 'yes']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     */
    public function testControl(): void
    {
        $expected = '<div class="mb-3 text">' .
            '<label class="form-label" for="my-input">My Input</label>' .
            '<input type="text" name="my-input" class="form-control" id="my-input">' .
            '</div>';
        $result = $this->Helper->control('my-input');
        $this->assertSame($expected, $result);

        //With html (`escape` option)
        $result = $this->Helper->control('my-input', ['label' => 'My <em>input</em> label']);
        $this->assertStringContainsString('<label class="form-label" for="my-input">My <em>input</em> label</label>', $result);

        //With form text (`help` option)
        $result = $this->Helper->control('my-input', ['help' => 'this is an help text']);
        $this->assertStringEndsWith('<div class="form-text">this is an help text</div></div>', $result);

        //With form text (`help` option) as array
        $result = $this->Helper->control('my-input', ['help' => ['first help text', 'second help text']]);
        $this->assertStringEndsWith('<div class="form-text">first help text</div><div class="form-text">second help text</div></div>', $result);

        //With input group (`append-text` and `prepend-text` options)
        $expected = '<div class="mb-3 text">' .
            '<label class="form-label" for="my-field">My Field</label>' .
            '<div class="input-group">' .
            '<span class="input-group-text">first text</span>' .
            '<input type="text" name="My field" class="form-control" id="my-field">' .
            '<span class="input-group-text">second text</span>' .
            '</div>' .
            '</div>';
        $result = $this->Helper->control('My field', ['prepend-text' => 'first text', 'append-text' => 'second text']);
        $this->assertSame($expected, $result);

        //With input group (`append-text` options) and a custom `formGroup` template value (the default one set by me-tools will not be used)
        $result = $this->Helper->control('My field', [
            'append-text' => 'second text',
            'templates' => ['formGroup' => '{{label}}<div>{{prepend}}{{input}}{{append}}{{error}}</div>'],
        ]);
        $this->assertStringNotContainsString('<div class="input-group">', $result);

        //With input group (`append-text` and `prepend-text` options) as button and submit
        $expected = '<div class="mb-3 text">' .
            '<label class="form-label" for="my-field">My Field</label>' .
            '<div class="input-group">' .
            '<button type="button" class="btn btn-primary"><i class="fa fa-home"> </i> Prepend button</button>' .
            '<input type="text" name="My field" class="form-control" id="my-field">' .
            '<div class="submit"><input type="submit" class="btn btn-primary" value="Append submit"></div>' .
            '</div>' .
            '</div>';
        $result = $this->Helper->control('My field', [
            'prepend-text' => $this->Helper->button('Prepend button', ['icon' => 'home']),
            'append-text' => $this->Helper->submit('Append submit'),
        ]);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     */
    public function testControlWithCheckbox(): void
    {
        $expected = '<div class="mb-3 form-check checkbox">' .
            '<input type="hidden" name="my-checkbox" value="0">' .
            '<input type="checkbox" name="my-checkbox" value="1" class="form-check-input" id="my-checkbox">' .
            '<label class="form-label form-check-label" for="my-checkbox">' .
            'My Checkbox' .
            '</label>' .
            '</div>';
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox']);
        $this->assertSame($expected, $result);

        //With form text (`help` option)
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox', 'help' => 'this is an help text']);
        $this->assertStringEndsWith('<div class="form-text">this is an help text</div></div>', $result);

        //With form text (`help` option) as array
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox', 'help' => ['first help text', 'second help text']]);
        $this->assertStringEndsWith('<div class="form-text">first help text</div><div class="form-text">second help text</div></div>', $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     */
    public function testControlWithCkeditor(): void
    {
        $expected = '<div class="mb-3 ckeditor">' .
            '<label class="visually-hidden" for="text">Text</label>' .
            '<textarea name="Text" class="form-control editor wysiwyg" id="text" rows="5"></textarea>' .
            '</div>';
        $result = $this->Helper->control('Text', ['type' => 'ckeditor']);
        $this->assertSame($expected, $result);

        //With `label` option (by default the label is `false` for `ckeditor`)
        $result = $this->Helper->control('Text', ['label' => 'My label', 'type' => 'ckeditor']);
        $this->assertStringContainsString('><label class="form-label" for="text">My label</label>', $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     */
    public function testControlWithRadio(): void
    {
        $options = ['options' => ['yes' => 'Yes', 'no' => 'No'], 'type' => 'radio'];

        $expected = '<div class="mb-3 radio">' .
            '<label class="form-label form-check-label">My Radio</label>' .
            '<input type="hidden" name="My radio" id="my-radio" value="">' .
            '<div class="form-check">' .
            '<input type="radio" name="My radio" value="yes" id="my-radio-yes" class="form-check-input">' .
            '<label for="my-radio-yes">Yes</label>' .
            '</div>' .
            '<div class="form-check">' .
            '<input type="radio" name="My radio" value="no" id="my-radio-no" class="form-check-input">' .
            '<label for="my-radio-no">No</label>' .
            '</div>' .
            '</div>';
        $result = $this->Helper->control('My radio', $options);
        $this->assertSame($expected, $result);

        //With a custom `radioWrapper` template value (the default one set by me-tools will not be used)
        $result = $this->Helper->control('My radio', $options + ['templates' => ['radioWrapper' => '<div>{{label}}</div>']]);
        $this->assertStringNotContainsString('<div class="form-check">', $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     */
    public function testControlWithSelect(): void
    {
        $expected = '<div class="mb-3 select">' .
            '<label class="form-label" for="my-select">My Select</label>' .
            '<select name="My select" class="form-select" id="my-select">' .
            '<option value=""></option>' .
            '<option value="1">First</option>' .
            '<option value="2">Second</option>' .
            '</select>' .
            '</div>';
        $result = $this->Helper->control('My select', ['options' => [1 => 'First', 2 => 'Second'], 'type' => 'select']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     * @uses \MeTools\View\Helper\FormHelper::select()
     */
    public function testControlWithSelectMultiple(): void
    {
        $expected = '<div class="mb-3 select">' .
            '<label class="form-label" for="my-select">My Select</label>' .
            '<input type="hidden" name="my-select" value="">' .
            '<select name="my-select[]" multiple="multiple" class="form-select" id="my-select">' .
            '<option value="1">First</option>' .
            '<option value="2">Second</option>' .
            '<option value="3">Third</option>' .
            '</select>' .
            '</div>';
        $result = $this->Helper->control('my-select', ['options' => ['1' => 'First', '2' => 'Second', '3' => 'Third'], 'multiple' => 'multiple', 'type' => 'select']);
        $this->assertSame($expected, $result);

        $expected = '<div class="mb-3 select">' .
            '<label class="form-label" for="my-select">My Select</label>' .
            '<input type="hidden" name="my-select" id="my-select" value="">' .
            '<div class="form-check">' .
            '<input type="checkbox" name="my-select[]" value="1" id="my-select-1" class="form-check-input">' .
            '<label class="form-check-label" for="my-select-1">First</label>' .
            '</div>' .
            '<div class="form-check">' .
            '<input type="checkbox" name="my-select[]" value="2" id="my-select-2" class="form-check-input">' .
            '<label class="form-check-label" for="my-select-2">Second</label>' .
            '</div>' .
            '<div class="form-check">' .
            '<input type="checkbox" name="my-select[]" value="3" id="my-select-3" class="form-check-input">' .
            '<label class="form-check-label" for="my-select-3">Third</label>' .
            '</div>' .
            '</div>';
        $result = $this->Helper->control('my-select', ['options' => [1 => 'First', 2 => 'Second', 3 => 'Third'], 'multiple' => 'checkbox', 'type' => 'select']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::control()
     */
    public function testControlWithTime(): void
    {
        $expected = '<div class="mb-3 time">' .
            '<label class="form-label" for="my-time">My Time</label>' .
            '<input type="time" name="My time" step="60" class="form-control" id="my-time" value="">' .
            '</div>';
        $result = $this->Helper->control('My time', ['type' => 'time']);
        $this->assertSame($expected, $result);
    }

    /**
     * A complete test for an inline form.
     * Tries to completely follow the example proposed by the Bootstrap documentation.
     * @see https://getbootstrap.com/docs/5.3/forms/layout/#inline-forms
     * @test
     * @uses \MeTools\View\Helper\FormHelper::createInline()
     * @uses \MeTools\View\Helper\FormHelper::control()
     * @uses \MeTools\View\Helper\FormHelper::end()
     */
    public function testInlineForm(): void
    {
        $result = $this->Helper->createInline();
        $this->assertSame('<form method="post" accept-charset="utf-8" class="row row-cols-lg-auto g-2 align-items-center" action="/">', $result);

        $expected = '<div class="col-12 text">' .
            '<label class="visually-hidden" for="username">Username</label>' .
            '<div class="input-group">' .
            '<span class="input-group-text">@</span>' .
            '<input type="text" name="username" class="form-control" id="username">' .
            '</div>' .
            '</div>';
        $result = $this->Helper->control('username', ['prepend-text' => '@']);
        $this->assertSame($expected, $result);

        $expected = '<div class="col-12 select">' .
            '<label class="visually-hidden" for="preference">Preference</label>' .
            '<select name="preference" class="form-select" id="preference">' .
            '<option value="">Choose</option>' .
            '<option value="1">One</option>' .
            '<option value="2">Two</option>' .
            '<option value="3">Three</option>' .
            '</select>' .
            '</div>';
        $result = $this->Helper->control('preference', ['empty' => 'Choose', 'options' => [1 => 'One', 2 => 'Two', 3 => 'Three'], 'type' => 'select']);
        $this->assertSame($expected, $result);

        $expected = '<div class="col-12 form-check checkbox">' .
            '<input type="hidden" name="remember-me" value="0">' .
            '<input type="checkbox" name="remember-me" value="1" class="form-check-input" id="remember-me">' .
            '<label class="form-label form-check-label" for="remember-me">Remember Me</label>' .
            '</div>';
        $result = $this->Helper->control('remember-me', ['type' => 'checkbox']);
        $this->assertSame($expected, $result);

        $expected = '<div class="col-12 submit"><input type="submit" class="btn btn-primary" value="Submit"></div>';
        $result = $this->Helper->submit();
        $this->assertSame($expected, $result);

        $result = $this->Helper->end();
        $this->assertSame('</form>', $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::isInline()
     */
    public function testIsInline(): void
    {
        $this->assertFalse($this->Helper->isInline());
        $this->Helper->createInline();
        $this->assertTrue($this->Helper->isInline());
        $this->Helper->end();
        $this->assertFalse($this->Helper->isInline());
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::postButton()
     * @uses \MeTools\View\Helper\FormHelper::button()
     */
    public function testPostButton(): void
    {
        $expected = '<form method="post" accept-charset="utf-8" action="#">' .
            '<button type="submit" class="btn btn-primary">' .
            '<i class="fa fa-home"> </i> Title' .
            '</button>' .
            '</form>';
        $result = $this->Helper->postButton('Title', '#', ['icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<form method="post" accept-charset="utf-8" action="#">' .
            '<button type="submit" class="btn btn-danger">' .
            '<i class="fa fa-home"> </i> Title' .
            '</button>' .
            '</form>';
        $result = $this->Helper->postButton('Title', '#', ['class' => 'btn-danger', 'icon' => 'home']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::select()
     */
    public function testSelect(): void
    {
        $expected = '<select name="My select">' .
            '<option value=""></option>' .
            '<option value="1">First</option>' .
            '<option value="2">Second</option>' .
            '</select>';
        $result = $this->Helper->select('My select', ['1' => 'First', '2' => 'Second']);
        $this->assertSame($expected, $result);

        //With a custom `empty` option
        $expected = '<select name="My select">' .
            '<option value="">Choose a value!</option>' .
            '<option value="1">First</option>' .
            '<option value="2">Second</option>' .
            '</select>';
        $result = $this->Helper->select('My select', ['1' => 'First', '2' => 'Second'], ['empty' => 'Choose a value!']);
        $this->assertSame($expected, $result);

        //With `required` option
        $expected = '<select name="My select" required="required">' .
            '<option value="">-- select a value --</option>' .
            '<option value="1">First</option>' .
            '<option value="2">Second</option>' .
            '</select>';
        $result = $this->Helper->select('My select', ['1' => 'First', '2' => 'Second'], ['required' => true]);
        $this->assertSame($expected, $result);

        //With `default` option
        $expected = '<select name="My select">' .
            '<option value="1">First</option>' .
            '<option value="2" selected="selected">Second</option>' .
            '</select>';
        $result = $this->Helper->select('My select', ['1' => 'First', '2' => 'Second'], ['default' => 2]);
        $this->assertSame($expected, $result);

        //With a `default` and a custom `empty` options
        $expected = '<select name="My select">' .
            '<option value="">Choose a value!</option>' .
            '<option value="1">First</option>' .
            '<option value="2" selected="selected">Second</option>' .
            '</select>';
        $result = $this->Helper->select('My select', ['1' => 'First', '2' => 'Second'], ['default' => '2', 'empty' => 'Choose a value!']);
        $this->assertSame($expected, $result);

        //With `multiple`
        $expected = '<input type="hidden" name="my-select" value="">' .
            '<select name="my-select[]" multiple="multiple">' .
            '<option value="1">First</option>' .
            '<option value="2">Second</option>' .
            '<option value="3">Third</option>' .
            '</select>';
        $result = $this->Helper->select('my-select', ['1' => 'First', '2' => 'Second', '3' => 'Third'], ['multiple' => 'multiple']);
        $this->assertSame($expected, $result);

        //With `multiple` as checkboxes
        $expected = '<input type="hidden" name="my-select" id="my-select" value="">' .
            '<div class="form-check">' .
            '<input type="checkbox" name="my-select[]" value="1" id="my-select-1" class="form-check-input">' .
            '<label for="my-select-1">First</label>' .
            '</div>' .
            '<div class="form-check">' .
            '<input type="checkbox" name="my-select[]" value="2" id="my-select-2" class="form-check-input">' .
            '<label for="my-select-2">Second</label>' .
            '</div>' .
            '<div class="form-check">' .
            '<input type="checkbox" name="my-select[]" value="3" id="my-select-3" class="form-check-input">' .
            '<label for="my-select-3">Third</label>' .
            '</div>';
        $result = $this->Helper->select('my-select', [1 => 'First', 2 => 'Second', 3 => 'Third'], ['multiple' => 'checkbox']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\FormHelper::submit()
     */
    public function testSubmit(): void
    {
        $expected = '<div class="submit"><input type="submit" class="btn btn-primary" value="My submit"></div>';
        $result = $this->Helper->submit('My submit');
        $this->assertSame($expected, $result);

        //With custom classes
        $expected = '<div class="submit"><input type="submit" class="my-custom-class btn btn-success" value="My submit"></div>';
        $result = $this->Helper->submit('My submit', ['class' => 'my-custom-class btn-success']);
        $this->assertSame($expected, $result);
    }
}

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
use Cake\View\View;
use MeTools\TestSuite\HelperTestCase;
use MeTools\View\Helper\BootstrapFormHelper;

/**
 * FormHelperTest class
 * @property \MeTools\View\Helper\BootstrapFormHelper $Helper
 */
class BootstrapFormHelperTest extends HelperTestCase
{
    /**
     * Test for `button()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::button()
     */
    public function testButton(): void
    {
        $expected = '<button class="btn btn-link" type="button"><i class="fas fa-trash-alt"> </i> My button</button>';
        $result = $this->Helper->button('My button', ['class' => 'btn-link', 'icon' => 'trash-alt']);
        $this->assertSame($expected, $result);

        $expected = '<button class="btn btn-primary" type="reset">My button</button>';
        $result = $this->Helper->button('My button', ['type' => 'reset']);
        $this->assertSame($expected, $result);

        $expected = '<button class="btn btn-success" type="submit">My button</button>';
        $result = $this->Helper->button('My button', ['type' => 'submit']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `ckeditor()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::ckeditor()
     */
    public function testCkeditor(): void
    {
        $expected = '<div class="input mb-3 textarea"><textarea name="my-field" class="editor form-control my-class wysiwyg" id="my-field" rows="5"></textarea></div>';
        $result = $this->Helper->ckeditor('my-field', ['class' => 'my-class']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `control()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControl(): void
    {
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><input type="text" name="my-field" class="form-control" id="my-field"/></div>';
        $result = $this->Helper->control('my-field');
        $this->assertSame($expected, $result);

        //With `required` option
        $expected = '<div class="input mb-3 text required"><label class="form-label fw-bolder" for="my-field">My Field</label><input type="text" name="my-field" class="form-control" required="required" id="my-field"';
        $result = $this->Helper->control('my-field', ['required' => true]);
        $this->assertStringStartsWith($expected, $result);

        //Help text (form text)
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><input type="text" name="my-field" class="form-control" id="my-field"/><div class="form-text text-muted">first text</div><div class="form-text text-muted">second text</div></div>';
        $result = $this->Helper->control('my-field', ['help' => ['first text', 'second text']]);
        $this->assertSame($expected, $result);

        //With input group
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><div class="input-group"><span class="input-group-text">first text</span><input type="text" name="my-field" class="form-control" id="my-field"/><span class="input-group-text">second text</span></div></div>';
        $result = $this->Helper->control('my-field', ['prepend-text' => 'first text', 'append-text' => 'second text']);
        $this->assertSame($expected, $result);

        //With a custom label
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">A custom label</label><input type="text" name="my-field" class="form-control" id="my-field"/></div>';
        $result = $this->Helper->control('my-field', ['label' => 'A custom label']);
        $this->assertSame($expected, $result);

        //With a label with some options
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder my-label-class" for="my-field">A custom label</label><input type="text" name="my-field" class="form-control" id="my-field"/></div>';
        $result = $this->Helper->control('my-field', ['label' => ['text' => 'A custom label', 'class' => 'my-label-class']]);
        $this->assertSame($expected, $result);

        //With a disabled label
        $expected = '<div class="input mb-3 text"><input type="text" name="my-field" class="form-control" id="my-field"/></div>';
        $result = $this->Helper->control('my-field', ['label' => false]);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `control()` method, with a "checkbox" type
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::checkbox()
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControlCheckboxType(): void
    {
        $expected = '<div class="input mb-3 form-check"><input type="hidden" name="my-checkbox" value="0"/><label class="form-check-label fw-bolder" for="my-checkbox"><input type="checkbox" name="my-checkbox" value="1" class="form-check-input" id="my-checkbox">My Checkbox</label></div>';
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox']);
        $this->assertSame($expected, $result);

        //With `required` option
        $expectedStart = '<div class="input mb-3 form-check required"><input type="hidden" name="my-checkbox" value="0"/><label class="form-check-label fw-bolder" for="my-checkbox"><input type="checkbox" name="my-checkbox" value="1"';
        $expectedEnd = 'class="form-check-input" id="my-checkbox" required="required">My Checkbox</label></div>';
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox', 'required' => true]);
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);

        //On "inline" form
        $this->Helper->createInline();
        $expected = '<div class="col-12"><div class="form-check"><input type="hidden" name="my-inline-field" value="0"/><label class="form-check-label" for="my-inline-field"><input type="checkbox" name="my-inline-field" value="1" class="form-check-input" id="my-inline-field">My Inline Field</label></div></div>';
        $result = $this->Helper->control('my-inline-field', ['type' => 'checkbox']);
        $this->assertSame($expected, $result);

        //With error
        $expectedStart = '<div class="input mb-3 form-check required"><input type="hidden" name="my-checkbox" value="0"/><label class="form-check-label fw-bolder" for="my-checkbox"><input type="checkbox" name="my-checkbox" value="1"';
        $expectedEnd = 'class="form-check-input is-invalid" id="my-checkbox" required="required">My Checkbox</label>My error</div>';
        /** @var \MeTools\View\Helper\BootstrapFormHelper&\PHPUnit\Framework\MockObject\MockObject $Helper */
        $Request = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['is'])
            ->getMock();
        $Request->method('is')->willReturn(true);
        $Helper = $this->getMockForHelper(BootstrapFormHelper::class, ['error', 'isFieldError'], new View($Request));
        $Helper->method('error')->willReturn('My error');
        $Helper->method('isFieldError')->willReturn(true);
        $result = $Helper->control('my-checkbox', ['type' => 'checkbox', 'required' => true]);
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);

        //With error on "inline" form
        $Helper->createInline();
        $expectedStart = '<div class="col-12"><div class="form-check required error"><input type="hidden" name="my-checkbox" value="0"/><label class="form-check-label" for="my-checkbox"><input type="checkbox" name="my-checkbox" value="1"';
        $expectedEnd = 'class="form-check-input is-invalid" id="my-checkbox" required="required">My Checkbox</label>My error</div></div>';
        $result = $Helper->control('my-checkbox', ['type' => 'checkbox', 'required' => true]);
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);
    }

    /**
     * Test for `control()` method, with a fields that contains the "password" word
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::_inputType()
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControlPasswordField(): void
    {
        $expected = '<div class="input mb-3 password"><label class="form-label fw-bolder" for="my-password">My Password</label><input type="password" name="my-password" class="form-control" id="my-password"/></div>';
        $result = $this->Helper->control('my-password');
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `control()` method, with an inline form
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControlWithInlineForm(): void
    {
        $this->Helper->createInline();

        $expected = '<div class="col-12 text"><label class="visually-hidden" for="my-inline-field">My Inline Field</label><input type="text" name="my-inline-field" class="form-control" id="my-inline-field"/></div>';
        $result = $this->Helper->control('my-inline-field');
        $this->assertSame($expected, $result);

        //With a custom label text
        $expected = '<div class="col-12 text"><label class="visually-hidden" for="my-inline-field">My label</label><input type="text" name="my-inline-field" class="form-control" id="my-inline-field"/></div>';
        $result = $this->Helper->control('my-inline-field', ['label' => 'My label']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `control()` method, with validation
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControlWithValidation(): void
    {
        $Request = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['is'])
            ->getMock();
        $Request->method('is')->willReturn(true);
        $View = new View($Request);

        //Input is valid
        $Helper = new BootstrapFormHelper($View);
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><div class="input-group has-validation"><input type="text" name="my-field" class="form-control is-valid" id="my-field"/><span class="input-group-text">Append text</span></div><div class="form-text text-muted">My help text</div></div>';
        $result = $Helper->control('my-field', ['append-text' => 'Append text', 'help' => 'My help text']);
        $this->assertSame($expected, $result);

        //Input is invalid and has an error
        $expectedStart = '<div class="input mb-3 text error"><label class="form-label fw-bolder" for="my-field">My Field</label><div class="input-group has-validation"><input type="text" name="my-field" class="form-control is-invalid" id="my-field"';
        $expectedEnd = '<span class="input-group-text">Append text</span>My error</div><div class="form-text text-muted">My help text</div></div>';
        /** @var \MeTools\View\Helper\BootstrapFormHelper&\PHPUnit\Framework\MockObject\MockObject $Helper */
        $Helper = $this->getMockForHelper(BootstrapFormHelper::class, ['error', 'isFieldError'], $View);
        $Helper->method('error')->willReturn('My error');
        $Helper->method('isFieldError')->willReturn(true);
        $result = $Helper->control('my-field', ['append-text' => 'Append text', 'help' => 'My help text']);
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);
    }

    /**
     * Test for `createInline()` method
     * @uses \MeTools\View\Helper\BootstrapFormHelper::createInline()
     * @uses \MeTools\View\Helper\BootstrapFormHelper::end()
     * @uses \MeTools\View\Helper\BootstrapFormHelper::isInline()
     */
    public function testCreateInline(): void
    {
        $this->assertFalse($this->Helper->isInline());

        $expected = '<form method="post" accept-charset="utf-8" class="align-items-center g-3 my-class row row-cols-lg-auto" action="/">';
        $result = $this->Helper->createInline(null, ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $this->assertTrue($this->Helper->isInline());

        $this->assertSame('</form>', $this->Helper->end());

        $this->assertFalse($this->Helper->isInline());
    }

    /**
     * Test for `label()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::label()
     */
    public function testLabel(): void
    {
        $expected = '<label class="fw-bolder my-class" for="my-fieldname"><i class="fas fa-home"> </i> My label</label>';
        $result = $this->Helper->label('my-fieldname', 'My label', ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `select()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::select()
     */
    public function testSelect(): void
    {
        $options = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $expected = '<select name="my-fieldname" class="form-select"><option value=""></option><optgroup label="options"><option value="a">A</option><option value="b">B</option><option value="c">C</option></optgroup></select>';
        $result = $this->Helper->select('my-fieldname', compact('options'));
        $this->assertSame($expected, $result);

        $expected = '<select name="my-fieldname" class="form-select"><optgroup label="options"><option value="a">A</option><option value="b" selected="selected">B</option><option value="c">C</option></optgroup></select>';
        $result = $this->Helper->select('my-fieldname', compact('options'), ['default' => 'b']);
        $this->assertSame($expected, $result);

        //As for the previous one
        $result = $this->Helper->select('my-fieldname', compact('options'), ['value' => 'b']);
        $this->assertSame($expected, $result);

        $expected = '<select name="my-fieldname" class="form-select"><option value="">-- empty --</option><optgroup label="options"><option value="a">A</option><option value="b">B</option><option value="c">C</option></optgroup></select>';
        $result = $this->Helper->select('my-fieldname', compact('options'), ['empty' => '-- empty --']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `postButton()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::postButton()
     */
    public function testPostButton(): void
    {
        $expected = [
            'form' => ['name' => 'preg:/post_[a-z0-9]+/', 'style' => 'display:none;', 'method' => 'post', 'action' => 'http://link'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'class' => 'btn btn-light', 'onclick', 'role' => 'button', 'title' => 'My title'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'My title',
            '/a',
        ];
        $result = $this->Helper->postButton('My title', 'http://link', ['icon' => 'home']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `submit()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::submit()
     */
    public function testSubmit(): void
    {
        $expected = '<div class="submit"><button class="btn btn-success" value="My caption"><i class="fas fa-home"> </i> My caption</button></div>';
        $result = $this->Helper->submit('My caption', ['icon' => 'home']);
        $this->assertSame($expected, $result);

        //Reset type
        $expected = '<div class="submit"><button class="btn btn-primary" value="My reset caption"><i class="fas fa-home"> </i> My reset caption</button></div>';
        $result = $this->Helper->submit('My reset caption', ['icon' => 'home', 'type' => 'reset']);
        $this->assertSame($expected, $result);

        //With empty `$caption`
        $expected = '<div class="submit"><button class="btn btn-success" value="Submit">Submit</button></div>';
        $result = $this->Helper->submit();
        $this->assertSame($expected, $result);
    }
}

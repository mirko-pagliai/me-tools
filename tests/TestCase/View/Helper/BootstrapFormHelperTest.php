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

        //Input is invalid and has an error
        $expectedStart = '<div class="input mb-3 text error"><label class="form-label fw-bolder" for="my-field">My Field</label><input type="text" name="my-field" class="form-control is-invalid" id="my-field"';
        $expectedEnd = '/>My error</div>';
        /** @var \MeTools\View\Helper\BootstrapFormHelper&\PHPUnit\Framework\MockObject\MockObject $Helper */
        $Helper = $this->getMockForHelper(BootstrapFormHelper::class, ['error', 'isFieldError']);
        $Helper->method('error')->willReturn('My error');
        $Helper->method('isFieldError')->willReturn(true);
        $result = $Helper->control('my-field');
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);

        //Input is valid (request is "post")
        $Request = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['is'])
            ->getMock();
        $Request->method('is')->willReturn(true);
        $Helper = new BootstrapFormHelper(new View($Request));
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><input type="text" name="my-field" class="form-control is-valid" id="my-field"/></div>';
        $result = $Helper->control('my-field');
        $this->assertSame($expected, $result);

        //Help text (form text)
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><input type="text" name="my-field" class="form-control" id="my-field"/><div class="form-text text-muted">first text</div><div class="form-text text-muted">second text</div></div>';
        $result = $this->Helper->control('my-field', ['help' => ['first text', 'second text']]);
        $this->assertSame($expected, $result);

        //Input group
        $expected = '<div class="input mb-3 text"><label class="form-label fw-bolder" for="my-field">My Field</label><div class="input-group"><span class="input-group-text">first text</span><input type="text" name="my-field" class="form-control" id="my-field"/><span class="input-group-text">second text</span></div></div>';
        $result = $this->Helper->control('my-field', ['prepend-text' => 'first text', 'append-text' => 'second text']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `control()` method, with a "checkbox" type
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControlCheckboxType(): void
    {
        $expected = '<div class="input mb-3 form-check"><input type="hidden" name="my-checkbox" value="0"/><label class="form-label fw-bolder" for="my-checkbox"><input type="checkbox" name="my-checkbox" value="1" class="form-check-input" id="my-checkbox">My Checkbox</label></div>';
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox']);
        $this->assertSame($expected, $result);

        //With `required` option
        $expectedStart = '<div class="input mb-3 form-check required"><input type="hidden" name="my-checkbox" value="0"/><label class="form-label fw-bolder" for="my-checkbox"><input type="checkbox" name="my-checkbox" value="1" class="form-check-input" required="required" id="my-checkbox"';
        $expectedEnd = '>My Checkbox</label></div>';
        $result = $this->Helper->control('my-checkbox', ['type' => 'checkbox', 'required' => true]);
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);
    }

    /**
     * Test for `control()` method, with a fields that contains the "password" word
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::control()
     */
    public function testControlPasswordField(): void
    {
        $expected = '<div class="input mb-3 password"><label class="form-label fw-bolder" for="my-password">My Password</label><input type="password" name="my-password" class="form-control" id="my-password"/></div>';
        $result = $this->Helper->control('my-password');
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `label()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::label()
     */
    public function testLabel(): void
    {
        $expected = '<label class="form-label fw-bolder" for="my-fieldname"><i class="fas fa-home"> </i> My label</label>';
        $result = $this->Helper->label('my-fieldname', 'My label', ['icon' => 'home']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `postButton()` method
     * @return void
     * @uses \MeTools\View\Helper\BootstrapFormHelper::postButton()
     */
    public function testPostButton(): void
    {
        $title = 'My title';
        $url = 'http://link';

        $expected = [
            'form' => ['name' => 'preg:/post_[a-z0-9]+/', 'style' => 'display:none;', 'method' => 'post', 'action' => $url],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/form',
            'a' => ['href' => '#', 'class' => 'btn btn-light', 'onclick', 'role' => 'button', 'title' => $title],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $result = $this->Helper->postButton($title, $url, ['icon' => 'home']);
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
    }
}

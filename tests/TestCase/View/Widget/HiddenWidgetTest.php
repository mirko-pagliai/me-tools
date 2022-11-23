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
namespace MeTools\Test\TestCase\View\Widget;

use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\FormHelper;

/**
 * HiddenWidgetTest class
 */
class HiddenWidgetTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\FormHelper
     */
    protected FormHelper $Helper;

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->Helper ??= new FormHelper(new View());
    }

    /**
     * Tests for `render()` method
     * @uses \MeTools\View\Widget\HiddenWidget::render()
     * @test
     */
    public function testRender(): void
    {
        $expected = '<input type="hidden" name="My field"/>';
        $result = $this->Helper->hidden('My field');
        $this->assertSame($expected, $result);

        $expected = '<input type="hidden" name="My field" class="form-control" id="my-field"/>';
        $result = $this->Helper->control('My field', ['type' => 'hidden']);
        $this->assertSame($expected, $result);
    }
}

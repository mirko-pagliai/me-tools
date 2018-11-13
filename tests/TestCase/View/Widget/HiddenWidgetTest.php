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
namespace MeTools\Test\TestCase\View\Widget;

use MeTools\TestSuite\TestCase;
use MeTools\TestSuite\Traits\MockTrait;
use MeTools\View\Helper\FormHelper;

/**
 * HiddenWidgetTest class
 */
class HiddenWidgetTest extends TestCase
{
    use MockTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Helper;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Helper = $this->getMockForHelper(FormHelper::class, null);
    }

    /**
     * Tests for `render()` method
     * @test
     */
    public function testRender()
    {
        $field = 'My field';

        $result = $this->Helper->hidden($field);
        $expected = ['input' => ['type' => 'hidden', 'name' => $field]];
        $this->assertHtml($expected, $result);

        $result = $this->Helper->control($field, ['type' => 'hidden']);
        $expected = ['input' => ['type' => 'hidden', 'name' => $field, 'id' => 'my-field']];
        $this->assertHtml($expected, $result);
    }
}

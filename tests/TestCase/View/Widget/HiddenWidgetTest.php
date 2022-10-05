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

use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\FormHelper;

/**
 * HiddenWidgetTest class
 */
class HiddenWidgetTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\FormHelper&\PHPUnit\Framework\MockObject\MockObject
     */
    protected FormHelper $Helper;

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (empty($this->Helper)) {
            /** @var \MeTools\View\Helper\FormHelper&\PHPUnit\Framework\MockObject\MockObject $Helper */
            $Helper = $this->getMockForHelper(FormHelper::class, []);
            $this->Helper = $Helper;
        }
    }

    /**
     * Tests for `render()` method
     * @test
     */
    public function testRender(): void
    {
        $field = 'My field';

        $expected = ['input' => ['type' => 'hidden', 'name' => $field]];
        $this->assertHtml($expected, $this->Helper->hidden($field));

        $expected = ['input' => ['type' => 'hidden', 'name' => $field, 'id' => 'my-field']];
        $this->assertHtml($expected, $this->Helper->control($field, ['type' => 'hidden']));
    }
}

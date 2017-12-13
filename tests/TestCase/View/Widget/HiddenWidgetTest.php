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
    protected $Form;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Form = new FormHelper(new View);
    }

    /**
     * Tests for `render()` method
     * @return void
     * @test
     */
    public function testRender()
    {
        $field = 'My field';

        $result = $this->Form->hidden($field);
        $expected = ['input' => ['type' => 'hidden', 'name' => $field]];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control($field, ['type' => 'hidden']);
        $expected = ['input' => ['type' => 'hidden', 'name' => $field, 'id' => 'my-field']];
        $this->assertHtml($expected, $result);
    }
}

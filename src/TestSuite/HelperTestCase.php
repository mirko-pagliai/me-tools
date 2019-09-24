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
 * @since       2.17.5
 */
namespace MeTools\TestSuite;

use MeTools\TestSuite\TestCase;

/**
 * Abstract class for test helpers
 */
abstract class HelperTestCase extends TestCase
{
    /**
     * Helper instance
     * @var \Cake\View\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $Helper;

    /**
     * If `true`, a mock instance of the helper will be created
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * Called before every test method
     * @return void
     * @uses $Helper
     * @uses $autoInitializeClass
     */
    public function setUp()
    {
        parent::setUp();

        if (!$this->Helper && $this->autoInitializeClass) {
            $className = $this->getOriginClassName($this);
            class_exists($className) ?: $this->fail(sprintf('Class `%s` does not exist', $className));

            $this->Helper = $this->getMockForHelper($className, null);

            if (method_exists($this->Helper, 'initialize')) {
                $this->Helper->initialize([]);
            }
        }
    }
}

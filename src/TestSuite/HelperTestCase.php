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
 * @since       2.17.5
 */
namespace MeTools\TestSuite;

use Cake\View\View;
use MeTools\TestSuite\TestCase;

/**
 * Abstract class for test helpers
 */
abstract class HelperTestCase extends TestCase
{
    /**
     * Helper instance
     * @var \Cake\View\Helper
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
     */
    public function setUp(): void
    {
        parent::setUp();

        if (!$this->Helper && $this->autoInitializeClass) {
            /** @var class-string<\Cake\View\Helper> $className */
            $className = $this->getOriginClassNameOrFail($this);
            $this->Helper = new $className(new View());
        }
        if ($this->Helper && method_exists($this->Helper, 'initialize')) {
            $this->Helper->initialize([]);
        }
    }
}

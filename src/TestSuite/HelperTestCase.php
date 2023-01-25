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

/**
 * Abstract class for test helpers
 * @property \Cake\View\Helper $Helper The helper instance for which a test is being performed
 */
abstract class HelperTestCase extends TestCase
{
    /**
     * Get magic method.
     *
     * It provides access to the cached properties of the test.
     * @param string $name Property name
     * @return \Cake\View\Helper|mixed
     * @throws \ReflectionException
     */
    public function __get(string $name)
    {
        if ($name === 'Helper') {
            if (empty($this->_cache['Helper'])) {
                $this->_cache['Helper'] = new $this->originClassName(new View());
                $this->_cache['Helper']->initialize([]);
            }

            return $this->_cache['Helper'];
        }

        return parent::__get($name);
    }
}

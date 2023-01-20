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
 * @property \Cake\View\Helper $Helper
 */
abstract class HelperTestCase extends TestCase
{
    /**
     * Magic method
     * @param string $name Property name
     * @return \Cake\View\Helper|void
     * @noinspection PhpRedundantVariableDocTypeInspection
     */
    public function __get(string $name)
    {
        if ($name === 'Helper') {
            if (empty($this->_cache['Helper'])) {
                /** @var class-string<\Cake\View\Helper> $className */
                $className = $this->getOriginClassNameOrFail($this);
                $this->_cache['Helper'] = new $className(new View());

                if (method_exists($this->_cache['Helper'], 'initialize')) {
                    $this->_cache['Helper']->initialize([]);
                }
            }

            return $this->_cache['Helper'];
        }
    }
}

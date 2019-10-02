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
 * @see         \MeTools\Utility\BBCode
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;
use MeTools\Utility\BBCode;

/**
 * BBCode Helper.
 *
 * This helper allows you to handle some BBCode.
 * The `parser()` method executes all parsers.
 * @deprecated 2.18.14
 */
class BBCodeHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html' => ['className' => 'MeTools.Html']];

    /**
     * @var \MeTools\Utility\BBCode
     */
    public $BBCode;

    /**
     * Calls methods provided by the `BBCode` utility
     * @param string $method Method to invoke
     * @param array $params Array of params for the method
     * @return mixed|void
     * @uses \MeTools\Utility\BBCode
     */
    public function __call($method, $params)
    {
        deprecationWarning('The `BBCodeHelper` is deprecated. Use instead the `BBCode` utility');

        if (!method_exists(BBCode::class, $method)) {
            parent::__call($method, $params);
        }

        $this->BBCode = $this->BBCode ?: new BBCode($this->Html);

        return call_user_func_array([$this->BBCode, $method], $params);
    }
}

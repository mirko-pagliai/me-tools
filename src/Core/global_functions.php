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

use MeTools\View\OptionsParser;

if (!function_exists('optionsParser')) {
    /**
     * Returns and instance of `OptionsParser`
     * @param array $options Existing options
     * @param array|null $defaults Default values
     * @return \MeTools\View\OptionsParser
     */
    function optionsParser(array $options = [], ?array $defaults = [])
    {
        return new OptionsParser($options, $defaults);
    }
}

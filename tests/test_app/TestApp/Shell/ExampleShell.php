<?php
/**
 * This file is part of cakephp-assets.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-assets
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       2.17.5
 */
namespace App\Shell;

use MeTools\Console\Shell;

class ExampleShell extends Shell
{
    public static $tableHeaders = ['number', 'char', 'word'];

    public static $tableRows = [
        [1, 'a', 'alfa'],
        [2, 'b', 'beta'],
        [3, 'c', 'gamma'],
    ];

    public function doNothing()
    {
    }

    public function printTable()
    {
        $table = self::$tableRows;
        array_unshift($table, self::$tableHeaders);
        $this->helper('table')->output($table);
    }
}

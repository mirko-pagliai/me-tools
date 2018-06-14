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
use MeTools\View\OptionsParser;

if (!function_exists('clearDir')) {
    /**
     * Cleans a directory, deleting all the files, even in sub-directories
     * @param string $directory Directory path
     * @return bool
     */
    function clearDir($directory)
    {
        $success = true;

        //Deletes each file
        foreach (array_values(dir_tree($directory, 'empty'))[1] as $file) {
            if (!safe_unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }
}

if (!function_exists('optionsParser')) {
    /**
     * Returns and instance of `OptionsParser`
     * @param array $options Existing options
     * @param array|null $defaults Default values
     * @return OptionsParser
     */
    function optionsParser(array $options = [], $defaults = [])
    {
        return new OptionsParser($options, $defaults);
    }
}
